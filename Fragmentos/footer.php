<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-brand">
            <h5>EcoAlerta CR</h5>
            <p>Reportes ambientales para una Costa Rica más limpia y segura.</p>
        </div>

        <nav class="footer-links" aria-label="Enlaces del sitio">
            <a href="/sc502-ln-proyecto-grupo5-ln-2026/Index.php">Inicio</a>
            <a href="/sc502-ln-proyecto-grupo5-ln-2026/Reportes/vistaReportes.php">Reportes</a>
            <a href="/sc502-ln-proyecto-grupo5-ln-2026/Institucional/bandejaReportes.html">Bandeja Reportes</a>
            <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                <a href="/sc502-ln-proyecto-grupo5-ln-2026/PanelAdmin/adminPanel.php">Panel Administracion</a>
            <?php endif; ?>
            <a href="/sc502-ln-proyecto-grupo5-ln-2026/PanelAdmin/adminPanel.html">Panel Admin</a>
            <a href="/sc502-ln-proyecto-grupo5-ln-2026/Informacion/vistaInformacion.php">Información</a>
            <a href="/sc502-ln-proyecto-grupo5-ln-2026/Contacto/vistaContacto.php">Contacto</a>
        </nav>

        <div class="footer-social">
            <p class="footer-small">Síguenos</p>
            <div class="social-row">
                <a href="#" aria-label="Facebook">Facebook</a>
                <a href="#" aria-label="Instagram">Instagram</a>
                <a href="#" aria-label="X">X</a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        2026 <span>© <span id="year"></span> EcoAlerta CR. Todos los derechos reservados.</span>
    </div>
</footer>
<script>
    document.getElementById('year').textContent = new Date().getFullYear();
</script>