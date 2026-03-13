<?php
// Cabeçalho comum: inicia sessão e imprime a parte superior da página (head + sidebar)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarioLogado = isset($_SESSION['usuario_id']);
$usuarioTipo = $usuarioLogado ? $_SESSION['tipo'] : null;
$usuarioNome = $usuarioLogado ? $_SESSION['nome'] : null;

// Classe do body pode ser definida pela página antes do include
if (!isset($bodyClass)) $bodyClass = '';

function isActive($name) {
    $current = basename($_SERVER['PHP_SELF']);
    return $current === $name;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dicionário Técnico SENAI</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --accent: #0c3a80ff;
            --accent-fade: rgba(13, 110, 253, 0.1);
            --sombra-leve: 0 4px 6px -1px rgba(13, 110, 253, 0.08), 0 2px 4px -1px rgba(13, 110, 253, 0.04);
            --sombra-forte: 0 20px 25px -5px rgba(13, 110, 253, 0.15), 0 10px 10px -5px rgba(13, 110, 253, 0.08);
        }

        .text-accent { color: var(--accent) !important; }
        .btn-accent { background-color: var(--accent); border-color: var(--accent); color: #fff; }
        .btn-accent:hover { background-color: var(--accent); opacity: 0.9; }
        .spinner-accent { color: var(--accent); }

        .categoria-matematica { --accent: #70040fff; --accent-fade: rgba(220,53,69,0.1); --sombra-leve: 0 4px 6px -1px rgba(220,53,69,0.08),0 2px 4px -1px rgba(220,53,69,0.04); --sombra-forte: 0 20px 25px -5px rgba(220,53,69,0.15),0 10px 10px -5px rgba(220,53,69,0.08); }

        body { font-family: 'Inter', sans-serif; background-color: #f4f7f9; }
        .sidebar { width: 280px; box-shadow: var(--sombra-leve); z-index: 10; min-height: 100vh; }
        .nav-link { color: #6c757d; font-weight: 500; border-radius: 0.5rem; transition: all 0.3s ease; }
        .nav-link:hover { background-color: var(--accent-fade); color: var(--accent); transform: translateX(5px); }
        .nav-link.active { background-color: var(--accent) !important; color: #fff !important; box-shadow: var(--sombra-leve); }
        .search-wrapper { position: relative; max-width: 600px; width: 100%; }
        .search-bar { padding-left: 2.8rem; border-radius: 1rem; border: none; box-shadow: var(--sombra-leve); transition: all 0.3s ease; }
        .search-bar:focus { box-shadow: var(--sombra-forte); }
        .search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #adb5bd; }
        .cartao-termo { border: none; border-left: 6px solid var(--accent); border-radius: 1rem; box-shadow: var(--sombra-leve); transition: all 0.3s ease; }
        .cartao-termo:hover { transform: translateY(-5px); box-shadow: var(--sombra-forte); }
        .avatar-sala { width: 32px; height: 32px; background-color: var(--accent-fade); color: var(--accent); }
        .btn-acao { box-shadow: var(--sombra-leve); transition: all 0.3s; }
        .btn-acao:hover { transform: translateY(-3px); box-shadow: var(--sombra-forte); }
        .sair{ border: 2px solid #70040fff}
        .sair:hover{background-color: #70040fff; color: white}
    </style>
</head>

<body class="d-flex flex-column flex-md-row <?php echo htmlspecialchars($bodyClass); ?>">

    <nav class="sidebar bg-white d-flex flex-column p-4">
        <a href="home.php" class="d-flex align-items-center mb-4 text-decoration-none text-accent fs-4 fw-bold">
            <i class="bi bi-book-half me-2"></i> Dicionário Digital
        </a>

        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-2">
                <a id="menu-home" href="home.php" class="nav-link <?php echo isActive('home.php') ? 'active' : ''; ?>">
                    <i class="bi bi-house-door me-2"></i> Início
                </a>
            </li>
            <li class="mt-4 mb-2 text-muted text-uppercase small fw-bold px-3">Categorias</li>
            <li class="nav-item mb-2">
                <a id="menu-cat-1" href="termos.php?categoria=1" class="nav-link <?php echo isActive('termos.php') && (isset($_GET['categoria']) ? intval($_GET['categoria']) === 1 : false) ? 'active' : ''; ?>">
                    <i class="bi bi-journal-text me-2"></i> Português
                </a>
            </li>
            <li class="nav-item mb-2">
                <a id="menu-cat-2" href="termos.php?categoria=2" class="nav-link <?php echo isActive('termos.php') && (isset($_GET['categoria']) ? intval($_GET['categoria']) === 2 : false) ? 'active' : ''; ?>">
                    <i class="bi bi-calculator me-2"></i> Matemática
                </a>
            </li>
            <?php if ($usuarioLogado): ?>
                <li class="nav-item mb-2">
                    <a id="menu-painel" href="painel.php" class="nav-link <?php echo isActive('painel.php') ? 'active' : ''; ?>">
                        <i class="bi bi-person-workspace me-2"></i> Aprovar Termos
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <hr>

        <div class="mt-auto">
            <?php if ($usuarioLogado): ?>
                <div class="mb-3 text-muted small">
                    Logado como:<br>
                    <strong class="text-dark"><?php echo htmlspecialchars($usuarioNome); ?></strong>
                </div>
                <a href="api/api_logout.php" class="btn w-100 fw-bold py-2 sair" >
                    <i class="bi bi-box-arrow-right me-2"></i> Sair
                </a>
            <?php else: ?>
                <a href="login.php" class="btn w-100 fw-bold py-2 btn-acao" style="background-color: rgba(13, 110, 253, 0.1);">
                    <i class="bi bi-person-circle me-2"></i> Acessar Conta
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="flex-grow-1 d-flex flex-column" style="height: 100vh; overflow-y: auto;">
