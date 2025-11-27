<?php
function comprobar_sesion(){
	session_start();
	if(!isset($_SESSION['usuario'])){	
		header("Location: index.php?redirigido=true");
	}		
}

