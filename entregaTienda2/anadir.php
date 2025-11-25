<?php 
/*comprueba que el usuario haya abierto sesión o redirige*/
require_once 'sesiones.php';
require_once 'bd.php';
comprobar_sesion();
$cod = $_POST['cod'];
$unidades = (int)$_POST['unidades'];

if(!isset($_SESSION['CodCarro'])){

	//primero llenamos el carrito de la sesión con lo que acaba de añadir
	$_SESSION['carrito'][$cod] = $unidades;		
	//nos crea el carro y sus lineas de carro
	insertar_pedido_nuevo($_SESSION['carrito'], $_SESSION['usuario']);
}else{
	/*si existe el carro pasamos a comprobar si el producto existe o no en el carro
	y sumamos las unidades*/
	$carroActual = $_SESSION['CodCarro'];
	if(isset($_SESSION['carrito'][$cod])){
	$_SESSION['carrito'][$cod] += $unidades;
	}else{
	$_SESSION['carrito'][$cod] = $unidades;		
	}
	insertar_pedido($_SESSION['carrito'], $_SESSION['usuario'], $carroActual);
}

header("Location: carrito.php");
