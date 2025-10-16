
console.log('Script carregado - verificando elementos...');

// Depois de selecionar os elementos, verifique:
console.log('Cor options:', document.querySelectorAll('.cor-option').length);
console.log('Tamanho options:', document.querySelectorAll('.tamanho-option').length);
console.log('Botões quantidade:', document.getElementById('decrease-qty'), document.getElementById('increase-qty'));
console.log('Botões compra:', document.querySelector('.btn-comprar'), document.querySelector('.btn-comprar-agora'));

// Carregar e exibir produtos
document.addEventListener('DOMContentLoaded', function() {
    carregarProdutos();
    
    // Configurar filtros
    const filtroBtns = document.querySelectorAll('.filtro-btn');
    filtroBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remover classe active de todos os botões
            filtroBtns.forEach(b => b.classList.remove('active'));
            // Adicionar classe active ao botão clicado
            this.classList.add('active');
            
            // Filtrar produtos
            const categoria = this.getAttribute('data-categoria');
            filtrarProdutos(categoria);
        });
    });
});

async function carregarProdutos() {
    try {
        const response = await fetch('produtos.json');
        const produtos = await response.json();
        
        // Exibir produtos na página inicial (apenas destaque)
        if (document.getElementById('produtos-destaque')) {
            const produtosDestaque = produtos.filter(produto => produto.destaque);
            exibirProdutos(produtosDestaque, 'produtos-destaque');
        }
        
        // Exibir todos os produtos na página de produtos
        if (document.getElementById('produtos-lista')) {
            exibirProdutos(produtos, 'produtos-lista');
        }
    } catch (error) {
        console.error('Erro ao carregar produtos:', error);
    }
}

function exibirProdutos(produtos, containerId) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    
    produtos.forEach(produto => {
        const produtoCard = document.createElement('div');
        produtoCard.className = 'produto-card';
        produtoCard.innerHTML = `
            <img src="${produto.imagem}" alt="${produto.nome}">
            <h4>${produto.nome}</h4>
            <p class="preco">R$ ${produto.preco.toFixed(2).replace('.', ',')}</p>
            <p class="descricao">${produto.descricao}</p>
            <span class="categoria">${produto.categoria}</span>
        `;
        container.appendChild(produtoCard);
    });
}

function filtrarProdutos(categoria) {
    const produtosCards = document.querySelectorAll('.produto-card');
    
    produtosCards.forEach(card => {
        const cardCategoria = card.querySelector('.categoria').textContent.toLowerCase();
        
        if (categoria === 'todos' || cardCategoria === categoria) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Carregar produtos do MySQL via PHP
async function carregarProdutos() {
    try {
        const response = await fetch('api_produtos.php');
        const produtos = await response.json();
        
        // Exibir produtos na página inicial (apenas destaque)
        if (document.getElementById('produtos-destaque')) {
            const produtosDestaque = produtos.filter(produto => produto.destaque);
            exibirProdutos(produtosDestaque, 'produtos-destaque');
        }
        
        // Exibir todos os produtos na página de produtos
        if (document.getElementById('produtos-lista')) {
            exibirProdutos(produtos, 'produtos-lista');
        }
    } catch (error) {
        console.error('Erro ao carregar produtos:', error);
    }
}