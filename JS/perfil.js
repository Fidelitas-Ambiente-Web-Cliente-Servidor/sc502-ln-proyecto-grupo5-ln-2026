//Esto es provisional, cuando se realice el backend, todo deberá hacerse funcional.
document.addEventListener('DOMContentLoaded', function() {
  // Verificar si el usuario está logueado
  const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
  if (!isLoggedIn) {
    // Redirigir al login si no está logueado
    window.location.href = '/sc502-ln-proyecto-grupo5-ln-2026/Auth/Login.html';
    return;
  }

  // Cargar datos del usuario desde localStorage (o de un futuro backend)
  const userName = localStorage.getItem('userName') || 'Usuario';
  const userEmail = localStorage.getItem('userEmail') || 'usuario@example.com';

  // Mostrar datos en la página
  document.getElementById('nombreUsuario').textContent = userName;
  document.getElementById('emailUsuario').textContent = userEmail;
  document.getElementById('nombre').value = userName;
  document.getElementById('email').value = userEmail;
  // Si guardamos teléfono, lo cargamos también
  const userTel = localStorage.getItem('userTel') || '8888-8888';
  document.getElementById('telefono').value = userTel;

  // Manejo de pestañas
  const tabs = document.querySelectorAll('.tab-link');
  const contents = document.querySelectorAll('.tab-content');

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      // Quitar clase active de todos
      tabs.forEach(t => t.classList.remove('active'));
      contents.forEach(c => c.classList.remove('active'));

      // Activar la pestaña actual
      tab.classList.add('active');
      const tabId = tab.getAttribute('data-tab');
      document.getElementById(tabId).classList.add('active');
    });
  });

  // Formulario de actualización de datos
  document.getElementById('formDatos').addEventListener('submit', function(e) {
    e.preventDefault();
    const nuevoNombre = document.getElementById('nombre').value;
    const nuevoEmail = document.getElementById('email').value;
    const nuevoTel = document.getElementById('telefono').value;

    // Guardar en localStorage (simulación)
    localStorage.setItem('userName', nuevoNombre);
    localStorage.setItem('userEmail', nuevoEmail);
    localStorage.setItem('userTel', nuevoTel);

    // Actualizar cabecera
    document.getElementById('nombreUsuario').textContent = nuevoNombre;
    document.getElementById('emailUsuario').textContent = nuevoEmail;

    alert('Datos actualizados correctamente');
  });

  // Formulario de cambio de contraseña (simulado)
  document.getElementById('formPassword').addEventListener('submit', function(e) {
    e.preventDefault();
    const actual = document.getElementById('actual').value;
    const nueva = document.getElementById('nueva').value;
    const confirmar = document.getElementById('confirmar').value;

    if (nueva !== confirmar) {
      alert('Las contraseñas no coinciden');
      return;
    }
    if (nueva.length < 6) {
      alert('La contraseña debe tener al menos 6 caracteres');
      return;
    }
    // Aquí iría la lógica real con backend
    alert('Contraseña cambiada correctamente');
    this.reset();
  });

  // Botón de cambiar avatar (simulado)
  document.getElementById('cambiarAvatar').addEventListener('click', function() {
    alert('Funcionalidad de cambio de avatar (simulada)');
  });

  // Botón de nuevo reporte
  document.querySelector('.btn-nuevo-reporte').addEventListener('click', function() {
    window.location.href = '/sc502-ln-proyecto-grupo5-ln-2026/Reportes/nuevoReporte.html';
  });

  // Cargar reportes (simulado)
  cargarReportes();
});

function cargarReportes() {
  // Esto vendría de una API en el futuro
  const reportes = [
    { id: 123, fecha: '15/02/2026', estado: 'pendiente' },
    { id: 124, fecha: '20/02/2026', estado: 'resuelto' },
    { id: 125, fecha: '25/02/2026', estado: 'en proceso' }
  ];

  const lista = document.getElementById('listaReportes');
  if (!lista) return;

  lista.innerHTML = ''; // Limpiar
  reportes.forEach(r => {
    const estadoClass = r.estado === 'pendiente' ? 'pendiente' : (r.estado === 'resuelto' ? 'resuelto' : 'en-proceso');
    const estadoTexto = r.estado === 'en proceso' ? 'En proceso' : r.estado.charAt(0).toUpperCase() + r.estado.slice(1);
    const card = document.createElement('div');
    card.className = 'reporte-card';
    card.innerHTML = `
      <div>
        <h4>Reporte #${r.id}</h4>
        <p>Fecha: ${r.fecha}</p>
      </div>
      <div>
        <span class="estado ${estadoClass}">${estadoTexto}</span>
        <a href="#" class="ver-detalle" data-id="${r.id}">Ver detalle</a>
      </div>
    `;
    lista.appendChild(card);
  });

  // Añadir eventos a los enlaces de detalle
  document.querySelectorAll('.ver-detalle').forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const id = this.getAttribute('data-id');
      alert(`Ver detalle del reporte #${id} (simulado)`);
    });
  });
}