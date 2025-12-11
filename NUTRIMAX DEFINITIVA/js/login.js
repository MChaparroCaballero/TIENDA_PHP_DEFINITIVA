

    // Función para marcar un input en rojo
    function marcarError(input, label,mensaje) {
        input.style.borderColor = 'red';
        input.style.backgroundColor = '#ffe6e6';
        label.style.color = 'red';
        input.placeholder = mensaje;
        input.value = '';
    }

    // Función para desmarcar un input
    function desmarcarError(input, label) {
        input.style.borderColor = '#888888';
        input.style.backgroundColor = '#cfcfcf';
        label.style.color = '';
        input.placeholder = '';
    }

    // Función para validar un input específico
    function validarInput(input) {
    const label = document.querySelector(`label[for="${input.id}"]`);
        if (input.value.trim() === '') {
            marcarError(input, label,'Campo requerido');
        } else {
            desmarcarError(input, label);
        }
    }


    document.addEventListener('DOMContentLoaded', function() {
    //las variables
    const form = document.querySelector('form');
    const usuarioInput = document.getElementById('usuario');
    const claveInput = document.getElementById('clave');
    const usuarioLabel = document.querySelector('label[for="usuario"]');
    const claveLabel = document.querySelector('label[for="clave"]');
    
    
    // El dominio requerido
    const regexGmail = /^[a-zA-Z0-9._-]+@gmail\.com$/;
    
    // Validación al enviar el formulario
    form.addEventListener('submit', function(e) {
        let valido = true;
        
        //validar el email introducido//
        const usuarioValor = usuarioInput.value.trim();
        if (usuarioValor === '') {
            // 1. Validar que no esté vacío
            marcarError(usuarioInput, usuarioLabel,'El usuario no puede estar vacío');
            valido = false;
        } else if (!regexGmail.test(usuarioValor)) {
            // 2. Validar que contenga '@gmail.com'
            marcarError(usuarioInput, usuarioLabel,'Debe ser un correo valido (ej: usuario@gmail.com)');
            // Opcional: Podrías poner aquí un alert o mensaje de error más específico
            console.log('Error: El campo de usuario debe contener un gemail valido (ej: usuario@gmail.com)');
            valido = false;
        } else {
            desmarcarError(usuarioInput, usuarioLabel);
        }
        if (claveInput.value.trim() === '') {
            marcarError(claveInput, claveLabel,'La clave es requerida');
            valido = false;
        } else {
            desmarcarError(claveInput, claveLabel);
        }

        if (!valido) {
            e.preventDefault();
        }
    });
});