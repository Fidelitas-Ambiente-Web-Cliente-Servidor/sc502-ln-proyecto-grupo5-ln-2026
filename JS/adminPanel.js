document.addEventListener("DOMContentLoaded", function () {
    // ==================== DATOS INICIALES ====================
    let usuarios = window.usuariosIniciales || [];
    let reportes = window.reportesIniciales || [];
    let mensajes = window.mensajesIniciales || [];

    // ==================== REFERENCIAS DOM ====================
    const tablaUsuarios = document.getElementById("tablaUsuarios");
    const tablaReportes = document.getElementById("tablaReportes");
    const tablaMensajes = document.getElementById("tablaMensajes");

    const totalUsuarios = document.getElementById("totalUsuarios");
    const totalReportes = document.getElementById("totalReportes");
    const totalPendientes = document.getElementById("totalPendientes");
    const totalResueltos = document.getElementById("totalResueltos");
    const totalNoLeidos = document.getElementById("totalNoLeidos");

    const botonesTabs = document.querySelectorAll(".tab-btn");
    const contenidosTabs = document.querySelectorAll(".tab-content");

    const modalDetalle = document.getElementById("modalDetalleReporte");
    const cerrarModalDetalle = document.getElementById("cerrarModalDetalle");
    const btnVerMapa = document.getElementById("btnVerMapa");

    // Variables de detalle
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

    let ubicacionActualReporte = "";

    // ==================== UTILIDADES ====================
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function showToast(message, isError = false) {
        const toast = document.createElement('div');
        toast.textContent = message;
        toast.style.position = 'fixed';
        toast.style.bottom = '20px';
        toast.style.right = '20px';
        toast.style.backgroundColor = isError ? '#dc3545' : '#28a745';
        toast.style.color = 'white';
        toast.style.padding = '10px 15px';
        toast.style.borderRadius = '8px';
        toast.style.zIndex = '9999';
        toast.style.fontWeight = 'bold';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    }

    // ==================== CAMBIO DE PESTAÑAS ====================
    botonesTabs.forEach((boton) => {
        boton.addEventListener("click", function () {
            const destino = this.dataset.tab;
            botonesTabs.forEach(btn => btn.classList.remove("active"));
            contenidosTabs.forEach(c => c.classList.remove("active"));
            this.classList.add("active");
            const seccion = document.getElementById(destino);
            if (seccion) seccion.classList.add("active");
        });
    });

    // ==================== RENDERIZAR USUARIOS ====================
    function renderUsuarios() {
        if (!tablaUsuarios) return;
        tablaUsuarios.innerHTML = "";
        usuarios.forEach(usuario => {
            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td>${usuario.id}</td>
                <td>${escapeHtml(usuario.nombre)}</td>
                <td>${escapeHtml(usuario.correo)}</td>
                <td>${usuario.rol}</td>
                <td>${usuario.estado}</td>
                <td>
                    <button class="accion-btn estado-btn" data-id="${usuario.id}" data-accion="estado-usuario">Cambiar Estado</button>
                    <button class="accion-btn rol-btn" data-id="${usuario.id}" data-accion="rol-usuario">Cambiar Rol</button>
                    <button class="accion-btn eliminar-btn" data-id="${usuario.id}" data-accion="eliminar-usuario">Eliminar</button>
                </td>
            `;
            tablaUsuarios.appendChild(fila);
        });
    }

    // ==================== RENDERIZAR REPORTES ====================
    function renderReportes() {
        if (!tablaReportes) return;
        tablaReportes.innerHTML = "";
        reportes.forEach(reporte => {
            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td>${reporte.id}</td>
                <td>${escapeHtml(reporte.tipo)}</td>
                <td>${escapeHtml(reporte.usuario)}</td>
                <td>${reporte.gravedad}</td>
                <td>${reporte.estado}</td>
                <td>${reporte.fecha}</td>
                <td>${reporte.hora}</td>
                <td>
                    <button class="accion-btn detalle-btn" data-id="${reporte.id}" data-accion="ver-detalle">Ver detalle</button>
                    <button class="accion-btn estado-btn" data-id="${reporte.id}" data-accion="estado-reporte">Cambiar Estado</button>
                    <button class="accion-btn eliminar-btn" data-id="${reporte.id}" data-accion="eliminar-reporte">Eliminar</button>
                </td>
            `;
            tablaReportes.appendChild(fila);
        });
    }

    // ==================== RENDERIZAR MENSAJES (ACTUALIZACIÓN INMEDIATA) ====================
    function renderMensajes() {
        console.log("Renderizando mensajes, cantidad:", mensajes.length);
        if (!tablaMensajes) {
            console.error("No se encontró la tabla #tablaMensajes");
            return;
        }
        tablaMensajes.innerHTML = "";
        if (mensajes.length === 0) {
            const fila = document.createElement("tr");
            fila.innerHTML = `<td colspan="8">No hay mensajes registrados</td>`;
            tablaMensajes.appendChild(fila);
        } else {
            mensajes.forEach(msg => {
                const estadoTexto = msg.leido ? "Leído" : "No leído";
                const estadoClass = msg.leido ? "leido" : "no-leido";
                const mensajeCorto = msg.mensaje.length > 50 ? msg.mensaje.substring(0, 50) + "…" : msg.mensaje;
                const fila = document.createElement("tr");
                fila.innerHTML = `
                    <td>${msg.id}</td>
                    <td>${escapeHtml(msg.nombre)}</td>
                    <td>${escapeHtml(msg.correo)}</td>
                    <td>${escapeHtml(msg.asunto)}</td>
                    <td>${escapeHtml(mensajeCorto)}</td>
                    <td><span class="estado-mensaje ${estadoClass}">${estadoTexto}</span></td>
                    <td>${msg.fecha_envio}</td>
                    <td>
                        ${!msg.leido ? `<button class="accion-btn leido-btn" data-id="${msg.id}" data-accion="marcar-leido-mensaje">Marcar leído</button>` : ''}
                        <button class="accion-btn eliminar-btn" data-id="${msg.id}" data-accion="eliminar-mensaje">Eliminar</button>
                    </td>
                `;
                tablaMensajes.appendChild(fila);
            });
        }
        actualizarContadorNoLeidos();
    }

    function actualizarContadorNoLeidos() {
        if (totalNoLeidos) {
            const noLeidos = mensajes.filter(m => !m.leido).length;
            totalNoLeidos.textContent = noLeidos;
        }
    }

    // ==================== RENDERIZAR ESTADÍSTICAS ====================
    function renderEstadisticas() {
        if (totalUsuarios) totalUsuarios.textContent = usuarios.length;
        if (totalReportes) totalReportes.textContent = reportes.length;
        if (totalPendientes) totalPendientes.textContent = reportes.filter(r => r.estado === "Pendiente").length;
        if (totalResueltos) totalResueltos.textContent = reportes.filter(r => r.estado === "Resuelto").length;
        actualizarContadorNoLeidos();
    }

    // ==================== DETALLE DE REPORTE ====================
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

    // ==================== EVENTOS DE USUARIOS ====================
    if (tablaUsuarios) {
        tablaUsuarios.addEventListener("click", function (e) {
            const boton = e.target.closest("button");
            if (!boton) return;
            const id = Number(boton.dataset.id);
            const accion = boton.dataset.accion;
            if (accion === "eliminar-usuario" && !confirm("¿Desea eliminar este usuario?")) return;

            let oldUsuarios = [...usuarios];
            if (accion === "estado-usuario") {
                usuarios = usuarios.map(u => u.id === id ? { ...u, estado: u.estado === "Activo" ? "Inactivo" : "Activo" } : u);
            } else if (accion === "rol-usuario") {
                usuarios = usuarios.map(u => u.id === id ? { ...u, rol: u.rol === "Usuario" ? "Administrador" : "Usuario" } : u);
            } else if (accion === "eliminar-usuario") {
                usuarios = usuarios.filter(u => u.id !== id);
            }
            renderUsuarios();
            renderEstadisticas();

            fetch('adminAcciones.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accion: accion, id: id })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    usuarios = oldUsuarios;
                    renderUsuarios();
                    renderEstadisticas();
                    showToast("Error: " + data.message, true);
                } else {
                    showToast("Actualizado");
                }
            })
            .catch(err => {
                usuarios = oldUsuarios;
                renderUsuarios();
                renderEstadisticas();
                showToast("Error de red", true);
            });
        });
    }

    // ==================== EVENTOS DE REPORTES ====================
    if (tablaReportes) {
        tablaReportes.addEventListener("click", function (e) {
            const boton = e.target.closest("button");
            if (!boton) return;
            const id = Number(boton.dataset.id);
            const accion = boton.dataset.accion;

            if (accion === "ver-detalle") {
                const reporte = reportes.find(r => r.id === id);
                if (reporte) abrirDetalleReporte(reporte);
                return;
            }
            if (accion === "eliminar-reporte" && !confirm("¿Desea eliminar este reporte?")) return;

            let payload = { accion: accion, id: id };
            let nuevoEstado = "";
            let oldReportes = [...reportes];

            if (accion === "estado-reporte") {
                const reporte = reportes.find(r => r.id === id);
                if (reporte) {
                    if (reporte.estado === "Pendiente") nuevoEstado = "En Proceso";
                    else if (reporte.estado === "En Proceso") nuevoEstado = "Resuelto";
                    else nuevoEstado = "Pendiente";
                    payload.estado = nuevoEstado;
                    reportes = reportes.map(r => r.id === id ? { ...r, estado: nuevoEstado } : r);
                } else return;
            } else if (accion === "eliminar-reporte") {
                reportes = reportes.filter(r => r.id !== id);
            }
            renderReportes();
            renderEstadisticas();

            fetch('adminAcciones.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    reportes = oldReportes;
                    renderReportes();
                    renderEstadisticas();
                    showToast("Error: " + data.message, true);
                } else {
                    showToast("Actualizado");
                }
            })
            .catch(err => {
                reportes = oldReportes;
                renderReportes();
                renderEstadisticas();
                showToast("Error de red", true);
            });
        });
    }

    // ==================== EVENTOS DE MENSAJES (INSTANTÁNEO) ====================
    if (tablaMensajes) {
        tablaMensajes.addEventListener("click", function (e) {
            const boton = e.target.closest("button");
            if (!boton) return;
            const id = Number(boton.dataset.id);
            const accion = boton.dataset.accion;

            if (accion === "eliminar-mensaje" && !confirm("¿Eliminar este mensaje permanentemente?")) return;

            // Guardar estado anterior para posible reversión
            let oldMensajes = [...mensajes];

            // ACTUALIZACIÓN OPTIMISTA INMEDIATA
            if (accion === "marcar-leido-mensaje") {
                // Convertimos m.id a número para asegurar que la comparación coincida con 'id'
                mensajes = mensajes.map(m => Number(m.id) === id ? { ...m, leido: true } : m);
                console.log("Mensaje marcado como leído (optimista)");
            } else if (accion === "eliminar-mensaje") {
                // Convertimos m.id a número aquí también
                mensajes = mensajes.filter(m => Number(m.id) !== id);
                console.log("Mensaje eliminado (optimista)");
            }

            // Renderizar la tabla y estadísticas al instante
            renderMensajes();
            renderEstadisticas();

            // Llamada al servidor (si falla, revertir)
            fetch('adminAcciones.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accion: accion, id: id })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    mensajes = oldMensajes;
                    renderMensajes();
                    renderEstadisticas();
                    showToast("Error: " + data.message, true);
                } else {
                    showToast(accion === "marcar-leido-mensaje" ? "Marcado como leído" : "Mensaje eliminado");
                }
            })
            .catch(err => {
                mensajes = oldMensajes;
                renderMensajes();
                renderEstadisticas();
                showToast("Error de conexión", true);
            });
        });
    }

    // ==================== MODAL Y MAPA ====================
    if (btnVerMapa) {
        btnVerMapa.addEventListener("click", function () {
            if (!ubicacionActualReporte) return;
            const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(ubicacionActualReporte)}`;
            window.open(url, "_blank");
        });
    }

    if (cerrarModalDetalle) {
        cerrarModalDetalle.addEventListener("click", () => { modalDetalle.style.display = "none"; });
    }
    window.addEventListener("click", (e) => {
        if (e.target === modalDetalle) modalDetalle.style.display = "none";
    });

    // ==================== INICIALIZACIÓN ====================
    console.log("Inicializando panel con", mensajes.length, "mensajes");
    renderUsuarios();
    renderReportes();
    renderMensajes();
    renderEstadisticas();
});