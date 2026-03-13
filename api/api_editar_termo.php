<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
require_once '../bd/bd.php';

// Apenas professores
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor') {
    echo json_encode(["sucesso" => false, "erro" => "Acesso negado."]);
    exit;
}

$dados = json_decode(file_get_contents('php://input'));
if (empty($dados->id) || empty($dados->palavra) || empty($dados->descricao) || empty($dados->categoria_id)) {
    echo json_encode(["sucesso" => false, "erro" => "Dados incompletos."]);
    exit;
}

$id = intval($dados->id);
$palavra = trim($dados->palavra);
$descricao = trim($dados->descricao);
$categoria_id = intval($dados->categoria_id);

if ($palavra === '' || $descricao === '') {
    echo json_encode(["sucesso" => false, "erro" => "Palavra e descrição não podem ser vazias."]);
    exit;
}

$sql = "UPDATE termos SET palavra = ?, descricao = ?, categoria_id = ? WHERE id = ?";
$stmt = $conexao->prepare($sql);
if ($stmt) {
    $stmt->bind_param('ssii', $palavra, $descricao, $categoria_id, $id);
    if ($stmt->execute()) {
        echo json_encode(["sucesso" => true, "mensagem" => "Termo atualizado."]);
    } else {
        echo json_encode(["sucesso" => false, "erro" => "Erro ao atualizar no banco." ]);
    }
    $stmt->close();
} else {
    echo json_encode(["sucesso" => false, "erro" => "Erro interno no servidor."]);
}

$conexao->close();
?>
