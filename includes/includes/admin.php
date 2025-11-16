<?php
include('includes/conexion.php');
include('includes/seguridad.php');
conectar();
validarSesion();
validarAdmin();

// Procesar formularios
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['agregar_disfraz'])) {
        $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
        $descripcion = mysqli_real_escape_string($con, $_POST['descripcion']);
        
        $foto_nombre = '';
        if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $foto_nombre = subirFoto($_FILES['foto']);
        }
        
        if($foto_nombre) {
            mysqli_query($con, "INSERT INTO disfraces (nombre, descripcion, foto) VALUES ('$nombre', '$descripcion', '$foto_nombre')");
        } else {
            mysqli_query($con, "INSERT INTO disfraces (nombre, descripcion) VALUES ('$nombre', '$descripcion')");
        }
        
        $_SESSION['mensaje'] = "Disfraz agregado exitosamente";
        
    } elseif(isset($_POST['editar_disfraz'])) {
        $id = intval($_POST['id']);
        $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
        $descripcion = mysqli_real_escape_string($con, $_POST['descripcion']);
        
        $update_sql = "UPDATE disfraces SET nombre = '$nombre', descripcion = '$descripcion' WHERE id = $id";
        
        if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $foto_nombre = subirFoto($_FILES['foto']);
            if($foto_nombre) {
                // Eliminar foto anterior si existe
                $old_foto = mysqli_fetch_assoc(mysqli_query($con, "SELECT foto FROM disfraces WHERE id = $id"));
                if($old_foto['foto'] && file_exists("fotos/" . $old_foto['foto'])) {
                    unlink("fotos/" . $old_foto['foto']);
                }
                $update_sql = "UPDATE disfraces SET nombre = '$nombre', descripcion = '$descripcion', foto = '$foto_nombre' WHERE id = $id";
            }
        }
        
        mysqli_query($con, $update_sql);
        $_SESSION['mensaje'] = "Disfraz actualizado exitosamente";
        
    } elseif(isset($_POST['eliminar_disfraz'])) {
        $id = intval($_POST['id']);
        mysqli_query($con, "UPDATE disfraces SET eliminado = 1 WHERE id = $id");
        $_SESSION['mensaje'] = "Disfraz eliminado exitosamente";
    }
    
    header("Location: admin.php");
    exit();
}

// Obtener disfraces
$disfraces = mysqli_query($con, "SELECT * FROM disfraces WHERE eliminado = 0 ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - Halloween</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="halloween-theme">
    <div class="container">
        <header>
            <h1>🛠️ Panel de Administración</h1>
            <nav>
                <a href="index.php">← Volver al Inicio</a>
                <a href="logout.php">Cerrar Sesión</a>
            </nav>
        </header>

        <main>
            <?php if(isset($_SESSION['mensaje'])): ?>
                <div class="mensaje"><?php echo sanitizar($_SESSION['mensaje']); unset($_SESSION['mensaje']); ?></div>
            <?php endif; ?>

            <section class="admin-section">
                <h2>Agregar Nuevo Disfraz</h2>
                <form method="POST" enctype="multipart/form-data" class="disfraz-form">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion">Descripción:</label>
                        <textarea id="descripcion" name="descripcion" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="foto">Foto:</label>
                        <input type="file" id="foto" name="foto" accept="image/*">
                    </div>
                    
                    <button type="submit" name="agregar_disfraz" class="btn-primary">Agregar Disfraz</button>
                </form>
            </section>

            <section class="admin-section">
                <h2>Gestionar Disfraces</h2>
                <div class="disfraces-list">
                    <?php while($disfraz = mysqli_fetch_assoc($disfraces)): ?>
                        <div class="disfraz-admin-card">
                            <div class="disfraz-info">
                                <h3><?php echo sanitizar($disfraz['nombre']); ?></h3>
                                <p><?php echo sanitizar($disfraz['descripcion']); ?></p>
                                <p><strong>Votos: <?php echo number_format($disfraz['votos'], 0); ?></strong></p>
                                
                                <?php if($disfraz['foto'] && file_exists("fotos/" . $disfraz['foto'])): ?>
                                    <img src="fotos/<?php echo sanitizar($disfraz['foto']); ?>" alt="<?php echo sanitizar($disfraz['nombre']); ?>" class="thumb">
                                <?php endif; ?>
                            </div>
                            
                            <div class="admin-actions">
                                <form method="POST" enctype="multipart/form-data" class="edit-form">
                                    <input type="hidden" name="id" value="<?php echo $disfraz['id']; ?>">
                                    <div class="form-group">
                                        <input type="text" name="nombre" value="<?php echo sanitizar($disfraz['nombre']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <textarea name="descripcion" required><?php echo sanitizar($disfraz['descripcion']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <input type="file" name="foto" accept="image/*">
                                    </div>
                                    <button type="submit" name="editar_disfraz" class="btn-edit">Actualizar</button>
                                </form>
                                
                                <form method="POST" class="delete-form" onsubmit="return confirm('¿Estás seguro de eliminar este disfraz?')">
                                    <input type="hidden" name="id" value="<?php echo $disfraz['id']; ?>">
                                    <button type="submit" name="eliminar_disfraz" class="btn-delete">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
<?php desconectar(); ?>
