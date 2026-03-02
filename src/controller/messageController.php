<?php

use model\messageModel;

// Debe estar autenticado
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Unauthorized']));
}

header('Content-Type: application/json');
$model = new messageModel();
$user = $_SESSION['user'];

// ── GET → devolver historial ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 100;
    $messages = $model->getMessages($limit);
    die(json_encode($messages));
}

// ── POST → guardar nuevo mensaje ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    $msg = isset($body['message']) ? trim($body['message']) : '';

    if ($msg === '') {
        http_response_code(400);
        die(json_encode(['error' => 'Empty message']));
    }

    $id = $model->saveMessage($user, $msg);
    die(json_encode(['success' => (bool) $id, 'id' => $id]));
}

// ── PUT → editar mensaje propio ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $body = json_decode(file_get_contents('php://input'), true);
    $id = isset($body['id']) ? (int) $body['id'] : 0;
    $msg = isset($body['message']) ? trim($body['message']) : '';

    if ($id <= 0 || $msg === '') {
        http_response_code(400);
        die(json_encode(['error' => 'Invalid data']));
    }

    $ok = $model->updateMessage($id, $user, $msg);
    if (!$ok) {
        http_response_code(403);
        die(json_encode(['error' => 'Forbidden or not found']));
    }
    die(json_encode(['success' => true]));
}

// ── DELETE → borrar mensaje propio (soft delete) ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $body = json_decode(file_get_contents('php://input'), true);
    $id = isset($body['id']) ? (int) $body['id'] : 0;

    if ($id <= 0) {
        http_response_code(400);
        die(json_encode(['error' => 'Invalid id']));
    }

    $ok = $model->deleteMessage($id, $user);
    if (!$ok) {
        http_response_code(403);
        die(json_encode(['error' => 'Forbidden or not found']));
    }
    die(json_encode(['success' => true]));
}

http_response_code(405);
die(json_encode(['error' => 'Method not allowed']));
?>