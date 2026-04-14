document.addEventListener("DOMContentLoaded", function () {
    // Tabs
    const tabs = document.querySelectorAll(".tab-link");
    const contenidos = document.querySelectorAll(".tab-content");
    tabs.forEach(tab => {
        tab.addEventListener("click", function () {
            tabs.forEach(t => t.classList.remove("active"));
            contenidos.forEach(c => c.classList.remove("active"));
            tab.classList.add("active");
            document.getElementById(tab.dataset.tab).classList.add("active");
        });
    });

    // === FILTROS PARA BANDEJA ===
    const bandejaLista = document.getElementById("bandejaLista");
    const filtroBusquedaBandeja = document.getElementById("filtroBusquedaBandeja");
    const filtroTipoBandeja = document.getElementById("filtroTipoBandeja");
    const filtroEstadoBandeja = document.getElementById("filtroEstadoBandeja");
    const filtroGravedadBandeja = document.getElementById("filtroGravedadBandeja");
    const btnLimpiarBandeja = document.getElementById("btnLimpiarFiltrosBandeja");

    function filtrarBandeja() {
        const cards = bandejaLista.querySelectorAll(".reporte-card");
        const busqueda = filtroBusquedaBandeja.value.trim().toLowerCase();
        const tipo = filtroTipoBandeja.value;
        const estado = filtroEstadoBandeja.value;
        const gravedad = filtroGravedadBandeja.value;

        cards.forEach(card => {
            let mostrar = true;
            const id = card.dataset.id;
            const desc = (card.dataset.desc || "").toLowerCase();
            if (busqueda && !id.includes(busqueda) && !desc.includes(busqueda)) mostrar = false;
            // Comparación insensible a mayúsculas/minúsculas para tipo
            if (tipo && card.dataset.tipo.toLowerCase() !== tipo.toLowerCase()) mostrar = false;
            if (estado && card.dataset.estado !== estado) mostrar = false;
            if (gravedad && card.dataset.gravedad !== gravedad) mostrar = false;
            card.style.display = mostrar ? "flex" : "none";
        });
    }

    if (filtroBusquedaBandeja) filtroBusquedaBandeja.addEventListener("input", filtrarBandeja);
    if (filtroTipoBandeja) filtroTipoBandeja.addEventListener("change", filtrarBandeja);
    if (filtroEstadoBandeja) filtroEstadoBandeja.addEventListener("change", filtrarBandeja);
    if (filtroGravedadBandeja) filtroGravedadBandeja.addEventListener("change", filtrarBandeja);
    if (btnLimpiarBandeja) {
        btnLimpiarBandeja.addEventListener("click", () => {
            filtroBusquedaBandeja.value = "";
            filtroTipoBandeja.value = "";
            filtroEstadoBandeja.value = "";
            filtroGravedadBandeja.value = "";
            filtrarBandeja();
        });
    }

    // === FILTROS PARA ASIGNACIÓN ===
    const asignacionLista = document.getElementById("asignacionLista");
    const filtroBusquedaAsignacion = document.getElementById("filtroBusquedaAsignacion");
    const filtroTipoAsignacion = document.getElementById("filtroTipoAsignacion");
    const btnLimpiarAsignacion = document.getElementById("btnLimpiarFiltrosAsignacion");

    function filtrarAsignacion() {
        const cards = asignacionLista.querySelectorAll(".reporte-card");
        const busqueda = filtroBusquedaAsignacion.value.trim().toLowerCase();
        const tipo = filtroTipoAsignacion.value;
        cards.forEach(card => {
            let mostrar = true;
            const id = card.dataset.id;
            if (busqueda && !id.includes(busqueda)) mostrar = false;
            // Comparación insensible a mayúsculas/minúsculas para tipo
            if (tipo && card.dataset.tipo.toLowerCase() !== tipo.toLowerCase()) mostrar = false;
            card.style.display = mostrar ? "flex" : "none";
        });
    }

    if (filtroBusquedaAsignacion) filtroBusquedaAsignacion.addEventListener("input", filtrarAsignacion);
    if (filtroTipoAsignacion) filtroTipoAsignacion.addEventListener("change", filtrarAsignacion);
    if (btnLimpiarAsignacion) {
        btnLimpiarAsignacion.addEventListener("click", () => {
            filtroBusquedaAsignacion.value = "";
            filtroTipoAsignacion.value = "";
            filtrarAsignacion();
        });
    }

    // === MODAL DETALLE ===
    const modalBandeja = document.getElementById("modalDetalleBandeja");
    const cerrarModal = document.getElementById("cerrarModalBandeja");
    document.querySelectorAll(".ver-detalle").forEach(btn => {
        btn.addEventListener("click", function(e) {
            const card = btn.closest(".reporte-card");
            document.getElementById("modalTitulo").textContent = "Reporte #" + card.dataset.id;
            document.getElementById("modalTipo").textContent = card.dataset.tipo;
            document.getElementById("modalFecha").textContent = card.dataset.fecha;
            document.getElementById("modalGravedad").textContent = (card.dataset.gravedad || "").toUpperCase();
            document.getElementById("modalDesc").textContent = card.dataset.desc;
            const fotoUrl = card.dataset.foto;
            const img = document.getElementById("modalImagen");
            const noImg = document.getElementById("modalNoImagen");
            if (fotoUrl && fotoUrl.trim() !== "") {
                img.src = fotoUrl;
                img.style.display = "block";
                noImg.style.display = "none";
            } else {
                img.style.display = "none";
                noImg.style.display = "block";
            }
            modalBandeja.style.display = "flex";
        });
    });
    if (cerrarModal) cerrarModal.addEventListener("click", () => modalBandeja.style.display = "none");
    window.addEventListener("click", e => { if (e.target === modalBandeja) modalBandeja.style.display = "none"; });

    // === ASIGNACIÓN ===
    document.querySelectorAll(".btn-asignar").forEach(btn => {
        btn.addEventListener("click", function() {
            const card = btn.closest(".reporte-card");
            const select = card.querySelector(".asignarInstitucion");
            const institucion_id = select.value;
            const reporte_id = btn.dataset.id;
            if (!institucion_id) { alert("Seleccione una institución"); return; }
            fetch('../PanelAdmin/adminAcciones.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accion: 'asignar-institucion', id_reporte: reporte_id, id_usuario: institucion_id })
            }).then(res => res.json()).then(data => {
                if(data.success) alert("Reporte asignado correctamente");
                else alert("Error: " + data.message);
            }).catch(e => console.error(e));
        });
    });

    // === ACTUALIZAR ESTADO ===
    const btnActualizarEstado = document.getElementById("btnActualizarEstado");
    if (btnActualizarEstado) {
        btnActualizarEstado.addEventListener("click", () => {
            let reporte_id = document.getElementById("selectReporteEstado").value;
            let estado = document.getElementById("nuevoEstadoReporte").value;
            fetch('../PanelAdmin/adminAcciones.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accion: 'estado-reporte', id: reporte_id, estado: estado })
            }).then(res => res.json()).then(data => {
                if(data.success) { alert("Actualizado a " + estado); location.reload(); }
                else alert("Error: " + data.message);
            });
        });
    }

    // === COMENTARIOS ===
    const btnGuardarComentario = document.getElementById("btnGuardarComentario");
    if (btnGuardarComentario) {
        btnGuardarComentario.addEventListener("click", () => {
            let reporte_id = document.getElementById("selectReporteComentario").value;
            let texto = document.getElementById("textoComentario").value;
            if (!texto.trim()) { alert("Escriba un comentario"); return; }
            fetch('../PanelAdmin/adminAcciones.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accion: 'agregar-comentario', id_reporte: reporte_id, texto: texto })
            }).then(res => res.json()).then(data => {
                if(data.success) { alert("Comentario agregado"); document.getElementById("textoComentario").value = ""; }
                else alert("Error: " + data.message);
            });
        });
    }

    // === CERRAR CASO ===
    const btnCerrarCaso = document.getElementById("btnCerrarCaso");
    if (btnCerrarCaso) {
        btnCerrarCaso.addEventListener("click", () => {
            let reporte_id = document.getElementById("selectReporteCerrar").value;
            if (confirm("¿Cerrar caso #" + reporte_id + "?")) {
                fetch('../PanelAdmin/adminAcciones.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ accion: 'cerrar-caso', id_reporte: reporte_id })
                }).then(res => res.json()).then(data => {
                    if(data.success) { alert("Caso cerrado"); location.reload(); }
                    else alert("Error: " + data.message);
                });
            }
        });
    }
});