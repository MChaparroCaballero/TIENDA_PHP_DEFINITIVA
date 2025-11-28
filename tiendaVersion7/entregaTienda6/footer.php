<footer>

<!--clase de bootstrap pra centrado automatico, crear un contenedor y darle padding-->
    <div class="container py-5">
        <div class="row"><!----nicia una fila que contendrá todas las columnas del pie de 
página, permitiendo que se alineen horizontalmente en pantallas grandes.------->
            
            <!--usamos las clases de bootstrap para indicar el tamaño en columnas que deberia usar en pantallas mediananas y pequeñas y el margen--->
            <div class="col-md-3 col-sm-6 mb-3">
                <h5><img src="img/iconos/carritoNegro.png" alt="Icono de categoría" class="icono-h5" style="width: 20px; height: 20px; vertical-align: middle; margin-right: 8px;">
                     Compra</h5>
                <ul class="list-unstyled"><!---clase de bootstrap que quita los marcadores de listas--->
                    <li><a href="categorias.php" >Inicio / Catálogo</a></li>
                    <li><a href="carrito.php" >Ver Carrito</a></li>
                    <li><a href="login.php" >Acceder / Registro</a></li>
                </ul>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-3">
                <h5>
                    <img src="img/iconos/informacion.png" alt="Icono de categoría" class="icono-h5" style="width: 20px; height: 20px; vertical-align: middle; margin-right: 8px;">
                     Información</h5>
                <ul class="list-unstyled">
                    <li><a>Contáctanos</a></li>
                    <li><a>Preguntas Frecuentes</a></li>
                    <li><a>Envíos y Devoluciones</a></li>
                </ul>
            </div>
            

            <div class="col-md-3 col-sm-6 mb-3">
                <h5><img src="img/iconos/red.png" alt="Icono de categoría" class="icono-h5" style="width: 20px; height: 20px; vertical-align: middle; margin-right: 8px;">
                    Síguenos</h5>
                <p class="text-50">
                    &copy; <?php echo date("Y"); ?> Tu Tienda. Todos los derechos reservados.
                </p>
            </div>
            
        </div>
    </div>
</footer>