<?php
session_start();

function sanitizar($dato) {
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

function validarSesion() {
    if(!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit();
    }
}

function validarAdmin() {
    if(!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] != 1) {
        header("Location: index.php");
        exit();
    }
}

function generarHash($clave) {
    return password_hash($clave, PASSWORD_DEFAULT);
}

function verificarHash($clave, $hash) {
    return password_verify($clave, $hash);
}

function subirFoto($file) {
    if($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
    
    if(!in_array($extension, $extensiones_permitidas)) {
        return false;
    }
    
    $nombre_archivo = uniqid() . '.' . $extension;
    $ruta_destino = "fotos/" . $nombre_archivo;
    
    if(move_uploaded_file($file['tmp_name'], $ruta_destino)) {
        return $nombre_archivo;
    }
    
    return false;
}
?>
