<?php 
	/*comprueba que el usuario haya abierto sesión o redirige*/
	require 'sesiones.php';
	require_once 'bd.php';
	comprobar_sesion();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset = "UTF-8">
		<title>Tabla de productos por categoría</title>	
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">	
	</head>
	<body>
		<?php 
		require 'cabecera.php';
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
		$producto = cargar_producto($codProd);
		// si ese codigo de producto no existe te mando al catalogo
    	if ($producto === FALSE) {
        header("Location: productos.php");
        exit;
    	}


		$nom = htmlspecialchars($producto['Nombre']);
    $des = htmlspecialchars($producto['Descripcion']);
    $precio = $producto['Precio'];
    $stock = $producto['stock'];
    $ruta_imagen = $carpeta_imagenes . $codProd . ".png";

		echo '<div class="container my-5">';
    	echo '<h1 class="mb-4">Detalle del Producto: ' . $nom . '</h1>';
    	echo '<hr>';
    
    	echo '<div class="row">';
		echo '<div class="col-md-5">';
    echo '<img src="' . $ruta_imagen . '" class="img-fluid rounded shadow-sm" alt="' . $nom . '">';
    echo '</div>'; // Cierra col-md-5
    
    // 2. COLUMNA DERECHA: INFORMACIÓN Y COMPRA
    echo '<div class="col-md-7">';
    
    // A. NOMBRE
    echo '<h2 class="mt-3 mt-md-0 mb-1">' . $nom . '</h2>';
    
    // B. DESCRIPCIÓN
    echo '<p class="lead text-muted border-bottom pb-3">' . $des . '</p>';
    
    // C. PRECIO Y UNIDADES (Misma línea)
    echo '<form action="anadir.php" method="POST" class="mt-4">';
    
    echo '<div class="d-flex align-items-center mb-4">';
    
    // PRECIO (Izquierda)
    echo '<div class="me-5">';
    echo '<span class="fs-4 fw-bold">Precio:</span>';
    echo '<span class="fs-4 text-primary">' . number_format($precio, 2, ',', '.') . ' €</span>';
    echo '</div>';
    
    // UNIDADES (Al lado del precio)
    echo '<div class="input-group" style="max-width: 200px;">';
    echo '<span class="input-group-text">Unidades:</span>';
    
    if ($stock > 0) {
        echo '<input type="number" name="unidades" class="form-control" value="1" min="1" max="' . $stock . '" required>';
    } else {
        echo '<input type="number" class="form-control bg-light" value="0" disabled>';
    }
    echo '</div>'; // Cierra input-group
    
    echo '</div>'; // Cierra d-flex (línea Precio + Unidades)
    
    // D. BOTÓN AÑADIR AL CARRO (Abajo)
    echo '<input type="hidden" name="cod" value="' . $codProd . '">';
    
    if ($stock > 0) {
        echo '<button type="submit" class="btn btn-lg btn-success w-75">';
        echo '🛒 Añadir al Carro';
        echo '</button>';
    } else {
        echo '<div class="alert alert-warning d-inline-block">';
        echo 'Producto Agotado';
        echo '</div>';
    }
    
    echo '</form>'; // Cierra Formulario de Añadir al Carrito
    
    // Información adicional y botón Volver
    echo '<p class="mt-3">';
    echo '<strong>Stock:</strong> ';
    echo $stock > 0 ? "<span class='text-success'>$stock unidades</span>" : "<span class='text-danger'>Agotado</span>";
    echo '</p>';
    echo '<a href="javascript:history.back()" class="btn btn-outline-secondary mt-3">← Volver al Catálogo</a>';
    
    echo '</div>'; // Cierra col-md-7 (Columna de Información)
    echo '</div>'; // Cierra row
    echo '</div>'; // Cierra container			
		?>				
	</body>
</html>