<div class="sidebar">
    <div class="logo-details">
        <a href="<?php echo BASE_URL; ?>index.php">
            <img src="<?php echo IMG_URL; ?>logooficial.png" alt="Logo SARCE">
        </a>
    </div>
    
    <ul class="nav-links">
        <li>
            <a href="<?php echo BASE_URL; ?>inicio.php">
                <i class="fas fa-th-large"></i>
                <span class="link_name">Panel de Inicio</span>
            </a>
        </li>
        <li>
            <a href="<?php echo MOD_PACIENTES; ?>lista_de_pacientes.php">
                <i class="fas fa-users"></i>
                <span class="link_name">Pacientes</span>
            </a>
        </li>

         <li>
            <a href="<?php echo MOD_PERSONAL; ?>personal.php">
                <i class="fas fa-user-md"></i>
                <span class="link_name">Personal Medico</span>
            </a>
        </li>
        <li>
            <a href="<?php echo MOD_MEDICAMENTOS; ?>medicamentos.php">
                <i class="fas fa-pills"></i>
                <span class="link_name">Medicamentos</span>
            </a>
        </li>
        <li>
            <a href="<?php echo MOD_CONSULTAS; ?>tablero.php">
                <i class="fas fa-chart-line"></i>
                <span class="link_name">Epidemiológia</span>
            </a>
        </li>
        
        <li>
            <a href="<?php echo MOD_SISTEMA; ?>perfil.php">
                <i class="fas fa-user-circle"></i>
                <span class="link_name">Usuario</span>
            </a>
        </li>

        <?php if (strtolower($_SESSION['rol']) === 'admin'): ?>
        <li>
            <a href="javascript:void(0)" onclick="toggleSubmenu()">
                <i class="fas fa-cogs"></i>
                <span class="link_name">Gestión de Sistema <i class="fas fa-chevron-down arrow-toggle" style="font-size: 12px; margin-left: 5px; transition: 0.3s;"></i></span>
            </a>
            <ul class="submenu" id="submenu-gestion">
                <li>
                    <a href="<?php echo MOD_SISTEMA; ?>usuarios.php"><i class="fas fa-users-cog"></i> Usuarios</a>
                </li>
                <li>
                    <a href="<?php echo MOD_SISTEMA; ?>bitacora.php"><i class="fas fa-history"></i> Bitácora</a>
                </li>
                <li>
                    <a href="<?php echo MOD_SISTEMA; ?>respaldo.php"><i class="fas fa-database"></i> Respaldo DB</a>
                </li>
                <li>
                    <a href="<?php echo MOD_SISTEMA; ?>restauracion.php"><i class="fas fa-upload"></i> Restauración</a>
                </li>
            </ul>
        </li>
        <?php endif; ?>

        <li class="log_out">
            <a href="<?php echo BASE_URL; ?>login.php?action=logout">
                <i class="fas fa-sign-out-alt"></i>
                <span class="link_name">Cerrar Sesión</span>
            </a>
        </li>
    </ul>
</div>

<script>
function toggleSubmenu() {
    const submenu = document.getElementById('submenu-gestion');
    const arrow = document.querySelector('.arrow-toggle');
    if (submenu.style.display === 'block') {
        submenu.style.display = 'none';
        if(arrow) arrow.style.transform = 'rotate(0deg)';
    } else {
        submenu.style.display = 'block';
        if(arrow) arrow.style.transform = 'rotate(180deg)';
    }
}
</script>