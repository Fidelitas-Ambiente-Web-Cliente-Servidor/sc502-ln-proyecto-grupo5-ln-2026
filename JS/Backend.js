document.addEventListener('DOMContentLoaded', function() {
  fetch("/sc502-ln-proyecto-grupo5-ln-2026/Fragmentos/header.html")
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


document.addEventListener("DOMContentLoaded", () => { //footer section
  const year = document.getElementById("year");
  if (year) year.textContent = new Date().getFullYear();

  // Efecto: resaltar fila al pasar el mouse (en panel admin)
  document.querySelectorAll("table.table tbody tr").forEach(tr => {
    tr.addEventListener("mouseenter", () => tr.style.background = "#f9fafb");
    tr.addEventListener("mouseleave", () => tr.style.background = "");
  });
});

});