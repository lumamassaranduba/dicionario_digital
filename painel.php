<?php
$bodyClass = '';
include __DIR__ . '/inc/header.php';
?>

    <header class="p-4 d-flex align-items-center justify-content-between w-100" style="max-width: 1000px; margin: 0 auto;">
        <div>
            <h1 class="h3 fw-bold text-accent mb-1">Meu Painel</h1>
            <p class="text-muted mb-0">Aprove termos enviados.</p>
        </div>
    </header>

    <section class="p-4 p-md-5 pt-0 w-100" style="max-width: 1000px; margin: 0 auto;">
        <?php if (! isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor'): ?>
            <div class="alert alert-warning">Acesso negado. Apenas professores podem acessar esta página.</div>
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
                    if (document.getElementById('lista-pendentes-perfil')) {
                        carregarPendentesPerfil();
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
                    const enviadoPor = t.usuario_id ? 'Adicionado por professor' : `Enviado por ${t.nome_sala}`;
                    html += `
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="fw-bold mb-1">${t.palavra}</h5>
                                ${t.exemplo ? `<p class="mb-2 text-secondary"><strong>Exemplo:</strong> ${t.exemplo}</p>` : ''}
                                <div class="text-muted small mb-2">${enviadoPor}</div>
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

        // Inicializa lista de pendentes
        carregarPendentesPerfil();
    </script>

<?php include __DIR__ . '/inc/footer.php'; ?>