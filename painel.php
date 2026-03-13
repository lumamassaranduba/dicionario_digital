<?php
$bodyClass = '';
include __DIR__ . '/inc/header.php';
?>

    <header class="p-4">
        <h1 class="h3 fw-bold text-accent mb-1">Meu Painel</h1>
        <p class="text-muted mb-0">Use esta área para enviar ou aprovar termos.</p>
    </header>

    <section class="p-4 p-md-5 pt-0 w-100" style="max-width: 900px; margin: 0 auto;">
        <?php if (! isset($_SESSION['usuario_id'])): ?>
            <div class="alert alert-warning">Faça login para acessar o painel.</div>
        <?php elseif ($_SESSION['tipo'] === 'aluno'): ?>
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

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Exemplo</label>
                        <textarea name="exemplo" class="form-control" rows="3" placeholder="Use em uma frase ou contexto." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">URL da imagem</label>
                        <input type="file" name="imagem" accept="image/*" required>
                        <div class="form-text">Selecione uma imagem relacionada ao termo.</div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                        <i class="bi bi-send-fill me-2"></i> Enviar para o Professor
                    </button>
                </form>
            </div>

        <?php elseif ($_SESSION['tipo'] === 'professor'): ?>
            <h2 class="fw-bold text-dark mb-2">Termos pendentes</h2>
            <p class="text-muted mb-4">Aprove ou rejeite termos enviados pelos alunos.</p>

            <div id="lista-pendentes">
                <div class="text-center text-muted mt-5">
                    <div class="spinner-border spinner-accent mb-2" role="status"></div>
                    <p>Carregando termos pendentes...</p>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <script>
    (function(){
        const div = document.getElementById('lista-pendentes');
        const categoriaId = <?php echo isset($_SESSION['categoria_id']) ? intval($_SESSION['categoria_id']) : 0; ?>;

        async function carregarPendentes() {
            div.innerHTML = '<div class="text-center text-muted mt-5"><div class="spinner-border spinner-accent mb-2" role="status"></div><p>Carregando termos pendentes...</p></div>';
            try {
                const resp = await fetch(`api/api_termos_pendentes.php?categoria_id=${categoriaId}`);
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
                                <h5 class="fw-bold mb-1">${escapeHtml(t.palavra)}</h5>
                                ${t.exemplo ? `<p class="mb-2 text-secondary"><strong>Exemplo:</strong> ${escapeHtml(t.exemplo)}</p>` : ''}
                                <div class="text-muted small mb-2">Enviado por ${escapeHtml(t.nome_aluno)}</div>
                                <p class="mb-3 text-secondary">${escapeHtml(descricaoCurta)}</p>
                                ${t.imagem ? `<img src="${escapeHtml(t.imagem)}" alt="${escapeHtml(t.palavra)}" class="img-fluid rounded mb-3" />` : ''}
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
                div.innerHTML = '<div class="alert alert-danger">Erro ao carregar termos pendentes.</div>';
            }
        }

        async function atualizarStatus(id, status) {
            try {
                const resp = await fetch('api/api_atualizar_termo.php', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id, status: status })
                });
                const resultado = await resp.json();
                if (resultado.sucesso) {
                    await carregarPendentes();
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

        // Inicializa
        carregarPendentes();
    })();
    </script>

<?php include __DIR__ . '/inc/footer.php'; ?>
