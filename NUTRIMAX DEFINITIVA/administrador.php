<?php 
    /*comprueba que el usuario haya abierto sesión o redirige*/
    require_once 'sesiones.php';
    require_once 'bd.php';
    comprobar_sesion();

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
        echo "<h2 class='mt-5 mb-4 text-center'>Productos</h2>";//m es margen, t top y 4 el nivel de espaciado, esto es una clase de bootstrap
        
        try {
            // Intenta cargar los productos. Esto lanzará una Exception si el carrito está vacío
            $productos=inventario();
             //------tabla de productos en el carrito------//
             echo "<table class='table table-striped table-hover border w-100 mt-5'>"; //abrir la tabla
            echo "<thead class='table-light'><tr><th>Codigo</th><th>Nombre</th><th>Descripcion</th><th>Stock</th><th>Categoria</th><th>Precio (Unidad)</th><th></th></tr></thead>";
            echo "<tbody>";
            
            //por cada producto del carro mostramos sus datos en la tabla
            foreach($productos as $producto){
                $cod = $producto['CodProd'];
                $nom = $producto['Nombre'];
                $desc = $producto['Descripcion'];
                $stock = $producto['stock'];
                $cat = $producto['CodCat'];
                $precio = $producto['Precio'];
                $estado = $producto['estado'];
                
                if($estado==="Descatalogado"){
                    continue;
                }
                
              

                // Fila de la tabla con los botones, formateando el precio a 2 decimales
                echo "<tr>
                <td>$cod</td>
                <td>$nom</td>
                <td>$desc</td>
                <td>$stock</td>
                <td>$cat</td>
                <td>$precio</td>
                <td class='d-flex flex-column'>
                    <div class='mb-2'>
                        <form action='ajustar_inventario.php' method='POST' class='d-inline me-1'>
                            <input name='cod' type='hidden' value='$cod'>
                            <input name='operacion' type='hidden' value='restar'>
                            <button type='submit' class='btn btn-sm btn-danger' title='Restar 1 unidad'> - </button>
                        </form>
                        <form action='ajustar_inventario.php' method='POST' class='d-inline'>
                            <input name='cod' type='hidden' value='$cod'>
                            <input name='operacion' type='hidden' value='sumar'>
                            <button type='submit' class='btn btn-sm btn-success' title='Sumar 1 unidad'> + </button>
                        </form>
                    </div>
                </td>
            </tr>";
            }
            
            echo "</tbody>";
            echo "</table>";
            
        } catch (Exception $e) {
            // Captura la excepción (e.g., bd vacía) y muestra un mensaje de error
            echo "<div class='container my-5'>";
    		echo "<p class='alert alert-danger'>ERROR:" . htmlspecialchars($e->getMessage()) ."</p>";
            echo "</div>";
            // No hay productos para mostrar, así que saltamos la creación de la tabla.
        }

	   		echo "<div class='d-flex justify-content-start align-items-center mt-3 mb-5'>";
            echo "<a href = 'AnadirNuevoProductoInventario.php'class='btn btn-success btn-lg rounded-pill mt-4 mb-4 ms-3 me-3 shadow'>Añadir producto</a>";
            echo "</div>"; // Cierre del contenedor Flexbox
		
        echo "</div>";
?>
<?php require 'footer.php';?>
<script src="js/cookies.js"></script>
    </body>
</html>