<?php
require_once '../../includes/auth.php';
if (strtolower($_SESSION['rol'] ?? '') !== 'admin') { header("Location: " . BASE_URL . "inicio.php"); exit(); }

$userCtrl = new UsuarioController($conexion);

// Manejo de habilitar/inhabilitar con SweetAlert
if (isset($_GET['id']) && isset($_GET['accion'])) {
    $resultado = $userCtrl->cambiarEstado($_GET['id'], ($_GET['accion'] === 'habilitar' ? 1 : 0));
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        window.onload = function() {
            Swal.fire({
                icon: '{$resultado['status']}',
                title: '" . ($resultado['status'] == 'success' ? '¡Éxito!' : 'Error') . "',
                text: '{$resultado['msg']}',
                confirmButtonColor: '#28a745'
            }).then(() => { window.location='usuarios.php'; });
        };
    </script>";
    exit();
}

if (isset($_GET['id']) && isset($_GET['reset'])) {
    $userCtrl->reiniciarClave($_GET['id'], 'sarce1234');
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        window.onload = function() {
            Swal.fire({
                icon: 'success',
                title: 'Contraseña Reiniciada',
                text: 'La nueva clave temporal es: sarce1234',
                confirmButtonColor: '#28a745'
            }).then(() => { window.location='usuarios.php'; });
        };
    </script>";
    exit();
}

$pageTitle = "Gestión de Usuarios | SARCE";
include '../../includes/layout_header.php';
$usuarios = $userCtrl->listar();
?>
<div class="contenedor">
    <div class="header-tablero">
        <h2><i class="fas fa-users-cog"></i> Gestión de Usuarios</h2>
        <a href="nuevo_usuario.php" class="btn-sarce btn-sarce-success"><i class="fas fa-plus"></i> REGISTRAR</a>
    </div>

    <div class="search-box-container">
        <i class="fas fa-search search-icon"></i>
        <input type="text" id="buscador" class="search-box" placeholder="Buscar por nombre, apellido o usuario...">
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody id="cuerpoTabla">
                <?php if ($usuarios): while($row = mysqli_fetch_assoc($usuarios)) { ?>
                <tr>
                    <td><span class="user-badge"><?php echo $row['usuario']; ?></span></td>
                    <td><?php echo $row['nombre'] . ' ' . $row['apellido']; ?></td>
                    <td><?php echo ucfirst($row['rol']); ?></td>
                    <td><span class="badge-cantidad" style="<?php echo !$row['estado'] ? 'background:#dc3545;' : ''; ?>"><?php echo $row['estado'] ? 'Activo' : 'Inactivo'; ?></span></td>
                    <td class="acciones-flex">
                        <a href="perfil.php?id=<?php echo $row['id']; ?>" class="btn-original btn-editar" title="Editar">
                            <i class="fas fa-edit"></i> EDITAR
                        </a>
                        <a href="usuarios.php?id=<?php echo $row['id']; ?>&accion=<?php echo $row['estado'] ? 'inhabilitar' : 'habilitar'; ?>" class="btn-original <?php echo $row['estado'] ? 'btn-inhabilitar' : 'btn-atender'; ?>" title="<?php echo $row['estado'] ? 'Inhabilitar' : 'Habilitar'; ?>">
                            <i class="fas <?php echo $row['estado'] ? 'fa-user-slash' : 'fa-user-check'; ?> text-white"></i> <?php echo $row['estado'] ? 'INHABILITAR' : 'HABILITAR'; ?>
                        </a>
                        <a href="javascript:void(0)" onclick="confirmarReset(<?php echo $row['id']; ?>)" class="btn-original btn-historial" title="Reiniciar Contraseña">
                            <i class="fas fa-key"></i> REINICIAR
                        </a>
                    </td>
                </tr>
                <?php } else: ?>
                    <tr><td colspan="5" style="text-align:center;">No se encontraron usuarios registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<footer class="footer-sarce-principal">
    <div class="footer-info">
        <p>
            <i class="fas fa-map-marker-alt"></i> <strong>Dirección:</strong> Jají, Municipio Campo Elías, Estado Mérida.
        </p>
        <p>
            <i class="fas fa-phone"></i> <strong>Teléfono de Atención:</strong> 04161537743
        </p>
    </div>
    <p>&copy; <?php echo date("Y"); ?> SARCE - Sistema de Control de Registro.</p>
</footer>
<?php include '../../includes/layout_footer.php'; ?>