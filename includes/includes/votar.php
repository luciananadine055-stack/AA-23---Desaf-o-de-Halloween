<?php
include('includes/conexion.php');
include('includes/seguridad.php');
conectar();
validarSesion();

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['disfraz_id'])) {
    $disfraz_id = intval($_POST['disfraz_id']);
    $usuario_id = $_SESSION['usuario_id'];
    
    // Verificar que el disfraz existe y no está eliminado
    $check_disfraz = mysqli_query($con, "SELECT id FROM disfraces WHERE id = $disfraz_id AND eliminado = 0");
    if(mysqli_num_rows($check_disfraz) == 0) {
        $_SESSION['error'] = "Disfraz no válido";
        header("Location: index.php");
        exit();
    }
    
    // Verificar si ya votó
    $check_voto = mysqli_query($con, "SELECT id FROM votos WHERE id_usuario = $usuario_id AND id_disfraz = $disfraz_id");
    if(mysqli_num_rows($check_voto) > 0) {
        $_SESSION['error'] = "Ya votaste por este disfraz";
        header("Location: index.php");
        exit();
    }
    
    // Insertar voto
    mysqli_query($con, "INSERT INTO votos (id_usuario, id_disfraz) VALUES ($usuario_id, $disfraz_id)");
    
    // Actualizar contador de votos
    mysqli_query($con, "UPDATE disfraces SET votos = votos + 1 WHERE id = $disfraz_id");
    
    $_SESSION['mensaje'] = "¡Voto registrado exitosamente!";
}

header("Location: index.php");
desconectar();
?>
