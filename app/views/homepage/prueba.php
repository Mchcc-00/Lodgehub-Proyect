<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>homepage</title>
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

</head>
    <?php
        include "../layouts/nav.php";
    ?>


<body>
    <div class="container">
        <header class="header">
            <h1>Dashboard de Gestión</h1>
            <p class="date" id="currentDate"></p>
        </header>

        <main class="dashboard">
            <!-- Sección Reservas -->
            <section class="section reservas-section">
                <h2 class="section-title">
                    <span class="icon">📅</span>
                    Reservas
                </h2>
                <div class="cards-grid">
                    <div class="card card-info" onclick="redirectTo('#reservas-hoy-inician')">
                        <div class="card-header">
                            <h3>Inician Hoy</h3>
                            <span class="card-icon">🟢</span>
                        </div>
                        <div class="card-number">12</div>
                        <div class="card-subtitle">Check-in programados</div>
                    </div>
                    
                    <div class="card card-warning" onclick="redirectTo('#reservas-hoy-terminan')">
                        <div class="card-header">
                            <h3>Terminan Hoy</h3>
                            <span class="card-icon">🔴</span>
                        </div>
                        <div class="card-number">8</div>
                        <div class="card-subtitle">Check-out programados</div>
                    </div>
                    
                    <div class="card card-success" onclick="redirectTo('#reservas-activas')">
                        <div class="card-header">
                            <h3>Activas</h3>
                            <span class="card-icon">✅</span>
                        </div>
                        <div class="card-number">45</div>
                        <div class="card-subtitle">Huéspedes actuales</div>
                    </div>
                    
                    <div class="card card-pending" onclick="redirectTo('#reservas-pendientes')">
                        <div class="card-header">
                            <h3>Pendientes</h3>
                            <span class="card-icon">⏳</span>
                        </div>
                        <div class="card-number">23</div>
                        <div class="card-subtitle">Por confirmar</div>
                    </div>
                    
                    <div class="card card-inactive" onclick="redirectTo('#reservas-inactivas')">
                        <div class="card-header">
                            <h3>Inactivas</h3>
                            <span class="card-icon">❌</span>
                        </div>
                        <div class="card-number">7</div>
                        <div class="card-subtitle">Canceladas/Vencidas</div>
                    </div>
                </div>
            </section>

            <!-- Sección Mantenimiento -->
            <section class="section mantenimiento-section">
                <h2 class="section-title">
                    <span class="icon">🔧</span>
                    Mantenimiento
                </h2>
                <div class="cards-grid maintenance-grid">
                    <div class="card card-danger" onclick="redirectTo('#mantenimiento-pendientes')">
                        <div class="card-header">
                            <h3>Pendientes</h3>
                            <span class="card-icon">⚠️</span>
                        </div>
                        <div class="card-number">15</div>
                        <div class="card-subtitle">Por iniciar</div>
                    </div>
                    
                    <div class="card card-process" onclick="redirectTo('#mantenimiento-proceso')">
                        <div class="card-header">
                            <h3>En Proceso</h3>
                            <span class="card-icon">🔄</span>
                        </div>
                        <div class="card-number">6</div>
                        <div class="card-subtitle">En ejecución</div>
                    </div>
                    
                    <div class="card card-completed" onclick="redirectTo('#mantenimiento-finalizados')">
                        <div class="card-header">
                            <h3>Finalizados</h3>
                            <span class="card-icon">✅</span>
                        </div>
                        <div class="card-number">28</div>
                        <div class="card-subtitle">Completados hoy</div>
                    </div>
                </div>
            </section>

            <!-- Sección PQRS -->
            <section class="section pqrs-section">
                <h2 class="section-title">
                    <span class="icon">📞</span>
                    PQRS
                </h2>
                <div class="cards-grid pqrs-grid">
                    <div class="card pqrs-high" onclick="redirectTo('#pqrs-alta')">
                        <div class="card-header">
                            <h3>Gravedad Alta</h3>
                            <span class="card-icon">🚨</span>
                        </div>
                        <div class="card-number">3</div>
                        <div class="card-subtitle">Sin responder</div>
                    </div>
                    
                    <div class="card pqrs-medium" onclick="redirectTo('#pqrs-media')">
                        <div class="card-header">
                            <h3>Gravedad Media</h3>
                            <span class="card-icon">⚠️</span>
                        </div>
                        <div class="card-number">7</div>
                        <div class="card-subtitle">Sin responder</div>
                    </div>
                    
                    <div class="card pqrs-low" onclick="redirectTo('#pqrs-baja')">
                        <div class="card-header">
                            <h3>Gravedad Baja</h3>
                            <span class="card-icon">ℹ️</span>
                        </div>
                        <div class="card-number">12</div>
                        <div class="card-subtitle">Sin responder</div>
                    </div>
                    
                    <div class="card pqrs-answered" onclick="redirectTo('#pqrs-respondidos')">
                        <div class="card-header">
                            <h3>Respondidos</h3>
                            <span class="card-icon">✅</span>
                        </div>
                        <div class="card-number">156</div>
                        <div class="card-subtitle">Total resueltos</div>
                    </div>
                </div>
                
                <div class="pqrs-actions">
                    <button class="btn-primary" onclick="redirectTo('/pqrs/todos')">
                        Ver Todos los PQRS
                        <span class="btn-icon">→</span>
                    </button>
                </div>
            </section>
        </main>
    </div>

    <script>
        // Mostrar fecha actual
        function updateDate() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            document.getElementById('currentDate').textContent = 
                now.toLocaleDateString('es-ES', options);
        }

        // Función para redireccionar
        function redirectTo(url) {
            console.log('Redirigiendo a:', url);
            // Aquí puedes cambiar por window.location.href = url; cuando tengas las URLs reales
            alert('Redirigiendo a: ' + url);
        }

        // Inicializar
        updateDate();
        
        // Actualizar fecha cada minuto
        setInterval(updateDate, 60000);
    </script>

    <!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>