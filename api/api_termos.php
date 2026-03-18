<?php
session_start();
require_once '../bd/bd.php';
header("Content-Type: application/json; charset=UTF-8");


$categoria_id = isset($_GET['categoria']) ? intval($_GET['categoria']) : 1;
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

// Busca por termos aprovados da categoria, opcionalmente filtrando por palavra/descrição
$sql = "SELECT t.id, t.palavra, t.descricao, s.nome AS enviado_por 
    , t.exemplo, t.imagem
        FROM termos t
        JOIN salas s ON t.sala_id = s.id
        WHERE t.categoria_id = ? AND t.status = 'aprovado'";

$params = [$categoria_id];
$types = 'i';

if ($q !== '') {
    $sql .= " AND (t.palavra LIKE ? OR t.descricao LIKE ?)";
    $qLike = "%{$q}%";
    $params[] = $qLike;
    $params[] = $qLike;
    $types .= 'ss';
}

$stmt = $conexao->prepare($sql);
$termos = array();

if ($stmt) {
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $resultado = $stmt->get_result();

    while ($linha = $resultado->fetch_assoc()) {
        $termos[] = $linha;
    }

    $stmt->close();
} else {
    // Em caso de erro no prepare, retorna lista vazia
}

echo json_encode($termos);
$conexao->close();
?>