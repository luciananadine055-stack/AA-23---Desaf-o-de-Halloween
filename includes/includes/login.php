<?php
include('includes/conexion.php');
include('includes/seguridad.php');
conectar();

if(isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $clave = $_POST['clave'];
    
    $sql = mysqli_query($con, "SELECT * FROM usuarios WHERE nombre = '$nombre'");
    
    if(mysqli_num_rows($sql) == 1) {
        $usuario = mysqli_fetch_assoc($sql);
        
        if(verificarHash($clave, $usuario['clave'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['es_admin'] = $usuario['es_admin'];
            
            header("Location: index.php");
            exit();
        } else {
            $error = "Credenciales incorrectas";
        }
    } else {
        $error = "Usuario no encontrado";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Halloween</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="halloween-theme">
    <div class="container">
        <header>
            <h1>🔐 Iniciar Sesión</h1>
            <nav>
                <a href="index.php">← Volver al Inicio</a>
            </nav>
        </header>

        <main class="form-container">
            <?php if(isset($_SESSION['mensaje'])): ?>
                <div class="mensaje"><?php echo sanitizar($_SESSION['mensaje']); unset($_SESSION['mensaje']); ?></div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="error"><?php echo sanitizar($error); ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="nombre">Nombre de Usuario:</label>
                    <input type="text" id="nombre" name="nombre" required 
                           value="<?php echo isset($_POST['nombre']) ? sanitizar($_POST['nombre']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="clave">Contraseña:</label>
                    <input type="password" id="clave" name="clave" required>
                </div>
                
                <button type="submit" class="btn-primary">Iniciar Sesión</button>
            </form>
            
            <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
            
            <!-- Credenciales de admin para prueba -->
            <div class="demo-credentials">
                <strong>Demo Admin:</strong><br>
                Usuario: admin<br>
                Contraseña: password
            </div>
        </main>
    </div>
</body>
</html>
<?php desconectar(); ?>
