<?php
require_once 'bd.php';
/*formulario de login habitual
si va bien abre sesión, guarda el nombre de usuario y redirige a principal.php 
si va mal, mensaje de error */
if ($_SERVER["REQUEST_METHOD"] == "POST") {  
	
	$usu = comprobar_usuario($_POST['usuario'], $_POST['clave']);
	if($usu===false){
		$err = true;
		/*guardamos aqui el usuario para basicamente luego 
		que no tenga que volver a poner su gmail si es incorrecto, vamos que no desaparezca sin mas*/
		$usuario = $_POST['usuario'];
	}else{
		session_start();
		// $usu tiene campos correo y clave
		$usuario = $_POST['usuario'];
		try{
			$idCarro=cargar_carro_pendiente($usuario);
		}catch(Exception $e){
			//IMPRIMIMOS EN CASO DE QUE HAYA UN ERROR AL CARGAR EL CARRO PENDIENTE//
			echo "<p>".$e->getMessage()."</p>";
		}
		if($idCarro==FALSE){
		$_SESSION['usuario'] = $usu;
		$_SESSION['carrito'] = [];
		}else{
		$_SESSION['usuario'] = $usu;
		$codCarroExistente = $idCarro['CodCarro']; 
    	// 3. Ahora enviamos ese código como parámetro para cargar los detalles
    	$_SESSION['CodCarro'] = $codCarroExistente;
		try{
			$_SESSION['carrito'] = cargar_productos_carrito_pendiente($codCarroExistente);
		}catch(Exception $e){
			//IMPRIMIMOS EN CASO DE QUE HAYA UN ERROR AL CARGAR EL CARRO PENDIENTE//
			echo "<p>".$e->getMessage()."</p>";
		}
    	
		}
		header("Location: categorias.php");
		return;
	}	
}
?>
<!DOCTYPE html>
<html>
	<head>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<link rel="stylesheet" type="text/css" href="css/estiloLogin.css">
		<title>Formulario de login</title>
		<meta charset = "UTF-8">
	</head>
	<body>	
		
		<script src="js/login.js"></script>
		<form action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method = "POST">
		<img src="img/logoMercadona.png" alt="Logo de la tienda" width="450" height="63">
		<?php if(isset($_GET["redirigido"])){
			echo "<p class='error-message'>Haga login para continuar</p>";
		}?>
		<?php if(isset($err) and $err == true){
			echo "<p class='error-message'> Revise usuario y contraseña</p>";
		}?>
		<div>	
			<label for = "usuario">Usuario:</label> 
			<input value = "<?php if(isset($usuario))echo $usuario;?>"
			id = "usuario" name = "usuario" type = "text" onblur="validarInput(this)">
		</div>
		<div>	
			<label for = "clave">Clave:</label> 
			<input id = "clave" name = "clave" type = "password" onblur="validarInput(this)">					
		</div>
		<input id="submit" type = "submit" value="Iniciar Sesión"></input>
		</form>
	</body>
</html>