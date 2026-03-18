<?php
session_start();
require_once '../bd/bd.php';
header("Content-Type: application/json; charset=UTF-8");

// apenas professores podem gerenciar essa listagem
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor') {
    echo json_encode(["sucesso" => false, "erro" => "Acesso negado."]);
    exit;
}

$sql = "SELECT t.id, t.palavra, t.descricao, t.exemplo, t.imagem, t.categoria_id, c.nome AS categoria, u.nome AS enviado_por
        FROM termos t
        JOIN usuarios u ON t.usuario_id = u.id
        LEFT JOIN categorias c ON t.categoria_id = c.id
        WHERE t.status = 'aprovado'
        ORDER BY t.palavra ASC";

$resultado = $conexao->query($sql);
$termos = [];
if ($resultado) {
    while ($linha = $resultado->fetch_assoc()) {
        $termos[] = $linha;
    }
}

echo json_encode($termos);
$conexao->close();
?>
