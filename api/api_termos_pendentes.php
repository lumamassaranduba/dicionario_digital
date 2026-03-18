<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
require_once '../bd/bd.php';

// Proteção extra: Só deixa rodar se for um professor logado
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor') {
    echo json_encode(["erro" => "Acesso negado."]);
    exit;
}

// Usa a categoria vinculada ao professor logado (segurança extra)
$categoria_id = isset($_SESSION['categoria_id']) ? intval($_SESSION['categoria_id']) : 0;

// Se por algum motivo não existir categoria na sessão, tenta puxar da URL como fallback
if ($categoria_id === 0 && isset($_GET['categoria_id'])) {
    $categoria_id = intval($_GET['categoria_id']);
}

file_put_contents('../debug.log', date('Y-m-d H:i:s') . " - api_termos_pendentes.php: categoria_id(session) = " . ($_SESSION['categoria_id'] ?? 'null') . ", categoria_id(usa) = $categoria_id\n", FILE_APPEND);

// Busca apenas termos 'pendentes' daquela categoria
$sql = "SELECT t.id, t.palavra, t.descricao, s.nome AS nome_sala 
    , t.exemplo, t.imagem, t.usuario_id
        FROM termos t
        JOIN salas s ON t.sala_id = s.id
        WHERE t.categoria_id = ? AND t.status = 'pendente'
        ORDER BY t.id DESC";

$stmt = $conexao->prepare($sql);
$termos = array();

if ($stmt) {
    $stmt->bind_param("i", $categoria_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    while ($linha = $resultado->fetch_assoc()) {
        $termos[] = $linha;
    }
    $stmt->close();
    file_put_contents('../debug.log', "Encontrados " . count($termos) . " termos\n", FILE_APPEND);
}

echo json_encode($termos);
$conexao->close();
