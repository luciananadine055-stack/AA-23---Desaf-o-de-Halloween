<?php
function conectar() {
    global $con;
    $con = mysqli_connect("localhost", "root", "", "halloween");
    
    if (mysqli_connect_errno()) {
        error_log("Error de conexión: " . mysqli_connect_error());
        return false;
    }
    
    $con->set_charset("utf8");
    return true;
}

function desconectar() {
    global $con;
    if(isset($con)) {
        mysqli_close($con);
    }
}
?>
