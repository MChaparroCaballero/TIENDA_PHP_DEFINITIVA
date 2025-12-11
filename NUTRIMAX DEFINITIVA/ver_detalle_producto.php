<?php 
	/*comprueba que el usuario haya abierto sesión o redirige*/
	require 'sesiones.php';
	require_once 'bd.php';
	comprobar_sesion();
    #Carpeta de imágenes (definición de una constante)
    	$carpeta_imagenes = "img/";
		$codProd = FALSE;
		#usamos fileter_var para evitar inyecciones y que compruebe que el numero entero que pasa ES UN ENTERO no que tiene letras por medio sino
		#nos devuelve FALSE y se jode todo
		if (isset($_GET['CodProd'])) {
    	$codProd = filter_var($_GET['CodProd'], FILTER_VALIDATE_INT);
		}
		#que efectivamente nos haya llegado un código de producto inválido pues te devuelvo al catalogo amigo
		if ($codProd === FALSE || $codProd <= 0) {
   		header("Location: productos.php"); 
    	exit;
		}
        try {
		$producto = cargar_producto($codProd);
		// si ese codigo de producto no existe te mando al catalogo
    	if ($producto === FALSE || $producto === NULL) {
            throw new Exception("Producto no encontrado.");
    	    }
        } catch (Exception $e) {
        // Capturamos cualquier excepción (Producto no encontrado o fallo de BD/SQL).
        // En ambos casos, queremos redirigir al catálogo.
        header("Location: productos.php");
        exit;
        }


        //almacenamos los valores del producto y les pasamos htmlspecials para que pueda traducir el navegador cualquier caracter especial//
		$nom = htmlspecialchars($producto['Nombre']);
    $des = htmlspecialchars($producto['Descripcion']);
    $precio = $producto['Precio'];
    $stock = $producto['stock'];

    //creamos la ruta de la imagen del producto, como las imagenes estan nombradas con el mismo codigo que su producto correspondiente,
    //  concatenacion simple
    $ruta_imagen = $carpeta_imagenes . $codProd . ".png";

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset = "UTF-8">
		<title>Tabla de productos por categoría</title>	
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link id="theme-link" rel="stylesheet" type="text/css" href="css/estilos.css">	
	</head>
	<body class="body">
		<?php 
		require 'cabecera.php';

    ///Contenedor principal centrado. container define el ancho y my-5
    //  añade un gran padding vertical para separarlo del header y footer
		echo '<div id="contenedorBloqueDatos" class="container my-5">';
    	echo '<h1 class="mb-4">Detalle del Producto: ' . $nom . '</h1>';
    	echo '<hr>';
    //Inicia la fila que contendrá las dos columnas principales (imagen y detalles).
    	echo '<div class="row">';
		echo '<div class="col-md-5">';//que ocupe 5 de 12 columanas en pantallas grandes y medianas //
    echo '<img id="productoEspecificoImagen" src="' . $ruta_imagen . '" class="img-fluid rounded" alt="' . $nom . '">';//hace que la imagen sea responsive (nunca exceda el ancho de su contenedor).//
    echo '</div>'; // CIERRA col-md-5 osea contenedor de la img
    
    // COLUMNA DERECHA: INFORMACIÓN Y COMPRA (columna, pantallas medianas, 7 de 12)
    echo '<div class="col-md-7">';
    
    // NOMBRE (bootstrap,margenes)

    echo '<h2 class="mt-3 mt-md-0 mb-1">' . $nom . '</h2>';
    
    // DESCRIPCIÓN (aumenta la fuente y añade una linea debajo)

    echo '<p class="lead border-bottom pb-3">' . $des . '</p>';
    
    // PRECIO Y UNIDADES (Misma línea)

    echo '<form action="anadir.php" method="POST" class="mt-4">';
    echo '<div class="d-flex align-items-center mb-4">';
    
    // PRECIO (Izquierda con margin end)
    echo '<div class="me-5">';
    //(bootstrap para letras de font)
    echo '<span class="fs-4 fw-bold">Precio:</span>';
    //(primary lo pone en azul)
    echo '<span class="fs-4 text-primary">' . number_format($precio, 2, ',', '.') . ' €</span>';
    echo '</div>';
    
    // UNIDADES (Al lado del precio), estilos predefinidos de bootstrap
    echo '<div class="input-group" style="max-width: 200px;">';
    echo '<span class="input-group-text">Unidades:</span>';
    
    if ($stock > 0) {
        echo '<input type="number" name="unidades" class="form-control" value="1" min="1" max="' . $stock . '" required>';
    } else {
        echo '<input type="number" class="form-control bg-light" value="0" disabled>';
    }
    echo '</div>'; // CIERRA input-group
    
    echo '</div>'; // CIERRA d-flex (línea Precio + Unidades)
    
    // BOTÓN AÑADIR AL CARRO (Abajo)
    echo '<input type="hidden" name="cod" value="' . $codProd . '">';
    
    //COMPROBAMOS SI HAY STOCK EN LA BD DE ESE PRODUCTO, SI ES ASI NOS EL BOTON DE COMPRAR SINO NOS DICE QUE ESTA AGOTADO EN ROJO
    if ($stock > 0) {
        echo '<button type="submit" class="btn btn-lg btn-success w-75">';//lo hace verde y en forma de pildora y su ancho
        echo 'Añadir al Carro';
        echo '</button>';
    } else {
        echo '<div class="alert alert-warning d-inline-block">'; //lo hace rojo y en la misma linea que el otro 
        echo 'Producto Agotado';
        echo '</div>';
    }
    
    echo '</form>'; // CIERRA FORMULARIO DE AÑADIR CARRITO
    
     //COMPROBAMOS SI HAY STOCK EN LA BD DE ESE PRODUCTO, SI ES ASI NOS PONE LA CANTIDAD SINO NOS DICE QUE ESTA AGOTADO EN ROJO
    // Información adicional y botón Volver
    echo '<p class="mt-3">';
    echo '<strong>Stock:</strong> ';
    //a lo operador ternario si esta pos lo enseña sino pues pone agotado
    echo $stock > 0 ? "<span id='unidadesStock' class='text-success'>$stock unidades</span>" : "<span class='text-danger'>Agotado</span>";
    echo '</p>';
    //el javascript:history.back() es para que el boton vuelva a la pagina anterior en la que estaba
    //  antes de esta sin yo tener que poner nada de la bd de codigo ni nada es puro retroceso
    echo '<a id="botonRetrocesoDeDetalleProducto" href="javascript:history.back()" class="btn btn-outline-secondary mt-3">← Volver al Catálogo</a>';
    
    echo '</div>'; // Cierra col-md-7 (Columna de Información)
    echo '</div>'; // Cierra row
    echo '</div>'; // Cierra container			
		?>		
    <?php require 'footer.php';?>	
    <script src="js/cookies.js"></script>	
	</body>
</html>