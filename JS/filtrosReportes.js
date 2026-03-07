// filtrosReportes.js - Versión mejorada con comparación por texto visible

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initFiltros, 100);
});

function initFiltros() {
    const filtroTipo = document.getElementById('filtroTipo');
    const filtroEstado = document.getElementById('filtroEstado');
    const busquedaInput = document.getElementById('busqueda');
    const btnBuscar = document.querySelector('.btn-buscar');
    const reportesGrid = document.getElementById('reportesGrid');
    const reportes = document.querySelectorAll('.reporte-card');

    if (!filtroTipo || !filtroEstado || !busquedaInput || !reportes.length) {
        return;
    }

    function getTextoBusqueda() {
        return busquedaInput.value.trim().toLowerCase();
    }

    function aplicarFiltros() {
        // Obtener textos seleccionados (el texto visible)
        let tipoSeleccionado = filtroTipo.options[filtroTipo.selectedIndex]?.text.trim() || '';
        if (tipoSeleccionado === 'Todos los tipos') tipoSeleccionado = '';

        let estadoSeleccionado = filtroEstado.options[filtroEstado.selectedIndex]?.text.trim() || '';
        if (estadoSeleccionado === 'Todos los estados') estadoSeleccionado = '';

        const textoBusqueda = getTextoBusqueda();

        let visibles = 0;

        reportes.forEach(reporte => {
            const tipo = reporte.querySelector('.tipo')?.textContent.trim() || '';
            const estadoElem = reporte.querySelector('.estado');
            let estado = estadoElem ? estadoElem.textContent.trim() : '';
            const ubicacion = reporte.querySelector('.ubicacion')?.textContent.trim() || '';
            const descripcion = reporte.querySelector('.descripcion')?.textContent.trim() || '';
            const titulo = reporte.querySelector('h3')?.textContent.trim() || '';

            const textoCompleto = (titulo + ' ' + ubicacion + ' ' + descripcion).toLowerCase();

            let coincide = true;

            // Filtro por tipo (comparar textos ignorando mayúsculas)
            if (tipoSeleccionado && tipo.toLowerCase() !== tipoSeleccionado.toLowerCase()) {
                coincide = false;
            }

            // Filtro por estado
            if (estadoSeleccionado && estado.toLowerCase() !== estadoSeleccionado.toLowerCase()) {
                coincide = false;
            }

            // Filtro por búsqueda
            if (textoBusqueda && !textoCompleto.includes(textoBusqueda)) {
                coincide = false;
            }

            if (coincide) {
                reporte.style.display = 'flex';
                visibles++;
            } else {
                reporte.style.display = 'none';
            }
        });

        mostrarMensajeNoResultados(visibles === 0);
    }

    function mostrarMensajeNoResultados(mostrar) {
        let mensaje = document.querySelector('.no-resultados');
        if (mostrar) {
            if (!mensaje) {
                mensaje = document.createElement('div');
                mensaje.className = 'no-resultados';
                mensaje.textContent = 'No se encontraron reportes que coincidan con los filtros.';
                mensaje.style.textAlign = 'center';
                mensaje.style.padding = '2rem';
                mensaje.style.color = 'var(--gray-dark)';
                mensaje.style.fontSize = '1.1rem';
                mensaje.style.backgroundColor = 'var(--white)';
                mensaje.style.borderRadius = '16px';
                mensaje.style.margin = '2rem 0';
                reportesGrid.parentNode.insertBefore(mensaje, reportesGrid.nextSibling);
            }
        } else {
            if (mensaje) {
                mensaje.remove();
            }
        }
    }

    filtroTipo.addEventListener('change', aplicarFiltros);
    filtroEstado.addEventListener('change', aplicarFiltros);
    busquedaInput.addEventListener('input', aplicarFiltros);
    if (btnBuscar) {
        btnBuscar.addEventListener('click', function(e) {
            e.preventDefault();
            aplicarFiltros();
        });
    }

    agregarBotonLimpiar();
    aplicarFiltros();
}

function agregarBotonLimpiar() {
    const filtrosGrid = document.querySelector('.filtros-grid');
    if (!filtrosGrid || document.getElementById('btnLimpiarFiltros')) return;

    const btnLimpiar = document.createElement('button');
    btnLimpiar.id = 'btnLimpiarFiltros';
    btnLimpiar.textContent = 'Limpiar filtros';
    btnLimpiar.style.padding = '0.8rem 1.5rem';
    btnLimpiar.style.backgroundColor = 'transparent';
    btnLimpiar.style.border = '1.5px solid var(--green-dark)';
    btnLimpiar.style.borderRadius = '30px';
    btnLimpiar.style.color = 'var(--green-dark)';
    btnLimpiar.style.fontWeight = '600';
    btnLimpiar.style.cursor = 'pointer';
    btnLimpiar.style.transition = 'all 0.2s';

    btnLimpiar.addEventListener('mouseenter', function() {
        this.style.backgroundColor = 'var(--green-dark)';
        this.style.color = 'white';
    });
    btnLimpiar.addEventListener('mouseleave', function() {
        this.style.backgroundColor = 'transparent';
        this.style.color = 'var(--green-dark)';
    });

    btnLimpiar.addEventListener('click', function() {
        document.getElementById('filtroTipo').value = '';
        document.getElementById('filtroEstado').value = '';
        document.getElementById('busqueda').value = '';
        document.getElementById('filtroTipo').dispatchEvent(new Event('change'));
    });

    filtrosGrid.appendChild(btnLimpiar);
}