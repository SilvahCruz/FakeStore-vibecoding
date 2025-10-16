<?php
session_start();

// Inicializar carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Produto específico
$produto = [
    'id' => 1,
    'nome' => 'Moleton Via Láctea',
    'preco_atual' => 149.90,
    'preco_original' => 224.85,
    'desconto' => 33,
    'avaliacao' => 4.8,
    'total_avaliacoes' => 851,
    'descricao' => 'Moleton comfort com estampa detalhada da via láctea',
    'caracteristicas' => [
        'Material 100% algodão',
        'Estampa de alta durabilidade', 
        'Lavagem segura',
        'Entrega rápida'
    ],
    'cores' => ['Preto', 'Branco', 'Azul Espacial'],
    'tamanhos' => ['P', 'M', 'G', 'GG']
];

// Adicionar ao carrinho
if (isset($_POST['adicionar_carrinho'])) {
    $cor = $_POST['cor'];
    $tamanho = $_POST['tamanho'];
    $quantidade = $_POST['quantidade'];
    
    $item_carrinho = [
        'produto_id' => $produto['id'],
        'nome' => $produto['nome'],
        'preco' => $produto['preco_atual'],
        'cor' => $cor,
        'tamanho' => $tamanho,
        'quantidade' => $quantidade
    ];
    
    $_SESSION['carrinho'][] = $item_carrinho;
    
    header('Location: carrinho.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $produto['nome']; ?> - Galaxia Store</title>
    <style>
        /* ===== ESTILOS GERAIS ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
            color: white;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* ===== HEADER ===== */
        .navbar {
            background: rgba(15, 15, 35, 0.95);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            text-decoration: none;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            background: rgba(99, 102, 241, 0.2);
            color: #a78bfa;
        }

        /* ===== PÁGINA DO PRODUTO ===== */
        .produto-page {
            padding: 2rem 0;
        }

        .produto-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: start;
        }

        .produto-galeria {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .produto-imagem {
            width: 100%;
            height: 500px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
        }

        .produto-info {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .produto-titulo {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            line-height: 1.2;
        }

        .produto-avaliacao {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .estrelas {
            color: #ffd700;
            font-size: 1.2rem;
        }

        .avaliacao-texto {
            color: #ccc;
            font-size: 1rem;
        }

        .produto-precos {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .preco-atual {
            font-size: 2.5rem;
            font-weight: 800;
            color: #a78bfa;
        }

        .preco-original {
            font-size: 1.5rem;
            color: #ccc;
            text-decoration: line-through;
        }

        .desconto {
            background: #00d26a;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .oferta-relampago {
            background: linear-gradient(45deg, #ff6b6b, #ff4757);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            text-align: center;
        }

        .timer {
            font-weight: 700;
            font-size: 1.1rem;
            margin-top: 0.5rem;
        }

        .frete-gratis {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            border-left: 4px solid #00d26a;
        }

        .selecao-grupo {
            margin-bottom: 1.5rem;
        }

        .rotulo {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 700;
            color: white;
            font-size: 1.1rem;
        }

        .opcoes-cores {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .opcao-cor {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 3px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .opcao-cor:hover {
            transform: scale(1.1);
        }

        .opcao-cor.ativa {
            border-color: #a78bfa;
        }

        .opcao-cor.ativa::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            text-shadow: 0 0 3px rgba(0,0,0,0.5);
        }

        .cor-preto { background-color: #000000; }
        .cor-branco { background-color: #ffffff; border: 1px solid #ccc; }
        .cor-azul { background-color: #1e90ff; }

        .opcoes-tamanhos {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .opcao-tamanho {
            padding: 1rem 1.5rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-weight: 700;
            min-width: 70px;
            text-align: center;
        }

        .opcao-tamanho:hover {
            border-color: #a78bfa;
            background: rgba(167, 139, 250, 0.1);
        }

        .opcao-tamanho.ativa {
            background: #a78bfa;
            border-color: #a78bfa;
            color: white;
        }

        .seletor-quantidade {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .controles-quantidade {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 0.5rem;
        }

        .botao-quantidade {
            width: 45px;
            height: 45px;
            border: none;
            background: #a78bfa;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .botao-quantidade:hover {
            background: #8b5cf6;
        }

        .input-quantidade {
            width: 70px;
            text-align: center;
            background: transparent;
            border: none;
            color: white;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .botoes-acao {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .botao-adicionar {
            flex: 2;
            background: linear-gradient(45deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            padding: 1.5rem 2rem;
            border-radius: 15px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .botao-adicionar:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
        }

        .botao-comprar {
            flex: 1;
            background: transparent;
            color: #a78bfa;
            border: 2px solid #a78bfa;
            padding: 1.5rem 2rem;
            border-radius: 15px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .botao-comprar:hover {
            background: #a78bfa;
            color: white;
        }

        .descricao-produto {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            padding: 2rem;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .titulo-descricao {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: white;
        }

        .texto-descricao {
            color: #ccc;
            margin-bottom: 1.5rem;
            line-height: 1.8;
            font-size: 1.1rem;
        }

        .lista-caracteristicas {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .lista-caracteristicas li {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: #ccc;
            font-size: 1.1rem;
        }

        .lista-caracteristicas li::before {
            content: '✓';
            color: #00d26a;
            font-weight: bold;
            font-size: 1.2rem;
        }

        /* ===== PRODUTOS RELACIONADOS ===== */
        .produtos-relacionados {
            margin-top: 4rem;
        }

        .titulo-relacionados {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
            background: linear-gradient(45deg, #a78bfa, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .produtos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .produto-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-radius: 15px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .produto-card:hover {
            transform: translateY(-5px);
            border-color: #a78bfa;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            text-decoration: none;
        }

        .produto-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .produto-card h4 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: white;
        }

        .produto-card .preco {
            font-size: 1.3rem;
            font-weight: 700;
            color: #a78bfa;
            margin-bottom: 0.5rem;
        }

        .produto-card .categoria {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: rgba(99, 102, 241, 0.2);
            color: #a78bfa;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* ===== FOOTER ===== */
        footer {
            background: rgba(15, 15, 35, 0.95);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            margin-top: 4rem;
            padding: 3rem 0 1rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .footer-section h3,
        .footer-section h4 {
            margin-bottom: 1rem;
            color: #a78bfa;
        }

        .footer-section p {
            color: #ccc;
            line-height: 1.6;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section ul li a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: #a78bfa;
            text-decoration: none;
        }

        .footer-bottom {
            max-width: 1200px;
            margin: 2rem auto 0;
            padding: 1rem 2rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
            color: #888;
            font-size: 0.9rem;
        }

        /* ===== RESPONSIVIDADE ===== */
        @media (max-width: 768px) {
            .produto-container {
                grid-template-columns: 1fr;
                gap: 2rem;
                padding: 0 1rem;
            }
            
            .produto-imagem {
                height: 300px;
                font-size: 3rem;
            }
            
            .produto-titulo {
                font-size: 2rem;
            }
            
            .preco-atual {
                font-size: 2rem;
            }
            
            .botoes-acao {
                flex-direction: column;
            }
            
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body class="produto-page">
    <!-- Header -->
    <header class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <div class="logo-saturno">
                    <div class="logo-detalhe logo-detalhe-1"></div>
                    <div class="logo-detalhe logo-detalhe-2"></div>
                </div>
                Galaxia Store
            </a>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="produtos.php">Produtos</a></li>
                <li><a href="carrinho.php">Carrinho</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="container">
        <form method="POST" action="produto.php">
            <div class="produto-container">
                <!-- Galeria de Imagens -->
                <div class="produto-galeria">
                    <div class="produto-imagem">
                        🌌
                    </div>
                </div>

                <!-- Informações do Produto -->
                <div class="produto-info">
                    <h1 class="produto-titulo"><?php echo $produto['nome']; ?></h1>
                    
                    <div class="produto-avaliacao">
                        <div class="estrelas">★★★★★</div>
                        <span class="avaliacao-texto"><?php echo $produto['avaliacao']; ?> (<?php echo $produto['total_avaliacoes']; ?> avaliações)</span>
                    </div>

                    <div class="produto-precos">
                        <div class="preco-atual">R$ <?php echo number_format($produto['preco_atual'], 2, ',', '.'); ?></div>
                        <div class="preco-original">R$ <?php echo number_format($produto['preco_original'], 2, ',', '.'); ?></div>
                        <div class="desconto">-<?php echo $produto['desconto']; ?>% OFF</div>
                    </div>

                    <div class="oferta-relampago">
                        <strong>OFERTA RELÂMPAGO</strong>
                        <div class="timer">Termina em 23:59:54</div>
                    </div>

                    <div class="frete-gratis">
                        <strong>Frete grátis</strong> Para São Paulo, São Paulo
                    </div>

                    <!-- Seleção de Cor -->
                    <div class="selecao-grupo">
                        <label class="rotulo">Cor:</label>
                        <div class="opcoes-cores">
                            <input type="radio" name="cor" value="Preto" id="cor-preto" checked hidden>
                            <label for="cor-preto" class="opcao-cor cor-preto ativa" title="Preto"></label>
                            
                            <input type="radio" name="cor" value="Branco" id="cor-branco" hidden>
                            <label for="cor-branco" class="opcao-cor cor-branco" title="Branco"></label>
                            
                            <input type="radio" name="cor" value="Azul Espacial" id="cor-azul" hidden>
                            <label for="cor-azul" class="opcao-cor cor-azul" title="Azul Espacial"></label>
                        </div>
                    </div>

                    <!-- Seleção de Tamanho -->
                    <div class="selecao-grupo">
                        <label class="rotulo">Tamanho:</label>
                        <div class="opcoes-tamanhos">
                            <?php foreach ($produto['tamanhos'] as $tamanho): ?>
                                <input type="radio" name="tamanho" value="<?php echo $tamanho; ?>" id="tamanho-<?php echo $tamanho; ?>" <?php echo $tamanho === 'M' ? 'checked' : ''; ?> hidden>
                                <label for="tamanho-<?php echo $tamanho; ?>" class="opcao-tamanho <?php echo $tamanho === 'M' ? 'ativa' : ''; ?>">
                                    <?php echo $tamanho; ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Seleção de Quantidade -->
                    <div class="selecao-grupo">
                        <label class="rotulo">Quantidade:</label>
                        <div class="seletor-quantidade">
                            <div class="controles-quantidade">
                                <button type="button" class="botao-quantidade" onclick="alterarQuantidade(-1)">-</button>
                                <input type="number" name="quantidade" class="input-quantidade" value="1" min="1" max="10" readonly>
                                <button type="button" class="botao-quantidade" onclick="alterarQuantidade(1)">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="botoes-acao">
                        <button type="submit" name="adicionar_carrinho" class="botao-adicionar">
                            Adicionar ao Carrinho
                        </button>
                        <button type="button" class="botao-comprar" onclick="comprarAgora()">
                            Comprar Agora
                        </button>
                    </div>

                    <!-- Descrição do Produto -->
                    <div class="descricao-produto">
                        <h3 class="titulo-descricao">Descrição do Produto</h3>
                        <p class="texto-descricao"><?php echo $produto['descricao']; ?></p>
                        <ul class="lista-caracteristicas">
                            <?php foreach ($produto['caracteristicas'] as $caracteristica): ?>
                                <li><?php echo $caracteristica; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </form>

        <!-- Produtos Relacionados -->
        <div class="produtos-relacionados">
            <h2 class="titulo-relacionados">Produtos Relacionados</h2>
            <div class="produtos-grid">
                <!-- Produto 1 -->
                <a href="produto.php?id=2" class="produto-card">
                    <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin-bottom: 1rem;">
                        🚀
                    </div>
                    <h4>Camiseta Astronauta</h4>
                    <div class="preco">R$ 79,90</div>
                    <div class="categoria">Camisetas</div>
                </a>
                <!-- Produto 2 -->
                <a href="produto.php?id=3" class="produto-card">
                    <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin-bottom: 1rem;">
                        🪐
                    </div>
                    <h4>Camiseta Planetária</h4>
                    <div class="preco">R$ 84,90</div>
                    <div class="categoria">Camisetas</div>
                </a>
                <!-- Produto 3 -->
                <a href="produto.php?id=4" class="produto-card">
                    <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin-bottom: 1rem;">
                        ⭐
                    </div>
                    <h4>Camiseta Estelar</h4>
                    <div class="preco">R$ 89,90</div>
                    <div class="categoria">Camisetas</div>
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Galaxia Store</h3>
                <p>Vista-se com a elegância do universo. Revele a assinatura estelar trajada no toque cósmico.</p>
            </div>
            <div class="footer-section">
                <h4>Links Rápidos</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="produtos.php">Produtos</a></li>
                    <li><a href="carrinho.php">Carrinho</a></li>
                    <li><a href="contato.php">Contato</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contato</h4>
                <p>contato@galaxiatino.com</p>
                <p>(11) 99999-9999</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Galaxia Store. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        // Controle de quantidade
        function alterarQuantidade(alteracao) {
            const input = document.querySelector('.input-quantidade');
            let valor = parseInt(input.value);
            valor += alteracao;
            
            if (valor < 1) valor = 1;
            if (valor > 10) valor = 10;
            
            input.value = valor;
        }

        // Seleção de cores
        document.querySelectorAll('.opcao-cor').forEach(cor => {
            cor.addEventListener('click', function() {
                document.querySelectorAll('.opcao-cor').forEach(c => c.classList.remove('ativa'));
                this.classList.add('ativa');
                // Atualiza o radio button correspondente
                const radioId = this.getAttribute('for');
                document.getElementById(radioId).checked = true;
            });
        });

        // Seleção de tamanhos
        document.querySelectorAll('.opcao-tamanho').forEach(tamanho => {
            tamanho.addEventListener('click', function() {
                document.querySelectorAll('.opcao-tamanho').forEach(t => t.classList.remove('ativa'));
                this.classList.add('ativa');
                // Atualiza o radio button correspondente
                const radioId = this.getAttribute('for');
                document.getElementById(radioId).checked = true;
            });
        });

        // Comprar agora
        function comprarAgora() {
            const form = document.querySelector('form');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'comprar_agora';
            input.value = '1';
            form.appendChild(input);
            form.submit();
        }
    </script>
</body>
</html>