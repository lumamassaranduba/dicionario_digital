<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor') {
    header('Location: login.php');
    exit;
}

$bodyClass = '';
include __DIR__ . '/inc/header.php';
?>

    <header class="p-4 d-flex align-items-center w-100" style="max-width: 1000px; margin: 0 auto;">
        <div class="search-wrapper" style="max-width: 600px; width: 100%;">
            <i class="bi bi-search search-icon"></i>
            <input id="search-input" type="text" class="form-control form-control-lg search-bar fs-6" placeholder="Busque por termos rejeitados...">
        </div>
    </header>

    <section class="p-4 p-md-5 pt-0 w-100" style="max-width: 1000px; margin: 0 auto;">
        <h2 class="fw-bold text-accent mb-2">Termos Rejeitados</h2>
        <p class="text-muted mb-4">Veja os termos recusados, edite ou aceite para reaproveitar.</p>

        <div id="lista-rejeitados">
            <div class="text-center text-muted mt-5">
                <div class="spinner-border spinner-accent mb-2" role="status"></div>
                <p>Carregando termos rejeitados...</p>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalEditarTermo" tabindex="-1" aria-labelledby="modalEditarTermoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarTermoLabel">Editar termo rejeitado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div id="alert-editar" class="alert d-none" role="alert"></div>
                    <input type="hidden" id="editar-id">
                    <div class="mb-3">
                        <label class="form-label">Palavra</label>
                        <input id="editar-palavra" type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea id="editar-descricao" rows="4" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Exemplo</label>
                        <textarea id="editar-exemplo" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button id="btn-salvar" type="button" class="btn btn-primary">Salvar e atualizar</button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="categoria-prof" value="<?php echo intval($_SESSION['categoria_id'] ?? 1); ?>">

    <script>
        let termosRejeitados = [];

        async function carregarRejeitados() {
            const categoriaId = document.getElementById('categoria-prof').value;
            const div = document.getElementById('lista-rejeitados');
            div.innerHTML = '<div class="text-center text-muted mt-5"><div class="spinner-border spinner-accent mb-2" role="status"></div><p>Carregando termos rejeitados...</p></div>';
            try {
                const resp = await fetch(`api/api_termos_rejeitados.php?categoria_id=${categoriaId}`);
                if (!resp.ok) throw new Error('HTTP ' + resp.status);
                const dados = await resp.json();
                termosRejeitados = Array.isArray(dados) ? dados : [];
                renderizarRejeitados(termosRejeitados);
            } catch (err) {
                console.error(err);
                div.innerHTML = '<div class="alert alert-danger">Erro ao buscar termos rejeitados.</div>';
            }
        }

        function renderizarRejeitados(termos) {
            const div = document.getElementById('lista-rejeitados');
            const query = document.getElementById('search-input').value.trim().toLowerCase();
            const filtrados = termos.filter(t => {
                const palavra = (t.palavra || '').toLowerCase();
                const desc = (t.descricao || '').toLowerCase();
                const exemplo = (t.exemplo || '').toLowerCase();
                return query === '' || palavra.includes(query) || desc.includes(query) || exemplo.includes(query);
            });

            if (!Array.isArray(filtrados) || filtrados.length === 0) {
                div.innerHTML = '<div class="alert alert-light text-center text-muted shadow-sm rounded-4 py-4">Nenhum termo rejeitado encontrado.</div>';
                return;
            }

            let html = '';
            filtrados.forEach(termo => {
                html += `
                    <div class="card cartao-termo mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between gap-3 align-items-start">
                                <div class="flex-grow-1">
                                    <h5 class="fw-bold text-danger mb-1">${escapeHtml(termo.palavra || '')}</h5>
                                    <div class="text-muted small mb-2">Enviado por <strong>${escapeHtml(termo.nome_aluno || 'Desconhecido')}</strong></div>
                                    <p class="text-secondary mb-2">${escapeHtml(termo.descricao || '')}</p>
                                    ${termo.exemplo ? `<p class="text-muted mb-2"><strong>Exemplo:</strong> ${escapeHtml(termo.exemplo)}</p>` : ''}
                                    ${termo.imagem ? `<img src="${escapeHtml(termo.imagem)}" alt="${escapeHtml(termo.palavra || '')}" class="img-fluid rounded" style="max-width:180px;">` : ''}
                                </div>
                                <div class="d-flex flex-column gap-2 text-end">
                                    <button class="btn btn-sm btn-outline-primary btn-acao" onclick="abrirEdicao(${termo.id}, ${JSON.stringify(termo.palavra || '')}, ${JSON.stringify(termo.descricao || '')}, ${JSON.stringify(termo.exemplo || '')})">Editar</button>
                                    <button class="btn btn-sm btn-success btn-acao" onclick="mudarStatus(${termo.id}, 'aprovado')">Aceitar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            div.innerHTML = html;
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        async function mudarStatus(id, novoStatus) {
            if (!confirm(`Tem certeza que deseja marcar este termo como ${novoStatus}?`)) return;
            try {
                const resp = await fetch('api/api_atualizar_termo.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, status: novoStatus })
                });
                const resultado = await resp.json();
                if (resultado.sucesso) {
                    await carregarRejeitados();
                    alert('Termo atualizado com sucesso.');
                } else {
                    alert('Erro: ' + (resultado.erro || 'Não foi possível atualizar.'));
                }
            } catch (err) {
                console.error(err);
                alert('Erro ao conectar com o servidor.');
            }
        }

        function abrirEdicao(id, palavra, descricao, exemplo) {
            document.getElementById('editar-id').value = id;
            document.getElementById('editar-palavra').value = palavra || '';
            document.getElementById('editar-descricao').value = descricao || '';
            document.getElementById('editar-exemplo').value = exemplo || '';
            const alertEditar = document.getElementById('alert-editar');
            alertEditar.className = 'alert d-none';
            const modal = new bootstrap.Modal(document.getElementById('modalEditarTermo'));
            modal.show();
        }

        document.getElementById('btn-salvar').addEventListener('click', async function() {
            const id = document.getElementById('editar-id').value;
            const palavra = document.getElementById('editar-palavra').value.trim();
            const descricao = document.getElementById('editar-descricao').value.trim();
            const exemplo = document.getElementById('editar-exemplo').value.trim();
            const alertEditar = document.getElementById('alert-editar');

            if (!palavra || !descricao) {
                alertEditar.className = 'alert alert-danger';
                alertEditar.innerText = 'Palavra e descrição são obrigatórias.';
                return;
            }

            const form = new FormData();
            form.append('id', id);
            form.append('palavra', palavra);
            form.append('descricao', descricao);
            form.append('exemplo', exemplo);
            form.append('categoria_id', document.getElementById('categoria-prof').value);

            try {
                const resp = await fetch('api/api_editar_termo.php', {
                    method: 'POST',
                    body: form
                });
                const json = await resp.json();
                if (json.sucesso) {
                    alertEditar.className = 'alert alert-success';
                    alertEditar.innerText = 'Termo editado com sucesso!';
                    carregarRejeitados();
                } else {
                    alertEditar.className = 'alert alert-danger';
                    alertEditar.innerText = json.erro || 'Erro ao editar termo.';
                }
            } catch (err) {
                console.error(err);
                alertEditar.className = 'alert alert-danger';
                alertEditar.innerText = 'Erro de conexão com o servidor.';
            }
        });

        document.getElementById('search-input').addEventListener('input', function () {
            renderizarRejeitados(termosRejeitados);
        });

        carregarRejeitados();
    </script>

<?php include __DIR__ . '/inc/footer.php'; ?>
