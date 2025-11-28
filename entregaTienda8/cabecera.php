<header id="cabeceraNavegacion">
<!--logo de la tienda--->
<img alt="Logo de la tienda"src="img/iconos/iconoLogoSupermercado.png" width="300px" height="70px">

<!--la navbar con ademas los datos de usuario--->
<div id="acciones">
     <div id="datosUsuario">
 Usuario: <?php echo $_SESSION['usuario']['nombre']?>
 </div>
 <!--los botones--->
 <div id="enlacesNav">
 <div class="contenedorIcono"><a href="categorias.php">
    <img src="./img/iconos/home.png" alt="Descripción de la imagen"></a></div>
    <div class="contenedorIcono">
    <a href="carrito.php">
    <img src="./img/iconos/carritoNegro.png" alt="Descripción de la imagen" ></a></div>
 <div class="contenedorIcono"><a href="logout.php">
    <img src="./img/iconos/logOut.webp" alt="Descripción de la imagen" ></a></div>
 </div>
</div>
</header>
