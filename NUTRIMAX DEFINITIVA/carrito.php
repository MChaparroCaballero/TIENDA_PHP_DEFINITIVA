<?php 
    /*comprueba que el usuario haya abierto sesión o redirige*/
    require_once 'sesiones.php';
    require_once 'bd.php';
    comprobar_sesion();
    
    // Inicialización de variables una para el subtotal y la otra para los productos para saber si hay o no
    $productos = FALSE;
    $subtotal_carrito = 0; 
	# Variable para controlar si se muestra el enlace de "Realizar pedido" o no
	$mostrar_enlace = FALSE;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset = "UTF-8">
        <title>Carrito de la compra</title>    
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
         <link id="theme-link" rel="stylesheet" type="text/css" href="css/estilos.css">	
    </head>
    <body class="body">

        <?php 
        require 'cabecera.php'; 
        echo "<div class='contenido-principal px-5'>";
        echo "<h2 class='mt-5 mb-4 text-center'>Carrito de la compra</h2>";//m es margen, t top y 4 el nivel de espaciado, esto es una clase de bootstrap
        
        try {
            // Intenta cargar los productos. Esto lanzará una Exception si el carrito está vacío
            $productos = cargar_productos(array_keys($_SESSION['carrito']));
			$mostrar_enlace = TRUE;
             //------tabla de productos en el carrito------//
             echo "<table class='table table-striped table-hover border w-100 mt-5'>"; //abrir la tabla
            echo "<thead class='table-light'><tr><th>Nombre</th><th>Precio (Unidad)</th><th>Unidades</th><th>Precio Línea</th><th></th></tr></thead>";
            echo "<tbody>";
            
            //por cada producto del carro mostramos sus datos en la tabla
            foreach($productos as $producto){
                $cod = $producto['CodProd'];
                $nom = $producto['Nombre'];
                $precio = $producto['Precio'];
                $unidades = $_SESSION['carrito'][$cod]; 
                
                // Cálculo del precio por unidades y acumulación al subtotal
                $precio_linea = $precio * $unidades;
                $subtotal_carrito += $precio_linea;

                // Fila de la tabla con los botones, formateando el precio a 2 decimales
                echo "<tr>
                <td>$nom</td>
                <td>$precio</td>
                <td>$unidades</td>
                <td>" . number_format($precio_linea, 2, ',', '.') . "</td>
                <td class='d-flex flex-column'>
                    <div class='mb-2'>
                        <form action='ajustar_unidades.php' method='POST' class='d-inline me-1'>
                            <input name='cod' type='hidden' value='$cod'>
                            <input name='operacion' type='hidden' value='restar'>
                            <button type='submit' class='btn btn-sm btn-danger' title='Restar 1 unidad'> - </button>
                        </form>
                        <form action='ajustar_unidades.php' method='POST' class='d-inline'>
                            <input name='cod' type='hidden' value='$cod'>
                            <input name='operacion' type='hidden' value='sumar'>
                            <button type='submit' class='btn btn-sm btn-success' title='Sumar 1 unidad'> + </button>
                        </form>
                    </div>
                </td>
            </tr>";
            }
            
            // Fila final para el subtotal
            echo "<tr class='table-secondary'>
            <td colspan='4' class='text-start'>
            <strong>SUBTOTAL: </strong>". number_format($subtotal_carrito, 2, ',', '.') ."</td>
            <td></td>
            </tr>";
            echo "</tbody>";
            echo "</table>";
            
        } catch (Exception $e) {
            // Captura la excepción (e.g., carrito vacío) y muestra un mensaje de error
            echo "<div class='container my-5'>";
    		echo "<p class='alert alert-danger'>ERROR:" . htmlspecialchars($e->getMessage()) ."</p>";
            echo "</div>";
            $mostrar_enlace = FALSE;
            // No hay productos para mostrar, así que saltamos la creación de la tabla.
        }

    
		if ($mostrar_enlace) { 
    		// Si la variable $mostrar_enlace es TRUE (hay productos) mostramos los botones de realizar el pedido
	   		echo "<div class='d-flex justify-content-start align-items-center mt-3 mb-5'>";
            echo "<a href = 'procesar_pedido.php'class='btn btn-success btn-lg rounded-pill mt-4 mb-4 ms-3 me-3 shadow'>Realizar pedido</a>";
            echo "<form action='eliminar_todas.php' method='POST'>
                        <input name='cod' type='hidden' value='todos'>
                        <button type='submit' class='btn btn-lg btn-danger rounded-pill shadow' title='Eliminar Todas las unidades'>Eliminar Todas</button>
                    </form>";
            echo "</div>"; // Cierre del contenedor Flexbox
		} else { 
    		// Si $mostrar_enlace es FALSE (el carrito está vacío o hubo un error) le decimos al usuario que tiene que rellenarlo
            echo "<div class='container my-5'>";
    		echo "<p class='alert alert-warning'>Añade productos al carrito para realizar un pedido.</p>";
            echo "</div>";
		}
        echo "</div>";
?>
<?php require 'footer.php';?>
<script src="js/cookies.js"></script>
    </body>
</html>