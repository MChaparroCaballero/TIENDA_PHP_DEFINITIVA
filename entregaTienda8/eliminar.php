<?php 
/*comprueba que el usuario haya abierto sesión o redirige*/
require_once 'sesiones.php';
comprobar_sesion();
$cod = $_POST['cod'];
$unidades = $_POST['unidades'];
/*si existe el código restamos las unidades, con mínimo de 0*/
if(isset($_SESSION['carrito'][$cod])){		

	//primero restamos las unidades que dice
	$_SESSION['carrito'][$cod] -= $unidades;
	if($_SESSION['carrito'][$cod] <= 0){
		//quitamos el producto del carro si es menor o igual a 0 para que desaparezca
		unset($_SESSION['carrito'][$cod]);
	}
	
}
//reirigimos para que vuelva a rehacer la tabla
header("Location: carrito.php");
exit;
