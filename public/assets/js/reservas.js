/**
 * JavaScript para la gestión de creación de Reservas
 * Archivo: reservas.js
 */

class ReservasFormManager {
    constructor() {
        this.init();
    }

    init() {
        this.cargarEventListeners();
        this.configurarValidaciones();
        this.aplicarEfectosVisuales();
    }

    cargarEventListeners() {
        // Eventos para cálculo de precio
        const habitacionSelect = document.getElementById('id_habitacion');
        const fechaInicio = document.getElementById('fechainicio');
        const fechaFin = document.getElementById('fechaFin');

        if (habitacionSelect) {
            habitacionSelect.addEventListener('change', () => {
                this.calcularPrecioSugerido();
                this.verificarDisponibilidad(); // <-- Añadido
            });
        }

        if (fechaInicio) {
            fechaInicio.addEventListener('change', (e) => {
                this.validarFecha(e.target, true);
                this.calcularPrecioSugerido();
                this.verificarDisponibilidad(); // <-- Añadido
                this.actualizarFechaMinima();
            });
        }

        if (fechaFin) {
            fechaFin.addEventListener('change', (e) => {
                this.validarFecha(e.target, false);
                this.calcularPrecioSugerido();
                this.verificarDisponibilidad(); // <-- Añadido
            });
        }

        // Evento del formulario
        const form = document.getElementById('form-reserva');
        if (form) {
            form.addEventListener('submit', (e) => this.validarFormulario(e));
        }

        // Eventos para campos numéricos
        this.configurarCamposNumericos();
    }

    configurarCamposNumericos() {
        const numericos = document.querySelectorAll('input[type="number"]');
        numericos.forEach(input => {
            input.addEventListener('focus', function() {
                this.style.backgroundColor = 'rgba(13, 110, 253, 0.05)';
            });
            
            input.addEventListener('blur', function() {
                this.style.backgroundColor = '';
            });

            // Validar valores mínimos
            input.addEventListener('input', function() {
                const min = parseInt(this.getAttribute('min')) || 0;
                const max = parseInt(this.getAttribute('max')) || 999;
                const value = parseInt(this.value);

                if (value < min) this.value = min;
                if (value > max) this.value = max;
            });
        });
    }

    calcularPrecioSugerido() {
        const habitacionSelect = document.getElementById('id_habitacion');
        const fechaInicio = document.getElementById('fechainicio');
        const fechaFin = document.getElementById('fechaFin');
        const precioSugeridoDiv = document.getElementById('precio-sugerido');
        const pagoFinalInput = document.getElementById('pagoFinal');
        
        if (!habitacionSelect || !fechaInicio || !fechaFin || !precioSugeridoDiv) return;

        const habitacionSeleccionada = habitacionSelect.selectedOptions[0];
        const fechaInicioVal = fechaInicio.value;
        const fechaFinVal = fechaFin.value;
        
        if (habitacionSeleccionada && habitacionSeleccionada.value && fechaInicioVal && fechaFinVal) {
            const precioNoche = parseFloat(habitacionSeleccionada.dataset.precio || 0);
            const inicio = new Date(fechaInicioVal);
            const fin = new Date(fechaFinVal);
            const noches = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24));
            
            if (noches > 0 && precioNoche > 0) {
                const precioTotal = precioNoche * noches;
                precioSugeridoDiv.innerHTML = `
                    <i class="fas fa-calculator"></i> 
                    Precio sugerido: <strong>$${this.formatearNumero(precioTotal)}</strong> 
                    (${noches} ${noches === 1 ? 'noche' : 'noches'} × $${this.formatearNumero(precioNoche)})
                `;
                precioSugeridoDiv.style.color = '#28a745';
                
                // Auto-llenar el campo de pago si está vacío
                if (pagoFinalInput && !pagoFinalInput.value) {
                    pagoFinalInput.value = precioTotal.toFixed(2);
                    pagoFinalInput.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
                    setTimeout(() => {
                        pagoFinalInput.style.backgroundColor = '';
                    }, 2000);
                }
            } else if (noches <= 0) {
                precioSugeridoDiv.innerHTML = '<i class="fas fa-exclamation-triangle text-warning"></i> Las fechas no son válidas';
                precioSugeridoDiv.style.color = '#dc3545';
            } else {
                precioSugeridoDiv.innerHTML = '<i class="fas fa-info-circle"></i> Precio no disponible para esta habitación';
                precioSugeridoDiv.style.color = '#6c757d';
            }
        } else {
            precioSugeridoDiv.innerHTML = '';
        }
    }

    actualizarFechaMinima() {
        const fechaInicio = document.getElementById('fechainicio');
        const fechaFin = document.getElementById('fechaFin');
        
        if (fechaInicio && fechaFin && fechaInicio.value) {
            const fechaMinima = new Date(fechaInicio.value);
            fechaMinima.setDate(fechaMinima.getDate() + 1);
            fechaFin.min = fechaMinima.toISOString().split('T')[0];
            
            // Si la fecha fin es anterior a la nueva fecha mínima, limpiarla
            if (fechaFin.value && new Date(fechaFin.value) <= new Date(fechaInicio.value)) {
                fechaFin.value = '';
            }
        }
    }

    configurarValidaciones() {
        // Configurar fecha mínima para fecha de inicio
        const fechaInicio = document.getElementById('fechainicio');
        if (fechaInicio) {
            const hoy = new Date();
            fechaInicio.min = hoy.toISOString().split('T')[0];
        }
    }

    validarFecha(elemento, esFechaInicio = true) {
        const fecha = new Date(elemento.value);
        const fechaComparacion = esFechaInicio ? 
            document.getElementById('fechaFin') : 
            document.getElementById('fechainicio');
        
        // Limpiar errores previos
        this.limpiarError(elemento);
        
        // Validar que la fecha de inicio no sea anterior a hoy
        if (esFechaInicio && fecha < new Date()) {
            this.mostrarError(elemento, 'La fecha de inicio no puede ser anterior a hoy');
            return false;
        }
        
        // Validar relación entre fechas
        if (fechaComparacion && fechaComparacion.value) {
            const fechaComp = new Date(fechaComparacion.value);
            
            if (esFechaInicio && fecha >= fechaComp) {
                this.mostrarError(elemento, 'La fecha de inicio debe ser anterior a la fecha de fin');
                return false;
            } else if (!esFechaInicio && fecha <= fechaComp) {
                this.mostrarError(elemento, 'La fecha de fin debe ser posterior a la fecha de inicio');
                return false;
            }
        }
        
        // Si llegamos aquí, la fecha es válida
        this.mostrarExito(elemento);
        return true;
    }

    mostrarError(elemento, mensaje) {
        elemento.style.borderColor = '#dc3545';
        elemento.style.backgroundColor = 'rgba(220, 53, 69, 0.05)';
        
        // Remover mensaje anterior si existe
        const errorAnterior = elemento.parentNode.querySelector('.error-message');
        if (errorAnterior) {
            errorAnterior.remove();
        }
        
        // Crear nuevo mensaje de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.cssText = `
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        `;
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${mensaje}`;
        elemento.parentNode.appendChild(errorDiv);
    }

    mostrarExito(elemento) {
        elemento.style.borderColor = '#198754';
        elemento.style.backgroundColor = 'rgba(25, 135, 84, 0.05)';
        
        const errorMsg = elemento.parentNode.querySelector('.error-message');
        if (errorMsg) {
            errorMsg.remove();
        }
    }
    
    limpiarError(elemento) {
        elemento.style.borderColor = '';
        elemento.style.backgroundColor = '';
        
        const errorMsg = elemento.parentNode.querySelector('.error-message');
        if (errorMsg) {
            errorMsg.remove();
        }
    }

    async validarFormulario(e) { // <-- Convertido a async
        const fechaInicio = document.getElementById('fechainicio');
        const fechaFin = document.getElementById('fechaFin');
        const btnCrear = document.getElementById('btn-crear');
        
        let formularioValido = true;

        // Prevenir envío para esperar la validación asíncrona
        e.preventDefault();
        // Mostrar estado de carga en el botón
        btnCrear.disabled = true;
        btnCrear.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validando...';

        // Validar fechas
        if (fechaInicio && !this.validarFecha(fechaInicio, true)) {
            formularioValido = false;
        }
        
        if (fechaFin && !this.validarFecha(fechaFin, false)) {
            formularioValido = false;
        }

        // Validar campos requeridos
        const camposRequeridos = [
            'pagoFinal', 'fechainicio', 'fechaFin', 'motivoReserva', 
            'id_habitacion', 'metodoPago', 'us_numDocumento', 
            'hue_numDocumento', 'estado'
        ];

        camposRequeridos.forEach(campo => {
            const elemento = document.getElementById(campo);
            if (elemento && !elemento.value.trim()) {
                this.mostrarError(elemento, 'Este campo es obligatorio');
                formularioValido = false;
            }
        });

        // Validar disponibilidad de la habitación de forma asíncrona
        const disponible = await this.verificarDisponibilidad();
        if (!disponible) {
            formularioValido = false;
        }

        if (!formularioValido) {
            // e.preventDefault(); // Ya se previno al inicio

            // Restaurar botón si había sido deshabilitado
            if (btnCrear) {
                btnCrear.disabled = false;
                btnCrear.innerHTML = '<i class="fas fa-save"></i> Crear Reserva';
            }
            
            // Scroll hacia el primer error
            const primerError = document.querySelector('.error-message');
            if (primerError) {
                primerError.parentNode.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
        } else {
            // Si todo es válido, ahora sí se envía el formulario
            btnCrear.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            document.getElementById('form-reserva').submit();
        }

        return formularioValido;
    }

    aplicarEfectosVisuales() {
        // Efecto de carga progresiva para los form groups
        const formGroups = document.querySelectorAll('.form-group');
        
        formGroups.forEach((group, index) => {
            group.style.opacity = '0';
            group.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                group.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                group.style.opacity = '1';
                group.style.transform = 'translateY(0)';
            }, index * 50);
        });

        // Efecto hover para los selects
        const selects = document.querySelectorAll('select.form-control');
        selects.forEach(select => {
            select.addEventListener('focus', function() {
                this.style.backgroundColor = 'rgba(13, 110, 253, 0.05)';
            });
            
            select.addEventListener('blur', function() {
                this.style.backgroundColor = '';
            });
        });
    }

    formatearNumero(numero) {
        return new Intl.NumberFormat('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        }).format(numero);
    }

    // Método para verificar disponibilidad de habitación (opcional)
    async verificarDisponibilidad() {
        const habitacion = document.getElementById('id_habitacion').value;
        const fechaInicio = document.getElementById('fechainicio').value;
        const fechaFin = document.getElementById('fechaFin').value;

        if (!habitacion || !fechaInicio || !fechaFin) return;
        
        // Limpiar error previo de disponibilidad
        const habitacionSelect = document.getElementById('id_habitacion');
        this.limpiarError(habitacionSelect);

        try {
            // NOTA: Asegúrate de que este controlador exista y funcione.
            // Por ahora, el código está listo para usarlo.
            const response = await fetch('/lodgehub/api.php?resource=reservas', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'verificarDisponibilidad', // Acción para el controlador
                    id_habitacion: habitacion,
                    fechainicio: fechaInicio,
                    fechaFin: fechaFin
                })
            });

            const data = await response.json();
            
            if (!data.disponible) {
                const habitacionSelect = document.getElementById('id_habitacion');
                this.mostrarError(habitacionSelect, data.message || 'Habitación no disponible para las fechas seleccionadas');
                return false;
            }

            return true;
        } catch (error) {
            console.warn('Error al verificar disponibilidad:', error);
            return true; // Asumir disponible si hay error de red para no bloquear al usuario. El backend hará la validación final.
        }
    }

    // Método para limpiar el formulario
    limpiarFormulario() {
        const form = document.getElementById('form-reserva');
        if (form) {
            form.reset();
            
            // Limpiar errores visuales
            document.querySelectorAll('.error-message').forEach(msg => msg.remove());
            document.querySelectorAll('.form-control').forEach(input => {
                input.style.borderColor = '';
                input.style.backgroundColor = '';
            });
            
            // Limpiar precio sugerido
            const precioSugerido = document.getElementById('precio-sugerido');
            if (precioSugerido) {
                precioSugerido.innerHTML = '';
            }
        }
    }

    // Método para autocompletar datos de prueba (solo para desarrollo)
    autocompletarDatosPrueba() {
        if (window.location.hostname !== 'localhost') return; // Solo en desarrollo
        
        const datos = {
            cantidadAdultos: 2,
            cantidadNinos: 1,
            cantidadDiscapacitados: 0,
            motivoReserva: 'Personal',
            metodoPago: 'Tarjeta',
            estado: 'Activa',
            pagoFinal: 150000,
            informacionAdicional: 'Reserva de prueba'
        };

        Object.entries(datos).forEach(([campo, valor]) => {
            const elemento = document.getElementById(campo);
            if (elemento && !elemento.value) {
                elemento.value = valor;
            }
        });
    }
}

// Utilidad para formatear números en campos de entrada
function formatearCampoNumerico(input) {
    const valor = parseFloat(input.value);
    if (!isNaN(valor)) {
        input.value = valor.toLocaleString('es-CO');
    }
}

// Función global para limpiar formato de números antes de enviar
function limpiarFormatoNumerico(input) {
    input.value = input.value.replace(/[^\d.,]/g, '').replace(',', '.');
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    try {
        const reservasManager = new ReservasFormManager();
        
        // Hacer el manager disponible globalmente para debugging
        if (window.location.hostname === 'localhost') {
            window.reservasManager = reservasManager;
        }
    } catch (error) {
        console.error('Error al inicializar ReservasFormManager:', error);
    }
});