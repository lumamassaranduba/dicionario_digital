<?php
session_start();
require_once '../bd/bd.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$dados = json_decode(file_get_contents("php://input"));

if(
    !empty($dados->palavra) && 
    !empty($dados->descricao) && 
    !empty($dados->categoria_id) && 
    !empty($dados->usuario_id)
) {
    // Monta o SQL usando '?' para proteger contra SQL Injection (Prepared Statement)
    // Nota: O status vai automaticamente como 'pendente'
    $sql = "INSERT INTO termos (palavra, descricao, status, categoria_id, usuario_id) VALUES (?, ?, 'pendente', ?, ?)";
    
    // Prepara a query
    $stmt = $conexao->prepare($sql);
    
    if($stmt) {
        // O "ssii" significa que estamos enviando: String, String, Inteiro, Inteiro
        $stmt->bind_param("ssii", $dados->palavra, $dados->descricao, $dados->categoria_id, $dados->usuario_id);
        
        // Executa a query no banco
        if($stmt->execute()) {
            http_response_code(201); // 201 significa "Criado com sucesso"
            echo json_encode(array("mensagem" => "Termo enviado com sucesso! Aguardando aprovação do professor."));
        } else {
            http_response_code(503); // 503 significa "Serviço indisponível/Erro no banco"
            echo json_encode(array("erro" => "Não foi possível salvar o termo."));
        }
        
        $stmt->close();
    } else {
        http_response_code(500);
        echo json_encode(array("erro" => "Erro interno ao preparar a query."));
    }
} else {
    // Se faltou algum dado (ex: esqueceu de preencher a palavra)
    http_response_code(400); // 400 significa "Requisição mal feita"
    echo json_encode(array("erro" => "Dados incompletos. Preencha todos os campos obrigatórios."));
}

$conexao->close();
?>