<?php
// 🛡️ SEGURANÇA: Verifica se é um aluno logado
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'aluno') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel da Sala - Tecna</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --azul-principal: #0d6efd;
            --sombra-leve: 0 4px 6px -1px rgba(13, 110, 253, 0.08), 0 2px 4px -1px rgba(13, 110, 253, 0.04);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f9;
        }

        .sidebar {
            width: 280px;
            box-shadow: var(--sombra-leve);
            z-index: 10;
            min-height: 100vh;
        }

        .nav-link {
            color: #6c757d;
            font-weight: 500;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
        }

        .nav-link:hover {
            background-color: rgba(13, 110, 253, 0.05);
            color: var(--azul-principal);
            transform: translateX(5px);
        }

        .nav-link.active {
            background-color: var(--azul-principal) !important;
            color: #fff !important;
            box-shadow: var(--sombra-leve);
        }

        .card-form {
            border: none;
            border-radius: 1rem;
            box-shadow: var(--sombra-leve);
        }
    </style>
</head>

<body class="d-flex flex-column flex-md-row">

    <nav class="sidebar bg-white d-flex flex-column p-4">
        <a href="index.php" class="d-flex align-items-center mb-4 text-decoration-none text-primary fs-4 fw-bold">
            <i class="bi bi-book-half me-2"></i> Tecna
        </a>

        <div class="mb-4 p-3 bg-light rounded text-center border">
            <small class="text-muted d-block">Logado como:</small>
            <strong class="text-dark fs-5"><?php echo $_SESSION['nome']; ?></strong>
            <span class="badge bg-success mt-2 d-block">Aluno</span>
        </div>

        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="dashboard_aluno.php" class="nav-link active">
                    <i class="bi bi-plus-circle me-2"></i> Enviar Termo
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php" class="nav-link">
                    <i class="bi bi-house-door me-2"></i> Ver Dicionário
                </a>
            </li>
        </ul>

        <hr>
        <div class="mt-auto">
            <a href="api/api_logout.php" class="btn btn-outline-danger w-100 fw-bold py-2">
                <i class="bi bi-box-arrow-right me-2"></i> Sair
            </a>
        </div>
    </nav>

    <main class="flex-grow-1 d-flex flex-column p-4 p-md-5" style="height: 100vh; overflow-y: auto;">

        <div class="w-100" style="max-width: 700px; margin: 0 auto;">
            <h2 class="fw-bold text-dark mb-2">Contribuir para o Dicionário</h2>
            <p class="text-muted mb-4">Adicione uma nova palavra. Ela passará pela aprovação do professor da matéria antes de ir para a página inicial.</p>

            <div class="card card-form p-4 bg-white">

                <div id="alerta-mensagem" class="alert d-none" role="alert"></div>

                <form id="formAdicionarTermo">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Palavra / Termo</label>
                        <input type="text" name="palavra" class="form-control form-control-lg" placeholder="Ex: Algoritmo" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Matéria (Categoria)</label>
                        <select name="categoria_id" class="form-select form-select-lg" required>
                            <option value="" disabled selected>Escolha a matéria...</option>
                            <option value="1">Português</option>
                            <option value="2">Matemática</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Significado</label>
                        <textarea name="descricao" class="form-control" rows="4" placeholder="Explique o que significa com suas palavras..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                        <i class="bi bi-send-fill me-2"></i> Enviar para o Professor
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('formAdicionarTermo').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const alerta = document.getElementById('alerta-mensagem');
            const botao = form.querySelector('button[type="submit"]');

            // Transforma os dados do formulário em JSON
            const formData = new FormData(form);
            const dadosObj = Object.fromEntries(formData.entries());

            botao.disabled = true;
            botao.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';

            try {
                const resposta = await fetch('api/api_adicionar_termo.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dadosObj)
                });

                const resultado = await resposta.json();

                alerta.classList.remove('d-none', 'alert-danger', 'alert-success');

                if (resultado.sucesso) {
                    alerta.classList.add('alert-success');
                    alerta.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i> ${resultado.mensagem}`;
                    form.reset(); // Limpa o formulário
                } else {
                    alerta.classList.add('alert-danger');
                    alerta.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i> ${resultado.erro}`;
                }
            } catch (erro) {
                alerta.classList.remove('d-none');
                alerta.classList.add('alert-danger');
                alerta.innerHTML = `<i class="bi bi-x-circle-fill me-2"></i> Erro ao conectar com o servidor.`;
            }

            botao.disabled = false;
            botao.innerHTML = '<i class="bi bi-send-fill me-2"></i> Enviar para o Professor';
        });
    </script>
</body>

</html>