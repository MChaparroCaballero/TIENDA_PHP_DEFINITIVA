<?php
function leer_config(){
$cadena_conexion = 'mysql:dbname=simulador_tienda;host=127.0.0.1;port=3307';
$usuario = 'root';
$clave = '';

    $bd = new PDO($cadena_conexion, $usuario, $clave);
    if(!$bd){
        throw new Exception("No se pudo conectar a la base de datos");
    }else{
        return $bd;
    }
}
function comprobar_usuario($nombre, $clave){
	$bd = leer_config();
	$ins = "select nombre, gmail from usuarios where gmail = '$nombre' 
			and clave = '$clave'";
	$resul = $bd->query($ins);	
	if($resul->rowCount() === 1){		
		return $resul->fetch();		
	}else{
		return FALSE;
	}
}
function cargar_categorias(){
	$bd = leer_config();
	$ins = "select CodCat, Nombre from categoria";
	$resul = $bd->query($ins);	
	if (!$resul) {
		return FALSE;
	}
	if ($resul->rowCount() === 0) {    
		return FALSE;
    }
	//si hay 1 o más
	return $resul;	
}
function cargar_categoria($codCat){
	$bd = leer_config();
	$ins = "select Nombre, Descripcion from categoria where Codcat = $codCat";
	$resul = $bd->query($ins);	
	if (!$resul) {
		throw new Exception("Error al conectar con la base datos");
	}
	if ($resul->rowCount() === 0) {    
		throw new Exception("No existe esa categoría.");
    }	
	//si hay 1 o más
	return $resul->fetch();	
}
function cargar_productos_categoria($codCat){
	$bd = leer_config();
	$sql = "select * from productos where CodCat  = $codCat";	
	$resul = $bd->query($sql);	
	if (!$resul) {
		return FALSE;
	}
	if ($resul->rowCount() === 0) {    
		throw new Exception("No hay productos asociados a esta categoría.");
    }	
	//si hay 1 o más
	return $resul;			
}
// recibe un array de códigos de productos
// devuelve un cursor con los datos de esos productos
function cargar_productos($codigosProductos){
	$bd = leer_config();
	if (empty($codigosProductos)) {
        // Lanza una excepción si el array está vacío
        throw new Exception("El carrito está vacío.");
    }
	$texto_in = implode(",", $codigosProductos);
	$ins = "select * from productos where CodProd in($texto_in)";
	$resul = $bd->query($ins);	
	if (!$resul) {
		return FALSE;
	}
	return $resul;	
}


//cargar un solo producto por su codigo, esto lo usamos en ver detalle
function cargar_producto($codProd){
    $bd = leer_config();
    
    // Usamos el marcador de posición '?' para la sentencia preparada.
    // Seleccionamos todos los campos necesarios.
    $ins = "select CodProd, Nombre, Descripcion, Precio, stock from productos where CodProd = ?";
    
    // Preparamos la sentencia
    $stmt = $bd->prepare($ins); 
    
    // Ejecutamos, pasando el código validado ($codProd) como parámetro
    $stmt->execute(array($codProd)); 
    
    // Comprobamos si hubo algún error o si no se encontró ningún registro
    if (!$stmt || $stmt->rowCount() === 0) {
        return FALSE; // No se encontró el producto o hubo un error
    }
    
    // Devolvemos solo un resultado (el array asociativo del producto)
    return $stmt->fetch(); 
}
function calcular_total_carrito($carrito_session){
    // Calcula el total basándose en los productos del carrito ($cod => $unidades)
    
    // Necesitas una función que cargue los productos del carrito (similar a tu carrito.php)
    $productos = cargar_productos(array_keys($carrito_session));
    $total = 0;
    
    if ($productos !== FALSE) {
        foreach($productos as $producto){
            $cod = $producto['CodProd'];
            $precio = $producto['Precio'];
            $unidades = $carrito_session[$cod]; 
            $total += $precio * $unidades;
        }
    }
    return $total;
}
function insertar_pedido($carrito, $codRes){
	$bd = leer_config();
	$bd->beginTransaction();


	if (!isset($codRes['gmail'])) {
        // Fallo de seguridad/integridad: el array de usuario no tiene el email.
        $bd->rollback();
        return FALSE; 
    }
    $emailUsuario = $codRes['gmail'];
	$total_calculado = calcular_total_carrito($carrito);


	$hora = date("Y-m-d H:i:s", time());
	// insertar el pedido
	$sql = "insert into carro(Fecha, Enviado, Total, usuario) 
			values(?,0,?,?)";
		
	$stmt = $bd->prepare($sql);
	$resul = $stmt->execute(array($hora, $total_calculado,$emailUsuario));
	if (!$resul) {
		return FALSE;
	}
	// coger el id del nuevo pedido para las filas detalle
	$pedido = $bd->lastInsertId();
	// insertar las filas en pedidoproductos
	foreach($carrito as $codProd=>$unidades){
		$sql = "insert into carroproductos(CodCarro, CodProd, Unidades) 
		             values( $pedido, $codProd, $unidades)";			
		 $resul = $bd->query($sql);	
		if (!$resul) {
			$bd->rollback();
			return FALSE;
		}
	}
	$bd->commit();
	return $pedido;
}

function actualizar_carro_enviado($codCarro){
    $bd = leer_config();
    
    // Consulta para cambiar el campo 'Enviado' de 0 a 1
    $sql = "UPDATE carro SET Enviado = 1 WHERE CodCarro = ?";
    
    // Preparamos la sentencia
    $stmt = $bd->prepare($sql); 
    
    // Ejecutamos, pasando el ID del carro como parámetro
    $resul = $stmt->execute([$codCarro]); 
    
    // Comprobamos si la ejecución fue exitosa
    if (!$resul) {
        // Devuelve FALSE si la actualización falló
        return FALSE; 
    } 
    
    // Devuelve TRUE si la actualización fue exitosa
    return TRUE;
}

