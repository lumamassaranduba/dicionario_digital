<?php
// api_criar_sala.php - Professores criam novas salas
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor') {
    echo json_encode(['erro' => 'Acesso negado.']);
    exit;
}

include __DIR__ . '/bd/bd.php';

header('Content-Type: application/json');

$nome = trim($_POST['nome'] ?? '');
$categoria_id = intval($_POST['categoria_id'] ?? 0);

if (empty($nome) || $categoria_id <= 0) {
    echo json_encode(['erro' => 'Nome e categoria são obrigatórios.']);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO salas (nome, categoria_id) VALUES (?, ?)");
    $stmt->bind_param("si", $nome, $categoria_id);
    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Sala criada com sucesso.']);
    } else {
        echo json_encode(['erro' => 'Erro ao criar sala.']);
    }
} catch (Exception $e) {
    echo json_encode(['erro' => 'Erro: ' . $e->getMessage()]);
}
?>