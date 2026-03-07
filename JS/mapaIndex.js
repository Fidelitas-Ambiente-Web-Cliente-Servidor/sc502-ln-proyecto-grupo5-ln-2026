// mapaIndex.js - Mapa interactivo con filtros

document.addEventListener('DOMContentLoaded', function() {
    // Verificar que el contenedor exista
    const mapContainer = document.getElementById('mapaReportes');
    if (!mapContainer) return;

    // Inicializar el mapa centrado en Costa Rica
    const map = L.map('mapaReportes').setView([9.7489, -83.7534], 8);

    // Capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Marcadores de ejemplo (simulando reportes)
    const reportes = [
        { id: 1, coords: [9.9348, -84.0875], tipo: 'contaminacion', estado: 'pendiente', nombre: 'Contaminación de río' },
        { id: 2, coords: [10.0, -84.2], tipo: 'tala', estado: 'proceso', nombre: 'Tala ilegal' },
        { id: 3, coords: [9.98, -83.03], tipo: 'quema', estado: 'resuelto', nombre: 'Quema de residuos' },
        { id: 4, coords: [10.15, -85.45], tipo: 'agua', estado: 'pendiente', nombre: 'Contaminación de agua' },
        { id: 5, coords: [9.37, -83.7], tipo: 'fauna', estado: 'proceso', nombre: 'Daño a fauna' },
        { id: 6, coords: [10.45, -84.65], tipo: 'contaminacion', estado: 'resuelto', nombre: 'Derrame químico' },
        { id: 7, coords: [9.75, -84.95], tipo: 'tala', estado: 'pendiente', nombre: 'Tala en reserva' }
    ];

    // Almacenar los marcadores para poder filtrarlos
    const markers = [];

    // Función para determinar color según estado
    function getColor(estado) {
        switch (estado) {
            case 'pendiente': return '#f44336'; // rojo
            case 'proceso': return '#ff9800';    // naranja
            case 'resuelto': return '#4caf50';   // verde
            default: return '#2196f3';            // azul
        }
    }

    // Crear los marcadores
    reportes.forEach(r => {
        const marker = L.circleMarker(r.coords, {
            radius: 10,
            fillColor: getColor(r.estado),
            color: '#333',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.8
        }).addTo(map)
          .bindPopup(`
              <b>${r.nombre}</b><br>
              Tipo: ${r.tipo}<br>
              Estado: <span style="color:${getColor(r.estado)}; font-weight:bold;">${r.estado}</span>
          `);
        
        // Guardar el marcador junto con sus propiedades para filtrar
        markers.push({
            marker: marker,
            tipo: r.tipo,
            estado: r.estado
        });
    });

    // Referencias a los selects
    const filtroTipo = document.getElementById('filtroTipo');
    const filtroEstado = document.getElementById('filtroEstado');

    // Función para aplicar filtros
    function aplicarFiltros() {
        const tipoSeleccionado = filtroTipo.value;
        const estadoSeleccionado = filtroEstado.value;

        markers.forEach(item => {
            const coincideTipo = (tipoSeleccionado === 'todos') || (item.tipo === tipoSeleccionado);
            const coincideEstado = (estadoSeleccionado === 'todos') || (item.estado === estadoSeleccionado);

            if (coincideTipo && coincideEstado) {
                item.marker.addTo(map); // asegurar que está en el mapa
                // Podríamos también cambiar opacidad o estilo, pero con add/remove es suficiente
            } else {
                map.removeLayer(item.marker);
            }
        });
    }

    // Escuchar cambios en los selects
    filtroTipo.addEventListener('change', aplicarFiltros);
    filtroEstado.addEventListener('change', aplicarFiltros);

    // Ajustar tamaño del mapa después de cargar el header (por si hay problemas)
    setTimeout(() => map.invalidateSize(), 200);

    // Opcional: botón para limpiar filtros (si quieres agregar uno)
});