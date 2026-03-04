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

    const botonAsignar = document.querySelector(".btn-guardar");
    const selectUsuario = document.getElementById("asignarUsuario");

    if (botonAsignar && selectUsuario) {

        botonAsignar.addEventListener("click", function () {

            let usuario = selectUsuario.value;

            if (usuario === "") {
                alert("Seleccione un usuario");
                return;
            }

            let card = botonAsignar.closest(".reporte-card");
            let titulo = card.querySelector("h4").textContent;

            alert(titulo + " asignado correctamente");

            selectUsuario.value = "";
        });
    }

});