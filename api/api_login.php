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
                $_SESSION['email']      = $usuario['email'];
                $_SESSION['tipo']       = $usuario['tipo']; // 'aluno' ou 'professor'

                // Para professores, categoria_id direto
                // Para alunos, buscar da turma
                if ($usuario['tipo'] === 'professor') {
                    $_SESSION['categoria_id'] = $usuario['categoria_id'];
                } else {
                    // Buscar categoria da turma
                    $sql_turma = "SELECT categoria_id FROM turmas WHERE email = ?";
                    $stmt_turma = $conexao->prepare($sql_turma);
                    if ($stmt_turma) {
                        $stmt_turma->bind_param("s", $usuario['email']);
                        $stmt_turma->execute();
                        $res_turma = $stmt_turma->get_result();
                        if ($res_turma->num_rows > 0) {
                            $turma = $res_turma->fetch_assoc();
                            $_SESSION['categoria_id'] = $turma['categoria_id'];
                        } else {
                            $_SESSION['categoria_id'] = 1; // default
                        }
                        $stmt_turma->close();
                    } else {
                        $_SESSION['categoria_id'] = 1; // default
                    }
                }

                // ==========================================
                // DIRECIONA TODOS PARA A PÁGINA PRINCIPAL (index.php)
                // ==========================================
                // Agora o conteúdo muda dentro da mesma página, mantendo o menu sempre igual.
                $pagina_destino = 'index.php?view=home';

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
