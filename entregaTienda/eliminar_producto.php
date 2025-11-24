<?php
require_once 'sesiones.php';
comprobar_sesion();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cod'])) {
    $cod = (int)$_POST['cod'];

    // Elimina la entrada completa del producto del carrito
    if (isset($_SESSION['carrito'][$cod])) {
        unset($_SESSION['carrito'][$cod]);
    }
}

// Redirigir de vuelta al carrito
header("Location: carrito.php");
exit;
?>