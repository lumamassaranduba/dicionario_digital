<?php
$bodyClass = '';
include __DIR__ . '/inc/header.php';
?>

    <header class="p-4 d-flex align-items-center justify-content-between w-100" style="max-width: 1000px; margin: 0 auto;">
        <div>
            <h1 class="h3 fw-bold text-accent mb-1">Meu Painel</h1>
            <p class="text-muted mb-0">Use esta área para enviar ou aprovar termos.</p>
        </div>
    </header>

    <section class="p-4 p-md-5 pt-0 w-100" style="max-width: 1000px; margin: 0 auto;">
        <?php if (! isset($_SESSION['usuario_id'])): ?>
            <div class="alert alert-warning">Faça login para acessar o painel.</div>
        <?php elseif ($_SESSION['tipo'] === 'aluno'): ?>
            <h2 class="fw-bold text-dark mb-2">Criar novo termo</h2>
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

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Exemplo</label>
                        <textarea name="exemplo" class="form-control" rows="3" placeholder="Use em uma frase ou contexto." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">URL da imagem</label>
                        <input type="file" name="imagem" accept="image/*">
                        <div class="form-text">Selecione uma imagem relacionada ao termo (opcional).</div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                        <i class="bi bi-send-fill me-2"></i> Enviar para o Professor
                    </button>
                </form>
            </div>

        <?php elseif ($_SESSION['tipo'] === 'professor'): ?>
            <?php if (isset($_GET['criar']) && $_GET['criar'] == 1): ?>
                <h2 class="fw-bold text-dark mb-2">Criar novo termo</h2>
                <p class="text-muted mb-4">Adicione um termo diretamente como professor.</p>

                <div class="card bg-white p-4 shadow-sm rounded-4">
                    <div id="alerta-mensagem-prof" class="alert d-none" role="alert"></div>

                    <form id="formAdicionarTermoProf">
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
                            <textarea name="descricao" class="form-control" rows="4" placeholder="Explique o que significa..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Exemplo</label>
                            <textarea name="exemplo" class="form-control" rows="3" placeholder="Use em uma frase ou contexto." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">URL da imagem</label>
                            <input type="file" name="imagem" accept="image/*">
                            <div class="form-text">Selecione uma imagem relacionada ao termo (opcional).</div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                            <i class="bi bi-send-fill me-2"></i> Criar Termo
                        </button>
                    </form>
                </div>
            <?php else: ?>
                    <h2 class="fw-bold text-dark mb-2">Termos pendentes</h2>
                    <p class="text-muted mb-4">Aprove ou rejeite termos enviados pelos alunos.</p>
                    <div id="lista-pendentes-perfil">
                        <div class="text-center text-muted mt-5">
                            <div class="spinner-border spinner-accent mb-2" role="status"></div>
                            <p>Carregando termos pendentes...</p>
                        </div>
                    </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>

    <script>
        async function atualizarStatus(id, status) {
            try {
                const resp = await fetch('api/api_atualizar_termo.php', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id, status: status })
                });
                const resultado = await resp.json();
                if (resultado.sucesso) {
                    // Recarrega dependendo de qual função chamou
                    if (document.getElementById('lista-pendentes-perfil')) {
                        carregarPendentesPerfil();
                    }
                    if (document.getElementById('lista-pendentes')) {
                        carregarPendentes();
                    }
                } else {
                    alert(resultado.erro || 'Erro ao atualizar status.');
                }
            } catch (err) {
                console.error(err);
                alert('Erro ao conectar com o servidor.');
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>\"]/g, function(s) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'})[s]; });
        }

        function escapeAttr(str) {
            if (!str) return '';
            return String(str).replace(/"/g, '&quot;');
        }

        async function carregarPendentesPerfil() {
            const div = document.getElementById('lista-pendentes-perfil');
            if (!div) return;
            div.innerHTML = '<div class="text-center text-muted mt-5"><div class="spinner-border spinner-accent mb-2" role="status"></div><p>Carregando termos pendentes...</p></div>';
            try {
                const resp = await fetch('api/api_termos_pendentes.php');
                if (!resp.ok) throw new Error('HTTP ' + resp.status);
                const termos = await resp.json();

                if (!Array.isArray(termos) || termos.length === 0) {
                    div.innerHTML = `
                        <div class="alert alert-light text-center text-muted shadow-sm rounded-4 py-4" role="alert">
                            <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
                            Nenhum termo pendente nesta categoria.
                        </div>
                    `;
                    return;
                }

                let html = '';
                termos.forEach(t => {
                    const descricaoCurta = t.descricao.length > 600 ? t.descricao.substring(0,600) + '...' : t.descricao;
                    html += `
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="fw-bold mb-1">${t.palavra}</h5>
                                ${t.exemplo ? `<p class="mb-2 text-secondary"><strong>Exemplo:</strong> ${t.exemplo}</p>` : ''}
                                <div class="text-muted small mb-2">Enviado por ${t.nome_aluno}</div>
                                <p class="mb-3 text-secondary">${descricaoCurta}</p>
                                ${t.imagem ? `<img src="${t.imagem}" alt="${t.palavra}" class="img-fluid rounded mb-3 termo-thumb" style="max-width: 180px; cursor: pointer;" data-img="${t.imagem}" data-alt="${t.palavra}" onclick="mostrarImagemModal(this.dataset.img, this.dataset.alt)" />` : ''}
                                <div class="d-flex gap-2">
                                    <button class="btn btn-success btn-aprovar" data-id="${t.id}">Aprovar</button>
                                    <button class="btn btn-outline-danger btn-rejeitar" data-id="${t.id}">Rejeitar</button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                div.innerHTML = html;

                document.querySelectorAll('.btn-aprovar').forEach(b => b.addEventListener('click', async (e) => {
                    const id = e.currentTarget.getAttribute('data-id');
                    await atualizarStatus(id, 'aprovado');
                }));

                document.querySelectorAll('.btn-rejeitar').forEach(b => b.addEventListener('click', async (e) => {
                    const id = e.currentTarget.getAttribute('data-id');
                    if (!confirm('Deseja rejeitar este termo?')) return;
                    await atualizarStatus(id, 'rejeitado');
                }));
            } catch (err) {
                console.error(err);
                div.innerHTML = `
                    <div class="alert alert-light text-center text-muted shadow-sm rounded-4 py-4" role="alert">
                        <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
                        Nenhum termo pendente nesta categoria.
                    </div>
                `;
            }
        }
         // Inicializa lista de pendentes também na página de perfil
        carregarPendentesPerfil();

    (function(){
        const div = document.getElementById('lista-pendentes');
        if (!div) return; // Se estiver na tela de criar termo, não tenta carregar pendentes
        const categoriaId = <?php echo (isset($_SESSION['categoria_id']) && intval($_SESSION['categoria_id']) > 0) ? intval($_SESSION['categoria_id']) : 1; ?>;

        async function carregarPendentes() {
            div.innerHTML = '<div class="text-center text-muted mt-5"><div class="spinner-border spinner-accent mb-2" role="status"></div><p>Carregando termos pendentes...</p></div>';
            try {
                const resp = await fetch(`api/api_termos_pendentes.php?categoria_id=${categoriaId}`);
                if (!resp.ok) throw new Error('HTTP ' + resp.status);
                const termos = await resp.json();

                // Se a API retornou um objeto de erro, mostre isso em vez de tratar como "nenhum termo".
                if (!Array.isArray(termos)) {
                    const mensagem = (termos && termos.erro) ? termos.erro : 'Erro ao carregar termos pendentes.';
                    div.innerHTML = `
                        <div class="alert alert-danger text-center shadow-sm rounded-4 py-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill fs-4 d-block mb-2"></i>
                            ${mensagem}
                        </div>
                    `;
                    return;
                }

                if (!Array.isArray(termos) || termos.length === 0) {
                    div.innerHTML = `
                        <div class="alert alert-light text-center text-muted shadow-sm rounded-4 py-4" role="alert">
                            <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
                            Nenhum termo pendente nesta categoria.
                        </div>
                    `;
                    return;
                }

                let html = '';
                termos.forEach(t => {
                    const descricaoCurta = t.descricao.length > 600 ? t.descricao.substring(0,600) + '...' : t.descricao;
                    html += `
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="fw-bold mb-1">${escapeHtml(t.palavra)}</h5>
                                ${t.exemplo ? `<p class="mb-2 text-secondary"><strong>Exemplo:</strong> ${escapeHtml(t.exemplo)}</p>` : ''}
                                <div class="text-muted small mb-2">Enviado por ${escapeHtml(t.nome_aluno)}</div>
                                <p class="mb-3 text-secondary">${escapeHtml(descricaoCurta)}</p>
                                ${t.imagem ? `<img src="${escapeHtml(t.imagem)}" alt="${escapeHtml(t.palavra)}" class="img-fluid rounded mb-3 termo-thumb" style="max-width: 180px; cursor: pointer;" data-img="${escapeAttr(t.imagem)}" data-alt="${escapeAttr(t.palavra)}" onclick="mostrarImagemModal(this.dataset.img, this.dataset.alt)" />` : ''}
                                <div class="d-flex gap-2">
                                    <button class="btn btn-success btn-aprovar" data-id="${t.id}">Aprovar</button>
                                    <button class="btn btn-outline-danger btn-rejeitar" data-id="${t.id}">Rejeitar</button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                div.innerHTML = html;

                document.querySelectorAll('.btn-aprovar').forEach(b => b.addEventListener('click', async (e) => {
                    const id = e.currentTarget.getAttribute('data-id');
                    await atualizarStatus(id, 'aprovado');
                }));

                document.querySelectorAll('.btn-rejeitar').forEach(b => b.addEventListener('click', async (e) => {
                    const id = e.currentTarget.getAttribute('data-id');
                    if (!confirm('Deseja rejeitar este termo?')) return;
                    await atualizarStatus(id, 'rejeitado');
                }));

            } catch (err) {
                console.error(err);
                div.innerHTML = `
                    <div class="alert alert-light text-center text-muted shadow-sm rounded-4 py-4" role="alert">
                        <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
                        Nenhum termo pendente nesta categoria.
                    </div>
                `;
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>\"]/g, function(s) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'})[s]; });
        }

        // Inicializa
        carregarPendentes();
    })();

    // Script para alunos enviar termo
    const formAdicionarTermo = document.getElementById('formAdicionarTermo');
    if (formAdicionarTermo) {
        formAdicionarTermo.addEventListener('submit', async function(e) {
            e.preventDefault();

            const alerta = document.getElementById('alerta-mensagem');
            const botao = formAdicionarTermo.querySelector('button[type="submit"]');

            // Usar FormData para incluir arquivo
            const formData = new FormData(formAdicionarTermo);

            botao.disabled = true;
            botao.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';

            try {
                const resposta = await fetch('api/api_adicionar_termo.php', {
                    method: 'POST',
                    body: formData
                });

                const resultado = await resposta.json();

                alerta.classList.remove('d-none', 'alert-danger', 'alert-success');

                if (resultado.sucesso) {
                    alerta.classList.add('alert-success');
                    alerta.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i> ${resultado.mensagem}`;
                    formAdicionarTermo.reset(); // Limpa o formulário
                } else {
                    alerta.classList.add('alert-danger');
                    alerta.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i> ${resultado.erro}`;
                }
                } catch (erro) {
                    console.error('Erro ao enviar:', erro);
                    alerta.classList.remove('d-none');
                    alerta.classList.add('alert-danger');
                    alerta.innerHTML = `<i class="bi bi-x-circle-fill me-2"></i> Erro ao conectar com o servidor.`;
                }            botao.disabled = false;
            botao.innerHTML = '<i class="bi bi-send-fill me-2"></i> Enviar para o Professor';
        });
    }

    // Script para professores criar termo
    const formAdicionarTermoProf = document.getElementById('formAdicionarTermoProf');
    if (formAdicionarTermoProf) {
        formAdicionarTermoProf.addEventListener('submit', async function(e) {
            e.preventDefault();

            const alerta = document.getElementById('alerta-mensagem-prof');
            const botao = formAdicionarTermoProf.querySelector('button[type="submit"]');

            // Usar FormData para incluir arquivo
            const formData = new FormData(formAdicionarTermoProf);

            botao.disabled = true;
            botao.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Criando...';

            try {
                const resposta = await fetch('api/api_adicionar_termo.php', {
                    method: 'POST',
                    body: formData
                });

                const resultado = await resposta.json();

                alerta.classList.remove('d-none', 'alert-danger', 'alert-success');

                if (resultado.sucesso) {
                    alerta.classList.add('alert-success');
                    alerta.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i> Termo criado com sucesso!`;
                    formAdicionarTermoProf.reset(); // Limpa o formulário
                } else {
                    alerta.classList.add('alert-danger');
                    alerta.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i> ${resultado.erro}`;
                }
                } catch (erro) {
                    console.error('Erro ao criar:', erro);
                    alerta.classList.remove('d-none');
                    alerta.classList.add('alert-danger');
                    alerta.innerHTML = `<i class="bi bi-x-circle-fill me-2"></i> Erro ao conectar com o servidor.`;
                }            botao.disabled = false;
            botao.innerHTML = '<i class="bi bi-send-fill me-2"></i> Criar Termo';
        });
    }
    </script>

<?php include __DIR__ . '/inc/footer.php'; ?> 