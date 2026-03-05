document.addEventListener("DOMContentLoaded", function () {

    const tabs = document.querySelectorAll(".tab-link");
    const contenidos = document.querySelectorAll(".tab-content");

    tabs.forEach(function (tab) {
        tab.addEventListener("click", function () {

            tabs.forEach(function (t) {
                t.classList.remove("active");
            });

            contenidos.forEach(function (c) {
                c.classList.remove("active");
            });

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
            let titulo = card.querySelector("h4").textContent;

            alert("Detalle de " + titulo);
        });
    });

    const botonAsignar = document.querySelector("#asignacion .btn-guardar");
    const selectUsuario = document.getElementById("asignarUsuario");

    if (botonAsignar && selectUsuario) {
        botonAsignar.addEventListener("click", function () {

            let usuario = selectUsuario.value;

            if (usuario === "") {
                alert("Seleccione un usuario");
                return;
            }

            alert("Reporte asignado correctamente");
            selectUsuario.value = "";
        });
    }

    const botonEstado = document.querySelector("#estado .btn-guardar");
    if (botonEstado) {
        botonEstado.addEventListener("click", function () {

            let selects = document.querySelectorAll("#estado select");
            let reporte = selects[0].value;
            let estado = selects[1].value;

            alert(reporte + " actualizado a estado: " + estado);
        });
    }

    const botonComentario = document.querySelector("#comentarios .btn-guardar");
    if (botonComentario) {
        botonComentario.addEventListener("click", function () {

            let reporte = document.querySelector("#comentarios select").value;
            let texto = document.querySelector("#comentarios textarea").value;

            if (texto === "") {
                alert("Escriba un comentario");
                return;
            }
            
            alert("Comentario agregado a " + reporte);
            document.querySelector("#comentarios textarea").value = "";
        });
    }

    const botonCerrar = document.querySelector("#cerrar .btn-guardar");
    if (botonCerrar) {
        botonCerrar.addEventListener("click", function () {

            let reporte = document.querySelector("#cerrar select").value;
            let confirmacion = confirm("¿Cerrar " + reporte + "?");

            if (confirmacion) {
                alert(reporte + " cerrado correctamente");
            }
        });
    }
});