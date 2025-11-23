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
	</head>
	<body>
		<?php require 'cabecera.php';?>
		<h1>Lista de categorías</h1>		
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
				echo "<ul>"; 
				foreach($categorias as $cat){              
                $url = "productos.php?categoria=".$cat['CodCat'];
                echo "<li><a href='$url'>".$cat['Nombre']."</a></li>";
				 
            	}
            	echo "</ul>";
			}

            
            
        }
        ?>
	</body>
</html>