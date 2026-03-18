<?php
$bodyClass = '';
include __DIR__ . '/inc/header.php';
?>

    <header class="p-4">
        <h1 class="h3 fw-bold text-accent mb-1">Registrar Novo Termo</h1>
        <p class="text-muted mb-0">Adicione um termo ao dicionário.</p>
    </header>

    <section class="p-4 p-md-5 pt-0 w-100" style="max-width: 900px; margin: 0 auto;">
        <div class="card bg-white p-4 shadow-sm rounded-4">
            <div id="alerta-termo" class="alert d-none" role="alert"></div>

            <form id="formRegistrarTermo">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Palavra / Termo</label>
                        <input type="text" name="palavra" class="form-control" placeholder="Ex: Algoritmo" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Matéria (Categoria)</label>
                        <select name="categoria_id" class="form-select" required>
                            <option value="" disabled selected>Escolha a matéria...</option>
                            <option value="1">Português</option>
                            <option value="2">Matemática</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Sala</label>
                    <select name="sala_id" class="form-select" required>
                        <option value="" disabled selected>Escolha a sala...</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Significado</label>
                    <textarea name="descricao" class="form-control" rows="4" placeholder="Explique o que significa..." required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Exemplo</label>
                    <textarea name="exemplo" class="form-control" rows="3" placeholder="Use em uma frase ou contexto." required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Imagem</label>
                    <input type="file" name="imagem" accept="image/*" class="form-control">
                    <div class="form-text">Selecione uma imagem relacionada ao termo (opcional).</div>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="bi bi-send-fill me-2"></i> Enviar Termo
                </button>
            </form>
        </div>
    </section>

    <script>
        async function carregarSalas() {
            try {
                const resp = await fetch('api/api_salas.php');
                const salas = await resp.json();
                const select = document.querySelector('select[name="sala_id"]');
                select.innerHTML = '<option value="" disabled selected>Escolha a sala...</option>';
                salas.forEach(s => {
                    select.innerHTML += `<option value="${s.id}">${s.nome} (${s.categoria_id === 1 ? 'Português' : 'Matemática'})</option>`;
                });
            } catch (err) {
                console.error(err);
            }
        }

        document.getElementById('formRegistrarTermo').addEventListener('submit', async function(e) {
            e.preventDefault();
            const alerta = document.getElementById('alerta-termo');
            const botao = this.querySelector('button[type="submit"]');

            const formData = new FormData(this);
            botao.disabled = true;
            botao.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';

            try {
                const resp = await fetch('api/api_adicionar_termo.php', { method: 'POST', body: formData });
                const result = await resp.json();
                alerta.classList.remove('d-none', 'alert-danger', 'alert-success');
                if (result.sucesso) {
                    alerta.classList.add('alert-success');
                    alerta.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i> ${result.mensagem}`;
                    this.reset();
                } else {
                    alerta.classList.add('alert-danger');
                    alerta.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i> ${result.erro}`;
                }
            } catch (err) {
                console.error(err);
                alerta.classList.remove('d-none');
                alerta.classList.add('alert-danger');
                alerta.innerHTML = `<i class="bi bi-x-circle-fill me-2"></i> Erro ao conectar com o servidor.`;
            }
            botao.disabled = false;
            botao.innerHTML = '<i class="bi bi-send-fill me-2"></i> Enviar Termo';
        });

        carregarSalas();
    </script>

<?php include __DIR__ . '/inc/footer.php'; ?>