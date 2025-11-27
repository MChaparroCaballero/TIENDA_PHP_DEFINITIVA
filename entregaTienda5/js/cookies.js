
// =========================================================
// FUNCIONES DE GESTIÓN DE COOKIES (MANTENIDAS)
// =========================================================

/**
 * Función para crear una cookie
 */

const cookieNombre = "theme";

function crearCookie(valor, dias) {
    const fechaD = new Date();
    fechaD.setTime(fechaD.getTime() + (dias * 24 * 60 * 60 * 1000));
    const expiracionD = "expires=" + fechaD.toUTCString();
    document.cookie = cookieNombre + "=" + valor + ";" + expiracionD + ";path=/";
}

function crearCookiePorMinutos(valor, minutos) {
    const fecha = new Date();
    // Cálculo: minutos * 60 (segundos) * 1000 (milisegundos)
    fecha.setTime(fecha.getTime() + (minutos * 60 * 1000)); 
    const expiracion = "expires=" + fecha.toUTCString();
    document.cookie = cookieNombre + "=" + valor + ";" + expiracion + ";path=/";
}

/**
 * Lee una cookie por su nombre
 */
function leerCookie() {
    const nombreEQ = cookieNombre + "=";
    const cookies = document.cookie.split(';');
    for (let c of cookies) {
        c = c.trim();
        if (c.indexOf(nombreEQ) === 0) {
            return c.substring(nombreEQ.length, c.length);
        }
    }
    return null;
    
}

// ---------------------------------------------------------
// FUNCIONES DE LÓGICA DE TEMA Y MODAL
// ---------------------------------------------------------

/*lo que vamos a hacer es cambiar la ruta del css osea hay dos 1 de css claro y otro del css oscuro*/
function aplicarTema(tema) {
    const link = document.getElementById('theme-link');
    const body = document.body;
    
    
// 2. Establecer la ruta del archivo CSS
    if (tema === 'oscuro') {
        link.href = 'css/estiloOscuro.css';
    }else if(tema === 'claro'){
        link.href = 'css/estilos.css';
    }
     else {
        link.href = 'css/estilos.css';
    }
}

/**
 * Genera y muestra la ventana flotante (modal) con los radio buttons.
 */
function mostrarModalSeleccionTema() {
    
    // --- 1. CREACIÓN DE LA ESTRUCTURA HTML DEL MODAL ---
    
    const modalHTML = `
        <div id="modalOverlay" class="modal-overlay">
            <div class="modal-content">
                <h2>Elige Tema</h2>
                
                <form id="themeSelectionForm">
                    <label>
                        <input type="radio" name="themeOption" value="claro" checked> 
                        Tema Claro
                    </label>
                    <br><br>
                    <label>
                        <input type="radio" name="themeOption" value="oscuro"> 
                        Tema Oscuro
                    </label>
                </form>

                <div class="modal-actions" style="margin-top: 20px;">
                    <button id="btnAceptarTema">Aceptar</button>
                    <button id="btnRechazarTema">Rechazar</button> </div>
                </div>
            </div>
        </div>
    `;

    // Añadir el modal al cuerpo del documento
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // --- 2. MANEJADOR DE EVENTOS (El que devuelve el valor) ---

    const btnAceptarTema = document.getElementById('btnAceptarTema');
    
    btnAceptarTema.addEventListener('click', function() {
        
        // Función para obtener el valor del radio button seleccionado
        const selectedRadio = document.querySelector('input[name="themeOption"]:checked');
        
        if (selectedRadio) {
            const temaElegido = selectedRadio.value;
            
            // 1. Almacenar el valor en la cookie (duración de 365 días)
            crearCookiePorMinutos(temaElegido, 5); 
            
            // 2. Aplicar el tema seleccionado inmediatamente
            aplicarTema(temaElegido); 
            
            // 3. Eliminar el modal del DOM
            const modalOverlay = document.getElementById('modalOverlay');
            modalOverlay.remove();
        }
        // Si no hay radio seleccionado (debería haber uno por el 'checked' inicial), no hacemos nada.
    });

    btnRechazarTema.addEventListener('click', function() {
        // 1. Simplemente elimina el modal del DOM
        const modalOverlay = document.getElementById('modalOverlay');
        modalOverlay.remove();
        // NOTA: Al no crear la cookie, el modal volverá a aparecer la próxima vez 
        // que se cargue la página hasta que el usuario elija Aceptar.
    });
}

// ---------------------------------------------------------
// FUNCIÓN PRINCIPAL DE INICIO
// ---------------------------------------------------------

/**
 * Esta función se ejecuta al cargar la página para comprobar la cookie.
 */
function iniciarTema() {
    
    // Comprobar si existe una cookie llamada "theme"
    const temaGuardado = leerCookie("theme");
    
    if (temaGuardado) {
        // Si la cookie existe: Aplicar el tema guardado
        aplicarTema(temaGuardado);
    } else {
        // Si la cookie NO existe: Mostrar el modal para la selección
        mostrarModalSeleccionTema();
        // Por defecto, la función aplicarTema se llama al aceptar el modal.
        // Mientras tanto, se asume el tema por defecto (claro).
    }
}

// ---------------------------------------------------------
// EJECUCIÓN DEL SCRIPT
// ---------------------------------------------------------

// Ejecutar la función principal cuando el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', iniciarTema);

// Si quieres usar este script en un archivo HTML simple, también puedes
// llamarlo directamente para asegurar que funciona incluso si no usas DOMContentLoaded:
// iniciarTema();