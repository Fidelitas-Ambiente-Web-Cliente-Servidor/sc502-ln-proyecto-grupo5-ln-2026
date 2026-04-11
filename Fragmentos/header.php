<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<header class="site-header">
    <h1>EcoAlerta CR</h1>
    <nav>
        <a href="/sc502-ln-proyecto-grupo5-ln-2026/Index.php">Inicio</a>
        <a href="/sc502-ln-proyecto-grupo5-ln-2026/Reportes/vistaReportes.php">Reportes</a>
        <a href="/sc502-ln-proyecto-grupo5-ln-2026/Informacion/vistaInformacion.php">Información</a>
        
        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
            <a href="/sc502-ln-proyecto-grupo5-ln-2026/Institucional/bandejaReportes.html">Bandeja Reportes</a>
            <a href="/sc502-ln-proyecto-grupo5-ln-2026/PanelAdmin/adminPanel.php">Panel Administración</a>
        <?php endif; ?>
        
        <a href="/sc502-ln-proyecto-grupo5-ln-2026/Contacto/vistaContacto.php">Contacto</a>
    </nav>
    <div>
        <form id="formularioBusqueda" action="?" method="get">
            <div class="areaBusqueda">
                <input type="text" name="busqueda" class="buscar" id="B1" placeholder="Búsqueda">
                <input type="submit" value="Buscar" class="busquedaBTN">
            </div>
        </form>
    </div>
    <div class="auth-section">
        <?php if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])): ?>
            <div class="user-menu">
                <div class="user-trigger">
                    <div class="user-avatar">
                        <img src="/sc502-ln-proyecto-grupo5-ln-2026/img/image.png" alt="Avatar" class="avatar-img">
                    </div>
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <span class="dropdown-arrow">▼</span>
                </div>
                <ul class="dropdown-menu">
                    <li><a href="/sc502-ln-proyecto-grupo5-ln-2026/Auth/perfiles.php">Mi perfil</a></li>
                    <li><a href="/sc502-ln-proyecto-grupo5-ln-2026/Auth/logout.php">Cerrar sesión</a></li>
                </ul>
            </div>
        <?php else: ?>
            <div class="auth-links logged-out">
                <a href="/sc502-ln-proyecto-grupo5-ln-2026/Auth/InicioS.php" class="inicio">Iniciar sesión</a>
                <a href="/sc502-ln-proyecto-grupo5-ln-2026/Auth/Registro.php" class="registro">Registrarse</a>
            </div>
        <?php endif; ?>
    </div>
</header>