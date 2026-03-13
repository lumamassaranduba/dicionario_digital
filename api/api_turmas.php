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
$sql = "SELECT id, nome, email, senha FROM usuarios WHERE tipo = 'aluno' ORDER BY nome ASC";
$resultado = $conexao->query($sql);
if ($resultado) {
    while ($linha = $resultado->fetch_assoc()) {
        $turmas[] = $linha;
    }
}

echo json_encode($turmas);
$conexao->close();
?>
