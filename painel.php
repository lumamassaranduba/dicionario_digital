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

<?php include __DIR__ . '/inc/footer.php'; ?>
