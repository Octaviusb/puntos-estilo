// JS para login con OTP

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('login-form');
    const otpInput = document.getElementById('otp');
    const loginBtn = document.getElementById('login-btn');
    const otpBtn = document.getElementById('otp-btn');
    const errorDiv = document.getElementById('error-message');

    // Paso 1: Solicitar OTP
    loginBtn.addEventListener('click', function(e) {
        e.preventDefault();
        errorDiv.textContent = '';
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        if (!email || !password) {
            errorDiv.textContent = 'Por favor, completa todos los campos.';
            return;
        }
        // Solicitar OTP
        fetch('../../query/generar_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_mail: email })
        })
        .then(res => res.text())
        .then(res => {
            if (res.startsWith('ok|')) {
                const otp = res.split('|')[1];
                alert('Tu código OTP es: ' + otp + '\n(En producción se enviaría por correo)');
                loginBtn.style.display = 'none';
                otpBtn.style.display = 'block';
                otpInput.focus(); // Enfocar el campo OTP
            } else {
                errorDiv.textContent = res;
            }
        })
        .catch(() => {
            errorDiv.textContent = 'Error de conexión al solicitar OTP.';
        });
    });

    // Paso 2: Validar OTP y login
    otpBtn.addEventListener('click', function(e) {
        e.preventDefault();
        errorDiv.textContent = '';
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const otp = otpInput.value.trim();
        if (!otp) {
            errorDiv.textContent = 'Por favor, ingresa el código OTP.';
            return;
        }
        fetch('../../query/valida.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_mail: email, pass: password, otp: otp })
        })
        .then(res => res.text())
        .then(res => {
            if (res === 'ok') {
                window.location.href = 'dashboard.php';
            } else {
                errorDiv.textContent = res;
            }
        })
        .catch(() => {
            errorDiv.textContent = 'Error de conexión al validar OTP.';
        });
    });
}); 