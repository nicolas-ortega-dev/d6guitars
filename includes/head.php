<?php
// Define valores por defecto para el título y las rutas públicas.
$pageTitle = $pageTitle ?? 'D6guitars';
$basePath = $basePath ?? '';

// Escapa la ruta base antes de imprimirla en atributos HTML.
$baseHref = htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8');
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" defer></script>
<link rel="stylesheet" href="<?php echo $baseHref; ?>style.css">
<script>window.D6_BASE_PATH = <?php echo json_encode($basePath); ?>;</script>
<script src="<?php echo $baseHref; ?>main.js" defer></script>
