<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item dropdown">
          <a id="lodgebub-dropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            LODGEHUB
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../homepage/homepage.php">Home</a></li>
            <li><a class="dropdown-item" href="../../../6_Reservas/2R/mainReservas.php">Reservas</a></li>
            <li><a class="dropdown-item" href="../../../HABITACIONES/views/dashboard.php">Habitaciones</a></li>
            <li><a class="dropdown-item" href="../../../MANTENIMIENTO/views/dashboard.php">Mantenimiento</a></li>
            <li><a class="dropdown-item" href="../../../app/views/Usuarios/lista.php">Usuarios</a></li>
            <li><a class="dropdown-item" href="../../../PQRS/views/dashboard.php">PQRS</a></li>

          </ul>
        </li>

      </ul>
      <form class="d-flex" role="perfil">
        
        <a href="../homepage/cerrarSesion.php" class="btn btn-danger">Cerrar sesión</a>
      </form>
    </div>
  </div>
</nav>

<style>

  .container-fluid {
    background: #437bafff;
    padding: 20px;
  }

  #lodgebub-dropdown {
    color: #ffffffff;
    margin: 0;
    padding: 0;
    list-style: none;
    font-weight: bold;
    text-transform: uppercase;
  }
  
</style>