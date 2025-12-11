<?php

//funcion que nos hace la conexion de mysql
function leer_config(){
  $host = 'localhost'; 
    $db_nombre = 'u336643015_nutrimax';
    $usuario = 'u336643015_mariachaparro';
    $clave = 'Merynightmoon.22';

    // Construcción de la cadena de conexión PDO (incluyendo nombre de DB y host)
    $cadena_conexion = "mysql:host=$host;dbname=$db_nombre;charset=utf8";

    // Intentamos conectar
    try {
        $bd = new PDO($cadena_conexion, $usuario, $clave);
        
        // Configuramos el manejo de errores para que lance excepciones si hay error sql pues nos lanza esa excepción
        $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $bd;
        
    } catch (PDOException $e) {
        // En lugar de un throw genérico, lanzamos una excepción específica o manejamos el error
        // En un entorno real, no mostrarías $e->getMessage() al usuario final por seguridad
        throw new Exception("No se pudo conectar a la base de datos. Error: " . $e->getMessage());
    }
}

$bd=leer_config();

// --- FUNCIONES DE LECTURA (SELECT) ---


//Funcion que lo comprueba el usuario//
function comprobar_usuario($nombre, $clave){
    global $bd;
    $ins = "select nombre, gmail from usuarios where gmail = ? and clave = ?";
    $stmt = $bd->prepare($ins);
    $resul = $stmt->execute(array($nombre, $clave));
    
    if (!$resul) { // Error crítico de ejecución
        throw new Exception("Error al verificar el usuario.");
    }
    
    if($stmt->rowCount() === 1){        
        return $stmt->fetch();       
    }else{
        return FALSE;
    }
}

//funcion que nos encuentra si hay un carro pendiente del usuario loggeado
function cargar_carro_pendiente($usuario){
    global $bd;
    $ins = "select CodCarro, enviado from carro where Usuario = ? and Enviado = 0";
    $stmt = $bd->prepare($ins);
    $resul = $stmt->execute(array($usuario));
    
    if (!$resul) { // Error crítico de ejecución
        throw new Exception("Error al buscar carro pendiente.");
    }
    
    if($stmt->rowCount() === 1){        
        return $stmt->fetch();       
    }else{
        return FALSE;
    }
}

//funcion que carga los productos de un carro pendiente
function cargar_productos_carrito_pendiente($codCarroExistente){
    global $bd;
    $carrito_sesion = [];
    $sql = "SELECT CodProd, Unidades FROM carroproductos WHERE CodCarro = ?";
    $stmt = $bd->prepare($sql);
        
    $resul = $stmt->execute(array($codCarroExistente));
    if ($resul === false) {
        throw new Exception("Error al ejecutar la consulta de carrito.");
    }

    foreach($stmt as $producto){
         $codProd =$producto['CodProd'];
         $unidades = (int)$producto['Unidades'];
         $carrito_sesion[$codProd] = $unidades;
    }

    return $carrito_sesion;
}

//funcion que carga todas las categorias//
function cargar_categorias(){
    global $bd;
    $ins = "select CodCat, Nombre from categoria";
    $resul = $bd->query($ins);  
    if (!$resul) {
        throw new Exception("Error al ejecutar la consulta de categorías.");
    }
    if ($resul->rowCount() === 0) {    
        return FALSE;
    }
    return $resul;       
}

//funcion que carga los datos de una categoria en especifico (sera usada en productos.php)
function cargar_categoria($codCat){
    global $bd;
    $ins = "select Nombre, Descripcion from categoria where Codcat = ?";
    $stmt = $bd->prepare($ins);
    $resul = $stmt->execute(array($codCat));
    
    if (!$resul) { // Error crítico de ejecución
        throw new Exception("Error al ejecutar la consulta para cargar categoría.");
    }
    
    if ($stmt->rowCount() === 0) {    
        throw new Exception("No existe esa categoría.");
    }  
    return $stmt->fetch();  
}

//funcion que carga todos los productos de una categoria (tambien productos.php//
function cargar_productos_categoria($codCat){
    global $bd;
    $sql = "select * from productos where CodCat  = ?";
    $stmt = $bd->prepare($sql);
    $resul = $stmt->execute(array($codCat));
    
    if (!$resul) { // Error crítico de ejecución
        throw new Exception("Error al cargar productos de la categoría.");
    }
    if ($stmt->rowCount() === 0) {    
        throw new Exception("No hay productos asociados a esta categoría.");
    }  
    return $stmt;              
}

//funcion que carga los datos de productos almacenados de un carro
function cargar_productos($codigosProductos){
    global $bd;
    if (empty($codigosProductos)) {
        throw new Exception("El carrito está vacío.");
    }
    $texto_in = implode(",", $codigosProductos);
    $ins = "select * from productos where CodProd in($texto_in)";
    $resul = $bd->query($ins);  
    
    if (!$resul) {
        //En caso de error de SQL, lanzamos la excepción
        throw new Exception("Error al cargar productos por códigos.");
    }
    return $resul;  
}

//funcion que carga todos los datos de un producto especifico (ver_detalle_producto.php)
function cargar_producto($codProd){
    global $bd;
    $ins = "select CodProd, Nombre, Descripcion, Precio, stock from productos where CodProd = ?";
    $stmt = $bd->prepare($ins); 
    $resul = $stmt->execute(array($codProd));
    
    if (!$resul) {
        throw new Exception("Error al cargar un producto específico.");
    }
    
    if ($stmt->rowCount() === 0) {
        return FALSE;
    }
    return $stmt->fetch(); 
}

//funcion que calcula el total del carrito//
function calcular_total_carrito($carrito_session){
    // Esta función llama a cargar_productos, que ya tiene manejo de excepciones.
    $productos = cargar_productos(array_keys($carrito_session));
    $total = 0;
    
    if ($productos !== FALSE) {
        foreach($productos as $producto){
            $cod = $producto['CodProd'];
            $precio = $producto['Precio'];
            $unidades = $carrito_session[$cod]; 
            $total += $precio * $unidades;
        }
    }else{
      $total = 0;
    }
    return $total;
}

// --- FUNCIONES DE ESCRITURA (INSERT/UPDATE/DELETE) ---

//funcion que crea un nuevo carro en caso de que no existiera uno previamente//
function crear_carro_nuevo($carrito, $codRes){
    global $bd;
    $bd->beginTransaction();

    if (!isset($codRes['gmail'])) {
        $bd->rollback();
        throw new Exception("Error de seguridad: Usuario no identificado.");
    }
    $emailUsuario = $codRes['gmail'];
    $total_calculado = calcular_total_carrito($carrito);

    $hora = date("Y-m-d H:i:s", time());
    // 1. Insertar el carro
    $sql = "insert into carro(Fecha, Enviado, Total, usuario) 
             values(?,0,?,?)";
    $stmt = $bd->prepare($sql);
    $resul = $stmt->execute(array($hora, $total_calculado,$emailUsuario));
    if (!$resul) {
        $bd->rollback();
        throw new Exception("Fallo al crear el registro del nuevo carro.");
    }
    $pedido = $bd->lastInsertId();

    // 2. Insertar las líneas de producto
    $sqlLineas = "INSERT INTO carroproductos(CodCarro, CodProd, Unidades) VALUES( ?, ?, ?)";
    $stmtLineas = $bd->prepare($sqlLineas);
    
    foreach($carrito as $codProd=>$unidades){
        $resul = $stmtLineas->execute(array($pedido, $codProd, $unidades));
        if (!$resul) {
            $bd->rollback();
            throw new Exception("Fallo al insertar un producto en el carro (CodProd: $codProd).");
        }
    }
    $bd->commit();
    $_SESSION['CodCarro'] = $pedido;
    return $pedido;
}

//funcion que elimina un producto de un carro aun no completado (carrito.php)
function eliminar_producto_carro_pendiente($codProd){
    global $bd;
    $gmail_del_usuario = $_SESSION['usuario']['gmail'];
    $totalNuevo;
    $datosCarro = cargar_carro_pendiente($gmail_del_usuario); 
    
    if (!$datosCarro) {
        return FALSE;
    }
    
    $idcarro = $datosCarro['CodCarro'];
    $ins = "delete from carroproductos where CodCarro = ? and CodProd = ?";
    $stmt = $bd->prepare($ins);
    $resul = $stmt->execute(array($idcarro,$codProd));
    
    if (!$resul) {
        throw new Exception("Error al eliminar la línea de producto.");
    }
    
    if($stmt->rowCount() === 1){
        unset($_SESSION['carrito'][$codProd]);

        //comprobamos si esa eliminacion ha dejado el carro vacio//
        if (empty($_SESSION['carrito'])) {
            $totalNuevo = 0;
            // Opcional: Podrías considerar eliminar el carro principal de la BD aquí 
            // si el carro vacío no debe persistir. Por ahora solo actualizamos el total a 0.
        } else {
            // Si NO está vacío, calcula el total como siempre
            $totalNuevo = calcular_total_carrito($_SESSION['carrito']);
        }  
        actualizar_total_carro($totalNuevo,$idcarro);     
        return TRUE;
    }else{
        return FALSE;
    }
}


//funcion que disminuye las cantidades de un producto en el carro (ajustar_unidades.php)
function disminuir_unidades_producto_carrito($codProd,$unidades){
    global $bd;
    $gmail_del_usuario = $_SESSION['usuario']['gmail'];

    $datosCarro = cargar_carro_pendiente($gmail_del_usuario); 
    
    if (!$datosCarro) {
        return FALSE;
    }

    $idcarro = $datosCarro['CodCarro'];
    $ins = "update carroproductos set Unidades = ? where CodCarro = ? and CodProd = ?";
    $stmt = $bd->prepare($ins);
    $resul = $stmt->execute(array($unidades,$idcarro,$codProd));
    
    if (!$resul) {
        throw new Exception("Error al intentar disminuir las unidades del producto.");
    }

    if($stmt->rowCount() === 1){
        $totalNuevo=calcular_total_carrito($_SESSION['carrito']);   
        actualizar_total_carro($totalNuevo,$idcarro);   
        return TRUE;
    }else{
        // Se devuelve FALSE si no se actualizó ninguna fila (ej. no existe la línea)
        return FALSE; 
    }
}
//funcion que aumenta las cantidades de un producto en el carro (ajustar_unidades.php)
function aumentar_unidades_producto_carrito($codProd,$unidades){
    global $bd;
    $gmail_del_usuario = $_SESSION['usuario']['gmail'];

    $datosCarro = cargar_carro_pendiente($gmail_del_usuario); 
    
    if (!$datosCarro) {
        return FALSE;
    }

    $idcarro = $datosCarro['CodCarro'];
    $ins = "update carroproductos set Unidades = ? where CodCarro = ? and CodProd = ?";
    $stmt = $bd->prepare($ins);
    $resul = $stmt->execute(array($unidades,$idcarro,$codProd));
    
    if (!$resul) {
        throw new Exception("Error al intentar aumentar las unidades del producto.");
    }
    
    if($stmt->rowCount() === 1){    
        $totalNuevo=calcular_total_carrito($_SESSION['carrito']);  
        actualizar_total_carro($totalNuevo,$idcarro);      
        return TRUE;
    }else{
        return FALSE;
    }
}
//funcion que actualiza el total del carro despues de modificaciones en carrito pero que aun no ha sido completado//
function actualizar_total_carro($total,$idCarro){
    global $bd;
    $ins = "update carro set Total = ? where CodCarro = ?";
    $stmt = $bd->prepare($ins);
    $resul = $stmt->execute(array($total,$idCarro));
    
    if (!$resul) {
        throw new Exception("Error al actualizar el total del carro.");
    }
    
    if($stmt->rowCount() === 1){        
        return TRUE;        
    }else{
        return FALSE;
    }
}


//funcion que inserta un nuevo producto al carro de un carro previamente existente//
function insertar_producto_al_carro($carrito, $codRes, $idcarro){
    global $bd;
    $bd->beginTransaction();

    if (!isset($codRes['gmail'])) {
        $bd->rollback();
        throw new Exception("Error: Usuario no identificado para la operación.");
    }

    // 1. Actualizar el total
    $total_calculado = calcular_total_carrito($carrito);
    $sql = "UPDATE carro SET total=? WHERE CodCarro=?";
    $stmt = $bd->prepare($sql);
    
    if (!$stmt->execute(array($total_calculado, $idcarro))) {
        $bd->rollback();
        throw new Exception("Fallo al actualizar el total del carro existente.");
    }

    // 2. Recorrer y sincronizar productos
    foreach($carrito as $codProd => $unidades){
        $sqlCheck = "SELECT Unidades FROM carroproductos WHERE CodCarro = ? AND CodProd = ?";
        $stmtCheck = $bd->prepare($sqlCheck);
        $resulCheck = $stmtCheck->execute(array($idcarro, $codProd));
        
        if (!$resulCheck) { // Error crítico de select
             $bd->rollback();
             throw new Exception("Error en la comprobación de existencia del producto.");
        }
        
        $existe = $stmtCheck->fetchColumn(); 

        if ($existe !== false) {
            $sqlUpdate = "UPDATE carroproductos SET Unidades = ? WHERE CodCarro = ? AND CodProd = ?";
            $stmtUpdate = $bd->prepare($sqlUpdate);
            $resul = $stmtUpdate->execute(array($unidades, $idcarro, $codProd));
        } else {
            $sqlInsert = "INSERT INTO carroproductos(CodCarro, CodProd, Unidades) VALUES(?, ?, ?)";
            $stmtInsert = $bd->prepare($sqlInsert);
            $resul = $stmtInsert->execute(array($idcarro, $codProd, $unidades));
        }

        if (!$resul) {
            $bd->rollback();
            throw new Exception("Fallo al insertar/actualizar la línea de producto ($codProd).");
        }
    }

    $bd->commit();
    return $idcarro; 
}


//funcion que completa el pedido, es la version final de un carro y actualiza la bd controlando si ha eliminado todo tambien//
function completar_pedido($carrito, $codRes, $idcarro){
    global $bd;
    $bd->beginTransaction(); 

    if (!isset($codRes['gmail'])) {
        $bd->rollback(); 
        throw new Exception("Error: Usuario no identificado para completar pedido."); 
    }
    
    // ============ GESTIÓN DE CARRO VACÍO ==========================================
    if (empty($carrito)) {
        // 1. Eliminar líneas
        $sqlDeleteProds = "DELETE FROM carroproductos WHERE CodCarro = ?";
        $stmtDeleteProds = $bd->prepare($sqlDeleteProds);
        if (!$stmtDeleteProds->execute(array($idcarro))) {
            $bd->rollback();
            throw new Exception("Fallo al eliminar líneas de productos del carro vacío.");
        }
        
        // 2. Eliminar carro principal
        $sqlDeleteCarro = "DELETE FROM carro WHERE CodCarro = ?";
        $stmtDeleteCarro = $bd->prepare($sqlDeleteCarro);
        if (!$stmtDeleteCarro->execute(array($idcarro))) {
             $bd->rollback();
             throw new Exception("Fallo al eliminar el registro principal del carro vacío."); 
        }
        
        $bd->commit();
        return 0; 
    }
    // ==============================================================================

    // 1. Actualizar el total
    $total_calculado = calcular_total_carrito($carrito);
    $sql = "UPDATE carro SET total=? WHERE CodCarro=?";
    $stmt = $bd->prepare($sql);
    
    if (!$stmt->execute(array($total_calculado, $idcarro))) {
        $bd->rollback();
        throw new Exception("Fallo al actualizar el total del carro al completar pedido.");
    }

    // 2. ELIMINAR TODAS las líneas de productos (para resincronizar)
    $sqlDelete = "DELETE FROM carroproductos WHERE CodCarro = ?";
    $stmtDelete = $bd->prepare($sqlDelete);
    if (!$stmtDelete->execute(array($idcarro))) {
        $bd->rollback();
        throw new Exception("Fallo al limpiar líneas de productos antes de completar pedido.");
    }

    // 3. PREPARAR sentencias
    $sqlUpdateStock = "UPDATE productos SET stock = stock - ? WHERE CodProd = ? AND stock >= ?";
    $stmtUpdateStock = $bd->prepare($sqlUpdateStock); 
    $sqlInsert = "INSERT INTO carroproductos(CodCarro, CodProd, Unidades) VALUES(?, ?, ?)";
    $stmtInsert = $bd->prepare($sqlInsert);
    
    // 4. REINSERTAR y REDUCIR STOCK
    foreach($carrito as $codProd => $unidades){
        // A) Insertar la línea
        $resulInsert = $stmtInsert->execute(array($idcarro, $codProd, $unidades));

        // B) Reducir el stock
        $stmtUpdateStock->execute(array($unidades, $codProd, $unidades));
        
        // Verificación CRÍTICA
        if (!$resulInsert || $stmtUpdateStock->rowCount() === 0) { 
            $bd->rollback();
            throw new Exception("Fallo de stock insuficiente o error de inserción para el producto: $codProd.");
        }
    }

    $bd->commit();
    return $idcarro; 
}


// NOTA: La función 'actualizar_carro_estado_enviado' original ya no es necesaria
// porque la lógica se integró en 'completar_pedido' para asegurar atomicidad (transacción).

//funcion que cambia el estado a completado del carro//
function actualizar_carro_estado_enviado($codCarro){
    global $bd;
    
    $sql = "UPDATE carro SET Enviado = 1 WHERE CodCarro = ?";
    $stmt = $bd->prepare($sql); 
    $resul = $stmt->execute([$codCarro]); 
    
    if (!$resul || $stmt->rowCount() === 0) {
         // Lanzar si falla la ejecución SQL
        throw new Exception("Fallo al marcar el carro $codCarro como completado/enviado.");
    } 
    
    return TRUE;
}

//funcion para comprobar el rol de un usuario para saber si es admin o no, admin es si es rol 1 y usuario si es rol 0//
function es_o_no_usuario(){
    global $bd;
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['gmail'])) { 
        return 0; // Devolver 0 si no está logueado (no es admin ni usuario)
    }
    $gmail= $_SESSION['usuario']['gmail'];
    $sql = "Select rol from usuarios WHERE GMAIL = ?";
    $stmt = $bd->prepare($sql); 
    $resul = $stmt->execute(array($gmail)); 

    if (!$resul || $stmt->rowCount() === 0) {
         // Lanzar si falla la ejecución SQL
        throw new Exception("Fallo al obtener el rol del usuario  $gmail.");
    } 
    
    $rol = $stmt->fetchColumn();
    if ($rol === false) {
        return 0;
    }
    // Convertir el resultado de la DB a entero ANTES de devolverlo
    return (int)$rol;
    

}

//funcion que nos devuelve todos los productos de la bd//
function inventario(){
    global $bd;
    $ins = "select CodProd, Nombre, Descripcion, Precio, CodCat, stock, estado from productos";
    $resul = $bd->query($ins);
    
    if ($resul === false) { 
        // Si hay un error SQL y la configuración de errores no está activa,
        // $resul será FALSE. Lanzamos una excepción.
        throw new Exception("Error al ejecutar la consulta de inventario.");
    }
    
    // si no hay resultados que de false
    if ($resul->rowCount() === 0) {
        return FALSE; // Devuelve FALSE si no hay productos
    }
    
    // 3. Devolver todos los resultados como un array asociativo
    return $resul->fetchAll();
}

//funcion que da el stock de un producto//
function obtenerStock($cod){
    global $bd;
     
    $sql_stock = "Select stock from productos where CodProd= ?";
    $stmt = $bd->prepare($sql_stock);
    if (!$stmt->execute(array($cod))) {
        throw new Exception("Fallo al obtener el stock");
    }
    
    $stock_actual = $stmt->fetchColumn(0); 
    // Comprobación adicional: Si no se encuentra el producto, fetchColumn() devuelve FALSE.
    if ($stock_actual === false) {
        // Si no hay producto con ese código, el stock es 0.
        return 0; 
    }
    
    // Devuelve el valor numérico de stock
    return (int)$stock_actual;
}

//funcion paara aumentar el stock de un producto//
function aumentarInventarioAdmin($cod, $stock){
    global $bd;
    $sql = "UPDATE productos SET stock=? WHERE CodProd=?";
    
    $pstmt = $bd->prepare($sql);
     if (!$pstmt->execute(array($stock, $cod))) {
        throw new Exception("Fallo al actualizar el inventario");
    }

}

//funcion para disminuir el stock de un producto//
function disminuirInventarioAdmin($cod,$stock){
    global $bd;
    $sql = "UPDATE productos SET stock=? WHERE CodProd=?";
    
    $pstmt = $bd->prepare($sql);
     if (!$pstmt->execute(array($stock, $cod))) {
        throw new Exception("Fallo al actualizar el inventario");
    }

}
//esta en verdad te lo eliminaria pero como hay un problema de cascada de las foraneas eso se deja para otro momento no se va a utilizar que
//sino nos elimina los pedidos que hayan comprado este producto anteriormente y no
function eliminarProductoDelAlmacen($cod){
    global $bd;
    $sql = "UPDATE productos SET estado='Descatalogado' WHERE CodProd=?";
    
    $pstmt = $bd->prepare($sql);
     if (!$pstmt->execute(array($cod))) {
        throw new Exception("Fallo al eliminar el inventario");
    }

}

//funcion que crea un nuevo producto en la bd y devuelve el id del nuevo producto creado//
function crearProducto($nombre, $descripcion, $stock, $categoria, $precio){
    global $bd;
    $sql = "Insert into productos (Nombre, Descripcion, stock, CodCat, Precio) values (?,?,?,?,?)";
    $stmt = $bd->prepare($sql); 
    $resul = $stmt->execute(array($nombre, $descripcion, $stock, $categoria, $precio)); 
    
    if (!$resul || $stmt->rowCount() === 0) {
         // Lanzar si falla la ejecución SQL
        $error_info = $stmt->errorInfo();
        
        // El elemento 2 del array errorInfo contiene el mensaje de error del driver (MySQL/MariaDB)
        $error_detalle = "Fallo al insertar un nuevo producto. Detalle BD: " . $error_info[2];
        
        // Lanzamos la excepción con el error detallado
        throw new Exception($error_detalle);
    } 
    
    // La comprobación de rowCount() no es estrictamente necesaria si se comprueba $resul, 
    // pero la dejamos si deseas mantenerla por si acaso.
    if ($stmt->rowCount() === 0) {
        throw new Exception("Fallo al insertar un nuevo producto (RowCount 0).");
    }
    
    $nuevo_cod_producto = $bd->lastInsertId();

    // 5. Devolver el ID generado
    return $nuevo_cod_producto;

}