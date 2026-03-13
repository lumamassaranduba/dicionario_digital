<?php
$bodyClass = '';
include __DIR__ . '/inc/header.php';
?>

    <header class="p-4">
        <h1 class="h3 fw-bold text-accent mb-1">Turmas</h1>
        <p class="text-muted mb-0">Gerencie nomes das turmas e suas senhas de acesso.</p>
    </header>

    <section class="p-4 p-md-5 pt-0 w-100" style="max-width: 900px; margin: 0 auto;">
        <div id="conteudo-turmas">
            <div class="text-center text-muted mt-5">
                <div class="spinner-border spinner-accent mb-2" role="status"></div>
                <p>Carregando turmas...</p>
            </div>
        </div>
    </section>

    <script>
        async function carregarTurmas() {
            const div = document.getElementById('conteudo-turmas');
            div.innerHTML = '<div class="text-center text-muted mt-5"><div class="spinner-border spinner-accent mb-2" role="status"></div><p>Carregando turmas...</p></div>';

            try {
                const resp = await fetch('api/api_turmas.php');
                if (!resp.ok) throw new Error('HTTP ' + resp.status);
                const turmas = await resp.json();

                if (!Array.isArray(turmas) || turmas.length === 0) {
                    div.innerHTML = '<div class="alert alert-light text-center text-muted shadow-sm rounded-4 py-4">Nenhuma turma encontrada.</div>';
                    return;
                }

                // monta tabela
                let html = `
                    <div class="card bg-white p-3 shadow-sm rounded-4">
                        <table class="table table-borderless align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Turma (nome)</th>
                                    <th style="width:260px">Senha</th>
                                    <th style="width:140px"></th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                turmas.forEach(t => {
                    const escNome = t.nome.replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    const escSenha = t.senha.replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    html += `
                        <tr data-id="${t.id}">
                            <td>${escNome}</td>
                            <td class="senha-cell"><span class="senha-text">${escSenha}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary btn-editar" data-id="${t.id}" data-nome="${escNome}" data-senha="${escSenha}">Editar</button>
                            </td>
                        </tr>
                    `;
                });

                html += '</tbody></table></div>';
                div.innerHTML = html;

                // adiciona handlers: abrir modal para editar
                document.querySelectorAll('.btn-editar').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const id = e.target.getAttribute('data-id');
                        const nome = e.target.getAttribute('data-nome');
                        const senha = e.target.getAttribute('data-senha');

                        // preencher modal
                        document.getElementById('modal-turma-id').value = id;
                        document.getElementById('modal-turma-nome').value = nome;
                        document.getElementById('modal-turma-senha').value = senha;
                        const modal = new bootstrap.Modal(document.getElementById('modalEditarTurma'));
                        modal.show();
                    });
                });

            } catch (err) {
                console.error(err);
                div.innerHTML = '<div class="alert alert-danger">Erro ao carregar turmas.</div>';
            }
        }

        // Carrega ao abrir
        carregarTurmas();
    </script>

<?php include __DIR__ . '/inc/footer.php'; ?>

<!-- Modal de edição -->
<div class="modal fade" id="modalEditarTurma" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Turma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal-turma-id">
                <div class="mb-3">
                    <label class="form-label">Nome da Turma</label>
                    <input id="modal-turma-nome" class="form-control" type="text">
                </div>
                <div class="mb-3">
                    <label class="form-label">Senha</label>
                    <input id="modal-turma-senha" class="form-control" type="text">
                </div>
                <div id="modal-turma-erro" class="text-danger small d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button id="modal-turma-salvar" type="button" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('modal-turma-salvar').addEventListener('click', async function(){
        const id = document.getElementById('modal-turma-id').value;
        const nome = document.getElementById('modal-turma-nome').value.trim();
        const senha = document.getElementById('modal-turma-senha').value.trim();
        const erroDiv = document.getElementById('modal-turma-erro');
        erroDiv.classList.add('d-none');

        if (nome === '') { erroDiv.innerText = 'Nome não pode ficar vazio.'; erroDiv.classList.remove('d-none'); return; }
        if (senha === '') { erroDiv.innerText = 'Senha não pode ficar vazia.'; erroDiv.classList.remove('d-none'); return; }

        try {
                const resp = await fetch('api/api_atualizar_turma.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: id, nome: nome, senha: senha })
                });
                const resultado = await resp.json();
                if (resultado.sucesso) {
                        // atualiza a linha na tabela
                        const tr = document.querySelector(`tr[data-id="${id}"]`);
                        if (tr) {
                                tr.children[0].innerText = nome;
                                tr.querySelector('.senha-text').innerText = senha;
                                // atualizar atributos do botão editar
                                const btn = tr.querySelector('.btn-editar');
                                if (btn) { btn.setAttribute('data-nome', nome); btn.setAttribute('data-senha', senha); }
                        }
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarTurma'));
                        modal.hide();
                } else {
                        erroDiv.innerText = resultado.erro || 'Erro ao atualizar.';
                        erroDiv.classList.remove('d-none');
                }
        } catch (err) {
                console.error(err);
                erroDiv.innerText = 'Erro ao conectar com o servidor.';
                erroDiv.classList.remove('d-none');
        }
});
</script>
