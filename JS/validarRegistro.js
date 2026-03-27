document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('registroForm');
  if (!form) return;

  // Función para mostrar error en un campo
  function mostrarError(input, mensaje) {
    const grupo = input.closest('.input-group');
    if (grupo) {
      grupo.classList.add('error');
      let errorSpan = grupo.querySelector('.error-mensaje');
      if (!errorSpan) {
        errorSpan = document.createElement('span');
        errorSpan.className = 'error-mensaje';
        grupo.appendChild(errorSpan);
      }
      errorSpan.textContent = mensaje;
    }
  }

  // Función para limpiar error de un campo
  function limpiarError(input) {
    const grupo = input.closest('.input-group');
    if (grupo) {
      grupo.classList.remove('error');
      const errorSpan = grupo.querySelector('.error-mensaje');
      if (errorSpan) errorSpan.remove();
    }
  }

  // Validar campo específico
  function validarCampo(input) {
    const valor = input.value.trim();
    const id = input.id;
    let valido = true;

    if (valor === '') {
      mostrarError(input, 'Este campo es obligatorio.');
      valido = false;
    } else {
      if (id === 'emailR') {
        if (!valor.includes('@') || !valor.includes('.')) {
          mostrarError(input, 'Correo electrónico inválido.');
          valido = false;
        } else {
          limpiarError(input);
        }
      } else if (id === 'telR') {
        const soloNumeros = /^\d+$/.test(valor);
        if (!soloNumeros || valor.length < 8) {
          mostrarError(input, 'Teléfono inválido (mínimo 8 dígitos numéricos).');
          valido = false;
        } else {
          limpiarError(input);
        }
      } else if (id === 'contraR') {
        if (valor.length < 6) {
          mostrarError(input, 'La contraseña debe tener al menos 6 caracteres.');
          valido = false;
        } else {
          limpiarError(input);
        }
      } else {
        limpiarError(input);
      }
    }
    return valido;
  }

  // Validar términos (ahora usando el nombre correcto 'terminos')
  function validarTerminos() {
    const terminosCheck = document.querySelector('input[name="terminos"]');
    const terminosGroup = document.querySelector('.terminos');
    if (!terminosCheck || !terminosCheck.checked) {
      terminosGroup.classList.add('error');
      let errorSpan = terminosGroup.querySelector('.error-mensaje');
      if (!errorSpan) {
        errorSpan = document.createElement('span');
        errorSpan.className = 'error-mensaje';
        terminosGroup.appendChild(errorSpan);
      }
      errorSpan.textContent = 'Debes aceptar los términos y condiciones.';
      return false;
    } else {
      terminosGroup.classList.remove('error');
      const errorSpan = terminosGroup.querySelector('.error-mensaje');
      if (errorSpan) errorSpan.remove();
      return true;
    }
  }

  // Evento submit
  form.addEventListener('submit', function(e) {
    // No prevenimos el envío por defecto; si hay errores, lo prevenimos después
    let formValido = true;

    const campos = [
      document.getElementById('nombreR'),
      document.getElementById('emailR'),
      document.getElementById('telR'),
      document.getElementById('contraR')
    ];

    campos.forEach(campo => {
      if (!validarCampo(campo)) {
        formValido = false;
      }
    });

    if (!validarTerminos()) {
      formValido = false;
    }

    if (!formValido) {
      e.preventDefault(); // Solo prevenimos si hay errores
    }
    // Si es válido, el formulario se envía normalmente
  });

  // Limpiar errores mientras se escribe
  const inputs = document.querySelectorAll('#nombreR, #emailR, #telR, #contraR');
  inputs.forEach(input => {
    input.addEventListener('input', function() {
      if (this.value.trim() !== '') {
        limpiarError(this);
      }
    });
  });

  // Limpiar error del checkbox al cambiar
  const terminosCheck = document.querySelector('input[name="terminos"]');
  if (terminosCheck) {
    terminosCheck.addEventListener('change', function() {
      const terminosGroup = document.querySelector('.terminos');
      if (this.checked) {
        terminosGroup.classList.remove('error');
        const errorSpan = terminosGroup.querySelector('.error-mensaje');
        if (errorSpan) errorSpan.remove();
      }
    });
  }
});