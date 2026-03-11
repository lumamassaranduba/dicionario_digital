<?php
// Inicia a sessão para podermos guardar os dados de quem logou
session_start();

// Define que a resposta será em formato JSON
header("Content-Type: application/json; charset=UTF-8");

// Puxa a sua conexão com o banco de dados
require_once '../bd/bd.php';

// Verifica se os dados vieram via POST (como configuramos no JavaScript)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Pega o email e senha digitados
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';

    // Verifica se os campos não estão vazios
    if (empty($email) || empty($senha)) {
        echo json_encode(array("sucesso" => false, "erro" => "Por favor, preencha e-mail e senha."));
        exit;
    }

    // Prepara a consulta SQL para buscar o usuário pelo email
    // Adicionei o categoria_id aqui para puxar a matéria do professor!
    $sql = "SELECT id, nome, email, senha, tipo, categoria_id FROM usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        // Se encontrou um usuário com esse email
        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();

            // VERIFICAÇÃO DE SENHA (comparando texto puro conforme o seu banco)
            if ($senha === $usuario['senha']) {

                // LOGIN APROVADO! Guarda as informações na Sessão do PHP
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nome']       = $usuario['nome'];
                $_SESSION['tipo']       = $usuario['tipo']; // 'aluno' ou 'professor'
                $_SESSION['categoria_id'] = $usuario['categoria_id']; // Guarda a matéria do professor!

                // ==========================================
                // AQUI ESTÁ A CORREÇÃO DOS NOMES DAS PÁGINAS!
                // ==========================================
                if ($usuario['tipo'] === 'professor') {
                    $pagina_destino = 'dashboard_professor.php'; // Nome atualizado!
                } else {
                    $pagina_destino = 'dashboard_aluno.php'; // Nome atualizado!
                }

                // Devolve para o JavaScript um JSON dizendo que deu certo e para onde ir
                echo json_encode(array(
                    "sucesso" => true,
                    "redirecionar" => $pagina_destino
                ));
            } else {
                // Senha errada
                echo json_encode(array("sucesso" => false, "erro" => "Senha incorreta. Tente novamente."));
            }
        } else {
            // E-mail não encontrado no banco
            echo json_encode(array("sucesso" => false, "erro" => "Usuário não encontrado."));
        }
        $stmt->close();
    } else {
        echo json_encode(array("sucesso" => false, "erro" => "Erro interno no servidor."));
    }
} else {
    echo json_encode(array("sucesso" => false, "erro" => "Método não permitido."));
}

$conexao->close();
