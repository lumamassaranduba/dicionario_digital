<?php
$bodyClass = '';
include __DIR__ . '/inc/header.php';
?>

    <header class="p-4">
        <h1 class="h3 fw-bold text-accent mb-1">Minha Conta</h1>
        <p class="text-muted mb-0">Atualize seu e-mail e senha de acesso.</p>
    </header>

    <section class="p-4 p-md-5 pt-0 w-100" style="max-width: 600px; margin: 0 auto;">
        <div class="card bg-white p-4 shadow-sm rounded-4">
            <div id="conta-mensagem" class="alert d-none" role="alert"></div>
            <form id="formConta">
                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input id="conta-email" name="email" type="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nova senha</label>
                    <input id="conta-senha" name="senha" type="password" class="form-control">
                    <div class="form-text">Deixe em branco para manter a senha atual.</div>
                </div>

                <button type="submit" class="btn btn-primary">Salvar alterações</button>
            </form>
        </div>
    </section>

    <script>
    // Carregar dados atuais do professor (nome/email) usando fetch simples
    async function carregarConta() {
        try {
            const resp = await fetch('api/api_turmas.php');
            if (!resp.ok) throw new Error('HTTP ' + resp.status);
            // Reutilizamos a API temporariamente para obter os dados de sessão no frontend
            // Melhor seria criar uma API dedicada '/api/api_me.php' — posso adicionar depois.
        } catch (err) {
            console.warn('Não foi possível buscar dados iniciais da conta.', err);
        }
    }

    document.getElementById('formConta').addEventListener('submit', async function(e){
        e.preventDefault();
        const msg = document.getElementById('conta-mensagem');
        msg.className = 'alert d-none';

        const email = document.getElementById('conta-email').value.trim();
        const senha = document.getElementById('conta-senha').value;

        if (email === '') {
            msg.className = 'alert alert-danger'; msg.innerText = 'E-mail não pode ficar vazio.'; return;
        }

        try {
            const resposta = await fetch('api/api_atualizar_professor.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: email, senha: senha })
            });
            const resultado = await resposta.json();
            if (resultado.sucesso) {
                msg.className = 'alert alert-success'; msg.innerText = resultado.mensagem || 'Atualizado com sucesso.';
            } else {
                msg.className = 'alert alert-danger'; msg.innerText = resultado.erro || 'Erro ao salvar.';
            }
        } catch (err) {
            console.error(err);
            msg.className = 'alert alert-danger'; msg.innerText = 'Erro ao conectar com o servidor.';
        }
    });
    </script>

<?php include __DIR__ . '/inc/footer.php'; ?>
