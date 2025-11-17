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
		<title>Lista de categorías</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	</head>
	<body>
		<?php require 'cabecera.php';?>
		<h1>Lista de categorías</h1>		
		<!--lista de vínculos con la forma 
		productos.php?categoria=1-->
		<?php
		$categorias = cargar_categorias();
		if($categorias===false){
			echo "<p class='error'>Error al conectar con la base datos</p>";
		}else{
			echo "<ul>"; //abrir la lista
			foreach($categorias as $cat){				
				/*$cat['nombre] $cat['codCat']*/
				$url = "productos.php?categoria=".$cat['CodCat'];
				echo "<li><a href='$url'>".$cat['Nombre']."</a></li>";
			}
			echo "</ul>";
		}
		?>
	</body>
</html>