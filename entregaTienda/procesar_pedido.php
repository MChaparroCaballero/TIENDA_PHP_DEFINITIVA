<?php
	/*comprueba que el usuario haya abierto sesión o redirige*/
	require 'correo.php';
	require 'sesiones.php';
	require_once 'bd.php';
	comprobar_sesion();
?>	
<!DOCTYPE html>
<html>
	<head>
		<meta charset = "UTF-8">
		<title>Pedidos</title>		
	</head>
	<body>
	<?php 
	require 'cabecera.php';			
	$resul = insertar_pedido($_SESSION['carrito'], $_SESSION['usuario']);
	if($resul === FALSE){
		echo "No se ha podido realizar el pedido<br>";			
	}else{
		$correo = $_SESSION['usuario']['gmail'];
		actualizar_carro_enviado($resul);
		echo "Pedido realizado con éxito.";							
		
		$_SESSION['carrito'] = [];

		}		
	?>		
	</body>
</html>
	