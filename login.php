<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dicionário Digital - Login</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --azul-suave: rgba(13, 110, 253, 0.1);
            --azul-foco: rgba(13, 110, 253, 0.5);
            --cor-primaria: #334155; 
            --cor-hover: #1e293b;
            --bg-body: #f1f5f9;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #cbd5e1;
        }

        body {
            background-color: var(--bg-body);
            background-image: radial-gradient(circle at top right, var(--azul-suave) 0%, transparent 40%);
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 420px;
            padding: 3.5rem 2.5rem;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(13, 110, 253, 0.05);
            text-align: center;
        }

        .icon-box {
            width: 64px;
            height: 64px;
            background-color: var(--azul-suave); 
            color: #093b83;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
        }

        h2 {
            color: var(--text-main);
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        p.subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 2.5rem;
        }

        .form-label {
            display: block;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 0.8rem 1rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 4px var(--azul-suave); 
            outline: none;
        }

        .btn-acesso {
            background-color: var(--cor-primaria);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.9rem;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
            transition: 0.2s ease;
        }

        .btn-acesso:hover {
            background-color: var(--cor-hover);
            transform: translateY(-2px);
        }

        .footer-link {
            margin-top: 2rem;
        }

        .footer-link a {
            color: #093b83;
            text-decoration: none;
            font-weight: 600;
        }

        .msg-erro {
            background-color: #fef2f2;
            color: #dc2626;
            padding: 0.75rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: none;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="icon-box">
            <i class="bi bi-person-badge-fill"></i>
        </div>

        <h2>Acesso ao Painel</h2>
        <p class="subtitle">Faça login para gerenciar seus termos.</p>

        <form id="formLogin">
            <div id="mensagem-erro" class="msg-erro"></div>

            <div class="mb-3">
                <label class="form-label">Usuário</label>
                <input type="text" name="email" class="form-control" placeholder="ex: usuario@escola.com" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Senha</label>
                <input type="password" name="senha" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-acesso">Entrar no Sistema</button>
        </form>

        <div class="footer-link">
            <a href="index.php"><i class="bi bi-arrow-left"></i> Voltar</a>
        </div>
    </div>

    <script>
        document.getElementById('formLogin').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const dados = new FormData(form);
            const msgErro = document.getElementById('mensagem-erro');

            try {
                const resposta = await fetch('api/api_login.php', {
                    method: 'POST',
                    body: dados
                });
                const resultado = await resposta.json();

                if (resultado.sucesso) {
                    window.location.href = resultado.redirecionar;
                } else {
                    msgErro.innerText = resultado.erro;
                    msgErro.style.display = 'block';
                }
            } catch (erro) {
                msgErro.innerText = "Erro ao conectar.";
                msgErro.style.display = 'block';
            }
        });
    </script>
</body>
</html>