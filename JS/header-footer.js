document.addEventListener('DOMContentLoaded', function() {
  fetch("/sc502-ln-proyecto-grupo5-ln-2026/Fragmentos/header1.html")
    .then(response => {
      if (!response.ok) throw new Error('Error al cargar el header');
      return response.text();
    })
    .then(data => {
      document.getElementById('header-container').innerHTML = data;
    })
    .catch(error => {
      console.error('No se pudo cargar el header:', error);
    });

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
