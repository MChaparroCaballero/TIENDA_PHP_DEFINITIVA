<?php 
    require 'sesiones.php';
    require_once 'bd.php';
    comprobar_sesion();
    
    // Extraer y limpiar las variables de la sesión 
    $mensaje = $_SESSION['alerta_mensaje'] ?? "Error desconocido al acceder a la confirmación.";
    $clase_alerta = $_SESSION['alerta_tipo'] ?? "danger";

    
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset = "UTF-8">
        <title>Confirmación de Pedido</title> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link id="theme-link" rel="stylesheet" type="text/css" href="css/estilos.css"> 
    </head>
    <body class="body">
	<script src="js/cookies.js"></script>
        <?php require 'cabecera.php'; ?>
        
        <div class='flex-grow-1 d-flex justify-content-center align-items-center'>
            <div class='container'>
				<?php 
				echo "<div class='alert " . $clase_alerta . " text-center' style='max-width: 400px; margin: 0 auto; font-size: 1.25em;'>";
				echo htmlspecialchars($mensaje);
				echo "</div>";
				 ?>
                <div class="text-center mt-4">
                    <a href="categorias.php" class="btn btn-success btn-lg">Volver al Catálogo</a>
                </div>
            </div>
        </div>
        
        <?php require 'footer.php'; ?>
    </body>
</html>