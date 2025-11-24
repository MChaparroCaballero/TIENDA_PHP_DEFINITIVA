<?php 
	/*comprueba que el usuario haya abierto sesión o redirige*/
	require 'sesiones.php';
	require_once 'bd.php';
	comprobar_sesion();
	//cargamos las categorías
	$categorias = cargar_categorias();
	if($categorias===false){
		$error = "Error al conectar con la base datos";
	}else{
		$error = null;
	}

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
		<link rel="stylesheet" type="text/css" href="css/estilos.css">
	</head>
	<body>
		<?php require 'cabecera.php';?>
		<h1 id="titulo">Categorías</h1>		
		<!--lista de vínculos con la forma 
		productos.php?categoria=1-->
		<?php
        if ($error) {
            echo "<p class='error'>".$error."</p>";
        } else {
            // Se asume que $categorias es un array con datos (o vacío)
           
			
            if (empty($categorias)) {
                 echo "<p>No hay categorías disponibles.</p>";
            }else{
				echo '<div class="container my-5">';
				echo '<div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">';
				foreach($categorias as $cat){              
                    $url = "productos.php?categoria=".$cat['CodCat'];
                    $imagen = "img/categorias/" . $cat['CodCat'] . '.png';
                    $cod = $cat['CodCat'];
                    
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
	</body>
</html>