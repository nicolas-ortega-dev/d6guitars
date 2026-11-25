<?php
// Inicia la sesión si el archivo se carga desde una página sin sesión activa.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prepara la ruta base para enlaces seguros en distintas carpetas.
$basePath = $basePath ?? '';
$baseHref = htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8');
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold fs-3" href="<?php echo $baseHref; ?>index.php">D6guitars</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?php echo $baseHref; ?>index.php">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $baseHref; ?>index.php#categorias">Categorías</a></li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <?php // Muestra opciones de usuario solo cuando existe una sesión iniciada. ?>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-dark dropdown-toggle text-white border-0" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-2"></i>Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?php echo $baseHref; ?>logout.php">Cerrar Sesión</a></li>
                        </ul>
                    </div>

                <?php else: ?>
                    <a href="<?php echo $baseHref; ?>login.php" class="btn btn-outline-light"><i class="bi bi-box-arrow-in-right me-2"></i>Acceder</a>
                <?php endif; ?>

                <button class="btn btn-outline-light position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                    <i class="bi bi-cart3 fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">0</span>
                </button>
            </div>
        </div>
    </div>
</nav>
