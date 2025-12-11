<?php
require_once 'sesiones.php';
require_once 'bd.php';
comprobar_sesion();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cod'], $_POST['operacion'])) {
    $cod = (int)$_POST['cod'];
    $operacion = $_POST['operacion'];


       $stock=obtenerStock($cod);
       if ($operacion === 'sumar') {
           $stock=$stock+1;
       //aumentamos el stock del producto a 1 más//
        aumentarInventarioAdmin($cod,$stock);
        } elseif ($operacion === 'restar') {
            // Disminuye en 1, pero descataloga  el producto si la cantidad es menor que 0
                if ($stock >=1) {
                    $stock=$stock-1;
                disminuirInventarioAdmin($cod,$stock);
                } else{
                    // Si llega a 1 y se resta, elimina el producto completamente pero por errores de cascada simplemente lo dejara a 0 para simular la eliminacion
                    eliminarProductoDelAlmacen($cod);
                }
        }
    
}

// Redirigir de vuelta a la ventana de administrador
header("Location: administrador.php");
exit;
?>