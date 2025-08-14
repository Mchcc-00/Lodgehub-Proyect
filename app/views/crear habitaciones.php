<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validación de Habitaciones - LodgeHub</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: white;
            font-size: 2.5rem;
            font-weight: 300;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .header p {
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
        }

        .form-container {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            color: white;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .required {
            color: #ff6b6b;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 12px;
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.6);
        }

        .form-control:focus {
            outline: none;
            border-color: #4CAF50;
            background: rgba(255,255,255,0.2);
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
        }

        .form-control.error {
            border-color: #ff6b6b;
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.2);
        }

        .form-control.valid {
            border-color: #4CAF50;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .error-message {
            color: #ff6b6b;
            font-size: 0.85rem;
            margin-top: 5px;
            display: none;
            background: rgba(255, 107, 107, 0.1);
            padding: 8px 12px;
            border-radius: 8px;
            border-left: 3px solid #ff6b6b;
        }

        .success-message {
            color: #4CAF50;
            font-size: 0.85rem;
            margin-top: 5px;
            display: none;
            background: rgba(76, 175, 80, 0.1);
            padding: 8px 12px;
            border-radius: 8px;
            border-left: 3px solid #4CAF50;
        }

        .validation-icon {
            position: absolute;
            right: 15px;
            top: 38px;
            font-size: 1.2rem;
            display: none;
        }

        .validation-icon.valid {
            color: #4CAF50;
        }

        .validation-icon.error {
            color: #ff6b6b;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 140px;
        }

        .btn-primary {
            background: #4CAF50;
            color: white;
        }

        .btn-secondary {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .validation-summary {
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid #ff6b6b;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            color: white;
            display: none;
        }

        .validation-summary h3 {
            color: #ff6b6b;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .validation-summary ul {
            list-style: none;
            padding-left: 0;
        }

        .validation-summary li {
            margin-bottom: 5px;
            padding-left: 20px;
            position: relative;
        }

        .validation-summary li::before {
            content: "•";
            color: #ff6b6b;
            position: absolute;
            left: 0;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }

        .file-input-label {
            display: block;
            padding: 12px 16px;
            border: 2px dashed rgba(255,255,255,0.3);
            border-radius: 12px;
            background: rgba(255,255,255,0.05);
            color: rgba(255,255,255,0.8);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-input-label:hover {
            border-color: rgba(255,255,255,0.5);
            background: rgba(255,255,255,0.1);
        }

        .file-selected {
            color: #4CAF50;
            margin-top: 5px;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            
            .btn-group {
                flex-direction: column;
                align-items: center;
            }
            
            .header h1 {
                font-size: 2rem;
            }
        }

        /* Animaciones */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .shake {
            animation: shake 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .error-message.show, .success-message.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏨 Gestión de Habitaciones</h1>
            <p>Sistema de validación completo</p>
        </div>

        <div class="form-container">
            <div id="validation-summary" class="validation-summary">
                <h3>❌ Errores de validación:</h3>
                <ul id="error-list"></ul>
            </div>

            <form id="habitacion-form" novalidate>
                <div class="form-row">
                    <div class="form-group">
                        <label for="numero">Número de Habitación <span class="required">*</span></label>
                        <input type="text" id="numero" name="numero" class="form-control" 
                               placeholder="Ej: 101, A1, S01" maxlength="5" required>
                        <div class="validation-icon"></div>
                        <div class="error-message" id="numero-error"></div>
                        <div class="success-message" id="numero-success">✅ Número válido</div>
                    </div>

                    <div class="form-group">
                        <label for="costo">Costo por Noche <span class="required">*</span></label>
                        <input type="number" id="costo" name="costo" class="form-control" 
                               placeholder="Ej: 150000" min="0" step="0.01" required>
                        <div class="validation-icon"></div>
                        <div class="error-message" id="costo-error"></div>
                        <div class="success-message" id="costo-success">✅ Costo válido</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="capacidad">Capacidad <span class="required">*</span></label>
                        <select id="capacidad" name="capacidad" class="form-control" required>
                            <option value="">Seleccione capacidad</option>
                            <option value="1">1 persona</option>
                            <option value="2">2 personas</option>
                            <option value="3">3 personas</option>
                            <option value="4">4 personas</option>
                            <option value="5">5 personas</option>
                            <option value="6">6 personas</option>
                            <option value="7">7 personas</option>
                            <option value="8">8 personas</option>
                        </select>
                        <div class="validation-icon"></div>
                        <div class="error-message" id="capacidad-error"></div>
                        <div class="success-message" id="capacidad-success">✅ Capacidad válida</div>
                    </div>

                    <div class="form-group">
                        <label for="tipoHabitacion">Tipo de Habitación <span class="required">*</span></label>
                        <select id="tipoHabitacion" name="tipoHabitacion" class="form-control" required>
                            <option value="">Seleccione tipo</option>
                            <option value="1">Individual</option>
                            <option value="2">Doble</option>
                            <option value="3">Suite</option>
                            <option value="4">Familiar</option>
                            <option value="5">Ejecutiva</option>
                        </select>
                        <div class="validation-icon"></div>
                        <div class="error-message" id="tipoHabitacion-error"></div>
                        <div class="success-message" id="tipoHabitacion-success">✅ Tipo válido</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="foto">Foto de la Habitación</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="foto" name="foto" accept="image/*">
                        <label for="foto" class="file-input-label">
                            📸 Seleccionar imagen (JPG, PNG, WEBP)
                        </label>
                    </div>
                    <div class="file-selected" id="file-selected" style="display: none;"></div>
                    <div class="error-message" id="foto-error"></div>
                    <div class="success-message" id="foto-success">✅ Imagen válida</div>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" 
                              placeholder="Descripción detallada de la habitación..." 
                              maxlength="1000"></textarea>
                    <div class="validation-icon"></div>
                    <div class="error-message" id="descripcion-error"></div>
                    <div class="success-message" id="descripcion-success">✅ Descripción válida</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="estado">Estado <span class="required">*</span></label>
                        <select id="estado" name="estado" class="form-control" required>
                            <option value="">Seleccione estado</option>
                            <option value="Disponible">🟢 Disponible</option>
                            <option value="Reservada">🟡 Reservada</option>
                            <option value="Ocupada">🔴 Ocupada</option>
                            <option value="Mantenimiento">🟣 Mantenimiento</option>
                        </select>
                        <div class="validation-icon"></div>
                        <div class="error-message" id="estado-error"></div>
                        <div class="success-message" id="estado-success">✅ Estado válido</div>
                    </div>

                    <div class="form-group">
                        <label for="estadoMantenimiento">Estado de Mantenimiento <span class="required">*</span></label>
                        <select id="estadoMantenimiento" name="estadoMantenimiento" class="form-control" required>
                            <option value="Activo">✅ Activo</option>
                            <option value="Inactivo">❌ Inactivo</option>
                        </select>
                        <div class="validation-icon"></div>
                        <div class="error-message" id="estadoMantenimiento-error"></div>
                        <div class="success-message" id="estadoMantenimiento-success">✅ Estado válido</div>
                    </div>
                </div>

                <div class="form-group" id="mantenimiento-group" style="display: none;">
                    <label for="descripcionMantenimiento">Descripción del Mantenimiento</label>
                    <textarea id="descripcionMantenimiento" name="descripcionMantenimiento" class="form-control" 
                              placeholder="Descripción detallada del mantenimiento requerido..." 
                              maxlength="500"></textarea>
                    <div class="validation-icon"></div>
                    <div class="error-message" id="descripcionMantenimiento-error"></div>
                    <div class="success-message" id="descripcionMantenimiento-success">✅ Descripción válida</div>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">
                        🔄 Limpiar
                    </button>
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        💾 Guardar Habitación
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Configuración de validaciones
        const validationRules = {
            numero: {
                required: true,
                pattern: /^[A-Za-z0-9]{1,5}$/,
                message: 'El número debe tener entre 1-5 caracteres alfanuméricos'
            },
            costo: {
                required: true,
                min: 1,
                max: 99999999.99,
                message: 'El costo debe estar entre $1 y $99,999,999.99'
            },
            capacidad: {
                required: true,
                min: 1,
                max: 999,
                message: 'La capacidad debe estar entre 1 y 999 personas'
            },
            tipoHabitacion: {
                required: true,
                message: 'Debe seleccionar un tipo de habitación'
            },
            foto: {
                required: false,
                types: ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'],
                maxSize: 5 * 1024 * 1024, // 5MB
                message: 'La imagen debe ser JPG, PNG o WEBP y menor a 5MB'
            },
            descripcion: {
                required: false,
                maxLength: 1000,
                message: 'La descripción no debe exceder 1000 caracteres'
            },
            estado: {
                required: true,
                values: ['Disponible', 'Reservada', 'Ocupada', 'Mantenimiento'],
                message: 'Debe seleccionar un estado válido'
            },
            estadoMantenimiento: {
                required: true,
                values: ['Activo', 'Inactivo'],
                message: 'Debe seleccionar un estado de mantenimiento'
            },
            descripcionMantenimiento: {
                required: false,
                maxLength: 500,
                message: 'La descripción de mantenimiento no debe exceder 500 caracteres'
            }
        };

        // Referencias DOM
        const form = document.getElementById('habitacion-form');
        const validationSummary = document.getElementById('validation-summary');
        const errorList = document.getElementById('error-list');
        const submitBtn = document.getElementById('submit-btn');
        const estadoSelect = document.getElementById('estado');
        const mantenimientoGroup = document.getElementById('mantenimiento-group');
        const fotoInput = document.getElementById('foto');
        const fileSelected = document.getElementById('file-selected');

        let validationErrors = {};

        // Event listeners
        form.addEventListener('submit', handleSubmit);
        estadoSelect.addEventListener('change', toggleMantenimientoDescription);
        fotoInput.addEventListener('change', handleFileSelect);

        // Agregar validación en tiempo real a todos los campos
        Object.keys(validationRules).forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field) {
                field.addEventListener('blur', () => validateField(fieldName));
                field.addEventListener('input', () => {
                    // Limpiar error al empezar a escribir
                    clearFieldError(fieldName);
                });
            }
        });

        // Validar campo individual
        function validateField(fieldName) {
            const field = document.getElementById(fieldName);
            const rule = validationRules[fieldName];
            const value = field.value.trim();

            // Limpiar errores previos
            clearFieldError(fieldName);

            let isValid = true;
            let errorMessage = '';

            // Validación requerido
            if (rule.required && !value) {
                isValid = false;
                errorMessage = `${getFieldLabel(fieldName)} es requerido`;
            }
            // Validaciones específicas por tipo
            else if (value) {
                switch (fieldName) {
                    case 'numero':
                        if (!rule.pattern.test(value)) {
                            isValid = false;
                            errorMessage = rule.message;
                        } else if (isRoomNumberDuplicated(value)) {
                            isValid = false;
                            errorMessage = 'Este número de habitación ya existe';
                        }
                        break;

                    case 'costo':
                        const cost = parseFloat(value);
                        if (isNaN(cost) || cost < rule.min || cost > rule.max) {
                            isValid = false;
                            errorMessage = rule.message;
                        }
                        break;

                    case 'capacidad':
                        const capacity = parseInt(value);
                        if (isNaN(capacity) || capacity < rule.min || capacity > rule.max) {
                            isValid = false;
                            errorMessage = rule.message;
                        }
                        break;

                    case 'tipoHabitacion':
                    case 'estado':
                    case 'estadoMantenimiento':
                        if (!rule.values.includes(value)) {
                            isValid = false;
                            errorMessage = rule.message;
                        }
                        break;

                    case 'descripcion':
                    case 'descripcionMantenimiento':
                        if (value.length > rule.maxLength) {
                            isValid = false;
                            errorMessage = rule.message;
                        }
                        break;

                    case 'foto':
                        // Validación de archivo se maneja en handleFileSelect
                        break;
                }
            }

            // Validación especial para descripción de mantenimiento
            if (fieldName === 'descripcionMantenimiento' && estadoSelect.value === 'Mantenimiento' && !value) {
                isValid = false;
                errorMessage = 'La descripción de mantenimiento es requerida cuando el estado es "Mantenimiento"';
            }

            // Mostrar resultado de validación
            if (isValid) {
                showFieldSuccess(fieldName);
                delete validationErrors[fieldName];
            } else {
                showFieldError(fieldName, errorMessage);
                validationErrors[fieldName] = errorMessage;
            }

            updateValidationSummary();
            return isValid;
        }

        // Mostrar error en campo
        function showFieldError(fieldName, message) {
            const field = document.getElementById(fieldName);
            const errorDiv = document.getElementById(`${fieldName}-error`);
            const successDiv = document.getElementById(`${fieldName}-success`);
            const icon = field.parentElement.querySelector('.validation-icon');

            field.classList.add('error');
            field.classList.remove('valid');
            
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.classList.add('show');
            }
            
            if (successDiv) {
                successDiv.classList.remove('show');
            }
            
            if (icon) {
                icon.textContent = '❌';
                icon.className = 'validation-icon error';
                icon.style.display = 'block';
            }

            // Animación de shake
            field.classList.add('shake');
            setTimeout(() => field.classList.remove('shake'), 600);
        }

        // Mostrar éxito en campo
        function showFieldSuccess(fieldName) {
            const field = document.getElementById(fieldName);
            const errorDiv = document.getElementById(`${fieldName}-error`);
            const successDiv = document.getElementById(`${fieldName}-success`);
            const icon = field.parentElement.querySelector('.validation-icon');

            field.classList.add('valid');
            field.classList.remove('error');
            
            if (errorDiv) {
                errorDiv.classList.remove('show');
            }
            
            if (successDiv) {
                successDiv.classList.add('show');
            }
            
            if (icon) {
                icon.textContent = '✅';
                icon.className = 'validation-icon valid';
                icon.style.display = 'block';
            }
        }

        // Limpiar error de campo
        function clearFieldError(fieldName) {
            const field = document.getElementById(fieldName);
            const errorDiv = document.getElementById(`${fieldName}-error`);
            const successDiv = document.getElementById(`${fieldName}-success`);
            const icon = field.parentElement.querySelector('.validation-icon');

            field.classList.remove('error', 'valid');
            
            if (errorDiv) {
                errorDiv.classList.remove('show');
            }
            
            if (successDiv) {
                successDiv.classList.remove('show');
            }
            
            if (icon) {
                icon.style.display = 'none';
            }
        }

        // Actualizar resumen de validación
        function updateValidationSummary() {
            const hasErrors = Object.keys(validationErrors).length > 0;
            
            if (hasErrors) {
                errorList.innerHTML = Object.values(validationErrors)
                    .map(error => `<li>${error}</li>`)
                    .join('');
                validationSummary.style.display = 'block';
                submitBtn.disabled = true;
            } else {
                validationSummary.style.display = 'none';
                submitBtn.disabled = false;
            }
        }

        // Obtener etiqueta del campo
        function getFieldLabel(fieldName) {
            const labels = {
                numero: 'Número de habitación',
                costo: 'Costo',
                capacidad: 'Capacidad',
                tipoHabitacion: 'Tipo de habitación',
                foto: 'Foto',
                descripcion: 'Descripción',
                estado: 'Estado',
                estadoMantenimiento: 'Estado de mantenimiento',
                descripcionMantenimiento: 'Descripción de mantenimiento'
            };
            return labels[fieldName] || fieldName;
        }

        // Verificar si el número de habitación está duplicado
        function isRoomNumberDuplicated(numero) {
            // Aquí simularemos números existentes - en producción sería una consulta a BD
            const existingRooms = ['101', '102', '103', '201', '202', 'A1', 'B2'];
            return existingRooms.includes(numero);
        }

        // Manejar selección de archivo
        function handleFileSelect(event) {
            const file = event.target.files[0];
            const rule = validationRules.foto;

            if (file) {
                let isValid = true;
                let errorMessage = '';

                // Validar tipo de archivo
                if (!rule.types.includes(file.type)) {
                    isValid = false;
                    errorMessage = 'Solo se permiten archivos JPG, PNG y WEBP';
                }

                // Validar tamaño
                if (file.size > rule.maxSize) {
                    isValid = false;
                    errorMessage = 'El archivo debe ser menor a 5MB';
                }

                if (isValid) {
                    fileSelected.textContent = `✅ Archivo seleccionado: ${file.name}`;
                    fileSelected.style.display = 'block';
                    showFieldSuccess('foto');
                    delete validationErrors.foto;
                } else {
                    fileSelected.style.display = 'none';
                    showFieldError('foto', errorMessage);
                    validationErrors.foto = errorMessage;
                    event.target.value = '';
                }
            } else {
                fileSelected.style.display = 'none';
                clearFieldError('foto');
                delete validationErrors.foto;
            }

            updateValidationSummary();
        }

        // Toggle descripción de mantenimiento
        function toggleMantenimientoDescription() {
            const isMantenimiento = estadoSelect.value === 'Mantenimiento';
            mantenimientoGroup.style.display = isMantenimiento ? 'block' : 'none';
            
            if (!isMantenimiento) {
                const descripcionField = document.getElementById('descripcionMantenimiento');
                descripcionField.value = '';
                clearFieldError('descripcionMantenimiento');
                delete validationErrors.descripcionMantenimiento;
                updateValidationSummary();
            }
        }

        // Manejar envío del formulario
        function handleSubmit(event) {
            event.preventDefault();

            // Validar todos los campos
            let isFormValid = true;
            Object.keys(validationRules).forEach(fieldName => {
                const fieldValid = validateField(fieldName);
                if (!fieldValid) isFormValid = false;
            });

            if (isFormValid) {
                // Simular envío exitoso
                submitBtn.textContent = '⏳ Guardando...';
                submitBtn.disabled = true;

                setTimeout(() => {
                    alert('✅ Habitación guardada exitosamente!');
                    resetForm();
                    submitBtn.textContent = '💾 Guardar Habitación';
                    submitBtn.disabled = false;
                }, 2000);
            } else {
                alert('❌ Por favor, corrija los errores antes de continuar');
            }
        }

        // Resetear formulario
        function resetForm() {
            form.reset();
            validationErrors = {};
            
            // Limpiar estados visuales
            Object.keys(validationRules).forEach(fieldName => {
                clearFieldError(fieldName);
            });
            
            validationSummary.style.display = 'none';
            mantenimientoGroup.style.display = 'none';
            fileSelected.style.display = 'none';
            submitBtn.disabled = false;
            
            // Resetear estado de mantenimiento a "Activo"
            document.getElementById('estadoMantenimiento').value = 'Activo';
        }

        // Inicializar formulario
        function initializeForm() {
            // Configurar estado inicial
            document.getElementById('estadoMantenimiento').value = 'Activo';
            updateValidationSummary();
        }

        // Inicializar cuando la página carga
        document.addEventListener('DOMContentLoaded', initializeForm);
    </script>
</body>
</html>