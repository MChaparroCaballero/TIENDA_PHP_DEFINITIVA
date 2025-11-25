<!DOCTYPE html>
<html>
	<head>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<link rel="stylesheet" type="text/css" href="css/estilos.css">
		<title>Formulario de login</title>
		<meta charset = "UTF-8">
	</head>
	<body>	
<header id="cabeceraNavegacion">
<img alt="Logo de la tienda"src="./img/logoMercadona.png" width="300px" height="40px">

<div id="acciones">
     <div id="datosUsuario">
 Usuario: <?php echo $_SESSION['usuario']['nombre']?>
 </div>
 <div id="enlacesNav">
 <div class="contenedorIcono"><a href="categorias.php">
    <img src="./img/iconos/home.png" alt="Descripción de la imagen"></a></div>
    <div class="contenedorIcono">
    <a href="carrito.php">
    <img src="./img/iconos/carritoNegro.png" alt="Descripción de la imagen" ></a></div>
 <div class="contenedorIcono"><a href="logout.php">
    <img src="./img/iconos/logOut.webp" alt="Descripción de la imagen" ></a></div>
 </div>
</div>
</header>
</body>
</html>