console.log('filtrosReportes.js loaded');

const API_REPORTES = new URL('../Auth/get_reportes.php', window.location.href).href;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFiltros);
} else {
    initFiltros();
}

let currentPage = 1;
let totalPages  = 1;

function initFiltros() {
    console.log('initFiltros ejecutado');

    const filtroTipo    = document.getElementById('filtroTipo');
    const filtroEstado  = document.getElementById('filtroEstado');
    const busquedaInput = document.getElementById('busqueda');
    const btnBuscar     = document.querySelector('.btn-buscar');
    const reportesGrid  = document.getElementById('reportesGrid');

    if (!filtroTipo || !filtroEstado || !busquedaInput || !reportesGrid) {
        console.error("Elementos no encontrados en DOM");
        return;
    }

    cargarReportes();

    function aplicarFiltros() {
        currentPage = 1;
        cargarReportes();
    }

    filtroTipo.addEventListener('change', aplicarFiltros);
    filtroEstado.addEventListener('change', aplicarFiltros);

    busquedaInput.addEventListener('input', function () {
        clearTimeout(this.timeout);
        this.timeout = setTimeout(aplicarFiltros, 400);
    });

    if (btnBuscar) {
        btnBuscar.addEventListener('click', function (e) {
            e.preventDefault();
            aplicarFiltros();
        });
    }

    agregarBotonLimpiar();
}

function cargarReportes() {
    const tipo     = document.getElementById('filtroTipo').value || 'todos';
    const estado   = document.getElementById('filtroEstado').value || 'todos';
    const busqueda = document.getElementById('busqueda').value.trim();

    const url = `${API_REPORTES}?tipo=${encodeURIComponent(tipo)}&estado=${encodeURIComponent(estado)}&busqueda=${encodeURIComponent(busqueda)}&page=${currentPage}`;

    console.log('Cargando reportes desde:', url);

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error("HTTP " + response.status);
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            const reportes = data.reportes || [];
            renderizarReportes(reportes);
            actualizarPaginacion(data.total || reportes.length, data.limite || 10);
        })
        .catch(error => {
            console.error('Error cargando reportes:', error);
            mostrarMensajeNoResultados(true);
        });
}

function renderizarReportes(reportes) {
    const reportesGrid = document.getElementById('reportesGrid');

    if (!reportesGrid) {
        console.error("reportesGrid no existe");
        return;
    }

    reportesGrid.innerHTML = '';

    if (!Array.isArray(reportes) || reportes.length === 0) {
        mostrarMensajeNoResultados(true);
        return;
    }

    mostrarMensajeNoResultados(false);

    reportes.forEach(reporte => {
        try {
            const card = document.createElement('article');
            card.className = 'reporte-card';

            const estadoClass = reporte.estado || 'pendiente';

            let estadoTexto = 'Pendiente';
            if (estadoClass === 'proceso')  estadoTexto = 'En proceso';
            if (estadoClass === 'resuelto') estadoTexto = 'Resuelto';

            card.innerHTML = `
                <div class="card-header">
                    <span class="tipo">${reporte.tipo || 'General'}</span>
                    <span class="estado ${estadoClass}">${estadoTexto}</span>
                </div>
                <h3>Reporte #${reporte.id || ''}</h3>
                <p class="ubicacion">${reporte.ubicacion || 'Sin ubicación'}</p>
                <p class="descripcion">${reporte.descripcion || ''}</p>
                <div class="card-footer">
                    <span class="fecha">${reporte.fecha_formateada || ''}</span>
                    <a href="/sc502-ln-proyecto-grupo5-ln-2026/Reportes/detalle.php?id=${reporte.id}" class="ver-detalle">Ver detalle →</a>
                </div>
            `;

            reportesGrid.appendChild(card);

        } catch (e) {
            console.error("Error renderizando reporte:", e, reporte);
        }
    });
}

function actualizarPaginacion(total, limite) {
    totalPages = Math.ceil(total / limite) || 1;

    const paginacionDiv    = document.querySelector('.paginacion');
    if (!paginacionDiv) return;

    const paginaActualSpan = paginacionDiv.querySelector('.pagina-actual');
    const btnAnterior      = paginacionDiv.querySelector('.btn-pagina:first-child');
    const btnSiguiente     = paginacionDiv.querySelector('.btn-pagina:last-child');

    if (!paginaActualSpan || !btnAnterior || !btnSiguiente) return;

    paginaActualSpan.textContent = `Página ${currentPage} de ${totalPages}`;

    btnAnterior.disabled  = currentPage === 1;
    btnSiguiente.disabled = currentPage === totalPages;

    btnAnterior.onclick = () => {
        if (currentPage > 1) {
            currentPage--;
            cargarReportes();
        }
    };

    btnSiguiente.onclick = () => {
        if (currentPage < totalPages) {
            currentPage++;
            cargarReportes();
        }
    };
}

function mostrarMensajeNoResultados(mostrar) {
    let mensaje = document.querySelector('.no-resultados');

    if (mostrar) {
        if (!mensaje) {
            mensaje = document.createElement('div');
            mensaje.className    = 'no-resultados';
            mensaje.textContent  = 'No se encontraron reportes.';
            mensaje.style.textAlign = 'center';
            mensaje.style.padding   = '2rem';
            mensaje.style.color     = '#555';

            const reportesGrid = document.getElementById('reportesGrid');
            if (reportesGrid) {
                reportesGrid.parentNode.insertBefore(mensaje, reportesGrid.nextSibling);
            }
        }
    } else {
        if (mensaje) mensaje.remove();
    }
}

function agregarBotonLimpiar() {
    const filtrosGrid = document.querySelector('.filtros-grid');
    if (!filtrosGrid || document.getElementById('btnLimpiarFiltros')) return;

    const btnLimpiar       = document.createElement('button');
    btnLimpiar.id          = 'btnLimpiarFiltros';
    btnLimpiar.textContent = 'Limpiar filtros';
    btnLimpiar.style.padding      = '0.8rem 1.5rem';
    btnLimpiar.style.border       = '1px solid #2c7';
    btnLimpiar.style.borderRadius = '20px';
    btnLimpiar.style.cursor       = 'pointer';

    btnLimpiar.addEventListener('click', function () {
        document.getElementById('filtroTipo').value   = '';
        document.getElementById('filtroEstado').value = '';
        document.getElementById('busqueda').value     = '';
        currentPage = 1;
        cargarReportes();
    });

    filtrosGrid.appendChild(btnLimpiar);
}