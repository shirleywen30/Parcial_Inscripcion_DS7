document.addEventListener('DOMContentLoaded', function() {
    const formulario = document.getElementById('formulario');
    
    const campos = formulario.querySelectorAll('input, select, textarea');
    
    campos.forEach(campo => {
        campo.addEventListener('blur', function() {
            validarCampo(this);
        });
        
        campo.addEventListener('change', function() {
            validarCampo(this);
        });
        
        campo.addEventListener('input', function() {
            if (this.classList.contains('invalido')) {
                this.classList.remove('invalido');
                this.parentElement.classList.remove('error');
            }
        });
    });
    
    formulario.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validarFormulario()) {
            this.submit();
        }
    });
});

function validarCampo(campo) {
    const grupo = campo.closest('.grupo');
    if (!grupo) return;
    
    let esValido = true;
    let mensaje = '';
    
    if (campo.name === 'identificacion') {
        if (campo.value.trim().length === 0) {
            esValido = false;
            mensaje = 'La identificacion es requerida';
        } else if (campo.value.trim().length < 5) {
            esValido = false;
            mensaje = 'Minimo 5 caracteres';
        }
    }
    
    if (campo.name === 'nombre') {
        if (campo.value.trim().length === 0) {
            esValido = false;
            mensaje = 'El nombre es requerido';
        } else if (campo.value.trim().length < 2) {
            esValido = false;
            mensaje = 'Minimo 2 caracteres';
        }
    }
    
    if (campo.name === 'apellido') {
        if (campo.value.trim().length === 0) {
            esValido = false;
            mensaje = 'El apellido es requerido';
        } else if (campo.value.trim().length < 2) {
            esValido = false;
            mensaje = 'Minimo 2 caracteres';
        }
    }
    
    if (campo.name === 'edad') {
        if (campo.value === '') {
            esValido = false;
            mensaje = 'La edad es requerida';
        } else {
            const edad = parseInt(campo.value);
            if (edad < 13 || edad > 120) {
                esValido = false;
                mensaje = 'Edad entre 13 y 120 años';
            }
        }
    }
    
    if (campo.name === 'correo') {
        if (campo.value.trim() === '') {
            esValido = false;
            mensaje = 'El correo es requerido';
        } else if (!validarEmail(campo.value)) {
            esValido = false;
            mensaje = 'Correo valido requerido';
        }
    }
    
    if (campo.name === 'celular') {
        if (campo.value.trim() === '') {
            esValido = false;
            mensaje = 'El celular es requerido';
        } else if (!validarCelular(campo.value)) {
            esValido = false;
            mensaje = 'Minimo 7 digitos';
        }
    }
    
    if (campo.name === 'pais_residencia_id') {
        if (campo.value === '') {
            esValido = false;
            mensaje = 'Selecciona un pais';
        }
    }
    
    if (campo.name === 'nacionalidad_id') {
        if (campo.value === '') {
            esValido = false;
            mensaje = 'Selecciona una nacionalidad';
        }
    }
    
    // Usar el padre inmediato del campo para colocar el error correctamente
    // en layouts de dos o tres columnas donde .grupo es el contenedor externo
    const errorContainer = campo.parentElement;

    if (esValido) {
        campo.classList.remove('invalido');
        errorContainer.classList.remove('error');
        const errorMsg = errorContainer.querySelector('.error-msg');
        if (errorMsg) errorMsg.textContent = '';
    } else {
        campo.classList.add('invalido');
        errorContainer.classList.add('error');
        const errorMsg = errorContainer.querySelector('.error-msg');
        if (errorMsg) errorMsg.textContent = mensaje;
    }
}

function validarEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function validarCelular(celular) {
    const soloNumeros = celular.replace(/\D/g, '');
    return soloNumeros.length >= 7;
}

function validarFormulario() {
    const formulario = document.getElementById('formulario');
    let hayErrores = false;
    
    const campos = formulario.querySelectorAll('[required]');
    campos.forEach(campo => {
        validarCampo(campo);
        if (campo.parentElement.classList.contains('error')) {
            hayErrores = true;
        }
    });
    
    const sexoSeleccionado = formulario.querySelector('input[name="sexo"]:checked');
    if (!sexoSeleccionado) {
        hayErrores = true;
        const grupoSexo = formulario.querySelector('.radio-group').closest('.grupo');
        if (grupoSexo) {
            grupoSexo.classList.add('error');
            const errorMsg = grupoSexo.querySelector('.error-msg');
            if (errorMsg) errorMsg.textContent = 'Selecciona un sexo';
        }
    } else {
        const grupoSexo = formulario.querySelector('.radio-group').closest('.grupo');
        if (grupoSexo) grupoSexo.classList.remove('error');
    }

    const areas = formulario.querySelectorAll('input[name="areas[]"]:checked');
    if (areas.length === 0) {
        hayErrores = true;
        const grupoAreas = formulario.querySelector('.grupo-areas');
        if (grupoAreas) {
            grupoAreas.classList.add('error');
            const errorMsg = grupoAreas.querySelector('.error-msg');
            if (errorMsg) errorMsg.textContent = 'Selecciona al menos un tema de interes';
        }
    } else {
        const grupoAreas = formulario.querySelector('.grupo-areas');
        if (grupoAreas) grupoAreas.classList.remove('error');
    }
    
    return !hayErrores;
}