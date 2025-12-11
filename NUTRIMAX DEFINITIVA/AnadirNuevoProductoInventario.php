<?php 
    /*comprueba que el usuario haya abierto sesión o redirige*/
    require_once 'sesiones.php';
    require_once 'bd.php';
    comprobar_sesion();
    $productos=inventario();

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset = "UTF-8">
        <title>Nuevo producto</title>    
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
         <link id="theme-link" rel="stylesheet" type="text/css" href="css/estilos.css">	
    </head>
     <body class="body">

        <?php 
        require 'cabecera.php'; 
        echo "<div class='contenido-principal px-5'>";
        echo "<h2 class='mt-5 mb-4 text-center'>Nuevo producto</h2>";//m es margen, t top y 4 el nivel de espaciado, esto es una clase de bootstrap ?>
        

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card p-4 shadow-sm">
                
                <!--lo del multipart/form-data era para que divida lo que manda el formulario, los campos con post y el archivo de la imagen con files  --->
                <form action="guardarProducto.php" method="POST" enctype="multipart/form-data">
                    <!--hacemos los distintos campos--->
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Producto:</label>
                        <input type="text" 
                               class="form-control" 
                               id="nombre" 
                               name="nombre" 
                               placeholder="Ej: Camiseta de Algodón"
                               required 
                               maxlength="100">
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción Detallada:</label>
                        <textarea class="form-control" 
                                  id="descripcion" 
                                  name="descripcion" 
                                  rows="3" 
                                  placeholder="Ej: 100% Algodón, color azul, talla L."
                                  required></textarea>
                    </div>

                    <div class="row">
                        
                        <div class="mb-4">
                        <label for="imagen" class="form-label">Subir Imagen del Producto:</label>
                        <input type="file" 
                               class="form-control" 
                               id="imagen" 
                               name="imagen" 
                               accept="image/*" 
                               required>
                        <div class="form-text">Solo se permiten imágenes (PNG).</div>
                    </div>
                    
                        <div class="col-md-4 mb-3">
                            <label for="stock" class="form-label">Stock Inicial:</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="stock" 
                                   name="stock" 
                                   required 
                                   min="0" 
                                   step="1"
                                   value="1">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="codCat" class="form-label">Código de Categoría (CodCat):</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="codCat" 
                                   name="codCat" 
                                   placeholder="Ej: CAT01"
                                   required 
                                   maxlength="10">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="precio" class="form-label">Precio Unitario (€):</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="precio" 
                                   name="precio" 
                                   required 
                                   min="0.01" 
                                   step="0.01"
                                   placeholder="Ej: 19.99">
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            Crear
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
		<?php
        echo "</div>";
?>
<?php require 'footer.php';?>
<script src="js/cookies.js"></script>
    </body>
</html>
</html>