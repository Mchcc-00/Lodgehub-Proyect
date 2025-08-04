<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Formulario PQRS</title>
  
  <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Estilos personalizados -->
  <link rel="stylesheet" href="../../../public/assets/css/stylesPQRS.css">
</head> 

<body>

  <!-- NAVBAR (debe ir primero dentro del <body>) -->
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
              data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
              aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item dropdown">
            <a id="lodgebub-dropdown" class="nav-link dropdown-toggle" href="#" role="button"
               data-bs-toggle="dropdown" aria-expanded="false">
              LODGEHUB
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="../../views/login/login.php">Home</a></li>
              <li><a class="dropdown-item active" href="mainReservas.php">Reservas</a></li>
              <li><a class="dropdown-item" href="../../../HABITACIONES/views/dashboard.php">Habitaciones</a></li>
              <li><a class="dropdown-item" href="../../../MANTENIMIENTO/views/dashboard.php">Mantenimiento</a></li>
              <li><a class="dropdown-item active" href="index.php">PQRS</a></li>
            </ul>
          </li>
        </ul>
        <form class="d-flex" role="perfil">
          <a href="../../app/views/homepage/cerrarSesion.php" class="btn btn-danger">Cerrar sesión</a>
        </form>
      </div>
    </div>
  </nav>

  <!-- Topbar
  <header class="topbar">
    <div class="topbar-left">
      <i class="fas fa-arrow-left"></i>
      <span class="topbar-title">LODGEHUB</span>
    </div>
    <div class="topbar-right">
      <img src="../../../public/img/iconoPerfil.png" alt="Logo Usuario" class="logo-img">
    </div>
  </header>

  Layout principal -->
  <!-- <div class="layout"> -->
    <!-- Sidebar -->
    <!-- <aside id="sidebar" class="sidebar">
      <nav class="nav-links">
        <a href="#">RESERVAS</a>
        <a href="#">HABITACIONES</a>
        <a href="#">MANTENIMIENTO</a>
      </nav>
      <div class="sidebar-footer">
        <button class="boton-info" onclick="tuFuncion()">
          <img src="../../../public/img/iconoPQRS.png" alt="LogoSoporte" class="logoSoporte">
        </button>
        <button class="logoButton" onclick="accionConfi()">
          <img src="../../../public/img/tuercaAjustes.png" alt="LogoConfi" class="logoConfi">
        </button>
      </div>
    </aside> -->

    <!-- Contenido principal con el formulario PQRS -->
    <main class="main-content">
      <div class="form-container">
        <h2>Nuevo PQRS</h2>
        <form id="pqrsForm" onsubmit="return validarFormulario()" method="post" action="../../controllers/PQRS.php">

          <div class="id-section">
            <label for="id">ID:</label>
            <input type="number" name="id" id="id" min="0" max="99999999" required />
            <button class="close-btn" type="button">×</button>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="fecha">Fecha de registro</label>
              <input type="date" id="fecha" name="fecha" required>
            </div>
            <div class="form-group">
              <label for="tipo_pqrs">Tipo PQRS</label>
              <select name="tipo_pqrs" id="tipo_pqrs" required>
                <option value="">Seleccionar</option>
                <option>Petición</option>
                <option>Queja</option>
                <option>Reclamo</option>
                <option>Sugerencia</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="urgencia">Nivel de urgencia</label>
              <select name="urgencia" id="urgencia" required>
                <option value="">Seleccionar</option>
                <option>Bajo</option>
                <option>Medio</option>
                <option>Alto</option>
              </select>          
            </div>
            <div class="form-group">
              <label for="categoria">Categoría</label>
              <select name="categoria" id="categoria" required>
                <option value="">Seleccionar</option>
                <option>Mantenimiento</option>
                <option>Servicio</option>
                <option>Otro</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group full-width">
              <label for="descripcion">Descripción</label>
              <textarea id="descripcion" name="descripcion" minlength="10" required></textarea>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="nombre">Nombre del solicitante</label>
              <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
              <label for="apellido">Apellido del solicitante</label>
              <input type="text" id="apellido" name="apellido" required>
            </div>
            <div class="form-group">
              <label for="empleado">Empleado que registra</label>
              <input type="text" id="empleado" name="empleado" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="tipo_documento">Tipo Documento</label>
              <select name="tipo_documento" id="tipo_documento" required>
                <option value="">Seleccionar</option>
                <option>CC</option>
                <option>CE</option>
                <option>TI</option>
              </select>
            </div>
            <div class="form-group">
              <label for="numero_documento">Número de Documento</label>
              <input type="text" id="numero_documento" name="numero_documento" pattern="[0-9]{6,15}" required>
            </div>
          </div>

          <div class="form-actions">
            <button class="btn btn-cancel" type="reset">Cancelar</button>
            <button class="btn btn-submit" type="submit">Solicitar</button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <!-- VALIDACIÓN -->
  <script>
    function validarFormulario() {
      const numeroDocumento = document.getElementById('numero_documento').value;
      const textoSinNumeros = ['nombre', 'apellido', 'empleado'];
      
      for (let campo of textoSinNumeros) {
        const valor = document.getElementById(campo).value;
        if (/\d/.test(valor)) {
          alert(`El campo "${campo}" no debe contener números.`);
          return false;
        }
      }

      if (!/^\d+$/.test(numeroDocumento)) {
        alert("El número de documento debe contener solo números.");
        return false;
      }

      return true;
    }
  </script>

  <!-- Bootstrap JS (AL FINAL del body como se indicó) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
          integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
          crossorigin="anonymous"></script>
</body>
</html>

