/**
 * SARCE - Scripts Globales Unificados
 * Centraliza las funciones de Pacientes, Personal, Consultas y Sistema.
 */

// --- VALIDACIONES Y UTILIDADES ---

function toggleMenu() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('active');
}

function soloLetras(e) {
    let key = e.keyCode || e.which;
    let tecla = String.fromCharCode(key).toLowerCase();
    let letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
    if (letras.indexOf(tecla) == -1) return false;
}

function soloNumeros(e) {
    let key = e.keyCode || e.which;
    if (key < 48 || key > 57) return false;
}

// --- MÓDULO PACIENTES Y PERSONAL ---
function calcularEdad() {
    const fechaInput = document.getElementById("fecha_nacimiento");
    const fecha = fechaInput.value;
    if (!fecha) return;

    const partes = fecha.split("-");
    const anioStr = partes[0];
    const anioNum = parseInt(anioStr);

    // Si el año es menor a 1000 (ej: 0001 al teclear), ignoramos para dejar que el usuario termine de escribir.
    if (anioStr.length < 4 || anioNum < 1000) return;

    const hoy = new Date();
    const cumple = new Date(fecha);

    // Validación de fecha futura
    if (cumple > hoy) {
        Swal.fire({
            icon: 'error',
            title: 'Fecha inválida',
            text: 'no se puede agregar fechas futuras',
            confirmButtonColor: '#dc3545'
        });
        fechaInput.value = "";
        document.getElementById("edad").value = "";
        return;
    }

    let edad = hoy.getFullYear() - cumple.getFullYear();
    const m = hoy.getMonth() - cumple.getMonth();
    if (m < 0 || (m === 0 && hoy.getDate() < cumple.getDate())) edad--;

    // Validación mayor de 100 años
    if (edad > 100) {
        Swal.fire({
            icon: 'error',
            title: 'Edad no permitida',
            text: 'no se permite un numero mayor a este',
            confirmButtonColor: '#dc3545'
        });
        fechaInput.value = "";
        document.getElementById("edad").value = "";
        return;
    }

    document.getElementById("edad").value = edad;
}

// --- MÓDULO CONSULTAS ---
function toggleMedicamentos() {
    const check = document.getElementById('entrega_check');
    const seccion = document.getElementById('seccion_medicamentos');
    seccion.style.display = check.checked ? 'block' : 'none';
}

let medicamentosSeleccionados = [];

/**
 * Agrega un medicamento a la lista de chips (Módulo Consultas/Atender)
 */
function agregarMedicamento() {
    const input = document.getElementById('buscador_med');
    if (!input) return;
    
    const valor = input.value.trim();
    if (valor === "") return;
    
    if (medicamentosSeleccionados.includes(valor)) {
        input.value = "";
        return;
    }

    medicamentosSeleccionados.push(valor);
    renderizarChips();
    input.value = "";
}

function eliminarMedicamento(index) {
    medicamentosSeleccionados.splice(index, 1);
    renderizarChips();
}

function renderizarChips() {
    const contenedor = document.getElementById('lista_chips');
    const inputFinal = document.getElementById('meds_final');
    if (!contenedor || !inputFinal) return;

    contenedor.innerHTML = "";
    medicamentosSeleccionados.forEach((med, index) => {
        const chip = document.createElement('div');
        chip.className = 'chip-medicamento';
        chip.innerHTML = `${med} <i class="fas fa-times" style="cursor:pointer; margin-left:5px;" onclick="eliminarMedicamento(${index})"></i>`;
        contenedor.appendChild(chip);
    });
    inputFinal.value = medicamentosSeleccionados.join(', ');
}

function verificarNuevaPatologia() {
    const select = document.getElementById('select_patologia');
    const box = document.getElementById('box_nueva_patologia');
    box.style.display = (select.value === 'nueva') ? 'block' : 'none';
}

/**
 * Validación de cédula para Pacientes en tiempo real (AJAX)
 */
function verificarCedulaPacienteRealTime(cedula) {
    // Limpiar caracteres no numéricos (por si se pega texto)
    const cleaned = cedula.replace(/[^0-9]/g, '');
    if (cleaned !== cedula) {
        document.getElementById("cedula_input").value = cleaned;
        cedula = cleaned;
    }

    if (cedula.length < 8) return;
    
    fetch(`registrar_paciente.php?verificar_ajax=1&cedula=${cedula}`)
        .then(response => response.json())
        .then(data => {
            if (data.existe) {
                Swal.fire({
                    icon: data.estado == 1 ? 'warning' : 'info',
                    title: data.estado == 1 ? 'Paciente ya registrado' : 'Paciente Inactivo',
                    text: data.estado == 1 
                        ? `La cédula ${cedula} ya se encuentra activa en el sistema.` 
                        : `La cédula ${cedula} está inhabilitada. ¿Desea reactivarla para este registro?`,
                    showCancelButton: data.estado == 0,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: data.estado == 0 ? 'Sí, reactivar' : 'Entendido',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed && data.estado == 0) {
                        window.location.href = `registrar_paciente.php?habilitar=1&cedula=${cedula}`;
                    }
                });
            }
        });
}

/**
 * Validación de cédula para Personal Médico (AJAX)
 */
function verificarCedulaPersonalRealTime(cedula) {
    // Limpiar caracteres no numéricos
    const cleaned = cedula.replace(/[^0-9]/g, '');
    if (cleaned !== cedula) {
        document.getElementById("cedula_input").value = cleaned;
        cedula = cleaned;
    }

    if (cedula.length < 8) return;
    
    fetch(`registrar_personal.php?verificar_ajax=1&cedula=${cedula}`)
        .then(response => response.json())
        .then(data => {
            if (data.existe) {
                Swal.fire({
                    icon: data.estado == 1 ? 'warning' : 'info',
                    title: data.estado == 1 ? 'Personal ya registrado' : 'Personal Inactivo',
                    text: data.estado == 1 
                        ? `La cédula ${cedula} ya se encuentra activa en el sistema.` 
                        : `La cédula ${cedula} está inhabilitada. ¿Desea reactivarla para este registro?`,
                    showCancelButton: data.estado == 0,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: data.estado == 0 ? 'Sí, reactivar' : 'Entendido',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed && data.estado == 0) {
                        window.location.href = `registrar_personal.php?habilitar=1&cedula=${cedula}`;
                    }
                });
            }
        });
}

/**
 * Renderiza el gráfico de torta en el tablero epidemiológico
 */
function renderGraficoMorbilidad(labels, data) {
    const ctx = document.getElementById('graficoMorbilidad');
    if (!ctx) return;

    if (typeof Chart === 'undefined') return;

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ['#28a745', '#002347', '#007bff', '#dc3545', '#f6ad55'],
                borderWidth: 1
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false 
        }
    });
}

/**
 * Confirmación universal para inhabilitar (Pacientes o Personal)
 * @param {string} id - Cédula o ID del registro
 * @param {string} tipo - 'paciente', 'personal' o 'medicamento'
 */
function confirmarInhabilitacion(id, tipo = 'paciente') {
    const config = {
        paciente: {
            titulo: '¿Inhabilitar Paciente?',
            url: `inhabilitar.php?cedula=${id}`
        },
        personal: {
            titulo: '¿Inhabilitar Personal?',
            url: `inhabilitar_personal.php?cedula=${id}`
        },
        medicamento: {
            titulo: '¿Inhabilitar Medicamento?',
            url: `inhabilitar_medicamento.php?id=${id}`
        }
    };

    const settings = config[tipo] || config.paciente;

    Swal.fire({
        title: settings.titulo,
        text: "Esta acción cambiará el estado a inactivo en el sistema.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = settings.url;
        }
    });
}

/**
 * Control del submenú en el Sidebar
 */
function toggleSubmenu() {
    const submenu = document.getElementById("submenu-gestion");
    const arrow = document.querySelector(".arrow-toggle");
    if (submenu) {
        const isVisible = submenu.style.display === "block";
        submenu.style.display = isVisible ? "none" : "block";
        if (arrow) arrow.style.transform = isVisible ? "rotate(0deg)" : "rotate(180deg)";
    }
}

/**
 * Lógica de búsqueda en tiempo real para tablas (Usuarios, Personal, etc.)
 */
document.addEventListener('DOMContentLoaded', () => {
    const buscador = document.getElementById('buscador');
    if (buscador) {
        buscador.addEventListener('input', function() {
            const filtro = this.value.toLowerCase();
            const filas = document.querySelectorAll('#cuerpoTabla tr');
            
            filas.forEach(fila => {
                // Obtener todo el texto de la fila para comparar
                const texto = fila.textContent.toLowerCase();
                // Si coincide, se muestra; si no, se oculta
                fila.style.display = texto.includes(filtro) ? '' : 'none';
            });
        });
    }
});

/**
 * Confirmación de reinicio de contraseña con SweetAlert2
 */
function confirmarReset(id) {
    Swal.fire({
        title: '¿Reiniciar contraseña?',
        text: 'La clave se restablecerá a la genérica: sarce1234',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Sí, reiniciar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'usuarios.php?id=' + id + '&reset=1';
        }
    });
}

console.log("SARCE: Scripts globales cargados correctamente.");