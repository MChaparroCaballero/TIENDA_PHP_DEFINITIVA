<?php 
/*comprueba que el usuario haya abierto sesión o redirige*/
require_once 'sesiones.php';
require_once 'bd.php';
comprobar_sesion();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //necesitamos try aqui para capturar las excepciones que puede lanzar cargar_productos//
    try {
    //eliminar lineas de producto
            // Intenta cargar los productos. Esto lanzará una Exception si el carrito está vacío
            $productos = cargar_productos(array_keys($_SESSION['carrito']));
			

        //Verificamos si hubo un error de BD (retornando FALSE) o si la carga fue exitosa.
        // Si $productos sigue siendo FALSE, asumimos un error de conexión/SQL que no fue la excepción de "carrito vacío".
        if($productos === FALSE && !empty(array_keys($_SESSION['carrito']))){
     
            throw new exception ("Error no se pudo procesar la eliminacion del carro");
        } 
        // Si $productos es un resultado válido (no FALSE y no pasó por el catch de carrito vacío), mostramos la tabla.
        else if ($productos !== FALSE) {

            //por cada producto del carro mostramos sus datos en la tabla
            foreach($productos as $producto){
                $cod = $producto['CodProd'];
                eliminar_producto_carro_pendiente($cod);
            }   
            //vacimaos el carro en sesion//
    $_SESSION['carrito'] = [];
    }
    } catch (Exception $e) {
       // Captura 'Carrito vacío' y tu excepción de 'Error de BD'.
        // Si la excepción es la de "carrito vacío", el objetivo de vaciar ya se cumplió.
        // Aseguramos que la sesión esté vacía antes de la redirección.
        $_SESSION['carrito'] = []; 
        
        // Opcional: Registra el error para el administrador
        error_log("Fallo al eliminar todos los productos: " . $e->getMessage());

        // El script continúa con la redirección, ya que el carrito de sesión
        // ha quedado vacío y no hay un error fatal.
    }
}
//redirigimos para que vuelva a crear la tabla
header("Location: carrito.php");
exit;
?>