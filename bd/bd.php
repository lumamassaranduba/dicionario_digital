<?php
// 2. Configurações de conexão com o Banco de Dados
$host = "localhost";
$usuario = "root";  // Padrão do XAMPP
$senha = "";        // Padrão do XAMPP (geralmente vazio)
$banco = "dicionario_tecnico";

// 3. Criando a conexão
$conexao = new mysqli($host, $usuario, $senha, $banco);

// Verifica se deu algum erro na conexão
if ($conexao->connect_error) {
    echo json_encode(["erro" => "Falha na conexão com o banco de dados."]);
    exit;
}

// Garante que os acentos (ç, ã, é) não fiquem desconfigurados
$conexao->set_charset("utf8mb4");
?>