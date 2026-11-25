<?php
// Inicia sesión para leer o guardar datos del usuario autenticado.
session_start();

// Carga la conexión a la base de datos.
require 'conexion.php';

// Redirige al inicio si el usuario ya tiene una sesión activa.
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Guarda el texto y tipo de alerta que se muestra en pantalla.
$mensaje = "";
$tipo_alerta = "";

// Procesa solamente los formularios enviados por POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Procesa el formulario de registro.
    if (isset($_POST['registro_submit'])) {
        // Escapa los datos recibidos antes de usarlos en consultas SQL.
        $nombre = $conn->real_escape_string($_POST['nombre_reg']);
        $email = $conn->real_escape_string($_POST['email_reg']);
        $password = password_hash($_POST['password_reg'], PASSWORD_DEFAULT);
        $direccion = $conn->real_escape_string($_POST['direccion_reg']);
        $telefono = $conn->real_escape_string($_POST['telefono_reg']);

        // Busca si el correo ya está registrado.
        $sql_check = "SELECT id FROM usuarios WHERE email = '$email'";
        if ($conn->query($sql_check)->num_rows > 0) {
            $mensaje = "El correo ya está registrado.";
            $tipo_alerta = "danger";
        } else {
            // Crea el usuario cuando el correo está disponible.
            $sql = "INSERT INTO usuarios (nombre, email, contraseña, direccion, telefono) VALUES ('$nombre', '$email', '$password', '$direccion', '$telefono')";
            if ($conn->query($sql) === TRUE) {
                $mensaje = "Registro exitoso. Ahora puedes iniciar sesión.";
                $tipo_alerta = "success";
            } else {
                $mensaje = "Error: " . $conn->error;
                $tipo_alerta = "danger";
            }
        }
    }

    // Procesa el formulario de inicio de sesión.
    if (isset($_POST['login_submit'])) {
        // Escapa el correo y conserva la contraseña para verificar su hash.
        $email = $conn->real_escape_string($_POST['email_login']);
        $password = $_POST['password_login'];

        // Obtiene los datos mínimos necesarios para autenticar al usuario.
        $sql = "SELECT id, nombre, contraseña FROM usuarios WHERE email = '$email'";
        $resultado = $conn->query($sql);

        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            // Verifica la contraseña ingresada contra el hash guardado.
            if (password_verify($password, $usuario['contraseña'])) {
                // Guarda los datos de sesión usados por el sitio.
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                header("Location: index.php");
                exit();
            } else {
                $mensaje = "Contraseña incorrecta.";
                $tipo_alerta = "danger";
            }
        } else {
            $mensaje = "Usuario no encontrado.";
            $tipo_alerta = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require __DIR__ . '/includes/head.php'; ?>
</head>

<body class="bg-light d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold fs-3" href="index.php">D6guitars</a>
            <div class="ms-auto">
            </div>
        </div>
    </nav>

<div class="container flex-grow-1 d-flex align-items-center justify-content-center py-5">
     <div class="card shadow-lg border-0 rounded-4" style="max-width: 500px; width: 100%;">

         <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
             <ul class="nav nav-pills nav-justified" id="authTabs" role="tablist">
                 <li class="nav-item" role="presentation">
                     <button class="nav-link active fw-bold rounded-pill"
                         id="login-tab" data-bs-toggle="tab" data-bs-target="#login"
                         type="button" role="tab" aria-selected="true">
                         Iniciar Sesión
                     </button>
                 </li>
                 <li class="nav-item" role="presentation">
                     <button class="nav-link fw-bold rounded-pill"
                         id="register-tab" data-bs-toggle="tab" data-bs-target="#register"
                         type="button" role="tab" aria-selected="false">
                         Registrarse
                     </button>
                 </li>
             </ul>
         </div>

         <div class="card-body p-4 p-md-5 pt-4">

             <?php if ($mensaje != ""): ?>
                 <div class="alert alert-<?php echo $tipo_alerta; ?> mb-4 rounded-3 text-center shadow-sm">
                     <?php echo $mensaje; ?>
                 </div>
             <?php endif; ?>

             <div class="tab-content" id="authTabsContent">

                 <div class="tab-pane fade show active" id="login" role="tabpanel">
                     <form method="POST" action="login.php">
                         <div class="form-floating mb-3">
                             <input type="email" class="form-control rounded-3" id="loginEmail" name="email_login" placeholder="nombre@ejemplo.com" required>
                             <label for="loginEmail">Correo Electrónico</label>
                         </div>
                         <div class="form-floating mb-3">
                             <input type="password" class="form-control rounded-3" id="loginPassword" name="password_login" placeholder="Contraseña" required>
                             <label for="loginPassword">Contraseña</label>
                         </div>
                         <button class="btn btn-dark w-100 py-3 rounded-3 fw-bold text-uppercase" type="submit" name="login_submit">Ingresar</button>
                     </form>
                 </div>

                 <div class="tab-pane fade" id="register" role="tabpanel">
                     <form method="POST" action="login.php">
                         <div class="form-floating mb-3">
                             <input type="text" class="form-control rounded-3" id="regNombre" name="nombre_reg" placeholder="Tu Nombre" required>
                             <label for="regNombre">Nombre Completo</label>
                         </div>
                         <div class="form-floating mb-3">
                             <input type="email" class="form-control rounded-3" id="regEmail" name="email_reg" placeholder="nombre@ejemplo.com" required>
                             <label for="regEmail">Correo Electrónico</label>
                         </div>
                         <div class="form-floating mb-3">
                             <input type="password" class="form-control rounded-3" id="regPassword" name="password_reg" placeholder="Contraseña" required>
                             <label for="regPassword">Contraseña</label>
                         </div>
                         <div class="form-floating mb-3">
                             <input type="text" class="form-control rounded-3" id="regDireccion" name="direccion_reg" placeholder="Dirección">
                             <label for="regDireccion">Dirección de Envío (Opcional)</label>
                         </div>
                         <div class="form-floating mb-4">
                             <input type="tel" class="form-control rounded-3" id="regTelefono" name="telefono_reg" placeholder="Teléfono">
                             <label for="regTelefono">Teléfono (Opcional)</label>
                         </div>
                         <button class="btn btn-danger w-100 py-3 rounded-3 fw-bold text-uppercase" type="submit" name="registro_submit">Crear Cuenta</button>
                     </form>
                 </div>

             </div>
         </div>
     </div>
 </div>

 <?php require __DIR__ . '/includes/footer.php'; ?>
</body>

</html>
