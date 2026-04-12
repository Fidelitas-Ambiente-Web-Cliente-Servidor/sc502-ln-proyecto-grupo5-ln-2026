document.addEventListener("DOMContentLoaded", function () {

    const tabs = document.querySelectorAll(".tab-link");
    const contenidos = document.querySelectorAll(".tab-content");

    tabs.forEach(function (tab) {
        tab.addEventListener("click", function () {
            tabs.forEach(t => t.classList.remove("active"));
            contenidos.forEach(c => c.classList.remove("active"));
            tab.classList.add("active");
            let objetivo = document.getElementById(tab.dataset.tab);
            if (objetivo) {
                objetivo.classList.add("active");
            }
        });
    });

    const botonesDetalle = document.querySelectorAll(".ver-detalle");
    botonesDetalle.forEach(function (boton) {
        boton.addEventListener("click", function (e) {
            e.preventDefault();
            let card = boton.closest(".reporte-card");
            let id = card.dataset.id;
            let tipo = card.dataset.tipo;
            let fecha = card.dataset.fecha;
            let desc = card.dataset.desc;
            let grav = card.dataset.gravedad;

            alert(`--- Reporte #${id} ---\nTipo: ${tipo}\nFecha: ${fecha}\nGravedad: ${grav.toUpperCase()}\n\nDescripción:\n${desc}`);
        });
    });

    // Asignacion de reportes
    const botonesAsignar = document.querySelectorAll(".btn-asignar");
    botonesAsignar.forEach(function (boton) {
        boton.addEventListener("click", function () {
            let card = boton.closest(".reporte-card");
            let select = card.querySelector(".asignarInstitucion");
            let institucion_id = select.value;
            let reporte_id = boton.dataset.id;

            if (institucion_id === "") {
                alert("Seleccione una institución");
                return;
            }

            fetch('../PanelAdmin/adminAcciones.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accion: 'asignar-institucion', id_reporte: reporte_id, id_usuario: institucion_id })
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    alert("Reporte asignado correctamente");
                }else{
                    alert("Error: " + data.message);
                }
            }).catch(e => console.error("Error", e));
        });
    });

    // Actualizar estado
    const btnActualizarEstado = document.getElementById("btnActualizarEstado");
    if (btnActualizarEstado) {
        btnActualizarEstado.addEventListener("click", function () {
            let reporte_id = document.getElementById("selectReporteEstado").value;
            let estado = document.getElementById("nuevoEstadoReporte").value;

            fetch('../PanelAdmin/adminAcciones.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accion: 'estado-reporte', id: reporte_id, estado: estado })
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    alert("Reporte " + reporte_id + " actualizado a estado: " + estado);
                    location.reload();
                } else {
                    alert("Error: " + data.message);
                }
            }).catch(e => console.error(e));
        });
    }

    // Comentarios
    const btnGuardarComentario = document.getElementById("btnGuardarComentario");
    if (btnGuardarComentario) {
        btnGuardarComentario.addEventListener("click", function () {
            let reporte_id = document.getElementById("selectReporteComentario").value;
            let texto = document.getElementById("textoComentario").value;

            if (texto.trim() === "") {
                alert("Escriba un comentario");
                return;
            }
            
            fetch('../PanelAdmin/adminAcciones.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accion: 'agregar-comentario', id_reporte: reporte_id, texto: texto })
            }).then(res => res.json()).then(data => {
                if(data.success){
                    alert("Comentario agregado al reporte " + reporte_id);
                    document.getElementById("textoComentario").value = "";
                }else{
                    alert("Error: " + data.message);
                }
            });
        });
    }

    // Cerrar caso
    const btnCerrarCaso = document.getElementById("btnCerrarCaso");
    if (btnCerrarCaso) {
        btnCerrarCaso.addEventListener("click", function () {
            let reporte_id = document.getElementById("selectReporteCerrar").value;
            let confirmacion = confirm("¿Cerrar caso para el reporte " + reporte_id + "?");

            if (confirmacion) {
                fetch('../PanelAdmin/adminAcciones.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ accion: 'cerrar-caso', id_reporte: reporte_id })
                }).then(res => res.json()).then(data => {
                    if(data.success){
                        alert("Reporte " + reporte_id + " cerrado correctamente");
                        location.reload();
                    }else{
                        alert("Error: " + data.message);
                    }
                });
            }
        });
    }
});