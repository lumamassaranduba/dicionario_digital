<?php
$bodyClass = '';
include __DIR__ . '/inc/header.php';
?>

    <header class="p-4">
        <h1 class="h3 fw-bold text-accent mb-1">Bem-vindo ao Dicionário</h1>
        <p class="text-muted mb-0">Escolha a matéria para explorar os termos.</p>
    </header>

    <section class="p-4 p-md-5 pt-0 w-100" style="max-width: 900px; margin: 0 auto;">
        <div class="row g-4">
            <div class="col-md-6">
                <a href="termos.php?categoria=1" class="card cartao-termo h-100 text-decoration-none">
                    <div class="card-body">
                        <h3 class="card-title fw-bold">Português</h3>
                        <p class="card-text text-muted">Explore termos aprovados para Português.</p>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a href="termos.php?categoria=2" class="card cartao-termo h-100 text-decoration-none" style="border-left-color: #dc3545;">
                    <div class="card-body">
                        <h3 class="card-title fw-bold">Matemática</h3>
                        <p class="card-text text-muted">Explore termos aprovados para Matemática.</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/inc/footer.php'; ?>
