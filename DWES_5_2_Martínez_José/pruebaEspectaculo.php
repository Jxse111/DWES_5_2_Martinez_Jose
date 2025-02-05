<?php

require_once './Espectaculos.php';
require_once './Conexion.php';
$conexion = Conexion::conectarEspectaculosMySQLi();

// Creamos  un objeto de la clase Espectáculos
$espectaculo = new Espectaculos("QUE", "Quedate conmigo", "02", "teatro");

// Insertamos  el espectáculo en la base de datos
$mensaje = $espectaculo->altaEspectaculos($espectaculo);
echo $mensaje;

//// Mostramos un espectáculo específico mediante su código
$codigoEspectaculo = $espectaculo->getCdespec();
echo "Mostrar Espectáculo con código 'QUE':";
echo $espectaculo->mostrarEspectaculo($codigoEspectaculo);

// Mostramos todos los espectáculos
echo "Lista de todos los espectáculos: ";
echo $espectaculo->mostrarTodosEspectaculos();

// Asignamos estrellas al espectáculo
$espectaculo->setEstrellas(4);
echo " Se han asignado 4 estrellas al espectáculo 'Quédate conmigo'.";

// Actualizamos el espectáculo en la base de datos
$mensaje = $espectaculo->altaEspectaculos($espectaculo);
echo $mensaje;

// Eliminamos el espectáculo
//$mensajeEliminar = $espectaculo->eliminarEspectaculo($codigoEspectaculo);
//echo $mensajeEliminar;
?>
