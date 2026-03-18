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
if (empty($dados->id)) {
    echo json_encode(["sucesso" => false, "erro" => "ID é obrigatório."]);
    exit;
}

$id = intval($dados->id);

$categoria_professor = $_SESSION['categoria_id'];

$sql = "DELETE FROM termos WHERE id = ? AND categoria_id = ?";
$stmt = $conexao->prepare($sql);
if ($stmt) {
    $stmt->bind_param('ii', $id, $categoria_professor);
    if ($stmt->execute()) {
        echo json_encode(["sucesso" => true, "mensagem" => "Termo excluído."]);
    } else {
        echo json_encode(["sucesso" => false, "erro" => "Erro ao deletar no banco."]);
    }
    $stmt->close();
} else {
    echo json_encode(["sucesso" => false, "erro" => "Erro interno no servidor."]);
}

$conexao->close();
?>
