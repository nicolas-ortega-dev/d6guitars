<?php
// Usa la sesión para mantener el carrito entre páginas.
session_start();

// Guarda el carrito recibido desde JavaScript.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lee y decodifica el JSON enviado en el cuerpo de la petición.
    $json = file_get_contents('php://input');
    $cartData = json_decode($json, true);
    
    // Actualiza la sesión solo si el contenido recibido es un arreglo.
    if (is_array($cartData)) {
        $_SESSION['d6guitars_cart'] = $cartData;
    }
    
    echo json_encode(['status' => 'success']);
    exit;
}

// Devuelve el carrito guardado cuando JavaScript lo solicita.
if (isset($_GET['action']) && $_GET['action'] === 'get') {
    header('Content-Type: application/json');
    // Responde con el carrito existente o con un arreglo vacío.
    echo json_encode(isset($_SESSION['d6guitars_cart']) ? $_SESSION['d6guitars_cart'] : []);
    exit;
}
?>
