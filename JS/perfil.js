// perfil.js - Versión con filtros en Mis Reportes

let reportesData = []; // Almacena los reportes originales

document.addEventListener('DOMContentLoaded', function() {
  // Verificar sesión (código existente)...
  const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
  if (!isLoggedIn) {
    window.location.href = '/sc502-ln-proyecto-grupo5-ln-2026/Auth/Login.html';
    return;
  }

  // Cargar datos del usuario (código existente)...
  const userName = localStorage.getItem('userName') || 'Usuario';
  const userEmail = localStorage.getItem('userEmail') || 'usuario@example.com';
  const userTel = localStorage.getItem('userTel') || '8888-8888';
  document.getElementById('nombreUsuario').textContent = userName;
  document.getElementById('emailUsuario').textContent = userEmail;
  document.getElementById('nombre').value = userName;
  document.getElementById('email').value = userEmail;
  document.getElementById('telefono').value = userTel;

  // Manejo de pestañas (código existente)...
  const tabs = document.querySelectorAll('.tab-link');
  const contents = document.querySelectorAll('.tab-content');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      contents.forEach(c => c.classList.remove('active'));
      tab.classList.add('active');
      const tabId = tab.getAttribute('data-tab');
      document.getElementById(tabId).classList.add('active');
      if (tabId === 'seguimiento') cargarSeguimiento();
    });
  });

  // Formularios (código existente)...
  document.getElementById('formDatos').addEventListener('submit', function(e) {
    e.preventDefault();
    const nuevoNombre = document.getElementById('nombre').value;
    const nuevoEmail = document.getElementById('email').value;
    const nuevoTel = document.getElementById('telefono').value;
    localStorage.setItem('userName', nuevoNombre);
    localStorage.setItem('userEmail', nuevoEmail);
    localStorage.setItem('userTel', nuevoTel);
    document.getElementById('nombreUsuario').textContent = nuevoNombre;
    document.getElementById('emailUsuario').textContent = nuevoEmail;
    alert('Datos actualizados correctamente');
  });

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
    alert('Contraseña cambiada correctamente (simulado)');
    this.reset();
  });

  document.getElementById('cambiarAvatar').addEventListener('click', () => alert('Funcionalidad de cambio de avatar (simulada)'));
  document.getElementById('btnNuevoReporte').addEventListener('click', () => {
    window.location.href = '/sc502-ln-proyecto-grupo5-ln-2026/Reportes/nuevoReporte.html';
  });

  // Inicializar reportes
  reportesData = obtenerReportesSimulados();
  cargarReportes(reportesData); // Carga inicial

  // Eventos de filtros
  const filtroEstado = document.getElementById('filtroEstadoReporte');
  const busquedaInput = document.getElementById('busquedaReporte');
  const btnBuscar = document.getElementById('btnBuscarReporte');

  function aplicarFiltros() {
    const estado = filtroEstado.value;
    const busqueda = busquedaInput.value.toLowerCase().trim();
    const filtrados = reportesData.filter(r => {
      if (estado !== 'todos' && r.estado !== estado) return false;
      if (busqueda && !r.descripcion.toLowerCase().includes(busqueda)) return false;
      return true;
    });
    cargarReportes(filtrados);
  }

  filtroEstado.addEventListener('change', aplicarFiltros);
  btnBuscar.addEventListener('click', aplicarFiltros);
  busquedaInput.addEventListener('keyup', e => { if (e.key === 'Enter') aplicarFiltros(); });

  // Cargar seguimiento por si acaso
  cargarSeguimiento();
});

function obtenerReportesSimulados() {
  return [
    { id: 123, fecha: '15/02/2026', estado: 'pendiente', descripcion: 'Contaminación de río en Heredia' },
    { id: 124, fecha: '20/02/2026', estado: 'resuelto', descripcion: 'Tala ilegal en Alajuela' },
    { id: 125, fecha: '25/02/2026', estado: 'proceso', descripcion: 'Quema de residuos en San José' },
    { id: 126, fecha: '01/03/2026', estado: 'pendiente', descripcion: 'Derrame químico en Cartago' },
    { id: 127, fecha: '05/03/2026', estado: 'resuelto', descripcion: 'Contaminación acústica' }
  ];
}

function cargarReportes(reportes) {
  const lista = document.getElementById('listaReportes');
  if (!lista) return;

  lista.innerHTML = '';
  if (reportes.length === 0) {
    lista.innerHTML = '<p class="sin-resultados">No hay reportes que coincidan con los filtros.</p>';
    return;
  }

  reportes.forEach(r => {
    const estadoClass = r.estado === 'pendiente' ? 'pendiente' : (r.estado === 'resuelto' ? 'resuelto' : 'en-proceso');
    const estadoTexto = r.estado === 'proceso' ? 'En proceso' : r.estado.charAt(0).toUpperCase() + r.estado.slice(1);
    const card = document.createElement('div');
    card.className = 'reporte-card';
    card.innerHTML = `
      <div>
        <h4>Reporte #${r.id}</h4>
        <p><strong>Fecha:</strong> ${r.fecha}</p>
        <p><strong>Descripción:</strong> ${r.descripcion}</p>
      </div>
      <div>
        <span class="estado ${estadoClass}">${estadoTexto}</span>
        <button class="btn-ver-detalle" data-id="${r.id}">Ver detalle</button>
      </div>
    `;
    lista.appendChild(card);
  });

  // Eventos para ver detalle
  document.querySelectorAll('.btn-ver-detalle').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const id = this.getAttribute('data-id');
      alert(`Mostrando detalle del reporte #${id} (próximamente: seguimiento detallado)`);
      document.querySelector('.tab-link[data-tab="seguimiento"]').click();
    });
  });
}

function cargarSeguimiento() {
  const timeline = document.getElementById('timeline');
  if (!timeline) return;
  const actividades = [
    { tipo: 'cambio', fecha: '26/02/2026 10:30', descripcion: 'Reporte #123: Estado cambiado a "En proceso"', usuario: 'Sistema' },
    { tipo: 'comentario', fecha: '26/02/2026 11:15', descripcion: 'Se asignó a la unidad de saneamiento.', usuario: 'Admin' },
    { tipo: 'cambio', fecha: '27/02/2026 09:00', descripcion: 'Reporte #124: Estado cambiado a "Resuelto"', usuario: 'Sistema' },
    { tipo: 'comentario', fecha: '27/02/2026 14:30', descripcion: 'Se adjuntó evidencia fotográfica.', usuario: 'Usuario' },
    { tipo: 'cambio', fecha: '28/02/2026 08:45', descripcion: 'Reporte #125: Nuevo reporte creado', usuario: 'Usuario' },
    { tipo: 'comentario', fecha: '28/02/2026 16:20', descripcion: 'Se solicita más información sobre la ubicación.', usuario: 'Admin' },
  ];
  timeline.innerHTML = '';
  actividades.forEach(act => {
    const item = document.createElement('div');
    item.className = `timeline-item ${act.tipo}`;
    item.innerHTML = `
      <div class="timeline-icon">${act.tipo === 'cambio' ? '🔄' : '💬'}</div>
      <div class="timeline-content">
        <span class="timeline-fecha">${act.fecha}</span>
        <p class="timeline-desc">${act.descripcion}</p>
        <span class="timeline-usuario">por ${act.usuario}</span>
      </div>
    `;
    timeline.appendChild(item);
  });
}