<?php
// Inicia la sesión solo si todavía no existe una activa.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define rutas y título usados por las plantillas compartidas.
$basePath = '';
$pageTitle = 'D6guitars | Inicio';

// Evita que usuarios sin sesión entren a la página principal.
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Carga el archivo JSON que contiene todos los productos.
$productosJson = file_get_contents(__DIR__ . '/backend/productos.json');

// Detiene la ejecución si el archivo no se puede leer.
if ($productosJson === false) {
    die("No se pudo leer el archivo.");
}

// Convierte el JSON a un arreglo asociativo de PHP.
$productosData = json_decode($productosJson, true);

// Valida que el JSON tenga una sintaxis correcta.
if (json_last_error() !== JSON_ERROR_NONE) {
    die("JSON Invalido: " . json_last_error_msg());
}

// Comprueba que exista el arreglo de productos esperado.
if (!isset($productosData['productos']) || !is_array($productosData['productos'])) {
    die("La estructura del JSON es incorrecta");
}

// Filtra solo los productos marcados como destacados.
$productosDestacados = [];
foreach ($productosData['productos'] as $producto) {
    if (!empty($producto['destacado'])) {
        $productosDestacados[] = $producto;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php require __DIR__ . '/includes/head.php'; ?>
</head>

<body>

    <?php include 'includes/navbar.php'; ?>

    <div id="carouselExampleCaptions" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="4000">
                <img src="Assets/Carusel/SungjinLpWhite.png" class="d-block w-100" alt="Modelo 1">
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                    <h2 class="fw-bold">Modelos que ya conoces</h2>
                    <p>Encuentra tu sonido ideal con nuestros clásicos.</p>
                </div>
            </div>
            <div class="carousel-item" data-bs-interval="4000">
                <img src="Assets/Carusel/JaeTeleDeluxe.png" class="d-block w-100" alt="Modelo 2">
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                    <h2 class="fw-bold">De alta confiabilidad</h2>
                    <p>Instrumentos ajustados por profesionales.</p>
                </div>
            </div>
            <div class="carousel-item" data-bs-interval="4000">
                <img src="Assets/Carusel/JaeRelicStrat.png" class="d-block w-100" alt="Modelo 3">
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                    <h2 class="fw-bold">Y máxima durabilidad</h2>
                    <p>Materiales de primera calidad para toda la vida.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <div class="container mt-5">
        <h2 class="text-center mb-5 fw-bold"></i>Productos Destacados</h2>
        <div class="product-grid mb-5">
            <?php foreach ($productosDestacados as $producto): ?>
                <?php require __DIR__ . '/shop/productos/productos_card.php'; ?>
            <?php endforeach; ?>
        </div>

        <hr class="my-5">

        <h2 id="categorias" class="text-center mb-5 fw-bold">Nuestras Categorías</h2>
        <div class="category-grid mb-5">

            <?php require __DIR__ . '/shop/categoria/categoria_card.php'; ?>

        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

</body>

</html>
