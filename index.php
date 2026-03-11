<?php
// MÁGICA AQUI: Iniciamos a sessão logo na linha 1 para o index.php saber quem está navegando!
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dicionário Técnico SENAI</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --azul-principal: #0d6efd;
            --sombra-leve: 0 4px 6px -1px rgba(13, 110, 253, 0.08), 0 2px 4px -1px rgba(13, 110, 253, 0.04);
            --sombra-forte: 0 20px 25px -5px rgba(13, 110, 253, 0.15), 0 10px 10px -5px rgba(13, 110, 253, 0.08);
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

        .search-wrapper {
            position: relative;
            max-width: 600px;
            width: 100%;
        }

        .search-bar {
            padding-left: 2.8rem;
            border-radius: 1rem;
            border: none;
            box-shadow: var(--sombra-leve);
            transition: all 0.3s ease;
        }

        .search-bar:focus {
            box-shadow: var(--sombra-forte);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
        }

        .cartao-termo {
            border: none;
            border-left: 6px solid var(--azul-principal);
            border-radius: 1rem;
            box-shadow: var(--sombra-leve);
            transition: all 0.3s ease;
        }

        .cartao-termo:hover {
            transform: translateY(-5px);
            box-shadow: var(--sombra-forte);
        }

        .avatar-sala {
            width: 32px;
            height: 32px;
            background-color: rgba(13, 110, 253, 0.1);
            color: var(--azul-principal);
        }

        .btn-acao {
            box-shadow: var(--sombra-leve);
            transition: all 0.3s;
        }

        .btn-acao:hover {
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

        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-2">
                <a href="index.php" class="nav-link">
                    <i class="bi bi-house-door me-2"></i> Início
                </a>
            </li>
            <li class="mt-4 mb-2 text-muted text-uppercase small fw-bold px-3">Categorias</li>
            <li class="nav-item mb-2">
                <a onclick="carregarTermos(1, 'Português')" id="menu-cat-1" class="nav-link active" style="cursor: pointer;">
                    <i class="bi bi-journal-text me-2"></i> Português
                </a>
            </li>
            <li class="nav-item mb-2">
                <a onclick="carregarTermos(2, 'Matemática')" id="menu-cat-2" class="nav-link" style="cursor: pointer;">
                    <i class="bi bi-calculator me-2"></i> Matemática
                </a>
            </li>
        </ul>

        <hr>

        <div class="mt-auto">
            <?php
            // ==========================================
            // LÓGICA DO BOTÃO INTELIGENTE
            // ==========================================
            if (isset($_SESSION['usuario_id'])) {
                // Se estiver logado, checa se é professor ou aluno para mandar pro lugar certo
                if ($_SESSION['tipo'] === 'professor') {
                    echo '<a href="dashboard_professor.php" class="btn btn-success w-100 fw-bold py-2 btn-acao">
                            <i class="bi bi-person-badge-fill me-2"></i> Meu Painel
                          </a>';
                } else {
                    echo '<a href="dashboard_aluno.php" class="btn btn-success w-100 fw-bold py-2 btn-acao">
                            <i class="bi bi-person-workspace me-2"></i> Meu Painel
                          </a>';
                }
            } else {
                // Se NÃO estiver logado, mostra o botão normal de login
                echo '<a href="login.php" class="btn btn-primary w-100 fw-bold py-2 btn-acao">
                        <i class="bi bi-person-circle me-2"></i> Acessar Conta
                      </a>';
            }
            ?>
        </div>
    </nav>

    <main class="flex-grow-1 d-flex flex-column" style="height: 100vh; overflow-y: auto;">

        <header class="p-4 d-flex align-items-center">
            <div class="search-wrapper">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="form-control form-control-lg search-bar fs-6" placeholder="Busque por termos, conceitos ou palavras-chave...">
            </div>
        </header>

        <section class="p-4 p-md-5 pt-0 w-100" style="max-width: 900px; margin: 0 auto;">
            <h2 class="fw-bold text-primary mb-4" id="titulo-categoria">Português</h2>

            <div id="lista-termos">
                <div class="text-center text-muted mt-5">
                    <div class="spinner-border text-primary mb-2" role="status"></div>
                    <p>Carregando termos...</p>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        async function carregarTermos(categoriaId, nomeCategoria) {
            document.getElementById('titulo-categoria').innerText = nomeCategoria;
            document.querySelectorAll('.nav-link').forEach(a => a.classList.remove('active'));
            document.getElementById(`menu-cat-${categoriaId}`).classList.add('active');

            const divLista = document.getElementById('lista-termos');
            divLista.innerHTML = '<div class="text-center text-muted mt-5"><div class="spinner-border text-primary mb-2" role="status"></div><p>Carregando termos...</p></div>';

            try {
                const resposta = await fetch(`api/api_termos.php?categoria=${categoriaId}`);
                const termos = await resposta.json();

                divLista.innerHTML = '';

                if (termos.length === 0) {
                    divLista.innerHTML = `
                        <div class="alert alert-light text-center text-muted shadow-sm rounded-4 py-4" role="alert">
                            <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
                            Nenhum termo aprovado nesta categoria ainda. ✨
                        </div>
                    `;
                    return;
                }

                termos.forEach(termo => {
                    const cartao = document.createElement('div');
                    cartao.className = 'card cartao-termo mb-4 bg-white p-2';
                    const inicial = termo.enviado_por.charAt(0).toUpperCase();

                    cartao.innerHTML = `
                        <div class="card-body">
                            <h4 class="card-title fw-bold text-dark mb-3">${termo.palavra}</h4>
                            <p class="card-text text-secondary mb-4 lh-lg">${termo.descricao}</p>
                            <div class="d-flex align-items-center text-muted small fw-medium">
                                <div class="avatar-sala rounded-circle d-flex align-items-center justify-content-center me-2">${inicial}</div>
                                Enviado por ${termo.enviado_por}
                            </div>
                        </div>
                    `;
                    divLista.appendChild(cartao);
                });

            } catch (erro) {
                console.error("Erro ao buscar API:", erro);
                divLista.innerHTML = `<div class="alert alert-danger text-center shadow-sm rounded-4 py-4" role="alert"><i class="bi bi-exclamation-triangle fs-4 d-block mb-2"></i>Erro ao carregar os dados do servidor.</div>`;
            }
        }

        window.onload = () => {
            carregarTermos(1, 'Português');
        };
    </script>
</body>

</html>