<?php
$bodyClass = '';
include __DIR__ . '/inc/header.php';
?>

    <header class="p-4">
        <h1 class="h3 fw-bold text-accent mb-1">Termos aprovados</h1>
        <p class="text-muted mb-0">Edite ou exclua termos aprovados.</p>
    </header>

    <section class="p-4 p-md-5 pt-0 w-100" style="max-width: 1000px; margin: 0 auto;">
        <div id="area-edicao" class="mb-4 d-none">
            <div class="card bg-white p-4 shadow-sm rounded-4">
                <h5 class="mb-3">Editar termo</h5>
                <div id="edicao-mensagem" class="alert d-none"></div>
                <input type="hidden" id="editar-id">
                <div class="mb-3">
                    <label class="form-label">Palavra / Termo</label>
                    <input id="editar-palavra" class="form-control form-control-lg" type="text">
                </div>
                <div class="mb-3">
                    <label class="form-label">Categoria</label>
                    <select id="editar-categoria" class="form-select"></select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea id="editar-descricao" class="form-control" rows="4"></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button id="btn-salvar-termo" class="btn btn-primary">Salvar</button>
                    <button id="btn-cancelar-edicao" class="btn btn-secondary">Cancelar</button>
                </div>
            </div>
        </div>

        <div id="lista-aprovados">
            <div class="text-center text-muted mt-5">
                <div class="spinner-border spinner-accent mb-2" role="status"></div>
                <p>Carregando termos aprovados...</p>
            </div>
        </div>
    </section>

    <script>
        async function carregarCategorias() {
            try {
                const resp = await fetch('api/api_termos.php?categoria=1'); // endpoint existente para garantir categorias
                // Não usamos a resposta; vamos buscar categorias via PHP seria melhor — mas como não há API, preenchemos manualmente
            } catch (err) {
                // ignore
            }
            // Hardcode categories based on DB (Português=1, Matemática=2)
            const sel = document.getElementById('editar-categoria');
            sel.innerHTML = '<option value="1">Português</option><option value="2">Matemática</option>';
        }

        async function carregarAprovados() {
            const div = document.getElementById('lista-aprovados');
            div.innerHTML = '<div class="text-center text-muted mt-5"><div class="spinner-border spinner-accent mb-2" role="status"></div><p>Carregando termos aprovados...</p></div>';
            try {
                const resp = await fetch('api/api_termos_aprovados.php');
                if (!resp.ok) throw new Error('HTTP ' + resp.status);
                const termos = await resp.json();
                if (!Array.isArray(termos) || termos.length === 0) {
                    div.innerHTML = '<div class="alert alert-light text-center text-muted shadow-sm rounded-4 py-4">Nenhum termo aprovado encontrado.</div>';
                    return;
                }

                let html = '';
                termos.forEach(t => {
                    const descricaoCurta = t.descricao.length > 220 ? t.descricao.substring(0,220) + '...' : t.descricao;
                    html += `
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="fw-bold mb-1">${escapeHtml(t.palavra)}</h5>
                                        <div class="text-muted small mb-2">Categoria: ${escapeHtml(t.categoria || '—')} • Enviado por ${escapeHtml(t.enviado_por)}</div>
                                        <p class="mb-0 text-secondary lh-lg">${escapeHtml(descricaoCurta)}</p>
                                    </div>
                                    <div class="ms-3 text-end">
                                        <button class="btn btn-sm btn-outline-primary btn-editar" data-id="${t.id}" data-palavra="${escapeAttr(t.palavra)}" data-descricao="${escapeAttr(t.descricao)}" data-categoria="${t.categoria_id}">Editar</button>
                                        <button class="btn btn-sm btn-danger btn-deletar" data-id="${t.id}">Excluir</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                div.innerHTML = html;

                document.querySelectorAll('.btn-editar').forEach(b => b.addEventListener('click', abrirEdicao));
                document.querySelectorAll('.btn-deletar').forEach(b => b.addEventListener('click', deletarTermo));

            } catch (err) {
                console.error(err);
                div.innerHTML = '<div class="alert alert-danger">Erro ao carregar termos aprovados.</div>';
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>\"]/g, function(s) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'})[s]; });
        }
        function escapeAttr(str) { return escapeHtml(str).replace(/'/g, '&#39;'); }

        function abrirEdicao(e) {
            const btn = e.currentTarget;
            const id = btn.getAttribute('data-id');
            const palavra = btn.getAttribute('data-palavra');
            const descricao = btn.getAttribute('data-descricao');
            const categoria = btn.getAttribute('data-categoria') || '1';

            document.getElementById('edicao-mensagem').className = 'alert d-none';
            document.getElementById('editar-id').value = id;
            document.getElementById('editar-palavra').value = palavra;
            document.getElementById('editar-descricao').value = descricao;
            document.getElementById('editar-categoria').value = categoria;
            document.getElementById('area-edicao').classList.remove('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        document.getElementById('btn-cancelar-edicao').addEventListener('click', function(){
            document.getElementById('area-edicao').classList.add('d-none');
        });

        document.getElementById('btn-salvar-termo').addEventListener('click', async function(){
            const id = document.getElementById('editar-id').value;
            const palavra = document.getElementById('editar-palavra').value.trim();
            const descricao = document.getElementById('editar-descricao').value.trim();
            const categoria = parseInt(document.getElementById('editar-categoria').value, 10) || 1;
            const msg = document.getElementById('edicao-mensagem');
            msg.className = 'alert d-none';

            if (palavra === '' || descricao === '') { msg.className = 'alert alert-danger'; msg.innerText = 'Palavra e descrição não podem ficar vazias.'; return; }

            try {
                const resp = await fetch('api/api_editar_termo.php', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id, palavra: palavra, descricao: descricao, categoria_id: categoria })
                });
                const resultado = await resp.json();
                if (resultado.sucesso) {
                    msg.className = 'alert alert-success'; msg.innerText = resultado.mensagem || 'Salvo.';
                    // refresh list
                    await carregarAprovados();
                    // esconder area de edicao
                    document.getElementById('area-edicao').classList.add('d-none');
                } else {
                    msg.className = 'alert alert-danger'; msg.innerText = resultado.erro || 'Erro ao salvar.';
                }
            } catch (err) {
                console.error(err);
                msg.className = 'alert alert-danger'; msg.innerText = 'Erro ao conectar com o servidor.';
            }
        });

        async function deletarTermo(e) {
            const id = e.currentTarget.getAttribute('data-id');
            if (!confirm('Tem certeza que deseja excluir este termo? Esta ação não pode ser desfeita.')) return;
            try {
                const resp = await fetch('api/api_deletar_termo.php', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id: id })
                });
                const resultado = await resp.json();
                if (resultado.sucesso) {
                    await carregarAprovados();
                } else {
                    alert(resultado.erro || 'Erro ao excluir.');
                }
            } catch (err) {
                console.error(err);
                alert('Erro ao conectar com o servidor.');
            }
        }

        // inicialização
        carregarCategorias();
        carregarAprovados();
    </script>

<?php include __DIR__ . '/inc/footer.php'; ?>
