<?php
// api_salas.php - Lista todas as salas
session_start();
include __DIR__ . '/../bd/bd.php';

header('Content-Type: application/json');

try {
    $stmt = $conexao->prepare("SELECT id, nome, categoria_id FROM salas ORDER BY nome");
    $stmt->execute();
    $result = $stmt->get_result();
    $salas = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($salas);
} catch (Exception $e) {
    echo json_encode(['erro' => 'Erro ao carregar salas: ' . $e->getMessage()]);
}
?>