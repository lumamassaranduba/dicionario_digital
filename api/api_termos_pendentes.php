<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
require_once '../bd/bd.php';

// Proteção extra: Só deixa rodar se for um professor logado
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor') {
    echo json_encode(["erro" => "Acesso negado."]);
    exit;
}

// Pega o ID da categoria que o Javascript enviou pela URL
$categoria_id = isset($_GET['categoria_id']) ? intval($_GET['categoria_id']) : 0;

// Busca apenas termos 'pendentes' daquela categoria
$sql = "SELECT t.id, t.palavra, t.descricao, u.nome AS nome_aluno 
        FROM termos t
        JOIN usuarios u ON t.usuario_id = u.id
        WHERE t.categoria_id = $categoria_id AND t.status = 'pendente'";

$resultado = $conexao->query($sql);
$termos = array();

if ($resultado && $resultado->num_rows > 0) {
    while ($linha = $resultado->fetch_assoc()) {
        $termos[] = $linha;
    }
}

echo json_encode($termos);
$conexao->close();
