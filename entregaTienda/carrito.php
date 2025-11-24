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
    </head>
    <body class="body">
        
        <?php 
        require 'cabecera.php'; 
        echo "<div class='contenido-principal'>";
        echo "<h2 class='mt-4 text-center'>Carrito de la compra</h2>";//m es margen, t top y 4 el nivel de espaciado, esto es una clase de bootstrap
        
        try {
            // Intenta cargar los productos. Esto lanzará una Exception si el carrito está vacío
            $productos = cargar_productos(array_keys($_SESSION['carrito']));
			$mostrar_enlace = TRUE;
            
        } catch (Exception $e) {
            // Captura la excepción (e.g., carrito vacío) y muestra un mensaje de error
            echo "<div class='container my-5'>";
    		echo "<p class='alert alert-danger'>ERROR:" . htmlspecialchars($e->getMessage()) ."</p>";
            echo "</div>";
            // No hay productos para mostrar, así que saltamos la creación de la tabla.
        }

        ##tabla de productos en el carrito##

        //Verificamos si hubo un error de BD (retornando FALSE) o si la carga fue exitosa.
        // Si $productos sigue siendo FALSE, asumimos un error de conexión/SQL que no fue la excepción de "carrito vacío".
        if($productos === FALSE && !empty(array_keys($_SESSION['carrito']))){
            echo "<div class='container my-5'>";
    		echo "<p class='alert alert-danger'>ERROR: No se pudieron cargar los productos de la base de datos.</p>";
            echo "</div>";
			$mostrar_enlace = FALSE;
        } 
        // Si $productos es un resultado válido (no FALSE y no pasó por el catch de carrito vacío), mostramos la tabla.
        else if ($productos !== FALSE) {
            
            echo "<table>"; //abrir la tabla
            
            echo "<tr><th>Nombre</th><th>Precio (Unidad)</th><th>Unidades</th><th>Precio Línea</th><th>Acciones</th></tr>";
            
            foreach($productos as $producto){
                $cod = $producto['CodProd'];
                $nom = $producto['Nombre'];
                $precio = $producto['Precio'];
                $unidades = $_SESSION['carrito'][$cod]; 
                
                // Cálculo del precio por unidades y acumulación al subtotal
                $precio_linea = $precio * $unidades;
                $subtotal_carrito += $precio_linea;

                // Fila de la tabla con los botones
                echo "<tr>
                    <td>$nom</td>
                    <td>$precio</td>
                    <td>$unidades</td>
                    <td>" . number_format($precio_linea, 2, ',', '.') . "</td>
                    <td>
                        <form action = 'ajustar_unidades.php' method = 'POST' style='display: inline;'><input name = 'cod' type='hidden' value = '$cod'><input name = 'operacion' type='hidden' value = 'restar'><input type = 'submit' value=' - ' title='Restar 1 unidad'></form>
                        <form action = 'ajustar_unidades.php' method = 'POST' style='display: inline;'><input name = 'cod' type='hidden' value = '$cod'><input name = 'operacion' type='hidden' value = 'sumar'><input type = 'submit' value=' + ' title='Sumar 1 unidad'></form>
                        <br>
                        <form action = 'eliminar_producto.php' method = 'POST' style='display: block; margin-top: 5px;'><input name = 'cod' type='hidden' value = '$cod'><input type = 'submit' value='Eliminar Todas' style='background-color: #f44336; color: white; border: none; padding: 5px 10px; cursor: pointer;'></form>
                    </td>
                </tr>";
            }
            
            // Fila final para el subtotal
            echo "<tr>
                <td colspan='4' style='text-align:right;'><strong>SUBTOTAL:</strong></td>
                <td><strong>" . number_format($subtotal_carrito, 2, ',', '.') . "</strong></td>
                <td></td>
              </tr>";
            echo "</table>";
        }
        
        echo "<hr>";

		if ($mostrar_enlace) { 
    		// Si la variable $mostrar_enlace es TRUE (hay productos)
	   		echo "<a href = 'procesar_pedido.php'class='btn btn-success btn-lg rounded-pill mt-4 mb-4 ms-3'>Realizar pedido</a>";
		} else { 
    		// Si $mostrar_enlace es FALSE (el carrito está vacío o hubo un error)
            echo "<div class='container my-5'>";
    		echo "<p class='alert alert-warning'>Añade productos al carrito para realizar un pedido.</p>";
            echo "</div>";
		}
        echo "</div>";
?>
<?php require 'footer.php';?>
    </body>
</html>