<?php 
/*comprueba que el usuario haya abierto sesión o redirige*/
require_once 'sesiones.php';
comprobar_sesion();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    //vacimaos el carro//
    $_SESSION['carrito'] = [];
}
header("Location: carrito.php");
exit;
?>