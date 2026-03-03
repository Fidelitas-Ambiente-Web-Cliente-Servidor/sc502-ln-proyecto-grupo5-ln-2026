document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    // Simulación: si email y password no están vacíos, "iniciar sesión"
    if (email && password) {
        localStorage.setItem('isLoggedIn', 'true');
        localStorage.setItem('userName', email.split('@')[0]); // Usar parte del email como nombre
        window.location.href = '/sc502-ln-proyecto-grupo5-ln-2026/Index.html';
    } else {
        alert('Credenciales inválidas');
    }
    });