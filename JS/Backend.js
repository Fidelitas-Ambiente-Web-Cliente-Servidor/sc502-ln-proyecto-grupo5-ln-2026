document.addEventListener('DOMContentLoaded', function() {
  // Cargar header
  fetch("/sc502-ln-proyecto-grupo5-ln-2026/Fragmentos/header.html")
    .then(response => {
      if (!response.ok) throw new Error('Error al cargar el header');
      return response.text();
    })
    .then(data => {
      document.getElementById('header-container').innerHTML = data;
      configurarSesion();
    })
    .catch(error => {
      console.error('No se pudo cargar el header:', error);
    });

  // Cargar footer
  fetch("/sc502-ln-proyecto-grupo5-ln-2026/Fragmentos/footer.html")
    .then(r => {
      if (!r.ok) throw new Error("Error al cargar el footer");
      return r.text();
    })
    .then(html => {
      const cont = document.getElementById("footer-container");
      if (!cont) {
        console.error("No existe #footer-container en esta página.");
        return;
      }
      cont.innerHTML = html;
      const year = cont.querySelector("#year");
      if (year) year.textContent = new Date().getFullYear();
    })
    .catch(err => console.error("No se pudo cargar el footer:", err));
});

function configurarSesion() {
  const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
  const userName = localStorage.getItem('userName') || 'Usuario';

  const loggedOutDiv = document.querySelector('.auth-links.logged-out');
  const loggedInDiv = document.querySelector('.auth-links.logged-in');
  const userLink = document.querySelector('.user-name .usuario');

  if (!loggedOutDiv || !loggedInDiv) return;

  if (isLoggedIn) {
    loggedOutDiv.style.display = 'none';
    loggedInDiv.style.display = 'flex';
    if (userLink) userLink.textContent = userName;

    // Configurar botón de cerrar sesión
    const logoutBtn = document.querySelector('#logout-link');
    if (logoutBtn) {
      logoutBtn.addEventListener('click', function(e) {
        e.preventDefault();
        localStorage.removeItem('isLoggedIn');
        localStorage.removeItem('userName');
        window.location.href = '/sc502-ln-proyecto-grupo5-ln-2026/Index.html';
      });
    }
  } else {
    loggedOutDiv.style.display = 'flex';
    loggedInDiv.style.display = 'none';
  }
}