<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dicionário Técnico SENAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* PALETA CLARA COM AZUL PRINCIPAL E SOMBRAS */
        :root {
            --bg-body: #f4f7f9;      /* Fundo geral bem claro e levemente azulado */
            --bg-surface: #ffffff;   /* Fundo branco puro para destacar as sombras */
            --text-main: #1e293b;    /* Texto escuro padrão */
            --text-muted: #64748b;   /* Texto secundário */
            --azul-principal: #0056b3; /* Azul corporativo e forte */
            --azul-claro: #e6f0fa;   /* Azul bem suave para fundos de botões */
            --sombra-leve: 0 4px 6px -1px rgba(0, 86, 179, 0.08), 0 2px 4px -1px rgba(0, 86, 179, 0.04);
            --sombra-forte: 0 20px 25px -5px rgba(0, 86, 179, 0.15), 0 10px 10px -5px rgba(0, 86, 179, 0.08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { display: flex; height: 100vh; background-color: var(--bg-body); color: var(--text-main); overflow: hidden; }
        
        /* MENU LATERAL COM SOMBRA */
        .sidebar { 
            width: 280px; 
            background-color: var(--bg-surface); 
            box-shadow: var(--sombra-leve); /* Sombra separando o menu do corpo */
            z-index: 10;
            display: flex; 
            flex-direction: column; 
            padding: 32px 24px; 
        }
        .logo { 
            font-size: 1.4rem; 
            font-weight: 800; 
            color: var(--azul-principal);
            margin-bottom: 40px; 
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .menu { list-style: none; flex-grow: 1; }
        .menu-titulo { 
            font-size: 0.8rem; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            color: var(--text-muted); 
            font-weight: 700; 
            margin-bottom: 12px;
            margin-top: 24px;
        }
        .menu a { 
            color: var(--text-muted); 
            text-decoration: none; 
            display: flex; 
            align-items: center; 
            padding: 12px 16px; 
            border-radius: 10px; 
            font-weight: 500;
            transition: all 0.3s ease; 
            cursor: pointer; 
            margin-bottom: 8px;
        }
        .menu a:hover { background-color: var(--azul-claro); color: var(--azul-principal); transform: translateX(5px); }
        .menu a.ativo { 
            background-color: var(--azul-principal); 
            color: #ffffff; 
            box-shadow: var(--sombra-leve);
        }
        
        /* BOTÃO DE LOGIN AZUL COM SOMBRA */
        .btn-login { 
            margin-top: auto; 
            background-color: var(--azul-principal); 
            color: #ffffff !important; 
            text-align: center; 
            justify-content: center; 
            font-weight: 600; 
            padding: 14px;
            border-radius: 12px;
            box-shadow: var(--sombra-leve);
            transition: all 0.3s ease;
        }
        .btn-login:hover { background-color: #004494; box-shadow: var(--sombra-forte); transform: translateY(-3px); }

        /* ÁREA PRINCIPAL */
        .main-content { flex-grow: 1; display: flex; flex-direction: column; overflow-y: auto; }
        
        /* BARRA DE PESQUISA FLUTUANTE */
        .top-bar { 
            padding: 24px 40px; 
            display: flex; 
            align-items: center; 
            background-color: transparent;
        }
        .search-wrapper {
            position: relative;
            width: 100%;
            max-width: 600px;
        }
        .search-bar { 
            width: 100%; 
            padding: 16px 20px 16px 50px; 
            background-color: var(--bg-surface);
            border: none; 
            border-radius: 16px; 
            font-size: 1rem; 
            color: var(--text-main);
            box-shadow: var(--sombra-leve);
            transition: all 0.3s ease;
        }
        .search-bar:focus { outline: none; box-shadow: var(--sombra-forte); }
        .search-icon { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); font-size: 1.2rem; }

        /* CONTEÚDO E CARTÕES COM PROFUNDIDADE */
        .conteudo { padding: 20px 40px 40px 40px; max-width: 1000px; margin: 0 auto; width: 100%; }
        .titulo-pagina { margin-bottom: 30px; font-size: 2rem; font-weight: 800; color: var(--azul-principal); }
        
        .cartao-termo { 
            background-color: var(--bg-surface); 
            padding: 28px; 
            border: none;
            border-radius: 20px; 
            margin-bottom: 24px; 
            box-shadow: var(--sombra-leve);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        /* Detalhe azul no canto do cartão */
        .cartao-termo::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 6px;
            background-color: var(--azul-principal);
        }
        .cartao-termo:hover { 
            transform: translateY(-5px); 
            box-shadow: var(--sombra-forte); 
        }
        .cartao-termo h3 { margin-bottom: 12px; color: var(--text-main); font-size: 1.4rem; font-weight: 700; }
        .cartao-termo p { color: var(--text-muted); line-height: 1.7; font-size: 1.05rem; margin-bottom: 20px; }
        
        .rodape-cartao { display: flex; align-items: center; gap: 10px; font-size: 0.9rem; color: #94a3b8; font-weight: 500; }
        .avatar-sala { width: 32px; height: 32px; background-color: var(--azul-claro); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--azul-principal); font-weight: bold; font-size: 0.9rem; }
        
        .mensagem-vazia { text-align: center; color: var(--text-muted); margin-top: 60px; font-size: 1.2rem; font-weight: 500; }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="logo"><span>📘</span> Tecna</div>
        <ul class="menu">
            <li><a href="index.php">🏠 Início</a></li>
            
            <div class="menu-titulo">Categorias</div>
            <li><a onclick="carregarTermos(1, 'Português')" id="menu-cat-1" class="ativo">📚 Português</a></li>
            <li><a onclick="carregarTermos(2, 'Matemática')" id="menu-cat-2">📐 Matemática</a></li>
        </ul>
        <a href="login.php" class="btn-login">Acessar Conta</a>
    </nav>

    <main class="main-content">
        <header class="top-bar">
            <div class="search-wrapper">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-bar" placeholder="Busque por termos, conceitos ou palavras-chave...">
            </div>
        </header>

        <section class="conteudo">
            <h2 class="titulo-pagina" id="titulo-categoria">Português</h2>
            
            <div id="lista-termos">
                <p class="mensagem-vazia">Carregando termos...</p>
            </div>
        </section>
    </main>

    <script>
        async function carregarTermos(categoriaId, nomeCategoria) {
            document.getElementById('titulo-categoria').innerText = nomeCategoria;
            document.querySelectorAll('.menu a').forEach(a => a.classList.remove('ativo'));
            document.getElementById(`menu-cat-${categoriaId}`).classList.add('ativo');

            const divLista = document.getElementById('lista-termos');
            divLista.innerHTML = '<p class="mensagem-vazia">Carregando termos...</p>';

            try {
                // Caminho da API
                const resposta = await fetch(`api/api_termos.php?categoria=${categoriaId}`);
                const termos = await resposta.json();

                divLista.innerHTML = ''; 

                if (termos.length === 0) {
                    divLista.innerHTML = '<p class="mensagem-vazia">Nenhum termo aprovado nesta categoria ainda. ✨</p>';
                    return;
                }

                termos.forEach(termo => {
                    const cartao = document.createElement('div');
                    cartao.className = 'cartao-termo';
                    
                    const inicial = termo.enviado_por.charAt(0).toUpperCase();
                    
                    cartao.innerHTML = `
                        <h3>${termo.palavra}</h3>
                        <p>${termo.descricao}</p>
                        <div class="rodape-cartao">
                            <div class="avatar-sala">${inicial}</div>
                            <span>Enviado por ${termo.enviado_por}</span>
                        </div>
                    `;
                    divLista.appendChild(cartao);
                });

            } catch (erro) {
                console.error("Erro ao buscar API:", erro);
                divLista.innerHTML = '<p class="mensagem-vazia" style="color: #ef4444;">Erro ao carregar os dados do servidor.</p>';
            }
        }

        window.onload = () => {
            carregarTermos(1, 'Português');
        };
    </script>
</body>
</html>