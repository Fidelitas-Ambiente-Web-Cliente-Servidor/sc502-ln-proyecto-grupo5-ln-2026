document.addEventListener('DOMContentLoaded', function() {
  // Esperar un momento para asegurar que el DOM esté completamente renderizado
  setTimeout(function() {
    const userMenu = document.querySelector('.user-menu');
    if (!userMenu) {
      console.log('No se encontró .user-menu (usuario no logueado)');
      return;
    }

    const trigger = userMenu.querySelector('.user-trigger');
    if (!trigger) {
      console.log('No se encontró .user-trigger');
      return;
    }

    // Función para abrir/cerrar
    function toggleMenu(event) {
      event.stopPropagation();
      userMenu.classList.toggle('open');
      console.log('Menú toggled, open:', userMenu.classList.contains('open'));
    }

    trigger.addEventListener('click', toggleMenu);

    // Cerrar al hacer clic fuera
    document.addEventListener('click', function(event) {
      if (!userMenu.contains(event.target)) {
        userMenu.classList.remove('open');
      }
    });

    // Cerrar con ESC
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape' && userMenu.classList.contains('open')) {
        userMenu.classList.remove('open');
      }
    });

    // Opcional: cerrar si se redimensiona la ventana (por si el menú queda mal posicionado)
    window.addEventListener('resize', function() {
      userMenu.classList.remove('open');
    });
  }, 100); // pequeño retraso por si hay algún renderizado asíncrono
});