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
		<title>Pedidos</title>	
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<link rel="stylesheet" type="text/css" href="css/estilos.css">	
	</head>
	<body class="d-flex flex-column min-vh-100">
	<?php 
	require 'cabecera.php';	
	echo "<div class='flex-grow-1 d-flex justify-content-center align-items-center'>";	//contendor responsive
	
	//esto false si falla
	$resul = completar_pedido($_SESSION['carrito'], $_SESSION['usuario'],$_SESSION['CodCarro']);

	//creamos variables donde almacenar los mensajes
	$mensaje = "";
    $clase_alerta = "";

	//que no se pudo completar el pedido? cambiamos a que el mensaje sea uno de error y que la clase
	//  sea alert-danger (bootstrap) para que automaticamenente la ponga con ese estilo y rojo
	if($resul === FALSE){
		$mensaje = "No se ha podido realizar el pedido";
        $clase_alerta = "alert-danger"; // Rojo para error		
	}else{
		//pero si el pedido se completo nos actualiza el estado del carro de 0 a 1 aka completado y nos informa de ello, ademas vaciamos el carro
		$correo = $_SESSION['usuario']['gmail'];
		actualizar_carro_enviado($resul);						
		$mensaje = "Pedido realizado con éxito.";
        $clase_alerta = "alert-success"; // Verde para éxito
		$_SESSION['carrito'] = [];

		}
		//creamos el mensaje de resultado de la accion
		echo "<div class='container'>";
            echo "<div class='alert {$clase_alerta} text-center' style='max-width: 400px; margin: 0 auto; font-size: 1.25em;'>";
            echo $mensaje;
            echo "</div>";
            echo "</div>";
			echo "</div>";
		require 'footer.php';
	?>		
	</body>
</html>
	