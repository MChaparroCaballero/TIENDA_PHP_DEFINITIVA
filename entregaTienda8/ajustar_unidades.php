<?php
require_once 'sesiones.php';
require_once 'bd.php';
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
        //para pasarlo como parametro y no aunque la hicieramos global tendriamos que actualizarla todo el rato osea cambiamos de dos lineas a 3
        $unidades=$_SESSION['carrito'][$cod];
        aumentar_unidades_producto_carrito($cod,$unidades);
    } elseif ($operacion === 'restar') {
        // Disminuye en 1, pero elimina el producto si la cantidad llega a 0
        if ($_SESSION['carrito'][$cod] > 1) {
            $_SESSION['carrito'][$cod]--;
            $unidades=$_SESSION['carrito'][$cod];
            disminuir_unidades_producto_carrito($cod,$unidades);
        } else {
            // Si llega a 1 y se resta, elimina el producto completamente
            eliminar_producto_carro_pendiente($cod);
            unset($_SESSION['carrito'][$cod]);
        }
    }
}

// Redirigir de vuelta al carrito
header("Location: carrito.php");
exit;
?>