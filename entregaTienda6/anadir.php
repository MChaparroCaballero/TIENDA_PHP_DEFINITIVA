<?php 
/*comprueba que el usuario haya abierto sesión o redirige*/
require_once 'sesiones.php';
require_once 'bd.php';
comprobar_sesion();
$cod = $_POST['cod'];
$unidades = (int)$_POST['unidades'];

//si no existe el carro o es nulo//
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
		//que existe el producto, se suman las unidades a las que ya tenia
	$_SESSION['carrito'][$cod] += $unidades;
	}else{
		//si no existe se añaden sin mas
	$_SESSION['carrito'][$cod] = $unidades;		
	}

	//insertamos una nueva linea de carroProducto en la bd, porque sino si cierra la ventana, 
	// no nos mantiene los productos.
	insertar_pedido($_SESSION['carrito'], $_SESSION['usuario'], $carroActual);
}

header("Location: carrito.php");
