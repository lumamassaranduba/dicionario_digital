<?php
$categoriaId = isset($_GET['categoria']) ? intval($_GET['categoria']) : 1;
if (!in_array($categoriaId, [1,2], true)) $categoriaId = 1;
$bodyClass = ($categoriaId === 2) ? 'categoria-matematica' : '';
include __DIR__ . '/inc/header.php';

$nome = $categoriaId === 2 ? 'Matemática' : 'Português';
?>

    <header class="p-4 d-flex align-items-center justify-content-center w-100" style="max-width: 1000px; margin: 0 auto;">
        <div class="search-wrapper" style="max-width: 600px; width: 100%;">
            <i class="bi bi-search search-icon"></i>
            <input id="search-input" type="text" class="form-control form-control-lg search-bar fs-6" placeholder="Busque por termos, conceitos ou palavras-chave...">
        </div>
    </header>

    <section class="p-4 p-md-5 pt-0 w-100" style="max-width: 1000px; margin: 0 auto;">
        <h2 class="fw-bold text-accent mb-2" id="titulo-categoria"><?php echo htmlspecialchars($nome); ?></h2>

        <div id="lista-termos">
            <div class="text-center text-muted mt-5">
                <div class="spinner-border spinner-accent mb-2" role="status"></div>
                <p>Carregando termos...</p>
            </div>
        </div>
    </section>

    <script>
    (function waitForFuncs(){
        // espera até as funções do footer estarem carregadas
        if (typeof carregarTermos === 'function' && typeof debounce === 'function') {
            const categoriaId = <?php echo json_encode($categoriaId); ?>;
            const nome = <?php echo json_encode($nome); ?>;
            const searchInput = document.getElementById('search-input');
            const buscar = debounce((valor) => carregarTermos(categoriaId, nome, valor), 350);
            if (searchInput) {
                searchInput.addEventListener('input', (event) => {
                    buscar(event.target.value);
                });
            }
            // carregamento inicial
            carregarTermos(categoriaId, nome);
        } else {
            setTimeout(waitForFuncs, 50);
        }
    })();
    </script>

<?php include __DIR__ . '/inc/footer.php'; ?>
