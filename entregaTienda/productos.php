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
		<style>
            /* Estilo para que toda la tarjeta sea clickable y muestre puntero */
            .producto-card {
                cursor: pointer; 
                transition: transform 0.2s, box-shadow 0.2s;
            }
            .producto-card:hover {
                transform: translateY(-5px); /* Pequeño efecto hover */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
            }
        </style>
	</head>
	<body>
		<?php 
		
		require 'cabecera.php';

		#para guardar errores, no un error MULTIPLES#
		$error_productos = NULL;
		#aqui guardamos el codigo de la categoria#
		$codCat = FALSE; 
		//Cargamos y validamos el ID de entrada osea la parseamos a un entero pero comprobando que sea 
		// valido osea no es simplemente un numero y letras lo verificamos completp
		if (isset($_GET['categoria'])) {
    	$codCat = filter_var($_GET['categoria'], FILTER_VALIDATE_INT);
		}
		// Si el código de categoría no es válido, mostramos un mensaje y salimos
		if ($codCat === FALSE || $codCat <= 0) {
            echo "<div class='container my-5'>";
            echo "<p class='alert alert-warning'>⚠️ Por favor, selecciona una categoría válida para ver los productos.</p>";
            echo "</div>";
            exit;
        }
		try {
            //cargamos la categoria para mostrar su nombre y descripcion
            $cat = cargar_categoria($codCat); 
            
             
            //cargamos los productos de esa categoria
            $productos = cargar_productos_categoria($codCat);
            
        } catch (Exception $e) {
            // Captura la excepción y muestra un mensaje de error 
            $error_productos = $e->getMessage();
			echo "<div class='container my-5'>";
			echo "<p class='alert alert-warning'>⚠️ " . htmlspecialchars($error_productos) . "</p>";
			echo "</div>";
			exit;
        }
		
		echo "<div class='container my-5'>";
		echo "<h1>Catálogo: ". htmlspecialchars($cat['Nombre']). "</h1>";
        echo "<p class='lead'>". htmlspecialchars($cat['Descripcion'])."</p>";
		echo '<div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">';	
		$carpeta_imagenes = "img/";

		foreach($productos as $producto){
			$cod = $producto['CodProd'];
			$nom = $producto['Nombre'];
			$precio = $producto['Precio'];
			$ruta_imagen = $carpeta_imagenes . $cod . ".png";	
			echo '<div class="col">';							
			echo "<tr>";
			echo '<div class="card h-100 shadow-sm producto-card" ';
        	echo "onclick=\"window.location.href='ver_detalle_producto.php?CodProd={$cod}'\">";
        
        	// Imagen
        	echo '<img src="' . $ruta_imagen . '" class="card-img-top" alt="' . $nom . '" style="height: 200px; object-fit: contain;">';
        	echo '<div class="card-body text-center d-flex flex-column">';
        	// Nombre
        	echo '<h5 class="card-title mb-1">' . $nom . '</h5>';
        
        	// Precio
       		 echo '<p class="card-text fs-4 text-primary mt-auto">';
        	echo number_format($precio, 2, ',', '.') . ' €';
        	echo '</p>';
        
        	echo '</div>'; // Cierra card-body
        	echo '</div>'; // Cierra card
        	echo '</div>'; // Cierra col

    	}
    	echo '</div>'; // Cierra row		
		?>				
	</body>
</html>