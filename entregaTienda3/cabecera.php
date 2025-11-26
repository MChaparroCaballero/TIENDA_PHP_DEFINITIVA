<header id="cabeceraNavegacion">
<img alt="Logo de la tienda"src="./img/logoMercadona.png" width="300px" height="40px">

<div id="acciones">
     <div id="datosUsuario">
 Usuario: <?php echo $_SESSION['usuario']['nombre']?>
 </div>
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
