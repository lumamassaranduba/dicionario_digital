<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
require_once '../bd/bd.php';

// Só professores podem atualizar a própria conta aqui
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor') {
    echo json_encode(["sucesso" => false, "erro" => "Acesso negado."]);
    exit;
}

$dados = json_decode(file_get_contents('php://input'));

$email = isset($dados->email) ? trim($dados->email) : '';
$senha = isset($dados->senha) ? trim($dados->senha) : '';

if ($email === '') {
    echo json_encode(["sucesso" => false, "erro" => "E-mail não pode ficar vazio."]);
    exit;
}

$usuarioId = intval($_SESSION['usuario_id']);

// Verifica se já existe outro usuário com esse email
$sqlCheck = "SELECT id FROM usuarios WHERE email = ? AND id <> ? LIMIT 1";
$stmtCheck = $conexao->prepare($sqlCheck);
if ($stmtCheck) {
    $stmtCheck->bind_param('si', $email, $usuarioId);
    $stmtCheck->execute();
    $res = $stmtCheck->get_result();
    if ($res && $res->num_rows > 0) {
        echo json_encode(["sucesso" => false, "erro" => "E-mail já está em uso por outro usuário."]);
        $stmtCheck->close();
        $conexao->close();
        exit;
    }
    $stmtCheck->close();
}

// Monta query dinâmica: atualiza email e opcionalmente senha
$fields = [];
$types = '';
$params = [];

$fields[] = 'email = ?'; $types .= 's'; $params[] = $email;
if ($senha !== '') { $fields[] = 'senha = ?'; $types .= 's'; $params[] = $senha; }

$sql = "UPDATE usuarios SET " . implode(', ', $fields) . " WHERE id = ? AND tipo = 'professor'";
$types .= 'i'; $params[] = $usuarioId;

$stmt = $conexao->prepare($sql);
if ($stmt) {
    $bind_names[] = $types;
    for ($i=0; $i<count($params); $i++) { $bind_names[] = &$params[$i]; }
    call_user_func_array(array($stmt, 'bind_param'), $bind_names);
    if ($stmt->execute()) {
        // Atualiza o dado de sessão (nome/email) se necessário
        $_SESSION['email'] = $email;
        echo json_encode(["sucesso" => true, "mensagem" => "Conta atualizada com sucesso."]);
    } else {
        echo json_encode(["sucesso" => false, "erro" => "Erro ao atualizar no banco." ]);
    }
    $stmt->close();
} else {
    echo json_encode(["sucesso" => false, "erro" => "Erro interno no servidor."]);
}

$conexao->close();
?>
