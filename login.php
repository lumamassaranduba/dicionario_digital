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
            /* Paleta Grafite Neutra (Slate) */
            --cor-primaria: #334155; /* Slate 700 - Grafite Profissional */
            --cor-hover: #1e293b;    /* Slate 800 - Quase Preto mas ainda Cinza */
            --bg-body: #f1f5f9;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #cbd5e1;
        }

        body {
            background-color: var(--bg-body);
            /* Background limpo com toques sutis de cinza */
            background-image: radial-gradient(circle at 10% 20%, rgba(51, 65, 85, 0.05) 0%, transparent 20%),
                              radial-gradient(circle at 90% 80%, rgba(51, 65, 85, 0.05) 0%, transparent 20%);
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
            /* Sombra profunda para dar destaque ao card branco */
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.1),
                        0 10px 10px -5px rgba(15, 23, 42, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.7);
            text-align: center;
        }

        .icon-box {
            width: 64px;
            height: 64px;
            background-color: #f8fafc;
            color: var(--cor-primaria);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            border: 1px solid var(--border);
        }

        h2 {
            color: var(--text-main);
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: -0.025em;
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
            background-color: #ffffff;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: var(--cor-primaria);
            box-shadow: 0 0 0 4px rgba(51, 65, 85, 0.1);
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
            transition: all 0.2s ease;
        }

        .btn-acesso:hover {
            background-color: var(--cor-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2);
        }

        .footer-link {
            margin-top: 2rem;
            font-size: 0.85rem;
        }

        .footer-link a {
            color: var(--cor-primaria);
            text-decoration: none;
            font-weight: 700;
        }

        .footer-link a:hover {
            text-decoration: underline;
        }

        .msg-erro {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fee2e2;
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
            <i class="bi bi-person-lock"></i>
        </div>

        <h2>Acesso ao Dicionário</h2>
        <p class="subtitle">Insira suas credenciais para continuar.</p>

        <form id="formLogin">
            <div id="mensagem-erro" class="msg-erro"></div>

            <div class="mb-3">
                <label class="form-label">Usuário ou E-mail</label>
                <input type="text" name="email" class="form-control" placeholder="ex: usuario@escola.com" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Senha</label>
                <input type="password" name="senha" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-acesso">Entrar no Sistema</button>
        </form>

        <div class="footer-link">
            <a href="index.php"><i class="bi bi-arrow-left"></i> Voltar à página inicial</a>
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
                msgErro.innerText = "Erro ao conectar com o servidor.";
                msgErro.style.display = 'block';
            }
        });
    </script>
</body>

</html>