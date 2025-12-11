<?php
require_once 'bd.php'; 
require_once 'sesiones.php';
comprobar_sesion();
//ruta de la carpeta de destino de las que subimos//
$carpetaDestino = "img/"; 

// Inicializar $codigoId y $target_file para usarlos más tarde de forma limpia.
$codigoId = null;
//
$rutaDestino = null; 

try {
   
    
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception("Método de solicitud no permitido.");
    }
    
    
    //comprobamos que hayan pasado todos los campos de forma valida, osea estan todos y no son nulos que si no casca//
    if (!isset($_POST['nombre'], $_POST['descripcion'], $_POST['stock'], $_POST['codCat'], $_POST['precio'])) {
        throw new Exception("Faltan datos obligatorios del producto en el formulario.");
    }
    
    // Preparamos los datos, basicamente nos aseguramos que los numericos sean numericos//
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $stock = (int)$_POST['stock'];
    $codCat = $_POST['codCat'];
    $precio = (float)$_POST['precio'];
    
    
    //el files es como un $post_especifico para imagenes que se suben en el navegador aka lo que se enviaba junto con el resto de campos del formulario gracias a enctype="multipart/form-data"//
    
    //primero comprobamos si existe un campo de tipo de archivos que se llame imagen y que no dio error, lo de UPLOAD_ERR es una constante de php que basicamente es 0 osea exito de subida entonces es Verificar
    //si existe el campo y que el codigo de error asociado al campo sea el de que tuvo exito
    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == UPLOAD_ERR_OK) {
        
        
        //guardamos la imagen completa, osea el img, el nombre, alt todo//
        $objetoImagen = $_FILES["imagen"];
        
        // Verificamos que archivo es realmente una imagen, con getimagesize que aparte de leer la extension tambien se pone a buscar metadatos en el archivo de los difereentes tipos de imagenes, que sino pone en el nombre .png y nos la cuelan que devolvera false si no encuentra o si hay algun error.
        //Y lo de tmp_name es basicamente que buscamos la ruta de donde a guardado PHP el archivo temporalmente en el servidor
        //OSEA LE ESTAMOS DICIENDO COMPRUEBA QUE LA IMAGEN DE ESTA RUTA ES UNA IMAGEN//
        $check = getimagesize($objetoImagen["tmp_name"]);
        if($check === false) {
             throw new Exception("El archivo subido no es una imagen válida.");
        }
        
        //Guardamos la ruta temporal//
        $rutaTemporal = $objetoImagen["tmp_name"];
        
    } else {
        // Si no se subió imagen, o falló la subida inicial, lanzamos excepción.
        throw new Exception("Error: No se ha seleccionado ninguna imagen o hubo un fallo de subida.");
    }

    
    
    //insertamos el producto y guardamos el id de la ultima insercion osea la de ahora//
    $codigoId = crearProducto($nombre, $descripcion, $stock, $codCat, $precio); 
    
    
    //Creamos el nuevo nombre de la imagen
    $nombreFinalImagen = $codigoId . ".png"; 
    
    //hacemos la ruta de destino
    $rutaDestino = $carpetaDestino . $nombreFinalImagen; 

    
    //con move_uploaded_file movemos un archivo temporal de donde esta a una ruta de destino//
   if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
        
        //si todo ha ido bien guardamos el tipo de mensaje de resultado y nombre de clase de bootstrap correspondiente en variables de sesiones, para que la siguiente pantalla acceda a ellas//
        $_SESSION['alerta_mensaje'] = "Producto añadido con éxito!"; 
        $_SESSION['alerta_tipo'] = "alert-success";
        //redirigimos a la ventana de resultado
        header("Location: producto_confirmacion.php"); 
        exit;
        
    } else {
        // Falla al mover el archivo hacemos throw
        throw new Exception("Fallo al mover el archivo al destino final. Verifique permisos de la carpeta '$carpetaDestino'.");
    }
       
    
} catch (Exception $e) {
    
   
    
    // si ocurre algun error con la imagen, al no ser transaccion no podemos hacer rollback pero en su ausencia comprobamos primero si existe el destino y el archivo y si es asi y aun asi hubo erro eliminamos el archivo//
    if ($rutaDestino && file_exists($rutaDestino)) {
        // No debería ocurrir si falló move_uploaded_file, pero es una buena práctica
        unlink($rutaDestino); 
    }
    
    //  Como ocurrio un error y no podemos desacer, directamente eliminamos de la bd el registro de producto que acabamos de crear, comprobando que el codigo existe no vaya a ser que es que ha habido un error antes de llegar alli que entonces volvera a dar error al intentar eliminar//
    if ($codigoId) {
        eliminarProductoDelAlmacen($codigoId);    
    }
    
    //guardamos el mensaje y el nombre de bootstrap de la clase correspondiente//
    $_SESSION['alerta_mensaje'] = "ERROR: No se pudo añadir el producto. Detalle: " . htmlspecialchars($e->getMessage());
    $_SESSION['alerta_tipo'] = "alert-danger";
    
    //redirigimos
    header("Location: producto_confirmacion.php"); 
    exit;
}
?>