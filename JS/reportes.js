document.addEventListener("DOMContentLoaded", function () {

    const inputImagenes = document.getElementById("imagenes");
    const preview = document.getElementById("previewImagenes");

    inputImagenes.addEventListener("change", function () {
        preview.innerHTML = "";

        let archivos = inputImagenes.files;

        for (let i = 0; i < archivos.length; i++) {
            let img = document.createElement("img");
            img.src = URL.createObjectURL(archivos[i]);
            img.style.width = "120px";
            img.style.margin = "10px";
            img.style.borderRadius = "12px";
            preview.appendChild(img);
        }
    });

    let mapa = L.map("mapa").setView([9.9281, -84.0907], 13);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "OpenStreetMap"
    }).addTo(mapa);

    let marcador = null;
    let ubicacionSeleccionada = null;

    mapa.on("click", function (e) {
        ubicacionSeleccionada = e.latlng;

        if (marcador) {
            marcador.setLatLng(e.latlng);
        } else {
            marcador = L.marker(e.latlng).addTo(mapa);
        }
    });

    let formulario = document.getElementById("formReporte");

    formulario.addEventListener("submit", function (e) {
        e.preventDefault();

        if (ubicacionSeleccionada == null) {
            alert("Debe seleccionar una ubicación en el mapa");
            return;
        }

        // Armar usando FormData para soportar imágenes
        let formData = new FormData();
        formData.append('accion', 'crear-reporte');
        formData.append('tipoProblema', document.getElementById('tipoProblema').value);
        formData.append('descripcion', document.getElementById('descripcion').value);
        formData.append('fecha', document.getElementById('fecha').value);
        formData.append('hora', document.getElementById('hora').value);
        formData.append('gravedad', document.getElementById('gravedad').value);
        formData.append('lat', ubicacionSeleccionada.lat);
        formData.append('lng', ubicacionSeleccionada.lng);

        let inputImg = document.getElementById("imagenes");
        if (inputImg.files.length > 0) {
            for (let i = 0; i < inputImg.files.length; i++) {
                formData.append('imagenes[]', inputImg.files[i]);
            }
        }

        // Enviar silenciosamente
        fetch('../PanelAdmin/adminAcciones.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Reporte enviado correctamente con ID: " + data.reporte_id);
                formulario.reset();
                preview.innerHTML = "";
            } else {
                alert("Hubo un problema: " + data.message);
            }
        })
        .catch(err => console.error("Error enviando reporte:", err));
    });

});