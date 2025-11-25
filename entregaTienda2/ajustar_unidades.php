<?php
require_once 'sesiones.php';
comprobar_sesion();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cod'], $_POST['operacion'])) {
    $cod = (int)$_POST['cod'];
    $operacion = $_POST['operacion'];

    if (!isset($_SESSION['carrito'][$cod])) {
        // Producto no encontrado, ignorar o redirigir
        header("Location: carrito.php");
        exit;
    }

    if ($operacion === 'sumar') {
        // Aumenta siempre en 1
        $_SESSION['carrito'][$cod]++;
    } elseif ($operacion === 'restar') {
        // Disminuye en 1, pero elimina el producto si la cantidad llega a 0
        if ($_SESSION['carrito'][$cod] > 1) {
            $_SESSION['carrito'][$cod]--;
        } else {
            // Si llega a 1 y se resta, elimina el producto completamente
            unset($_SESSION['carrito'][$cod]);
        }
    }
}

// Redirigir de vuelta al carrito
header("Location: carrito.php");
exit;
?>