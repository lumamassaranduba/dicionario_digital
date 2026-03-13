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

// Pega os dados do formulário multipart
if (!empty($_POST['palavra']) && !empty($_POST['descricao']) && !empty($_POST['categoria_id']) && !empty($_POST['exemplo']) && isset($_FILES['imagem'])) {

$palavra = trim($_POST['palavra']);
$descricao = trim($_POST['descricao']);
$exemplo = trim($_POST['exemplo']);
$categoria_id = intval($_POST['categoria_id']);
$usuario_id = $_SESSION['usuario_id'];
$status = 'pendente';

// Lidar com upload de imagem
$imagem = '';
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $fileName = uniqid() . '_' . basename($_FILES['imagem']['name']);
    $uploadFile = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $uploadFile)) {
        $imagem = 'uploads/' . $fileName;
    } else {
        echo json_encode(["sucesso" => false, "erro" => "Erro ao salvar imagem."]);
        exit;
    }
}

    // Prepara a inserção no banco
    $sql = "INSERT INTO termos (palavra, descricao, exemplo, imagem, status, categoria_id, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssssii", $palavra, $descricao, $exemplo, $imagem, $status, $categoria_id, $usuario_id);

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
} else {
    echo json_encode(["sucesso" => false, "erro" => "Por favor, preencha todos os campos."]);
}

$conexao->close();
