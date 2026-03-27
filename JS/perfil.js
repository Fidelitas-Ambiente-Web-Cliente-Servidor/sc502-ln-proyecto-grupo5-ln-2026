let reportesData = [];

document.addEventListener('DOMContentLoaded', function() {
    // Manejo de pestañas
    const tabs = document.querySelectorAll('.tab-link');
    const contents = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            const tabId = tab.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
            if (tabId === 'reportes') {
                cargarReportes();
            } else if (tabId === 'seguimiento') {
                cargarSeguimiento();
            }
        });
    });

    // Botón nuevo reporte
    const btnNuevo = document.getElementById('btnNuevoReporte');
    if (btnNuevo) {
        btnNuevo.addEventListener('click', () => {
            window.location.href = '/sc502-ln-proyecto-grupo5-ln-2026/Reportes/nuevoReporte.php';
        });
    }

    // Filtros
    const filtroEstado = document.getElementById('filtroEstadoReporte');
    const busquedaInput = document.getElementById('busquedaReporte');
    const btnBuscar = document.getElementById('btnBuscarReporte');
    if (filtroEstado && busquedaInput && btnBuscar) {
        function aplicarFiltros() {
            cargarReportes();
        }
        filtroEstado.addEventListener('change', aplicarFiltros);
        btnBuscar.addEventListener('click', aplicarFiltros);
        busquedaInput.addEventListener('keyup', e => { if (e.key === 'Enter') aplicarFiltros(); });
    }

    // Cambiar avatar
    const cambiarAvatar = document.getElementById('cambiarAvatar');
    if (cambiarAvatar) {
        cambiarAvatar.addEventListener('click', () => alert('Funcionalidad de cambio de avatar (próximamente)'));
    }
});

function cargarReportes() {
    const estado = document.getElementById('filtroEstadoReporte').value;
    const busqueda = document.getElementById('busquedaReporte').value;
    let url = 'get_reportes.php?';
    if (estado !== 'todos') url += `estado=${estado}&`;
    if (busqueda) url += `busqueda=${encodeURIComponent(busqueda)}`;
    fetch(url)
        .then(response => response.json())
        .then(reportes => renderReportes(reportes))
        .catch(error => console.error('Error cargando reportes:', error));
}

function renderReportes(reportes) {
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
    fetch('get_seguimiento.php')
        .then(response => response.json())
        .then(seguimiento => renderSeguimiento(seguimiento))
        .catch(error => console.error('Error cargando seguimiento:', error));
}

function renderSeguimiento(seguimiento) {
    const timeline = document.getElementById('timeline');
    if (!timeline) return;
    timeline.innerHTML = '';
    if (seguimiento.length === 0) {
        timeline.innerHTML = '<p>No hay seguimiento disponible.</p>';
        return;
    }
    seguimiento.forEach(act => {
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

const alerts = document.querySelectorAll('.alert');
alerts.forEach(alert => {
    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transition = 'opacity 0.3s';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
});