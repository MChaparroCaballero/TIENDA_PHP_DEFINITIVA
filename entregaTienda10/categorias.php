<?php 
	/*comprueba que el usuario haya abierto sesión o redirige*/
	require 'sesiones.php';
	require_once 'bd.php';
	comprobar_sesion();
	//cargamos las categorías
	$categorias = cargar_categorias();

	//si no encuentra almacenamos un error con la bd
	if($categorias===false){
		$error = "Error al conectar con la base datos";
	}else{
		//sino error es nulo
		$error = null;
	}

	// si error es verdadero categorias esta vacia
	if ($error) {
        $categorias = []; 
    }
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset = "UTF-8">
		<title>Lista de categorías</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<link id="theme-link" rel="stylesheet" type="text/css" href="css/estilos.css">
	</head>
	<body class="body">
		<?php require 'cabecera.php';?>
		<h1 id="titulo">Categorías</h1>		
		<!--lista de vínculos con la forma 
		productos.php?categoria=1-->
		<?php
		//si error es true pues mostramos un mensaje de error
        if ($error) {
            echo "<p class='error'>".$error."</p>";
        } else {
            
           
			//si error no es true puede ser que o categorias este vacio el array el cual informamos de ello o este lleno
            if (empty($categorias)) {
                 echo "<p>No hay categorías disponibles.</p>";
            }else{
				//si categorias tiene elementos, creamos un contenedor principal donde crear las tarjetas
				echo '<div class="container my-5">';
				//hacemos columna con especificaciones a distintos tamaños (responsividad)
				echo '<div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">';

				//por cada categoria vamos a coger sus datos y crear una tarjeta
				foreach($categorias as $cat){              
                    $url = "productos.php?categoria=".$cat['CodCat'];

					//creamos la ruta de la img , y como comparten codigo concatenamos y ya esta
                    $imagen = "img/categorias/" . $cat['CodCat'] . '.png';
                    $cod = $cat['CodCat'];
                    

					//usamos bootstrap para toque moderno y efectos de sombra
                    echo '<div class="col">';
                    echo '<div class="card h-100 shadow-sm producto-card" onclick="window.location.href=\'' . htmlspecialchars($url) . '\'">';
                    echo '<img src="' . htmlspecialchars($imagen) . '" class="card-img-top" alt="' . htmlspecialchars($cat['Nombre']) . '" style="height: 250px; object-fit: contain;">';
                    echo '<div class="card-body text-center">';
                    echo '<h5 class="card-title">' . htmlspecialchars($cat['Nombre']) . '</h5>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
				echo '</div>';
                echo '</div>';
			}

            
            
        }
        ?>
		<?php require 'footer.php';?>
		<script src="js/cookies.js"></script>
	</body>
</html>