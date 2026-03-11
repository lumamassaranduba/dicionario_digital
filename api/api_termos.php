<?php
session_start();
require_once '../bd/bd.php';
header("Content-Type: application/json; charset=UTF-8");


$categoria_id = isset($_GET['categoria']) ? intval($_GET['categoria']) : 1;

$sql = "SELECT t.id, t.palavra, t.descricao, u.nome AS enviado_por 
        FROM termos t
        JOIN usuarios u ON t.usuario_id = u.id
        WHERE t.categoria_id = $categoria_id AND t.status = 'aprovado'";

$resultado = $conexao->query($sql);
$termos = array();

if ($resultado->num_rows > 0) {
    while($linha = $resultado->fetch_assoc()) {
        $termos[] = $linha;
    }
}

echo json_encode($termos);
$conexao->close();
?>