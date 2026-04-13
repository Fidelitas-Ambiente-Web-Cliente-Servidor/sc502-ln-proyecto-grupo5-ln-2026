// mapaIndex.js - Mapa interactivo con datos reales

let map;
let markers = [];

document.addEventListener('DOMContentLoaded', function() {
    const mapContainer = document.getElementById('mapaReportes');
    if (!mapContainer) return;

    // Inicializar mapa centrado en Costa Rica
    map = L.map('mapaReportes').setView([9.7489, -83.7534], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Cargar reportes desde el endpoint
    cargarReportes();

    // Configurar filtros
    const filtroTipo = document.getElementById('filtroTipo');
    const filtroEstado = document.getElementById('filtroEstado');
    if (filtroTipo) filtroTipo.addEventListener('change', aplicarFiltros);
    if (filtroEstado) filtroEstado.addEventListener('change', aplicarFiltros);

    // Ajustar tamaño del mapa después de cargar elementos del header
    setTimeout(() => map.invalidateSize(), 200);
});

function cargarReportes() {
    fetch('/sc502-ln-proyecto-grupo5-ln-2026/api/reportes.php')
        .then(response => response.json())
        .then(data => {
            window.reportesData = data; // guardar globalmente para filtros
            renderizarMarcadores(data);
        })
        .catch(error => console.error('Error cargando reportes:', error));
}

function renderizarMarcadores(reportes) {
    // Limpiar marcadores existentes
    markers.forEach(m => map.removeLayer(m));
    markers = [];

    reportes.forEach(r => {
        const color = getColor(r.estado);
        const marker = L.circleMarker([r.lat, r.lng], {
            radius: 10,
            fillColor: color,
            color: '#333',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.8
        }).addTo(map);

        marker.bindPopup(`
            <b>Reporte #${r.id}</b><br>
            <b>Tipo:</b> ${r.tipo}<br>
            <b>Estado:</b> <span style="color:${color};">${r.estado}</span><br>
            <b>Fecha:</b> ${r.fecha}<br>
            <p>${r.descripcion.substring(0, 100)}${r.descripcion.length > 100 ? '…' : ''}</p>
            <a href="/sc502-ln-proyecto-grupo5-ln-2026/Reportes/detalle.php?id=${r.id}" target="_blank">Ver detalle</a>
        `);

        markers.push({
            marker: marker,
            tipo: r.tipo,
            estado: r.estado
        });
    });
}

function getColor(estado) {
    switch (estado.toLowerCase()) {
        case 'pendiente': return '#f44336';
        case 'en proceso': return '#ff9800';
        case 'resuelto': return '#4caf50';
        default: return '#2196f3';
    }
}

function aplicarFiltros() {
    const tipoSeleccionado = document.getElementById('filtroTipo').value;
    const estadoSeleccionado = document.getElementById('filtroEstado').value;

    markers.forEach(item => {
        const coincideTipo = (tipoSeleccionado === 'todos') || (item.tipo.toLowerCase() === tipoSeleccionado);
        const coincideEstado = (estadoSeleccionado === 'todos') || (item.estado.toLowerCase() === estadoSeleccionado);

        if (coincideTipo && coincideEstado) {
            item.marker.addTo(map);
        } else {
            map.removeLayer(item.marker);
        }
    });
}