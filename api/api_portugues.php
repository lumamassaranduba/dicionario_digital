<?php
// 1. Cabeçalhos obrigatórios para uma API
// Permite que o Front-end (HTML/JS) acesse essa página sem bloqueios (CORS)
header("Access-Control-Allow-Origin: *");
// Avisa ao navegador que a resposta será no formato JSON
header("Content-Type: application/json; charset=UTF-8");

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

// 4. O comando SQL
// Busca os termos de Português (id = 1) que estão aprovados
$sql = "SELECT t.id, t.palavra, t.descricao, u.nome AS enviado_por 
        FROM termos t
        JOIN usuarios u ON t.usuario_id = u.id
        WHERE t.categoria_id = 1 AND t.status = 'aprovado'";

$resultado = $conexao->query($sql);

// 5. Montando a resposta
$termos = array();

if ($resultado->num_rows > 0) {
    // Pega cada linha que o banco retornou e guarda no array
    while($linha = $resultado->fetch_assoc()) {
        $termos[] = $linha;
    }
}

// Converte o array do PHP para o formato JSON e imprime na tela
echo json_encode($termos);

// Fecha a conexão com o banco para economizar memória
$conexao->close();
?>