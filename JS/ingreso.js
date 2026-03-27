document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        if (!email.value.trim() || !password.value.trim()) {
            alert('Por favor complete todos los campos');
            e.preventDefault();
        }
    });
});