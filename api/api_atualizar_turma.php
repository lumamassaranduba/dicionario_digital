<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
require_once '../bd/bd.php';

// Só professores podem atualizar
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor') {
    echo json_encode(["sucesso" => false, "erro" => "Acesso negado."]);
    exit;
}

$dados = json_decode(file_get_contents('php://input'));

if (empty($dados->id)) {
    echo json_encode(["sucesso" => false, "erro" => "ID da turma é obrigatório."]);
    exit;
}

$id = intval($dados->id);
$nome = isset($dados->nome) ? trim($dados->nome) : null;
$senha = isset($dados->senha) ? trim($dados->senha) : null;

// Build dynamic SQL depending on provided fields
$fields = [];
$types = '';
$params = [];

if ($nome !== null) {
    if ($nome === '') {
        echo json_encode(["sucesso" => false, "erro" => "Nome não pode ser vazio."]);
        exit;
    }
    $fields[] = 'nome = ?';
    $types .= 's';
    $params[] = $nome;
}

if ($senha !== null) {
    if ($senha === '') {
        echo json_encode(["sucesso" => false, "erro" => "Senha não pode ser vazia."]);
        exit;
    }
    $fields[] = 'senha = ?';
    $types .= 's';
    $params[] = $senha;
}

if (count($fields) === 0) {
    echo json_encode(["sucesso" => false, "erro" => "Nada a atualizar."]);
    exit;
}

$sql = "UPDATE usuarios SET " . implode(', ', $fields) . " WHERE id = ? AND tipo = 'aluno'";
$types .= 'i';
$params[] = $id;

$stmt = $conexao->prepare($sql);
if ($stmt) {
    // bind_param requires references
    $bind_names[] = $types;
    for ($i=0; $i<count($params); $i++) {
        $bind_names[] = &$params[$i];
    }
    call_user_func_array(array($stmt, 'bind_param'), $bind_names);

    if ($stmt->execute()) {
        echo json_encode(["sucesso" => true, "mensagem" => "Turma atualizada."]);
    } else {
        echo json_encode(["sucesso" => false, "erro" => "Erro ao atualizar no banco." ]);
    }
    $stmt->close();
} else {
    echo json_encode(["sucesso" => false, "erro" => "Erro interno no servidor."]);
}

$conexao->close();
?>
