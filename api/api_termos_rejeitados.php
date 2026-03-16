<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
require_once '../bd/bd.php';

// Proteção extra: Só deixa rodar se for um professor logado
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor') {
    echo json_encode(["erro" => "Acesso negado."]);
    exit;
}

$categoria_id = isset($_GET['categoria_id']) ? intval($_GET['categoria_id']) : 0;

$sql = "SELECT t.id, t.palavra, t.descricao, t.exemplo, t.imagem, t.categoria_id, u.nome AS nome_aluno
        FROM termos t
        JOIN usuarios u ON t.usuario_id = u.id
        WHERE t.categoria_id = ? AND t.status = 'rejeitado'";

$stmt = $conexao->prepare($sql);
$termos = [];
if ($stmt) {
    $stmt->bind_param("i", $categoria_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($linha = $res->fetch_assoc()) {
        $termos[] = $linha;
    }
    $stmt->close();
}

echo json_encode($termos);
$conexao->close();
