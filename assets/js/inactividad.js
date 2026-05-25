/**
 * Control de Inactividad Global - SARCE
 */
function iniciarTemporizadorInactividad(baseUrl) {
    let tiempo;

    const resetearTemporizador = () => {
        clearTimeout(tiempo);
        tiempo = setTimeout(() => {
            Swal.fire({
                icon: 'warning',
                title: 'Sesión Expirada',
                text: 'Cierre por inactividad detectado.',
                confirmButtonColor: '#28a745',
                allowOutsideClick: false,
                confirmButtonText: 'Aceptar'
            }).then(() => {
                window.location.href = baseUrl + "login.php?timeout=1";
            });
        }, 600000); // 10 minutos (600,000 ms)
    };

    // Reiniciar el conteo con cualquier interacción del usuario
    window.addEventListener('load', resetearTemporizador);
    document.addEventListener('mousemove', resetearTemporizador);
    document.addEventListener('keypress', resetearTemporizador);
    document.addEventListener('click', resetearTemporizador);
}