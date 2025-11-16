<?php
include('includes/conexion.php');
include('includes/seguridad.php');
conectar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Halloween - Disfraces</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="halloween-theme">
    <div class="container">
        <header>
            <h1>🎃 Disfraces de Halloween 🎃</h1>
            <nav>
                <?php if(isset($_SESSION['usuario_id'])): ?>
                    <span>Hola, <?php echo sanitizar($_SESSION['usuario_nombre']); ?></span>
                    <?php if($_SESSION['es_admin'] == 1): ?>
                        <a href="admin.php">Admin</a>
                    <?php endif; ?>
                    <a href="logout.php">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="login.php">Iniciar Sesión</a>
                    <a href="registro.php">Registrarse</a>
                <?php endif; ?>
            </nav>
        </header>

        <main>
            <h2>Disfraces Disponibles</h2>
            <div class="disfraces-grid">
                <?php
                $sql = mysqli_query($con, "SELECT * FROM disfraces WHERE eliminado = 0 ORDER BY votos DESC");
                
                if(mysqli_num_rows($sql) > 0) {
                    while($disfraz = mysqli_fetch_assoc($sql)) {
                        echo '
                        <div class="disfraz-card">
                            <div class="foto-disfraz">';
                        
                        if(file_exists("fotos/" . $disfraz['foto']) && !empty($disfraz['foto'])) {
                            echo '<img src="fotos/' . sanitizar($disfraz['foto']) . '" alt="' . sanitizar($disfraz['nombre']) . '">';
                        } else {
                            echo '<div class="sin-foto">🎭</div>';
                        }
                        
                        echo '
                            </div>
                            <h3>' . sanitizar($disfraz['nombre']) . '</h3>
                            <p>' . sanitizar($disfraz['descripcion']) . '</p>
                            <div class="votos">
                                <strong>Votos: ' . number_format($disfraz['votos'], 0) . '</strong>';
                        
                        if(isset($_SESSION['usuario_id'])) {
                            // Verificar si ya votó
                            $usuario_id = $_SESSION['usuario_id'];
                            $check_voto = mysqli_query($con, "SELECT id FROM votos WHERE id_usuario = $usuario_id AND id_disfraz = " . $disfraz['id']);
                            
                            if(mysqli_num_rows($check_voto) == 0) {
                                echo '<form method="POST" action="votar.php" class="voto-form">
                                        <input type="hidden" name="disfraz_id" value="' . $disfraz['id'] . '">
                                        <button type="submit" class="btn-votar">👍 Votar</button>
                                      </form>';
                            } else {
                                echo '<span class="ya-voto">✅ Ya votaste</span>';
                            }
                        } else {
                            echo '<a href="login.php" class="btn-login">Inicia sesión para votar</a>';
                        }
                        
                        echo '
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p>No hay disfraces disponibles.</p>';
                }
                ?>
            </div>
        </main>
    </div>
</body>
</html>
<?php desconectar(); ?>
