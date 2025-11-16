<nav class="navbar">
  <a href="/Jobyz/index.php"><img src="assets\Images\Logo\Jobyz_v3_lateral-removebg-preview.png" class="nav-logo" alt="Jobyz Logo"></a>
  <div class="nav-actions">
    <button class="notif-icon" title="Notificaciones">
      <img src="assets\Images\Iconos\campana.svg" class="icono-header" alt="campana">
    </button>

    <div class="profile-menu">
      <button class="profile-icon" title="Perfil">
        <img src="assets\Images\Iconos\usuario.svg" class="icono-header" alt="usuario">
      </button>
      <div class="menu-dropdown">
        <a href="/Jobyz/index.php?page=solicitudes">Ver solicitudes</a>
        <a href="/Jobyz/index.php?page=ofertas">Ver ofertas</a>
        <?php if($_SESSION['rol_id'] == 2 ) { ?>
          <a href="/Jobyz/index.php?page=perfil_alumno">Ver perfil</a>
        <?php } else { ?>
          <a href="/Jobyz/index.php?page=perfil_empresa">Ver perfil</a>
        <?php } ?>
        
        <a href="/Jobyz/index.php?page=logout">Cerrar sesión</a>
      </div>
    </div>
  </div>
</nav>
