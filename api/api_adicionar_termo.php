<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
require_once '../bd/bd.php';

// Proteção extra: Só alunos podem enviar termos
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'aluno') {
    echo json_encode(["sucesso" => false, "erro" => "Acesso negado."]);
    exit;
}

// Pega os dados JSON do formulário
$dados = json_decode(file_get_contents("php://input"));

if (!empty($dados->palavra) && !empty($dados->descricao) && !empty($dados->categoria_id)) {

    $palavra = trim($dados->palavra);
    $descricao = trim($dados->descricao);
    $categoria_id = intval($dados->categoria_id);
    $usuario_id = $_SESSION['usuario_id']; // O banco sabe quem enviou pelo ID da sessão!
    $status = 'pendente'; // Sempre entra como pendente até o professor aprovar

    // Prepara a inserção no banco
    $sql = "INSERT INTO termos (palavra, descricao, status, categoria_id, usuario_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssii", $palavra, $descricao, $status, $categoria_id, $usuario_id);

        if ($stmt->execute()) {
            echo json_encode(["sucesso" => true, "mensagem" => "Termo enviado com sucesso! Aguarde a aprovação do professor."]);
        } else {
            echo json_encode(["sucesso" => false, "erro" => "Erro ao salvar no banco de dados."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["sucesso" => false, "erro" => "Erro interno no servidor."]);
    }
} else {
    echo json_encode(["sucesso" => false, "erro" => "Por favor, preencha todos os campos."]);
}

$conexao->close();
