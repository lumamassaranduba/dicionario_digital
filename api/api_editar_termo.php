<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Origin: *");
require_once '../bd/bd.php';

// Apenas professores
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor') {
    echo json_encode(["sucesso" => false, "erro" => "Acesso negado."]);
    exit;
}

if (!isset($_POST['id']) || !isset($_POST['palavra']) || !isset($_POST['descricao']) || !isset($_POST['categoria_id']) || !isset($_POST['exemplo'])) {
    echo json_encode(["sucesso" => false, "erro" => "Dados incompletos."]);
    exit;
}

$id = intval($_POST['id']);
$palavra = trim($_POST['palavra']);
$descricao = trim($_POST['descricao']);
$exemplo = trim($_POST['exemplo']);
$categoria_id = intval($_POST['categoria_id']);

if ($palavra === '' || $descricao === '' || $exemplo === '') {
    echo json_encode(["sucesso" => false, "erro" => "Palavra, descrição e exemplo não podem ser vazios."]);
    exit;
}

// Lidar com upload de imagem opcional
$imagemUpdate = '';
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $fileName = uniqid() . '_' . basename($_FILES['imagem']['name']);
    $uploadFile = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $uploadFile)) {
        $imagemUpdate = ", imagem = 'uploads/$fileName'";
    } else {
        echo json_encode(["sucesso" => false, "erro" => "Erro ao salvar nova imagem."]);
        exit;
    }
}

$sql = "UPDATE termos SET palavra = ?, descricao = ?, exemplo = ?, categoria_id = ? $imagemUpdate WHERE id = ? AND categoria_id = ?";
$stmt = $conexao->prepare($sql);
if ($stmt) {
    $categoria_professor = $_SESSION['categoria_id'];
    if ($imagemUpdate) {
        $stmt->bind_param('sssiis', $palavra, $descricao, $exemplo, $categoria_id, $id, $categoria_professor);
    } else {
        $stmt->bind_param('sssiii', $palavra, $descricao, $exemplo, $categoria_id, $id, $categoria_professor);
    }
    if ($stmt->execute()) {
        echo json_encode(["sucesso" => true, "mensagem" => "Termo atualizado."]);
    } else {
        echo json_encode(["sucesso" => false, "erro" => "Erro ao atualizar no banco."]);
    }
    $stmt->close();
} else {
    echo json_encode(["sucesso" => false, "erro" => "Erro interno no servidor."]);
}

$conexao->close();
?>
