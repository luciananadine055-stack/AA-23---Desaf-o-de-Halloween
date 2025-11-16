<?php
include('includes/conexion.php');
include('includes/seguridad.php');
conectar();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $clave = $_POST['clave'];
    $clave_confirm = $_POST['clave_confirm'];
    
    $errores = [];
    
    if(empty($nombre)) {
        $errores[] = "El nombre es requerido";
    }
    
    if(empty($clave)) {
        $errores[] = "La contraseña es requerida";
    }
    
    if($clave !== $clave_confirm) {
        $errores[] = "Las contraseñas no coinciden";
    }
    
    // Verificar si el usuario ya existe
    $check_user = mysqli_query($con, "SELECT id FROM usuarios WHERE nombre = '$nombre'");
    if(mysqli_num_rows($check_user) > 0) {
        $errores[] = "El nombre de usuario ya existe";
    }
    
    if(empty($errores)) {
        $clave_hash = generarHash($clave);
        $sql = "INSERT INTO usuarios (nombre, clave) VALUES ('$nombre', '$clave_hash')";
        
        if(mysqli_query($con, $sql)) {
            $_SESSION['mensaje'] = "Registro exitoso. Ahora puedes iniciar sesión.";
            header("Location: login.php");
            exit();
        } else {
            $errores[] = "Error en el registro: " . mysqli_error($con);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Halloween</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="halloween-theme">
    <div class="container">
        <header>
            <h1>👻 Registro de Usuario</h1>
            <nav>
                <a href="index.php">← Volver al Inicio</a>
            </nav>
        </header>

        <main class="form-container">
            <?php if(isset($errores)): ?>
                <?php foreach($errores as $error): ?>
                    <div class="error"><?php echo sanitizar($error); ?></div>
                <?php endforeach; ?>
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
                
                <div class="form-group">
                    <label for="clave_confirm">Confirmar Contraseña:</label>
                    <input type="password" id="clave_confirm" name="clave_confirm" required>
                </div>
                
                <button type="submit" class="btn-primary">Registrarse</button>
            </form>
            
            <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
        </main>
    </div>
</body>
</html>
<?php desconectar(); ?>
