    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function getQueryParam(name) {
            const params = new URLSearchParams(window.location.search);
            return params.get(name);
        }

        function setCategoriaVisual(categoriaId) {
            const root = document.documentElement;
            if (categoriaId === 2) {
                root.classList.add('categoria-matematica');
            } else {
                root.classList.remove('categoria-matematica');
            }
        }

        function mostrarSpinner() {
            const divLista = document.getElementById('lista-termos');
            if (!divLista) return;
            divLista.innerHTML = '<div class="text-center text-muted mt-5"><div class="spinner-border spinner-accent mb-2" role="status"></div><p>Carregando termos...</p></div>';
        }

        async function carregarTermos(categoriaId, nomeCategoria, busca = '') {
            setCategoriaVisual(categoriaId);

            const titulo = document.getElementById('titulo-categoria');
            const divLista = document.getElementById('lista-termos');
            if (!titulo || !divLista) return; // Não está na view de termos

            titulo.innerText = nomeCategoria;
            document.querySelectorAll('.nav-link').forEach(a => a.classList.remove('active'));
            const menuLink = document.getElementById(`menu-cat-${categoriaId}`);
            if (menuLink) menuLink.classList.add('active');

            mostrarSpinner();

            try {
                let url = `api/api_termos.php?categoria=${categoriaId}`;
                if (busca.trim() !== '') {
                    url += `&q=${encodeURIComponent(busca.trim())}`;
                    titulo.innerText = `Resultados para "${busca.trim()}"`;
                }

                const resposta = await fetch(url);
                if (!resposta.ok) { throw new Error(`HTTP ${resposta.status}`); }

                const termos = await resposta.json();
                divLista.innerHTML = '';

                if (!Array.isArray(termos) || termos.length === 0) {
                    divLista.innerHTML = `
                        <div class="alert alert-light text-center text-muted shadow-sm rounded-4 py-4" role="alert">
                            <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
                            Nenhum termo aprovado nesta categoria ainda. ✨
                        </div>
                    `;
                    return;
                }

                termos.forEach(termo => {
                    const cartao = document.createElement('div');
                    cartao.className = 'card cartao-termo mb-4 bg-white p-2';
                    const inicial = termo.enviado_por ? termo.enviado_por.charAt(0).toUpperCase() : '?';

                    cartao.innerHTML = `
                        <div class="card-body">
                            <h4 class="card-title fw-bold text-dark mb-3">${termo.palavra}</h4>
                            <p class="card-text text-secondary mb-4 lh-lg">${termo.descricao}</p>
                            ${termo.exemplo ? `<p class="card-text text-secondary mb-3"><strong>Exemplo:</strong> ${termo.exemplo}</p>` : ''}
                            ${termo.imagem ? `<div class="mb-4"><img src="${termo.imagem}" alt="${termo.palavra}" class="img-fluid rounded termo-thumb" style="max-width: 180px; cursor: pointer;" data-img="${termo.imagem}" data-alt="${termo.palavra}" onclick="mostrarImagemModal(this.dataset.img, this.dataset.alt)" /></div>` : ''}
                            <div class="d-flex align-items-center text-muted small fw-medium">
                                <div class="avatar-sala rounded-circle d-flex align-items-center justify-content-center me-2">${inicial}</div>
                                Enviado por ${termo.enviado_por}
                            </div>
                        </div>
                    `;
                    divLista.appendChild(cartao);
                });

            } catch (erro) {
                console.error("Erro ao buscar API:", erro);
                divLista.innerHTML = `<div class="alert alert-danger text-center shadow-sm rounded-4 py-4" role="alert"><i class="bi bi-exclamation-triangle fs-4 d-block mb-2"></i>Erro ao carregar os dados do servidor.</div>`;
            }
        }

        function debounce(fn, delay) {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => fn(...args), delay);
            };
        }

        function mostrarImagemModal(src, alt = '') {
            if (!src) return;
            const img = document.getElementById('modalImagemImg');
            if (!img) return;
            img.src = src;
            img.alt = alt;
            const modal = new bootstrap.Modal(document.getElementById('modalImagem'));
            modal.show();
        }
    </script>

    <!-- Modal de visualização de imagem -->
    <div class="modal fade" id="modalImagem" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body p-0">
                    <img id="modalImagemImg" src="" alt="" class="img-fluid rounded" style="width: 100%; height: auto;" />
                </div>
            </div>
        </div>
    </div>
</body>

</html>
