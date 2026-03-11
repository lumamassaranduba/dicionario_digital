<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dicionário Técnico - Login</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --bg-body: #f1f5f9;      /* Slate 100 */
            --text-main: #1e293b;    /* Slate 800 */
            --text-muted: #64748b;  /* Slate 500 */
            --primary: #e11d48;     /* Crimson / Rose 600 */
            --primary-hover: #be123c;
            --border: #cbd5e1;
        }

        body {
            background-color: var(--bg-body);
            background-image: radial-gradient(circle at top right, #e2e8f0 0%, transparent 40%),
                              radial-gradient(circle at bottom left, #e2e8f0 0%, transparent 40%);
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        /* O Card de Destaque */
        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 420px;
            padding: 3rem 2.5rem;
            border-radius: 24px; /* Cantos bem arredondados e modernos */
            
            /* Sombra multicamadas para dar profundidade real */
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 
                        0 10px 10px -5px rgba(0, 0, 0, 0.04);
            
            border: 1px solid rgba(255, 255, 255, 0.7);
            text-align: center;
        }

        .icon-box {
            width: 60px;
            height: 60px;
            background-color: rgba(225, 29, 72, 0.1);
            color: var(--primary);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 1.5rem;
        }

        h2 {
            color: var(--text-main);
            font-weight: 700;
            font-size: 1.75rem;
            letter-spacing: -0.025em;
            margin-bottom: 0.5rem;
        }

        p.subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 2.5rem;
        }

        /* Inputs Customizados */
        .form-label {
            display: block;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.2s;
            background-color: #f8fafc;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(225, 29, 72, 0.1);
            outline: none;
        }

        /* Botão Crimson */
        .btn-crimson {
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.8rem;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.2s;
        }

        .btn-crimson:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(225, 29, 72, 0.3);
        }

        .btn-crimson:active {
            transform: translateY(0);
        }

        .footer-link {
            margin-top: 2rem;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .footer-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="icon-box">
            <i class="bi bi-journal-bookmark-fill"></i>
        </div>
        
        <h2>Bem-vindo</h2>
        <p class="subtitle">Acesse o dicionário técnico da sua sala.</p>

        <form>
            <div class="mb-3">
                <label class="form-label">Usuário</label>
                <input type="text" class="form-control" placeholder="ex: profmat@gmail.com" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Senha</label>
                <input type="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-crimson">Acessar Sistema</button>
        </form>

    </div>

</body>
</html>