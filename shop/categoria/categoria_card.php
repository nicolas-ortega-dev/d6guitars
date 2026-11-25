<?php
// Evita mostrar categorías a usuarios sin sesión.
if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . ($basePath ?? '') . "login.php");
    exit();
}

// Lee el JSON que contiene las categorías del sitio.
$jsonString = file_get_contents(__DIR__ . "/../../backend/categorias.json");

// Detiene la ejecución si el archivo no se puede leer.
if ($jsonString === false) {
    die("No se pudo leer el archivo.");
}

// Convierte el JSON a un arreglo asociativo.
$data = json_decode($jsonString, true);

// Valida que el JSON sea correcto.
if (json_last_error() !== JSON_ERROR_NONE) {
    die("JSON Invalido: " . json_last_error_msg());
}

// Comprueba que exista el arreglo de categorías esperado.
if (!isset($data['categorias']) || !is_array($data['categorias'])) {
    die("La estructura del JSON es incorrecta");
}

// Guarda las categorías para renderizar una tarjeta por cada una.
$categorias = $data['categorias'];
?>
<?php foreach ($categorias as $categoria): ?>
    <?php
    // Escapa los valores de cada categoría antes de imprimirlos.
    $categoryHref = htmlspecialchars(($basePath ?? '') . ($categoria['href'] ?? '#'), ENT_QUOTES, 'UTF-8');
    $categoryImage = htmlspecialchars(($basePath ?? '') . ($categoria['imageUrl'] ?? ''), ENT_QUOTES, 'UTF-8');
    $categoryName = htmlspecialchars($categoria['nombre'] ?? 'Sin nombre', ENT_QUOTES, 'UTF-8');
    ?>
    <a href="<?php echo $categoryHref; ?>" class="category-card-link">
    <div class="category-card bg-dark text-white text-center d-flex align-items-center justify-content-center" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('<?php echo $categoryImage; ?>');">
        <h3 class="fw-bold display-6"><?php echo $categoryName; ?></h3>
    </div>
</a>
<?php endforeach; ?>
