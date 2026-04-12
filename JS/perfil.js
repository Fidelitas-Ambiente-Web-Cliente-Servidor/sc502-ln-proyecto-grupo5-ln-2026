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
            } else if (tabId === 'mensajes') {
                cargarMensajes();
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

    // Cerrar alertas automáticamente
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.3s';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
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

// ==================== NUEVAS FUNCIONES PARA MENSAJES ====================
function cargarMensajes() {
    fetch('get_mensajes_usuario.php')
        .then(response => response.json())
        .then(mensajes => renderMensajes(mensajes))
        .catch(error => console.error('Error cargando mensajes:', error));
}

function renderMensajes(mensajes) {
    const contenedor = document.getElementById('listaMensajes');
    if (!contenedor) return;
    contenedor.innerHTML = '';
    if (mensajes.length === 0) {
        contenedor.innerHTML = '<p class="sin-resultados">No has enviado ningún mensaje de contacto aún.</p>';
        return;
    }
    mensajes.forEach(msg => {
        const estadoTexto = msg.leido ? 'Leído por el equipo' : 'No leído aún';
        const estadoClass = msg.leido ? 'leido' : 'no-leido';
        const mensajeCorto = msg.mensaje.length > 150 ? msg.mensaje.substring(0, 150) + '…' : msg.mensaje;
        const card = document.createElement('div');
        card.className = 'reporte-card'; // reutilizamos estilo
        card.innerHTML = `
            <div>
                <h4>${escapeHtml(msg.asunto)}</h4>
                <p><strong>Fecha:</strong> ${msg.fecha_envio}</p>
                <p><strong>Mensaje:</strong> ${escapeHtml(mensajeCorto)}</p>
            </div>
            <div>
                <span class="estado-mensaje ${estadoClass}">${estadoTexto}</span>
                ${msg.mensaje.length > 150 ? `<button class="btn-ver-mensaje" data-mensaje="${escapeHtml(msg.mensaje)}">Ver completo</button>` : ''}
            </div>
        `;
        contenedor.appendChild(card);
    });

    // Evento para botones "Ver completo"
    document.querySelectorAll('.btn-ver-mensaje').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const mensajeCompleto = btn.getAttribute('data-mensaje');
            alert(`Mensaje completo:\n\n${mensajeCompleto}`);
        });
    });
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}