document.addEventListener("DOMContentLoaded", function () {

 //Datos precargados, simulacion de datos 

    let usuarios = [
        { id: 1, nombre: "Bryan Solano", correo: "bryan@ecoalerta.com", rol: "Usuario", estado: "Activo" },
        { id: 2, nombre: "Ashlee Ramírez", correo: "ashlee@ecoalerta.com", rol: "Institución", estado: "Activo" },
        { id: 3, nombre: "Jafet Mora", correo: "jafet@ecoalerta.com", rol: "Usuario", estado: "Bloqueado" },
        { id: 4, nombre: "Josue Arias", correo: "josue@ecoalerta.com", rol: "Administrador", estado: "Activo" }
    ];

    let reportes = [
        {
            id: 101,
            tipo: "Basura",
            usuario: "Bryan Solano",
            descripcion: "Se encontró acumulación de basura en un lote baldío cerca de una escuela.",
            gravedad: "Media",
            estado: "Pendiente",
            fecha: "2026-03-05",
            hora: "14:30",
            ubicacion: "San José, Costa Rica",
            imagenes: [
                "https://via.placeholder.com/150?text=Reporte+1",
                "https://via.placeholder.com/150?text=Reporte+1B"
            ]
        },
        {
            id: 102,
            tipo: "Incendio Forestal",
            usuario: "Jafet Mora",
            descripcion: "Se reporta humo y fuego en una zona montañosa cercana a la comunidad.",
            gravedad: "Alta",
            estado: "En Proceso",
            fecha: "2026-03-04",
            hora: "09:15",
            ubicacion: "Cartago, Costa Rica",
            imagenes: [
                "https://via.placeholder.com/150?text=Reporte+2"
            ]
        },
        {
            id: 103,
            tipo: "Contaminación de agua",
            usuario: "Ashlee Ramírez",
            descripcion: "El río presenta coloración extraña y desechos flotando en la superficie.",
            gravedad: "Alta",
            estado: "Resuelto",
            fecha: "2026-03-03",
            hora: "11:00",
            ubicacion: "Alajuela, Costa Rica",
            imagenes: [
                "https://via.placeholder.com/150?text=Reporte+3"
            ]
        },
        {
            id: 104,
            tipo: "Tala ilegal",
            usuario: "Bryan Solano",
            descripcion: "Se observó tala de árboles sin autorización en una zona protegida.",
            gravedad: "Crítica",
            estado: "Pendiente",
            fecha: "2026-03-02",
            hora: "16:45",
            ubicacion: "Limón, Costa Rica",
            imagenes: [
                "https://via.placeholder.com/150?text=Reporte+4"
            ]
        }
    ];

    let ubicacionActualReporte = "";

   //Referencias DOM

    const tablaUsuarios = document.getElementById("tablaUsuarios");
    const tablaReportes = document.getElementById("tablaReportes");

    const totalUsuarios = document.getElementById("totalUsuarios");
    const totalReportes = document.getElementById("totalReportes");
    const totalPendientes = document.getElementById("totalPendientes");
    const totalResueltos = document.getElementById("totalResueltos");

    const botonesTabs = document.querySelectorAll(".tab-btn");
    const contenidosTabs = document.querySelectorAll(".tab-content");

    const modalDetalle = document.getElementById("modalDetalleReporte");
    const cerrarModalDetalle = document.getElementById("cerrarModalDetalle");

    const detalleId = document.getElementById("detalleId");
    const detalleTipo = document.getElementById("detalleTipo");
    const detalleUsuario = document.getElementById("detalleUsuario");
    const detalleGravedad = document.getElementById("detalleGravedad");
    const detalleEstado = document.getElementById("detalleEstado");
    const detalleFecha = document.getElementById("detalleFecha");
    const detalleHora = document.getElementById("detalleHora");
    const detalleUbicacion = document.getElementById("detalleUbicacion");
    const detalleDescripcion = document.getElementById("detalleDescripcion");
    const detalleImagenes = document.getElementById("detalleImagenes");
    const btnVerMapa = document.getElementById("btnVerMapa");

    //Cambio ventanas

    botonesTabs.forEach((boton) => {
        boton.addEventListener("click", function () {
            const destino = this.dataset.tab;

            botonesTabs.forEach(btn => btn.classList.remove("active"));
            contenidosTabs.forEach(c => c.classList.remove("active"));

            this.classList.add("active");

            const seccion = document.getElementById(destino);
            if (seccion) {
                seccion.classList.add("active");
            }
        });
    });

    //Renderizacion Usuarios

    function renderUsuarios() {
        tablaUsuarios.innerHTML = "";

        usuarios.forEach(usuario => {
            const fila = document.createElement("tr");

            fila.innerHTML = `
                <td>${usuario.id}</td>
                <td>${usuario.nombre}</td>
                <td>${usuario.correo}</td>
                <td>${usuario.rol}</td>
                <td>${usuario.estado}</td>
                <td>
                    <button class="accion-btn estado-btn" data-id="${usuario.id}" data-accion="estado-usuario">
                        Cambiar Estado
                    </button>

                    <button class="accion-btn rol-btn" data-id="${usuario.id}" data-accion="rol-usuario">
                        Cambiar Rol
                    </button>

                    <button class="accion-btn eliminar-btn" data-id="${usuario.id}" data-accion="eliminar-usuario">
                        Eliminar
                    </button>
                </td>
            `;

            tablaUsuarios.appendChild(fila);
        });
    }

    //Renderizacion Reportes

    function renderReportes() {
        tablaReportes.innerHTML = "";

        reportes.forEach(reporte => {
            const fila = document.createElement("tr");

            fila.innerHTML = `
                <td>${reporte.id}</td>
                <td>${reporte.tipo}</td>
                <td>${reporte.usuario}</td>
                <td>${reporte.gravedad}</td>
                <td>${reporte.estado}</td>
                <td>${reporte.fecha}</td>
                <td>${reporte.hora}</td>
                <td>
                    <button class="accion-btn detalle-btn"
                        data-id="${reporte.id}"
                        data-accion="ver-detalle">
                        Ver detalle
                    </button>

                    <button class="accion-btn estado-btn"
                        data-id="${reporte.id}"
                        data-accion="estado-reporte">
                        Cambiar Estado
                    </button>

                    <button class="accion-btn eliminar-btn"
                        data-id="${reporte.id}"
                        data-accion="eliminar-reporte">
                        Eliminar
                    </button>
                </td>
            `;

            tablaReportes.appendChild(fila);
        });
    }

    //Renderizacion Estadisticas

    function renderEstadisticas() {
        totalUsuarios.textContent = usuarios.length;
        totalReportes.textContent = reportes.length;
        totalPendientes.textContent = reportes.filter(r => r.estado === "Pendiente").length;
        totalResueltos.textContent = reportes.filter(r => r.estado === "Resuelto").length;
    }

    // Detalles del reporte

    function abrirDetalleReporte(reporte) {
        detalleId.textContent = reporte.id;
        detalleTipo.textContent = reporte.tipo;
        detalleUsuario.textContent = reporte.usuario;
        detalleGravedad.textContent = reporte.gravedad;
        detalleEstado.textContent = reporte.estado;
        detalleFecha.textContent = reporte.fecha;
        detalleHora.textContent = reporte.hora;
        detalleUbicacion.textContent = reporte.ubicacion;
        detalleDescripcion.textContent = reporte.descripcion;

        ubicacionActualReporte = reporte.ubicacion;

        detalleImagenes.innerHTML = "";

        if (reporte.imagenes && reporte.imagenes.length > 0) {
            reporte.imagenes.forEach(ruta => {
                const img = document.createElement("img");
                img.src = ruta;
                img.alt = `Imagen del reporte ${reporte.id}`;
                detalleImagenes.appendChild(img);
            });
        } else {
            detalleImagenes.innerHTML = "<p>No hay imágenes disponibles.</p>";
        }

        modalDetalle.style.display = "flex";
    }

    // Eventos usuarios

    tablaUsuarios.addEventListener("click", function (e) {
        const boton = e.target.closest("button");
        if (!boton) return;

        const id = Number(boton.dataset.id);
        const accion = boton.dataset.accion;

        if (accion === "estado-usuario") {
            usuarios = usuarios.map(usuario => {
                if (usuario.id === id) {
                    usuario.estado = usuario.estado === "Activo" ? "Bloqueado" : "Activo";
                }
                return usuario;
            });
        }

        if (accion === "rol-usuario") {
            usuarios = usuarios.map(usuario => {
                if (usuario.id === id) {
                    if (usuario.rol === "Usuario") usuario.rol = "Institución";
                    else if (usuario.rol === "Institución") usuario.rol = "Administrador";
                    else usuario.rol = "Usuario";
                }
                return usuario;
            });
        }

        if (accion === "eliminar-usuario") {
            const confirmar = confirm("¿Desea eliminar este usuario?");
            if (!confirmar) return;
            usuarios = usuarios.filter(usuario => usuario.id !== id);
        }

        renderUsuarios();
        renderEstadisticas();
    });

    // Eventos reportes


    tablaReportes.addEventListener("click", function (e) {
        const boton = e.target.closest("button");
        if (!boton) return;

        const id = Number(boton.dataset.id);
        const accion = boton.dataset.accion;

        if (accion === "ver-detalle") {
            const reporte = reportes.find(r => r.id === id);
            if (reporte) {
                abrirDetalleReporte(reporte);
            }
            return;
        }

        if (accion === "estado-reporte") {
            reportes = reportes.map(reporte => {
                if (reporte.id === id) {
                    if (reporte.estado === "Pendiente") reporte.estado = "En Proceso";
                    else if (reporte.estado === "En Proceso") reporte.estado = "Resuelto";
                    else reporte.estado = "Pendiente";
                }
                return reporte;
            });
        }

        if (accion === "eliminar-reporte") {
            const confirmar = confirm("¿Desea eliminar este reporte?");
            if (!confirmar) return;
            reportes = reportes.filter(r => r.id !== id);
        }

        renderReportes();
        renderEstadisticas();
    });


    // Boton para mostrar mapa


    if (btnVerMapa) {
        btnVerMapa.addEventListener("click", function () {
            if (!ubicacionActualReporte) return;

            const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(ubicacionActualReporte)}`;
            window.open(url, "_blank");
        });
    }


    // cierre de modal


    cerrarModalDetalle.addEventListener("click", function () {
        modalDetalle.style.display = "none";
    });

    window.addEventListener("click", function (e) {
        if (e.target === modalDetalle) {
            modalDetalle.style.display = "none";
        }
    });


    // Inicializacion datos


    renderUsuarios();
    renderReportes();
    renderEstadisticas();
});