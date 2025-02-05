<?php

require_once './Conexion.php';
require_once './patrones.php';
require_once './funcionesBaseDeDatos.php';
require_once './funcionesValidacion.php';

/**
 * Description of Espectaculos
 * Clase Espectaculos que crea un objeto espectaculo.
 * @author José Martínez Estrada
 */
class Espectaculos {

    //Atributos Estáticos
    private static int $numEspectaculos = 0;
    //Atributos Privados
    private string $cdespec;
    private string $cdgru;
    //Atributos Públicos
    private string $nombre;
    private string $tipo = "teatro";
    private string $descripción;
    private int $estrellas;

    //Atributos Protegidos

    /**
     * Constructor de la clase Espectaculos que crea un espectaculo con los campos requeridos de la base de datos 
     * @param type $cdespec código del espectáculo
     * @param type $nombre nombre del espectáculo
     * @param type $cdgru código del grupo que interpretaá el espectáculo 
     * @param type $tipo tipo de espectáculo (Por defecto será teatro, ya que no puede estar vacío. Debe ser un espectáculo de teatro, de tv, un musical o cine).
     */
    public function __construct($cdespec, $nombre, $cdgru, $tipo) {
        $this->cdespec = validarCadena($cdespec);
        $this->nombre = validarCadena($nombre);
        $this->cdgru = validarNumero($cdgru);
        $this->estrellas = 0;
        $this->descripción = "";
        self::$numEspectaculos++;
    }

    //Métodos getter y setter
    public static function getNumEspectaculos() {
        return self::$numEspectaculos;
    }

    public function getCdespec() {
        return $this->cdespec;
    }

    public function getCdgru() {
        return $this->cdgru;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function getDescripción() {
        return $this->descripción;
    }

    public function getEstrellas() {
        return $this->estrellas;
    }

    public function setTipo(string $tipo) {
        $this->tipo = $tipo;
    }

    public function setDescripción(string $descripción) {
        $this->descripción = $descripción;
    }

    public function setEstrellas(int $estrellas) {
        $this->estrellas = $estrellas;
    }

    //Métodos de la base de datos

    /**
     * Método que da de alta o actualiza un espectaculo a la base de datos
     * @param type $espectaculo objeto espectaculo que será el nuevo espectaculo que queremos crear en la base de datos
     * @return string devuelve si se ha realizado o no correctamente la inserción
     */
    public function altaEspectaculos() {
        $conexionBD = Conexion::conectarEspectaculosMySQLi();
        $mensajeExito = "";
        if (!$conexionBD || $conexionBD->connect_error) {
            $mensajeExito = "Error en la conexión: " . $conexionBD->connect_error;
        }
        $codigoEspectaculo = $this->getCdespec();
        $nombreEspectaculo = $this->getNombre();
        $tipoEspectaculo = $this->getTipo();
        $estrellasEspectaculo = $this->getEstrellas();
        $codigoGrupo = $this->getCdgru();
        if (noExisteCodigoEspectaculo($codigoEspectaculo, $conexionBD)) {
            // Inserción del nuevo espectaculo
            $consultaEspectaculo = "INSERT INTO espectaculo (cdespec, nombre, tipo, estrellas, cdgru) VALUES (?, ?, ?, ?, ?)";
            $consultaPreparada = $conexionBD->prepare($consultaEspectaculo);
            $consultaPreparada->bind_param("sssis", $codigoEspectaculo, $nombreEspectaculo, $tipoEspectaculo, $estrellasEspectaculo, $codigoGrupo);
            if ($consultaPreparada->execute()) {
                $mensajeExito = "Inserción realizada correctamente.";
            } else {
                $mensajeExito = "Error en la inserción.";
                header("Location: ./errores/errorCreacionEspectaculo.html");
            }
        } else {
            // Si el código existe, procedemos  a actualizar
            $consultaActualizacion = "UPDATE espectaculo SET nombre = ?, tipo = ?, estrellas = ?, cdgru = ? WHERE cdespec = ?";
            $consultaPreparada = $conexionBD->prepare($consultaActualizacion);

            $consultaPreparada->bind_param("ssiss", $nombreEspectaculo, $tipoEspectaculo, $estrellasEspectaculo, $codigoGrupo, $codigoEspectaculo);

            if ($consultaPreparada->execute()) {
                $mensajeExito = "Actualización realizada correctamente.";
            } else {
                $mensajeExito = "Error en la actualización.";
                header("Location: ./errores/errorActualizacionEspectaculo.html");
            }
        }
        $consultaPreparada->close();
        return $mensajeExito;
    }

//Método actualizar(Lo cree para realizar pruebas)
//    public function actualizarEspectaculo($espectaculo) {
//        $conexionBD = Conexion::conectarEspectaculosMySQLi();
//        $mensajeExito = "";
//
//        if (!$conexionBD || $conexionBD->connect_error) {
//            $mensajeExito = "Error en la conexión: " . $conexionBD->connect_error;
//        }
//
//        $codigoEspectaculo = $espectaculo->getCdespec();
//        $nombreEspectaculo = $espectaculo->getNombre();
//        $tipoEspectaculo = $espectaculo->getTipo();
//        $estrellasEspectaculo = $espectaculo->getEstrellas();
//        $codigoGrupo = $espectaculo->getCdgru();
//
//        // Verificar si el código de espectaculo no existe
//        $existeCodigo = noExisteCodigoEspectaculo($codigoEspectaculo, $conexionBD);
//
//        if ($existeCodigo) {
//            $mensajeExito = "El código de espectaculo no existe. No se puede actualizar.";
//        } else {
//            // El código existe, proceder a actualizar
//            $consultaActualizacion = "UPDATE espectaculo SET nombre = ?, tipo = ?, estrellas = ?, cdgru = ? WHERE cdespec = ?";
//            $consultaPreparada = $conexionBD->prepare($consultaActualizacion);
//
//            $consultaPreparada->bind_param("ssiss", $nombreEspectaculo, $tipoEspectaculo, $estrellasEspectaculo, $codigoGrupo, $codigoEspectaculo);
//
//            if ($consultaPreparada->execute()) {
//                $mensajeExito = "Actualización realizada correctamente.";
//            } else {
//                $mensajeExito = "Error en la actualización.";
//                header("Location: ./errores/errorActualizacionEspectaculo.html");
//            }
//            $consultaPreparada->close();
//        }
//        return $mensajeExito;
//    }

    /**
     * Método que elimina un espectáculo existente en la base de datos
     * @param type $codigoEspectaculo el código del espectaculo que queremos eliminar
     * @return string devuelve una cadena de texto en el caso de que se realice correctamente u ocurra algún error
     */
    public function eliminarEspectaculo($codigoEspectaculo) {
        $conexionBD = Conexion::conectarEspectaculosMySQLi();
        $mensajeExito = "";

        // Verificamos si el código del espectáculo existe en la base de datos
        if (!noExisteCodigoEspectaculo($codigoEspectaculo, $conexionBD)) {
            $consultaEliminarEspectaculos = $conexionBD->stmt_init();

            // Preparamos la consulta SQL para eliminar el espectáculo
            $consultaEliminarEspectaculos->prepare("DELETE FROM espectaculo WHERE cdespec = ?");

            // Asociamos el parámetro a la consulta
            $consultaEliminarEspectaculos->bind_param("s", $codigoEspectaculo);

            // Ejecutamos la consulta
            if ($consultaEliminarEspectaculos->execute()) {
                $mensajeExito = "El espectáculo se eliminó correctamente.";
            } else {
                // Redirigir a la página de error si algo falla
                header("Location: ./errores/errorEliminacionEspectaculo.html");
            }
            // Cerramos la consulta
            $consultaEliminarEspectaculos->close();
        }
        return $mensajeExito;
    }

    /**
     * Método que busca el espectáculo existente con todos sus campos
     * @param type $codigoEspectaculo  el código del espectaculo del que queremos observar sus campos
     * @return string devuelve una cadena de texto en el caso de que falle, sino devuelve un array con todos  los campos de dicho espectaculo
     */
    public static function buscarEspectaculo($codigoEspectaculo) {
        $conexionBD = Conexion::conectarEspectaculosMySQLi();
        $mensajeResultado = " ";
        $esValido = false;
        if (!$conexionBD || $conexionBD->connect_error) {
            $mensajeResultado = "Error en la conexión: " . $conexionBD->connect_error;
        }

        // Verificar si el espectáculo existe
        $consultaExiste = $conexionBD->prepare("SELECT * FROM espectaculo WHERE cdespec = ?");
        $consultaExiste->bind_param("s", $codigoEspectaculo);
        $consultaExiste->execute();
        $resultado = $consultaExiste->get_result();

        if ($resultado->num_rows === 0) {
            $mensajeResultado = "El código del espectáculo no existe.";
        } else {
            $esValido = true;
            // Obtener los datos
            $datosEspectaculo = $resultado->fetch_assoc();
            $consultaExiste->close();
        }
        return $esValido ? $datosEspectaculo : $mensajeResultado;
    }

    public static function mostrarEspectaculo($codigoEspectaculo) {
        $conexionBD = Conexion::conectarEspectaculosMySQLi();
        $mensajeResultado = "";
        if (!$conexionBD || $conexionBD->connect_error) {
            $mensajeResultado = "Error en la conexión: " . $conexionBD->connect_error;
        }

        // Verificar si el espectáculo existe
        $consultaExiste = $conexionBD->prepare("SELECT * FROM espectaculo WHERE cdespec = ?");
        $consultaExiste->bind_param("s", $codigoEspectaculo);
        $consultaExiste->execute();
        $resultado = $consultaExiste->get_result();

        if ($resultado->num_rows === 0) {
            $mensajeResultado = "El código del espectáculo no existe.";
        }
        // Obtener los datos
        $datosEspectaculo = $resultado->fetch_assoc();
        $mensajeResultado = "Espectáculo encontrado: ";

        foreach ($datosEspectaculo as $campo => $valor) {
            $mensajeResultado .= "$campo: $valor, ";
        }

        // Eliminar la última coma y espacio extra
        $mensajeResultado = rtrim($mensajeResultado, ", ");

        $consultaExiste->close();
        return $mensajeResultado;
    }

    /**
     * Método que muestra todos los espectáculos de la base de datos existentes
     * @return string devuelve una cadena con todos los espectáculos encontrados en caso de exito, sino devuelve error.
     */
    public function mostrarTodosEspectaculos() {
        // Conectar a la base de datos
        $conexionBD = Conexion::conectarEspectaculosMySQLi();
        $mensajeResultado = "";

        // Consulta para obtener todos los espectáculos
        $consultaMostrarEspectaculos = "SELECT * FROM espectaculo";
        $resultadoConsultaMostrarEspectaculos = $conexionBD->query($consultaMostrarEspectaculos);

        // Verificar que la consulta se haya ejecutado y que existan registros
        if ($resultadoConsultaMostrarEspectaculos->num_rows > 0) {
            while ($datosConsultaMostrarEspectaculos = $resultadoConsultaMostrarEspectaculos->fetch_assoc()) {
                $mensajeResultado .= "Espectáculo: ";
                // Recorre cada campo del registro
                foreach ($datosConsultaMostrarEspectaculos as $campo => $valor) {
                    $mensajeResultado .= $campo . ": " . $valor;
                }
            }
        } else {
            $mensajeResultado = "No se encontraron espectáculos en la base de datos.";
        }

        return $mensajeResultado;
    }
}
