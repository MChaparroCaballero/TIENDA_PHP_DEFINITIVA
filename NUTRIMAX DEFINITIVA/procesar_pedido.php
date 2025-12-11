<?php
    /*comprueba que el usuario haya abierto sesión o redirige*/
    require 'sesiones.php';
    require_once 'bd.php';
    comprobar_sesion();

    //Inicializar mensajes en la sesión
    $_SESSION['alerta_mensaje'] = "";
    $_SESSION['alerta_tipo'] = "";
    
    // Si no hay carro en sesión, redirigir inmediatamente
    if (!isset($_SESSION['CodCarro']) || empty($_SESSION['carrito'])) {
        $_SESSION['alerta_mensaje'] = "Error: El carro de la sesión está vacío o es inválido.";
        $_SESSION['alerta_tipo'] = "alert-danger";
        header("Location: pedido_confirmacion.php");
        exit;
    }

    try {
        //Ejecutar la transacción crítica: Stock y Total (debe incluir el COMMIT)
        $id_carro_completado = completar_pedido($_SESSION['carrito'], $_SESSION['usuario'], $_SESSION['CodCarro']);
        
        //+Ejecutar la actualización de estado
        if ($id_carro_completado !== 0) {
            // Esta función lanza una excepción si falla al actualizar el estado
            actualizar_carro_estado_enviado($id_carro_completado);
        }

        //ÉXITO COMPLETO: Stock reducido y estado actualizado.
        
        // REINICIAMOS LA SESIÓN (CRÍTICO)
        unset($_SESSION['CodCarro']); 
        $_SESSION['carrito'] = []; 

        $_SESSION['alerta_mensaje'] = "¡Pedido realizado con éxito! Tu carro ha sido completado.";
        $_SESSION['alerta_tipo'] = "alert-success";

    } catch (Exception $e) {
        // FALLO: Capturamos cualquier excepción (fallo de stock, BD, o estado)
        
        // Nota: Si falla completar_pedido, el catch se ejecuta y el COMMIT no sucede.
        // Si falla actualizar_carro_estado_enviado, el catch se ejecuta, pero el COMMIT de
        // completar_pedido YA SÍ se ejecutó (como lo modularizamos).
        
        $_SESSION['alerta_mensaje'] = "ERROR: No se pudo finalizar el pedido. Detalle: " . htmlspecialchars($e->getMessage());
        $_SESSION['alerta_tipo'] = "alert-danger";
    }

    //REDIRECCIÓN
    header("Location: pedido_confirmacion.php");
    exit;
?>