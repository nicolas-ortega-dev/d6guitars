<?php
// Inicia la sesión si todavía no está activa.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define la ruta base porque esta página está dentro de subcarpetas.
$basePath = '../../';

// Bloquea el acceso cuando no hay usuario autenticado.
if (!isset($_SESSION['usuario_id'])) {
    header("Location:  ../../login.php");
    exit();
}

// Obtiene el slug de categoría desde una variable previa o desde la URL.
$slug = $categoriaSlug ?? ($_GET['slug'] ?? '');

// Lee los archivos JSON necesarios para categorías y productos.
$categoriasJson = file_get_contents(__DIR__ . '/../../backend/categorias.json');
$productosJson = file_get_contents(__DIR__ . '/../../backend/productos.json');

// Detiene la ejecución si alguno de los archivos no se puede leer.
if ($categoriasJson === false || $productosJson === false) {
    die("No se pudo leer el archivo.");
}

// Convierte y valida el JSON de categorías.
$categoriasData = json_decode($categoriasJson, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("JSON Invalido: " . json_last_error_msg());
}

// Convierte y valida el JSON de productos.
$productosData = json_decode($productosJson, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("JSON Invalido: " . json_last_error_msg());
}

// Comprueba que ambos JSON tengan la estructura esperada.
if (!isset($categoriasData['categorias'], $productosData['productos']) || !is_array($categoriasData['categorias']) || !is_array($productosData['productos'])) {
    die("La estructura del JSON es incorrecta");
}

// Busca la categoría que coincide con el slug recibido.
$categoriaActual = null;
foreach ($categoriasData['categorias'] as $categoria) {
    if (($categoria['slug'] ?? '') === $slug) {
        $categoriaActual = $categoria;
        break;
    }
}

// Responde con 404 si la categoría solicitada no existe.
if ($categoriaActual === null) {
    http_response_code(404);
    die("Categoria no encontrada.");
}

// Filtra los productos que pertenecen a la categoría actual.
$productos = [];
foreach ($productosData['productos'] as $producto) {
    if (($producto['categoria_slug'] ?? '') === $slug) {
        $productos[] = $producto;
    }
}

// Define textos usados por la vista y por la etiqueta title.
$categoriaNombre = $categoriaActual['nombre'];
$tituloPagina = $categoriaActual['titulo'] ?? $categoriaNombre;
$pageTitle = 'D6guitars | ' . $categoriaNombre;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require __DIR__ . '/../../includes/head.php'; ?>
</head>

<body>
    <?php require __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="container mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../index.php" class="text-decoration-none text-danger">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($categoriaNombre, ENT_QUOTES, 'UTF-8'); ?></li>
            </ol>
        </nav>

        <h1 class="text-center mb-5 fw-bold"><?php echo htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8'); ?></h1>

        <div class="product-grid mb-5">
            <?php foreach ($productos as $producto): ?>
                <?php require __DIR__ . '/../productos/productos_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <?php require __DIR__ . '/../../includes/footer.php'; ?>
</body>


</html>
