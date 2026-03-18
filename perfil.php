<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor') {
    header('Location: login.php');
    exit;
}

$bodyClass = '';
include __DIR__ . '/inc/header.php';
?>

    <header class="p-4">
        <h1 class="h3 fw-bold text-accent mb-1">Minha Conta</h1>
        <p class="text-muted mb-0">Atualize seu e-mail e senha.</p>
    </header>

    <section class="p-4 p-md-5 pt-0 w-100" style="max-width: 1000px; margin: 0 auto;">
        <div class="card bg-white p-4 shadow-sm rounded-4" style="max-width: 600px; margin: 0 auto;">
            <div id="alerta-perfil" class="alert d-none" role="alert"></div>

            <form id="formPerfil">
                <div class="mb-3">
                    <label class="form-label fw-semibold">E-mail</label>
                    <input id="perfil-email" type="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Senha atual</label>
                    <input id="perfil-senha-atual" type="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nova senha</label>
                    <input id="perfil-nova-senha" type="password" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Confirmar nova senha</label>
                    <input id="perfil-confirmar-senha" type="password" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                    <i class="bi bi-check2-circle me-2"></i> Salvar alterações
                </button>
            </form>
        </div>

        
    </section>

    <script>
        async function atualizarStatus(id, status) {
            try {
                const resp = await fetch('api/api_atualizar_termo.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, status })
                });
                const json = await resp.json();
                if (json.sucesso) {
                    carregarPendentesPerfil(); // Recarrega a lista
                } else {
                    alert('Erro: ' + (json.erro || 'Falha ao atualizar.'));
                }
            } catch (err) {
                console.error(err);
                alert('Erro ao conectar com o servidor.');
            }
        }

        const formPerfil = document.getElementById('formPerfil');
        formPerfil.addEventListener('submit', async function(e) {
            e.preventDefault();
            const alerta = document.getElementById('alerta-perfil');
            const email = document.getElementById('perfil-email').value.trim();
            const senhaAtual = document.getElementById('perfil-senha-atual').value;
            const novaSenha = document.getElementById('perfil-nova-senha').value;
            const confirmarSenha = document.getElementById('perfil-confirmar-senha').value;

            alerta.className = 'alert d-none';

            try {
                const resp = await fetch('api/api_atualizar_perfil.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        email,
                        senha_atual: senhaAtual,
                        nova_senha: novaSenha,
                        confirmar_senha: confirmarSenha
                    })
                });
                const json = await resp.json();
                if (json.sucesso) {
                    alerta.className = 'alert alert-success';
                    alerta.innerText = json.mensagem || 'Dados atualizados com sucesso.';
                } else {
                    alerta.className = 'alert alert-danger';
                    alerta.innerText = json.erro || 'Erro ao atualizar dados.';
                }
            } catch (err) {
                console.error(err);
                alerta.className = 'alert alert-danger';
                alerta.innerText = 'Erro ao conectar com o servidor.';
            }
        });
    </script>

<?php include __DIR__ . '/inc/footer.php'; ?>
