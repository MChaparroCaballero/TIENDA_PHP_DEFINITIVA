<?php
require_once 'bd.php';
/*formulario de login habitual
si va bien abre sesión, guarda el nombre de usuario y redirige a principal.php 
si va mal, mensaje de error */
if ($_SERVER["REQUEST_METHOD"] == "POST") {  
	
	//comprobamos el usuario
	$usu = comprobar_usuario($_POST['usuario'], $_POST['clave']);

	//que no coincide con la bd? error es true y almacenamos lo que ha puesto para que pueda ver si se confundio
	if($usu===false){
		$err = true;
		/*guardamos aqui el usuario para basicamente luego 
		que no tenga que volver a poner su gmail si es incorrecto, vamos que no desaparezca sin mas*/
		$usuario = $_POST['usuario'];

	}else{
		//si va bien? abrimos una sesion
		session_start();
		// $usu tiene campos correo y clave
		$usuario = $_POST['usuario'];

		//comprobamos si este usuario tenia un carro pendiente para cargarlo, imprimir error en caso de fallo de la carga
		try{
			$idCarro=cargar_carro_pendiente($usuario);
		}catch(Exception $e){
			//IMPRIMIMOS EN CASO DE QUE HAYA UN ERROR AL CARGAR EL CARRO PENDIENTE//
			echo "<p>".$e->getMessage()."</p>";
		}

		//si no existia un carro previo , creamos uno vacio y almacenamos al usuario
		if($idCarro==FALSE){
		$_SESSION['usuario'] = $usu;
		$_SESSION['carrito'] = [];

		}else{
		//que existia un carro previo? almacenamos la id del carro no completado que tenia el usuario para usarlo posteriormente
		$_SESSION['usuario'] = $usu;
		$codCarroExistente = $idCarro['CodCarro']; 
    	// Ahora enviamos ese código como parámetro para cargar los detalles (productos)
    	$_SESSION['CodCarro'] = $codCarroExistente;
		try{
			$_SESSION['carrito'] = cargar_productos_carrito_pendiente($codCarroExistente);
		}catch(Exception $e){
			//IMPRIMIMOS EN CASO DE QUE HAYA UN ERROR AL CARGAR EL CARRO PENDIENTE//
			echo "<p>".$e->getMessage()."</p>";
		}
    	
		}
		//le redirigimos a categorias
		header("Location: categorias.php");
		return;//usamos return en vez de exit porque esta en un if y es basicamente que me pare el script antes de que se escriba el html
	}	
}
?>
<!DOCTYPE html>
<html>
	<head>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<link id="theme-link" rel="stylesheet" type="text/css" href="css/estilos.css">
		<title>Formulario de login</title>
		<meta charset = "UTF-8">
	</head>
	<body id="bodyLogin">	
		
	
		<script src="js/login.js"></script>
		<script src="js/cookies.js"></script>
		<!--redirigimos al propio nombre del archivo, poque necesita validar aqui (javascript)-->
		<form class="formLogin" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method = "POST">
		<img src="img/iconos/iconoLogoSupermercado.png" alt="Logo de la tienda" class="logo">

		<!--comprobamos si habia sido redirigido anteriormente para mostra un mensaje de error o otro-->

		<!--si intento basicamente moverse por la cabecera del navegador-->
		<?php if(isset($_GET["redirigido"])){
			echo "<p class='error-message'>Haga login para continuar</p>";
		}?>
		<!--si no ha metido bien las credenciales-->
		<?php if(isset($err) and $err == true){
			echo "<p class='error-message'> Revise usuario y contraseña</p>";
		}?>

		<div>	
			<label for = "usuario">Usuario:</label> 
			<input value = "<?php if(isset($usuario))echo $usuario;?>"
			id = "usuario" name = "usuario" type = "text" required onblur="validarInput(this)">
		</div>
		<div>	
			<label for = "clave">Clave:</label> 
			<input id = "clave" name = "clave" type = "password" required onblur="validarInput(this)">					
		</div>
		<input id="submit" type = "submit" value="Iniciar Sesión"></input>
		</form>
	</body>
</html>