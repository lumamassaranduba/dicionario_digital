<?php
// Redireciona para a página inicial separada.
header('Location: home.php');
exit;
?>

        /* classes utilitárias para ajudar a aplicar a cor atual (accent) */
        .text-accent {
            color: var(--accent) !important;
        }

        .btn-accent {
            background-color: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .btn-accent:hover {
            background-color: var(--accent);
            opacity: 0.9;
        }

        .spinner-accent {
            color: var(--accent);
        }

        .categoria-matematica {
            --accent: #dc3545;
            --accent-fade: rgba(220, 53, 69, 0.1);
            --sombra-leve: 0 4px 6px -1px rgba(220, 53, 69, 0.08), 0 2px 4px -1px rgba(220, 53, 69, 0.04);
            --sombra-forte: 0 20px 25px -5px rgba(220, 53, 69, 0.15), 0 10px 10px -5px rgba(220, 53, 69, 0.08);
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
            background-color: var(--accent-fade);
            color: var(--accent);
            transform: translateX(5px);
        }

        .nav-link.active {
            background-color: var(--accent) !important;
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
            border-left: 6px solid var(--accent);
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
            background-color: var(--accent-fade);
            color: var(--accent);
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

<body class="d-flex flex-column flex-md-row <?php echo ($view === 'termos' && $categoriaId === 2) ? 'categoria-matematica' : ''; ?>">

    <nav class="sidebar bg-white d-flex flex-column p-4">
        <a href="index.php?view=home" class="d-flex align-items-center mb-4 text-decoration-none text-accent fs-4 fw-bold">
            <i class="bi bi-book-half me-2"></i> Dicionário Digital
        </a>

        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-2">
                <a id="menu-home" href="index.php?view=home" class="nav-link <?php echo $view === 'home' ? 'active' : ''; ?>">
                    <i class="bi bi-house-door me-2"></i> Início
                </a>
            </li>
            <li class="mt-4 mb-2 text-muted text-uppercase small fw-bold px-3">Categorias</li>
            <li class="nav-item mb-2">
                <a id="menu-cat-1" href="index.php?view=termos&categoria=1" class="nav-link <?php echo $view === 'termos' && $categoriaId === 1 ? 'active' : ''; ?>">
                    <i class="bi bi-journal-text me-2"></i> Português
                </a>
            </li>
            <li class="nav-item mb-2">
                <a id="menu-cat-2" href="index.php?view=termos&categoria=2" class="nav-link <?php echo $view === 'termos' && $categoriaId === 2 ? 'active' : ''; ?>">
                    <i class="bi bi-calculator me-2"></i> Matemática
                </a>
            </li>
            <?php if ($usuarioLogado): ?>
                <li class="nav-item mb-2">
                    <a id="menu-painel" href="index.php?view=painel" class="nav-link <?php echo $view === 'painel' ? 'active' : ''; ?>">
                        <i class="bi bi-person-workspace me-2"></i> Meu Painel
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <hr>

        <div class="mt-auto">
            <?php if ($usuarioLogado): ?>
                <div class="mb-3 text-muted small">
                    Logado como:<br>
                    <strong class="text-dark"><?php echo htmlspecialchars($usuarioNome); ?></strong>
                </div>
                <a href="api/api_logout.php" class="btn btn-outline-danger w-100 fw-bold py-2">
                    <i class="bi bi-box-arrow-right me-2"></i> Sair
                </a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary w-100 fw-bold py-2 btn-acao">
                    <i class="bi bi-person-circle me-2"></i> Acessar Conta
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="flex-grow-1 d-flex flex-column" style="height: 100vh; overflow-y: auto;">

        <?php if ($view === 'termos'): ?>
            <header class="p-4 d-flex align-items-center">
                <div class="search-wrapper">
                    <i class="bi bi-search search-icon"></i>
                    <input id="search-input" type="text" class="form-control form-control-lg search-bar fs-6" placeholder="Busque por termos, conceitos ou palavras-chave...">
                </div>
            </header>
        <?php else: ?>
            <header class="p-4">
                <h1 class="h3 fw-bold text-accent mb-1"><?php echo $view === 'home' ? 'Bem-vindo ao Dicionário' : 'Meu Painel'; ?></h1>
                <?php if ($view === 'home'): ?>
                    <p class="text-muted mb-0">Escolha a matéria para explorar os termos.</p>
                <?php elseif ($view === 'painel'): ?>
                    <p class="text-muted mb-0">Use esta área para enviar ou aprovar termos.</p>
                <?php endif; ?>
            </header>
        <?php endif; ?>

        <section class="p-4 p-md-5 pt-0 w-100" style="max-width: 900px; margin: 0 auto;">
            <?php if ($view === 'home'): ?>
                <div class="row g-4">
                    <div class="col-md-6">
                        <a href="index.php?view=termos&categoria=1" class="card cartao-termo h-100 text-decoration-none">
                            <div class="card-body">
                                <h3 class="card-title fw-bold">Português</h3>
                                <p class="card-text text-muted">Explore termos aprovados para Português.</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="index.php?view=termos&categoria=2" class="card cartao-termo h-100 text-decoration-none" style="border-left-color: #dc3545;">
                            <div class="card-body">
                                <h3 class="card-title fw-bold">Matemática</h3>
                                <p class="card-text text-muted">Explore termos aprovados para Matemática.</p>
                            </div>
                        </a>
                    </div>
                </div>

            <?php elseif ($view === 'termos'): ?>
                <h2 class="fw-bold text-accent mb-4" id="titulo-categoria">Português</h2>

                <div id="lista-termos">
                    <div class="text-center text-muted mt-5">
                        <div class="spinner-border spinner-accent mb-2" role="status"></div>
                        <p>Carregando termos...</p>
                    </div>
                </div>

            <?php elseif ($view === 'painel'): ?>
                <?php if (! $usuarioLogado): ?>
                    <div class="alert alert-warning">Faça login para acessar o painel.</div>
                <?php elseif ($usuarioTipo === 'aluno'): ?>
                    <h2 class="fw-bold text-dark mb-2">Contribuir para o Dicionário</h2>
                    <p class="text-muted mb-4">Adicione um termo e ele ficará pendente até ser aprovado pelo professor.</p>

                    <div class="card bg-white p-4 shadow-sm rounded-4">
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

                <?php elseif ($usuarioTipo === 'professor'): ?>
                    <h2 class="fw-bold text-dark mb-2">Termos pendentes</h2>
                    <p class="text-muted mb-4">Aprove ou rejeite termos enviados pelos alunos.</p>

                    <div id="lista-pendentes">
                        <div class="text-center text-muted mt-5">
                            <div class="spinner-border spinner-accent mb-2" role="status"></div>
                            <p>Carregando termos pendentes...</p>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function getQueryParam(name) {
            const params = new URLSearchParams(window.location.search);
            return params.get(name);
        }

        function setCategoriaVisual(categoriaId) {
            const root = document.documentElement;
            if (categoriaId === 2) {
                root.classList.add('categoria-matematica');
            } else {
                root.classList.remove('categoria-matematica');
            }
        }

        function mostrarSpinner() {
            const divLista = document.getElementById('lista-termos');
            if (!divLista) return;
            divLista.innerHTML = '<div class="text-center text-muted mt-5"><div class="spinner-border spinner-accent mb-2" role="status"></div><p>Carregando termos...</p></div>';
        }

        async function carregarTermos(categoriaId, nomeCategoria, busca = '') {
            setCategoriaVisual(categoriaId);

            const titulo = document.getElementById('titulo-categoria');
            const divLista = document.getElementById('lista-termos');
            if (!titulo || !divLista) return; // Não está na view de termos

            titulo.innerText = nomeCategoria;
            document.querySelectorAll('.nav-link').forEach(a => a.classList.remove('active'));
            const menuLink = document.getElementById(`menu-cat-${categoriaId}`);
            if (menuLink) menuLink.classList.add('active');

            mostrarSpinner();

            try {
                let url = `api/api_termos.php?categoria=${categoriaId}`;
                if (busca.trim() !== '') {
                    url += `&q=${encodeURIComponent(busca.trim())}`;
                    titulo.innerText = `Resultados para "${busca.trim()}"`;
                }

                const resposta = await fetch(url);
                if (!resposta.ok) {
                    throw new Error(`HTTP ${resposta.status}`);
                }

                const termos = await resposta.json();
                divLista.innerHTML = '';

                if (!Array.isArray(termos) || termos.length === 0) {
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
                    const inicial = termo.enviado_por ? termo.enviado_por.charAt(0).toUpperCase() : '?';

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

        function debounce(fn, delay) {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => fn(...args), delay);
            };
        }

        window.onload = () => {
            const view = getQueryParam('view') || 'home';

            if (view === 'termos') {
                const categoriaQuery = parseInt(getQueryParam('categoria'), 10);
                const categoriaId = [1, 2].includes(categoriaQuery) ? categoriaQuery : 1;
                const nome = categoriaId === 2 ? 'Matemática' : 'Português';

                const searchInput = document.getElementById('search-input');
                const buscar = debounce((valor) => carregarTermos(categoriaId, nome, valor), 350);

                if (searchInput) {
                    searchInput.addEventListener('input', (event) => {
                        buscar(event.target.value);
                    });
                }

                carregarTermos(categoriaId, nome);
            }
        };
    </script>
</body>

</html>