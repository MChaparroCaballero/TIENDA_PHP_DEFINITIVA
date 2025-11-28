<?php


//funcion que nos hace la conexion de mysql
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

//funcion que comprueba si el usuario existe
function comprobar_usuario($nombre, $clave){
	$bd = leer_config();
	$ins = "select nombre, gmail from usuarios where gmail = ? 
			and clave = ?";
    //usamos prepared statements
    $stmt = $bd->prepare($ins);
    $stmt->execute(array($nombre, $clave));
	if($stmt->rowCount() === 1){		
		return $stmt->fetch();		
	}else{
		return FALSE;
	}
}

//buscamos si hay un carro pendiente con ese usuario 
function cargar_carro_pendiente($usuario){
	$bd = leer_config();
	$ins = "select CodCarro, enviado from carro where Usuario = ?
			and Enviado = 0";
	$stmt = $bd->prepare($ins);
	$stmt->execute(array($usuario));
	
	if($stmt->rowCount() === 1){		
		return $stmt->fetch();		
	}else{
		return FALSE;
	}

}
//en el caso de que exista un carro pendiente de ese usuario esta funcion vuelva 
// los productos que tenia a el carro que se hizo de sesion para que no los pierda
function cargar_productos_carrito_pendiente($codCarroExistente){
	

	$bd = leer_config();
	$carrito_sesion = []; // Inicializamos el array [codProd => unidades]
    
    //buscamos los productos y unidades de el carro pendiente
    $sql = "SELECT CodProd, Unidades FROM carroproductos WHERE CodCarro = ?";
    
    //Preparar el statement
	$stmt = $bd->prepare($sql);
        
        $resul = $stmt->execute(array($codCarroExistente));
		if ($resul === false) {
        // Obtenemos información detallada del error de PDO y lanzamos nuestra propia Exception.
        throw new Exception("Error al ejecutar la consulta de carrito.");
    }


        //si todo va bien almacenamos en variables primero y luego en la variable de sesion del carro
        foreach($stmt as $producto){
			 $codProd =$producto['CodProd'];
			  $unidades = (int)$producto['Unidades'];
			  $carrito_sesion[$codProd] = $unidades;
		}

    // Devolver el array (vacío si no se encontraron productos)
    return $carrito_sesion;
}

//funcion que nos carga todas las categorias de la bd
function cargar_categorias(){
    $bd = leer_config();
	$ins = "select CodCat, Nombre from categoria";
	$resul = $bd->query($ins);	
	if (!$resul) {
		return FALSE;
        throw new Exception("Error al ejecutar la consulta de categorías.");
	}
	if ($resul->rowCount() === 0) {    
		return FALSE;
    }
	//si hay 1 o más
	return $resul;		
}

//funcion para cargar los datos de una categoria en especifico, la necesitamos en productos//
function cargar_categoria($codCat){
	$bd = leer_config();
	$ins = "select Nombre, Descripcion from categoria where Codcat = ?";
    $stmt = $bd->prepare($ins);
    $stmt->execute(array($codCat));
	if (!$stmt) {
		throw new Exception("Error al conectar con la base datos");
	}
	if ($stmt->rowCount() === 0) {    
		throw new Exception("No existe esa categoría.");
    }	
	//si hay 1 o más
	return $stmt->fetch();	
}

//cargamos todos los productos de una categoria especifica
function cargar_productos_categoria($codCat){
	$bd = leer_config();
	$sql = "select * from productos where CodCat  = ?";
    $stmt = $bd->prepare($sql);
    $stmt->execute(array($codCat));	
	if (!$stmt) {
		return FALSE;
        throw new Exception("Error al conectar con la base datos");
	}
	if ($stmt->rowCount() === 0) {    
		throw new Exception("No hay productos asociados a esta categoría.");
    }	
	//si hay 1 o más
	return $stmt;			
}

//funcion para cargar los productos en el carrito
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

//funcion para calcular el total de un carro de productos
function calcular_total_carrito($carrito_session){
    // Calcula el total basándose en los productos del carrito ($cod => $unidades)
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

//funcion para insertar pedido nuevo osea este usuario no tenia un carro pendiente
function insertar_pedido_nuevo($carrito, $codRes){
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

	// preparar la inserción de las filas detalle fuera del bucle por buena practica
	//porque aunque no es el caso cuando se vaya a utilizar esta funcion que es solo para una sola insercion
	//hipoteticamente si tuvieramos que insertar muchas filas seria mejor asi
	$sqlLineas = "INSERT INTO carroproductos(CodCarro, CodProd, Unidades) 
                  VALUES( ?, ?, ?)";
    $stmtLineas = $bd->prepare($sqlLineas);
	// insertar las filas en pedidoproductos
	foreach($carrito as $codProd=>$unidades){
		$sql = "insert into carroproductos(CodCarro, CodProd, Unidades) 
		             values( ?, ?, ?)";	
		//porque almacenamos el execute en una variable para comprobar si ha ido bien o mal		
		$resul = $stmtLineas->execute(array($pedido, $codProd, $unidades));
		if (!$resul) {
			$bd->rollback();
			return FALSE;
		}
	}
	$bd->commit();
	$_SESSION['CodCarro'] = $pedido; // Guardamos el ID del carro en la sesión
	return $pedido;
}

//funcion que actualiza un pedido pendiente
function insertar_pedido($carrito, $codRes, $idcarro){
    $bd = leer_config();
    $bd->beginTransaction();

    if (!isset($codRes['gmail'])) {
        $bd->rollback();
        return FALSE; 
    }

    //Actualizamos el total del carro existente
    $total_calculado = calcular_total_carrito($carrito);
    $sql = "UPDATE carro SET total=? WHERE CodCarro=?";
    $stmt = $bd->prepare($sql);
    
    if (!$stmt->execute(array($total_calculado, $idcarro))) {
        $bd->rollback();
        return FALSE;
    }

    // 2. Recorremos el carrito para sincronizar productos
    foreach($carrito as $codProd => $unidades){
        
        // Comprobar si este producto YA existe en este carro en la BD
        $sqlCheck = "SELECT Unidades FROM carroproductos WHERE CodCarro = ? AND CodProd = ?";
        $stmtCheck = $bd->prepare($sqlCheck);
        $stmtCheck->execute(array($idcarro, $codProd));
        
        // fetchColumn devuelve el dato si existe, o false si no existe
        $existe = $stmtCheck->fetchColumn(); 

        if ($existe !== false) {
            // --- CASO: YA EXISTE -> HACEMOS UPDATE ---
            $sqlUpdate = "UPDATE carroproductos SET Unidades = ? WHERE CodCarro = ? AND CodProd = ?";
            $stmtUpdate = $bd->prepare($sqlUpdate);
            $resul = $stmtUpdate->execute(array($unidades, $idcarro, $codProd));
        } else {
            // --- CASO: NO EXISTE -> HACEMOS INSERT ---
            $sqlInsert = "INSERT INTO carroproductos(CodCarro, CodProd, Unidades) VALUES(?, ?, ?)";
            $stmtInsert = $bd->prepare($sqlInsert);
            $resul = $stmtInsert->execute(array($idcarro, $codProd, $unidades));
        }

        // Si falla cualquiera de las dos operaciones
        if (!$resul) {
            $bd->rollback();
            return FALSE;
        }
    }

    $bd->commit();
    
    // Devolvemos el idcarro que nos pasaron, porque es el que estamos usando
    return $idcarro; 
}


function completar_pedido($carrito, $codRes, $idcarro){
    
	//en vez de buscar si existen o no los productos y hacer update o insert
	//vamos a eliminar todo y volver a insertar todo desde el carrito de sesion que ya deberia de tener aqui los valores finales
    $bd = leer_config();
    $bd->beginTransaction();

    if (!isset($codRes['gmail'])) {
        $bd->rollback();
        return FALSE; 
    }
    
    // ============ELIMINACION DEL CARRO VACIO=============================================================
    if (empty($carrito)) {
        // 1. Eliminar todas las líneas de productos asociadas a este carro
        $sqlDeleteProds = "DELETE FROM carroproductos WHERE CodCarro = ?";
        $stmtDeleteProds = $bd->prepare($sqlDeleteProds);
        $stmtDeleteProds->execute(array($idcarro)); // No verificamos el resultado, la FKCASCADE debería manejarlo, pero es buena práctica.
        
        // 2. Eliminar el registro del carro persistente de la tabla 'carro'
        $sqlDeleteCarro = "DELETE FROM carro WHERE CodCarro = ?";
        $stmtDeleteCarro = $bd->prepare($sqlDeleteCarro);
        
        if (!$stmtDeleteCarro->execute(array($idcarro))) {
             $bd->rollback();
             return FALSE; // Fallo al eliminar el registro principal
        }
        
        $bd->commit();
        
        // Devolvemos 0 o FALSE para indicar que el carro persistente fue ELIMINADO.
        // El script principal deberá usar esto para hacer unset($_SESSION['CodCarro']).
        return 0; // Se utiliza 0 o NULL para indicar que el carro se borró.
    }
    // =============================================================

    // 1. Actualizamos el total del carro existente (asumiendo que calcular_total_carrito usa $carrito)
    $total_calculado = calcular_total_carrito($carrito);
    $sql = "UPDATE carro SET total=? WHERE CodCarro=?";
    $stmt = $bd->prepare($sql);
    
    if (!$stmt->execute(array($total_calculado, $idcarro))) {
        $bd->rollback();
        return FALSE;
    }

    // 2. ELIMINAMOS TODOS los productos del carro persistente (sincronización).
    $sqlDelete = "DELETE FROM carroproductos WHERE CodCarro = ?";
    $stmtDelete = $bd->prepare($sqlDelete);
    if (!$stmtDelete->execute(array($idcarro))) {
        $bd->rollback();
        return FALSE;
    }

    // 3. REINSERTAMOS todos los productos desde el carrito de sesión.
    $sqlInsert = "INSERT INTO carroproductos(CodCarro, CodProd, Unidades) VALUES(?, ?, ?)";
    $stmtInsert = $bd->prepare($sqlInsert);
    
    foreach($carrito as $codProd => $unidades){
        $resul = $stmtInsert->execute(array($idcarro, $codProd, $unidades));

        if (!$resul) {
            $bd->rollback();
            return FALSE;
        }
    }

    $bd->commit();
    
    // Devolvemos el idcarro si la actualización fue exitosa
    return $idcarro; 
}


//funcion que cambia el estado del carro a completado aka 1
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

