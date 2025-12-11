<?php
function comprobar_sesion(){
	session_start();
	if(!isset($_SESSION['usuario'])){	
		header("Location: index.php?redirigido=true");//redirigido lo usamos en index para que nos muestre o no los mensajes de error//
	}		
}

