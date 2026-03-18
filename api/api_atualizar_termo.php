<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
require_once '../bd/bd.php';

// Proteção extra: Só deixa rodar se for um professor logado
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor') {
    echo json_encode(["sucesso" => false, "erro" => "Acesso negado."]);
    exit;
}

// Recebe os dados JSON que o Front-end enviou (ID do termo e novo status)
$dados = json_decode(file_get_contents("php://input"));

if (!empty($dados->id) && !empty($dados->status)) {
    $id = intval($dados->id);
    $status = $dados->status;

    // Validação de segurança: garante que o status só pode ser 'aprovado' ou 'rejeitado'
    if ($status !== 'aprovado' && $status !== 'rejeitado') {
        echo json_encode(["sucesso" => false, "erro" => "Status inválido."]);
        exit;
    }

    // Atualiza o banco de dados usando prepared statements (segurança máxima)
    $categoria_professor = $_SESSION['categoria_id'];
    $sql = "UPDATE termos SET status = ? WHERE id = ? AND categoria_id = ?";
    $stmt = $conexao->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sii", $status, $id, $categoria_professor);
        if ($stmt->execute()) {
            echo json_encode(["sucesso" => true, "mensagem" => "Status atualizado!"]);
        } else {
            echo json_encode(["sucesso" => false, "erro" => "Erro ao atualizar no banco de dados."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["sucesso" => false, "erro" => "Erro interno no servidor."]);
    }
} else {
    echo json_encode(["sucesso" => false, "erro" => "Dados incompletos."]);
}

$conexao->close();
