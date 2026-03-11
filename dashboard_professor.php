<!-- prof --><?php
                // 🛡️ SEGURANÇA MÁXIMA: Verifica se o usuário está logado e se é realmente um professor
                session_start();

                if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor') {
                    // Se não for professor, manda pro login na hora!
                    header("Location: login.php");
                    exit;
                }
                ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Professor - Tecna</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* MANTENDO A IDENTIDADE VISUAL */
        :root {
            --azul-principal: #0d6efd;
            --sombra-leve: 0 4px 6px -1px rgba(13, 110, 253, 0.08), 0 2px 4px -1px rgba(13, 110, 253, 0.04);
            --sombra-forte: 0 20px 25px -5px rgba(13, 110, 253, 0.15), 0 10px 10px -5px rgba(13, 110, 253, 0.08);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f9;
        }

        /* MENU LATERAL */
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

        /* CARTÕES DE TERMOS PENDENTES */
        .cartao-pendente {
            border: none;
            border-left: 6px solid #ffc107;
            /* Amarelo para indicar "Pendente" */
            border-radius: 1rem;
            box-shadow: var(--sombra-leve);
            transition: all 0.3s ease;
        }

        .cartao-pendente:hover {
            transform: translateY(-3px);
            box-shadow: var(--sombra-forte);
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
            <strong class="text-dark fs-6"><?php echo $_SESSION['nome']; ?></strong>
            <span class="badge bg-primary mt-2 d-block">Professor</span>
        </div>

        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="dashboard_professor.php" class="nav-link active">
                    <i class="bi bi-inboxes me-2"></i> Termos Pendentes
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php" class="nav-link">
                    <i class="bi bi-house-door me-2"></i> Ver Dicionário Público
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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Termos Aguardando Aprovação</h2>
                <p class="text-muted">Revise os termos enviados pelos alunos para a sua matéria.</p>
            </div>
            <input type="hidden" id="cat-prof" value="<?php echo $_SESSION['categoria_id']; ?>">
        </div>

        <div id="lista-pendentes" class="w-100" style="max-width: 900px;">
            <div class="text-center text-muted mt-5">
                <div class="spinner-border text-primary mb-2" role="status"></div>
                <p>Buscando termos pendentes...</p>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Função para carregar os termos que estão com status 'pendente'
        async function carregarPendentes() {
            const idCategoria = document.getElementById('cat-prof').value;
            const divLista = document.getElementById('lista-pendentes');

            try {
                // Vamos criar essa API no próximo passo!
                const resposta = await fetch(`api/api_termos_pendentes.php?categoria_id=${idCategoria}`);
                const termos = await resposta.json();

                divLista.innerHTML = '';

                if (termos.length === 0) {
                    divLista.innerHTML = `
                        <div class="alert alert-light text-center text-muted shadow-sm rounded-4 py-5" role="alert">
                            <i class="bi bi-emoji-smile fs-1 d-block mb-3 text-warning"></i>
                            <h5 class="fw-bold text-dark">Tudo limpo por aqui!</h5>
                            Nenhum termo aguardando aprovação na sua matéria no momento.
                        </div>
                    `;
                    return;
                }

                termos.forEach(termo => {
                    const cartao = document.createElement('div');
                    cartao.className = 'card cartao-pendente mb-4 bg-white p-2';

                    cartao.innerHTML = `
                        <div class="card-body d-flex justify-content-between align-items-start flex-wrap gap-3">
                            <div class="flex-grow-1" style="min-width: 250px;">
                                <h4 class="card-title fw-bold text-dark mb-2">${termo.palavra}</h4>
                                <p class="card-text text-secondary mb-3">${termo.descricao}</p>
                                <div class="text-muted small fw-medium">
                                    <i class="bi bi-person me-1"></i> Enviado por: <span class="text-dark">${termo.nome_aluno}</span>
                                </div>
                            </div>
                            
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <button onclick="atualizarStatus(${termo.id}, 'aprovado')" class="btn btn-success fw-bold shadow-sm">
                                    <i class="bi bi-check-lg me-1"></i> Aprovar
                                </button>
                                <button onclick="atualizarStatus(${termo.id}, 'rejeitado')" class="btn btn-outline-danger fw-bold">
                                    <i class="bi bi-x-lg me-1"></i> Rejeitar
                                </button>
                            </div>
                        </div>
                    `;
                    divLista.appendChild(cartao);
                });

            } catch (erro) {
                console.error("Erro ao buscar pendentes:", erro);
                divLista.innerHTML = `
                    <div class="alert alert-danger text-center shadow-sm rounded-4 py-4" role="alert">
                        <i class="bi bi-exclamation-triangle fs-4 d-block mb-2"></i>
                        Erro ao carregar os termos do servidor.
                    </div>
                `;
            }
        }

        // Função para aprovar ou rejeitar (Manda a ordem pro PHP)
        async function atualizarStatus(idTermo, novoStatus) {
            if (!confirm(`Tem certeza que deseja marcar este termo como ${novoStatus}?`)) return;

            try {
                // Vamos criar essa API também!
                const resposta = await fetch('api/api_atualizar_termo.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: idTermo,
                        status: novoStatus
                    })
                });

                const resultado = await resposta.json();

                if (resultado.sucesso) {
                    // Recarrega a lista para o termo sumir da tela
                    carregarPendentes();
                } else {
                    alert("Erro: " + resultado.erro);
                }
            } catch (erro) {
                alert("Erro de conexão com o servidor.");
            }
        }

        // Carrega a lista assim que a página abre
        window.onload = carregarPendentes;
    </script>
</body>

</html>