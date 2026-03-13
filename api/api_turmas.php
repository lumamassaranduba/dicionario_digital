<?php
session_start();
require_once '../bd/bd.php';
header("Content-Type: application/json; charset=UTF-8");

// Só professores podem listar/gerenciar turmas
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor') {
    echo json_encode(["sucesso" => false, "erro" => "Acesso negado."]);
    exit;
}

$turmas = [];
$sql = "SELECT t.id, t.nome, t.email, t.senha, c.nome AS categoria FROM turmas t JOIN categorias c ON t.categoria_id = c.id ORDER BY t.nome ASC";
$resultado = $conexao->query($sql);
if ($resultado) {
    while ($linha = $resultado->fetch_assoc()) {
        $turmas[] = $linha;
    }
}

echo json_encode($turmas);
$conexao->close();
?>
