<?php
session_start();
require_once '../bd/bd.php';

header("Content-Type: application/json");

$dados = json_decode(file_get_contents("php://input"));

if(
    !empty($dados->palavra) && 
    !empty($dados->descricao) && 
    !empty($dados->categoria_id) && 
    !empty($dados->usuario_id)
) {
    
    $sql = "INSERT INTO termos (palavra, descricao, status, categoria_id, usuario_id) VALUES (?, ?, 'pendente', ?, ?)";
    
  
    $stmt = $conexao->prepare($sql);
    
    if($stmt) {
       
        $stmt->bind_param("ssii", $dados->palavra, $dados->descricao, $dados->categoria_id, $dados->usuario_id);
        
        
        if($stmt->execute()) {
            http_response_code(201); 
            echo json_encode(array("mensagem" => "Termo enviado com sucesso! Aguardando aprovação do professor."));
        } else {
            http_response_code(503); 
            echo json_encode(array("erro" => "Não foi possível salvar o termo."));
        }
        
        $stmt->close();
    } else {
        http_response_code(500);
        echo json_encode(array("erro" => "Erro interno ao preparar a query."));
    }
} else {
   
    http_response_code(400); 
    echo json_encode(array("erro" => "Dados incompletos. Preencha todos os campos obrigatórios."));
}

$conexao->close();
?>