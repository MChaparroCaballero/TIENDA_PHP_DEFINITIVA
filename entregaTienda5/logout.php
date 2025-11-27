<?php
	//session_start();    // unirse a la sesión
						//comprobar si existe la variable usuario????
	require_once 'sesiones.php';	
	comprobar_sesion();
	$_SESSION = array();
	session_destroy();	// eliminar la sesion
	setcookie(session_name(), 123, time() - 1000); // eliminar la cookie
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset = "UTF-8">
		<title>Sesión cerrada</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<link id="theme-link" rel="stylesheet" type="text/css" href="css/estilos.css">
	</head>
	<body class="d-flex min-vh-100 justify-content-center align-items-center">
		<div id="contenedorDespedida">
		<p>La sesión se cerró correctamente, hasta la próxima</p>
		<div id="AccionDespedia"><a href = "index.php">Ir a la página de login</a></div>
	</div>
	<script src="js/cookies.js"></script>
	</body>
</html>