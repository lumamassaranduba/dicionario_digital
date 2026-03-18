<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Origin: *");
require_once '../bd/bd.php';

// Proteção extra: Só alunos ou professores podem enviar termos
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo'], ['aluno', 'professor'])) {
    echo json_encode(["sucesso" => false, "erro" => "Acesso negado."]);
    exit;
}

try {
    // Log para debug
    file_put_contents('../debug.log', date('Y-m-d H:i:s') . " - Iniciando API\n", FILE_APPEND);

    // Pega os dados do formulário multipart
    if (!empty($_POST['palavra']) && !empty($_POST['descricao']) && !empty($_POST['categoria_id']) && !empty($_POST['exemplo'])) {
        file_put_contents('../debug.log', "Campos preenchidos: " . $_POST['palavra'] . "\n", FILE_APPEND);
        file_put_contents('../debug.log', "Categoria: " . $_POST['categoria_id'] . ", Usuario: " . $_SESSION['usuario_id'] . "\n", FILE_APPEND);

        $palavra = trim($_POST['palavra']);
        $descricao = trim($_POST['descricao']);
        $exemplo = trim($_POST['exemplo']);
        $categoria_id = intval($_POST['categoria_id']);
        $usuario_id = $_SESSION['usuario_id'];
        // Sempre criar como pendente para que o termo vá para a fila de aprovação,
        // mesmo que quem esteja criando seja professor.
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
                file_put_contents('../debug.log', "Imagem salva: $imagem\n", FILE_APPEND);
            } else {
                file_put_contents('../debug.log', "Erro upload imagem\n", FILE_APPEND);
                echo json_encode(["sucesso" => false, "erro" => "Erro ao salvar imagem."]);
                exit;
            }
        } else {
            file_put_contents('../debug.log', "Imagem não enviada ou erro: " . ($_FILES['imagem']['error'] ?? 'não set') . "\n", FILE_APPEND);
        }

        // Prepara a inserção no banco
        file_put_contents('../debug.log', "Preparando SQL\n", FILE_APPEND);
        $sql = "INSERT INTO termos (palavra, descricao, exemplo, imagem, status, categoria_id, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssssii", $palavra, $descricao, $exemplo, $imagem, $status, $categoria_id, $usuario_id);

            if ($stmt->execute()) {
                file_put_contents('../debug.log', "Inserido com sucesso\n", FILE_APPEND);
                echo json_encode(["sucesso" => true, "mensagem" => "Termo enviado com sucesso! Aguarde a aprovação do professor."]);
            } else {
                file_put_contents('../debug.log', "Erro execute: " . $stmt->error . "\n", FILE_APPEND);
                echo json_encode(["sucesso" => false, "erro" => "Erro ao salvar no banco de dados."]);
            }
            $stmt->close();
        } else {
            file_put_contents('../debug.log', "Erro prepare: " . $conexao->error . "\n", FILE_APPEND);
            echo json_encode(["sucesso" => false, "erro" => "Erro interno no servidor."]);
        }
    } else {
        file_put_contents('../debug.log', "Campos não preenchidos\n", FILE_APPEND);
        echo json_encode(["sucesso" => false, "erro" => "Por favor, preencha todos os campos."]);
    }
} catch (Exception $e) {
    file_put_contents('../debug.log', "Erro catch: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(["sucesso" => false, "erro" => "Erro interno: " . $e->getMessage()]);
}

$conexao->close();
?>
