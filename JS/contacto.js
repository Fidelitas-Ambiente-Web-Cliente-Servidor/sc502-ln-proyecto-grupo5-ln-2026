document.addEventListener("DOMContentLoaded", function () {

    const formContacto = document.getElementById("formContacto");
    const mensajeConfirmacion = document.getElementById("mensajeConfirmacion");

    formContacto.addEventListener("submit", function (e) {

        e.preventDefault();

        const nombre = document.getElementById("nombre").value.trim();
        const correo = document.getElementById("correo").value.trim();
        const asunto = document.getElementById("asunto").value.trim();
        const mensaje = document.getElementById("mensaje").value.trim();

        if (nombre === "" || correo === "" || asunto === "" || mensaje === "") {
            alert("Por favor complete todos los campos.");
            return;
        }

        if (!correo.includes("@")) {
            alert("Ingrese un correo válido.");
            return;
        }

        mensajeConfirmacion.textContent = "Mensaje enviado correctamente. Gracias por contactarnos.";

        formContacto.reset();

    });

});