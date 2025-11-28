

    // Función para marcar un input en rojo
    function marcarError(input, label) {
        input.style.borderColor = 'red';
        input.style.backgroundColor = '#ffe6e6';
        label.style.color = 'red';
    }

    // Función para desmarcar un input
    function desmarcarError(input, label) {
        input.style.borderColor = '#888888';
        input.style.backgroundColor = '#cfcfcf';
        label.style.color = '';
    }

    // Función para validar un input específico
    function validarInput(input) {
    const label = document.querySelector(`label[for="${input.id}"]`);
        if (input.value.trim() === '') {
            marcarError(input, label);
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
    
    // Validación al enviar el formulario
    form.addEventListener('submit', function(e) {
        let valido = true;

        if (usuarioInput.value.trim() === '') {
            marcarError(usuarioInput, usuarioLabel);
            valido = false;
        } else {
            desmarcarError(usuarioInput, usuarioLabel);
        }

        if (claveInput.value.trim() === '') {
            marcarError(claveInput, claveLabel);
            valido = false;
        } else {
            desmarcarError(claveInput, claveLabel);
        }

        if (!valido) {
            e.preventDefault();
        }
    });
});