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
});