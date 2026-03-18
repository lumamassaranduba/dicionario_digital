<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
require_once '../bd/bd.php';

// Só professores podem usar essa API (pode ser ajustado para alunos também se desejar)
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor') {
    echo json_encode(["sucesso" => false, "erro" => "Acesso negado."]);
    exit;
}

$dados = json_decode(file_get_contents('php://input'));

if (!$dados || empty($dados->email) || empty($dados->senha_atual)) {
    echo json_encode(["sucesso" => false, "erro" => "Dados incompletos."]);
    exit;
}

$email = trim($dados->email);
$senhaAtual = trim($dados->senha_atual);
$novaSenha = isset($dados->nova_senha) ? trim($dados->nova_senha) : '';
$confirmarSenha = isset($dados->confirmar_senha) ? trim($dados->confirmar_senha) : '';

// Busca senha atual do usuário para validar
$sql = "SELECT senha FROM usuarios WHERE id = ? LIMIT 1";
$stmt = $conexao->prepare($sql);
if (!$stmt) {
    echo json_encode(["sucesso" => false, "erro" => "Erro interno no servidor."]);
    exit;
}

$stmt->bind_param('i', $_SESSION['usuario_id']);
$stmt->execute();
$res = $stmt->get_result();
$usuario = $res->fetch_assoc();
$stmt->close();

if (!$usuario || $usuario['senha'] !== $senhaAtual) {
    echo json_encode(["sucesso" => false, "erro" => "Senha atual incorreta."]);
    exit;
}

if ($novaSenha !== '') {
    if ($novaSenha !== $confirmarSenha) {
        echo json_encode(["sucesso" => false, "erro" => "A nova senha e a confirmação não conferem."]);
        exit;
    }
}

// Atualiza email (e senha, se enviado)
if ($novaSenha !== '') {
    $sql = "UPDATE usuarios SET email = ?, senha = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('ssi', $email, $novaSenha, $_SESSION['usuario_id']);
    }
} else {
    $sql = "UPDATE usuarios SET email = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('si', $email, $_SESSION['usuario_id']);
    }
}

if (!$stmt) {
    echo json_encode(["sucesso" => false, "erro" => "Erro interno no servidor."]);
    exit;
}

if ($stmt->execute()) {
    $_SESSION['nome'] = $_SESSION['nome']; // mantém
    echo json_encode(["sucesso" => true, "mensagem" => "Dados atualizados com sucesso."]);
} else {
    echo json_encode(["sucesso" => false, "erro" => "Erro ao salvar no banco de dados."]);
}
$stmt->close();
$conexao->close();
