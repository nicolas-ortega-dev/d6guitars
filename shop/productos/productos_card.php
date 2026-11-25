<?php
// Obtiene la imagen del producto o deja una ruta vacía si no existe.
$imagePath = $producto['imagen'] ?? '';

// Agrega la ruta base cuando la imagen es local y no empieza con slash.
if (!preg_match('/^(https?:)?\/\//', $imagePath) && substr($imagePath, 0, 1) !== '/') {
    $imagePath = ($basePath ?? '') . $imagePath;
}

// Escapa los datos antes de imprimirlos en HTML y atributos.
$productName = htmlspecialchars($producto['nombre'] ?? 'Sin nombre', ENT_QUOTES, 'UTF-8');
$productDescription = htmlspecialchars($producto['descripcion'] ?? '', ENT_QUOTES, 'UTF-8');
$productModalDescription = htmlspecialchars($producto['descripcion_modal'] ?? ($producto['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8');
$productPrice = htmlspecialchars($producto['precio'] ?? '0.00', ENT_QUOTES, 'UTF-8');
$productImage = htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8');
$productAlt = htmlspecialchars($producto['alt'] ?? ($producto['nombre'] ?? 'Producto'), ENT_QUOTES, 'UTF-8');
?>
<div class="product-card">
    <div class="product-img-wrapper">
        <img class="product-photo" src="<?php echo $productImage; ?>" alt="<?php echo $productAlt; ?>">
    </div>
    <div class="product-info">
        <h3 class="product-title"><?php echo $productName; ?></h3>
        <p class="descripcion"><?php echo $productDescription; ?></p>
        <p class="price">$<?php echo $productPrice; ?></p>
        <button type="button" class="btn btn-dark btn-details mt-auto w-100"
            data-bs-toggle="modal" data-bs-target="#productModal"
            data-name="<?php echo $productName; ?>"
            data-desc="<?php echo $productModalDescription; ?>"
            data-price="<?php echo $productPrice; ?>"
            data-img="<?php echo $productImage; ?>">
            Ver Detalles
        </button>
    </div>
</div>
