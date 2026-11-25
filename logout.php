<?php
// Inicia la sesión para poder limpiar sus datos.
session_start();

// Elimina las variables de sesión del usuario.
session_unset();

// Destruye la sesión actual.
session_destroy();

// Envía al usuario de vuelta al inicio.
header("Location: index.php");
exit();
?>
