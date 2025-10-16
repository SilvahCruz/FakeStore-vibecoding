<?php
session_start();

// Inicializar carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Produtos exemplo (em um sistema real, viria do banco de dados)
$produtos = [
    1 => ['id' => 1, 'nome' => 'Camiseta Estelar', 'preco' => 79.90, 'imagem' => 'camiseta.jpg'],
    2 => ['id' => 2, 'nome' => 'Calça Cósmica', 'preco' => 129.90, 'imagem' => 'calca.jpg'],
    3 => ['id' => 3, 'nome' => 'Tênis Galático', 'preco' => 199.90, 'imagem' => 'tenis.jpg'],
    4 => ['id' => 4, 'nome' => 'Jaqueta Nebulosa', 'preco' => 159.90, 'imagem' => 'jaqueta.jpg']
];

// Adicionar produto ao carrinho
if (isset($_POST['adicionar'])) {
    $produto_id = $_POST['produto_id'];
    $quantidade = $_POST['quantidade'] ?? 1;
    
    if (isset($produtos[$produto_id])) {
        if (isset($_SESSION['carrinho'][$produto_id])) {
            $_SESSION['carrinho'][$produto_id]['quantidade'] += $quantidade;
        } else {
            $_SESSION['carrinho'][$produto_id] = [
                'produto' => $produtos[$produto_id],
                'quantidade' => $quantidade
            ];
        }
    }
}

// Remover produto do carrinho
if (isset($_GET['remover'])) {
    $produto_id = $_GET['remover'];
    if (isset($_SESSION['carrinho'][$produto_id])) {
        unset($_SESSION['carrinho'][$produto_id]);
    }
}

// Atualizar quantidade
if (isset($_POST['atualizar'])) {
    foreach ($_POST['quantidade'] as $produto_id => $quantidade) {
        if (isset($_SESSION['carrinho'][$produto_id])) {
            if ($quantidade > 0) {
                $_SESSION['carrinho'][$produto_id]['quantidade'] = $quantidade;
            } else {
                unset($_SESSION['carrinho'][$produto_id]);
            }
        }
    }
}

// Calcular totais
$total_itens = 0;
$total_preco = 0;

foreach ($_SESSION['carrinho'] as $item) {
    $total_itens += $item['quantidade'];
    $total_preco += $item['produto']['preco'] * $item['quantidade'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galaxia Store - Carrinho</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #0f0f1a;
            color: #ffffff;
            line-height: 1.6;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(108, 99, 255, 0.1) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(108, 99, 255, 0.1) 0%, transparent 20%);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        header {
            background-color: rgba(26, 26, 46, 0.9);
            padding: 20px 0;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #6c63ff;
            text-align: center;
            margin-bottom: 10px;
            text-shadow: 0 0 10px rgba(108, 99, 255, 0.5);
        }

        nav ul {
            display: flex;
            justify-content: center;
            list-style: none;
            gap: 30px;
        }

        nav a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            padding: 5px 10px;
            border-radius: 4px;
        }

        nav a:hover {
            color: #6c63ff;
            background-color: rgba(108, 99, 255, 0.1);
        }

        /* Main Content */
        main {
            min-height: 70vh;
            padding: 40px 20px;
        }

        .page-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-title h1 {
            font-size: 36px;
            color: #ffffff;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }

        .cart-icon {
            font-size: 60px;
            margin-bottom: 20px;
            color: #6c63ff;
        }

        /* Carrinho Styles */
        .carrinho-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        <?php if (empty($_SESSION['carrinho'])): ?>
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-cart-message {
            font-size: 18px;
            margin-bottom: 30px;
            color: #b0b0b0;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.8;
        }

        .explore-btn {
            background: linear-gradient(135deg, #6c63ff, #4a44b5);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(108, 99, 255, 0.4);
            text-decoration: none;
            display: inline-block;
        }

        .explore-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(108, 99, 255, 0.6);
        }
        <?php else: ?>
        .cart-items {
            background: rgba(26, 26, 46, 0.8);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid #2a2a3e;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #2a2a3e;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #6c63ff, #4a44b5);
            border-radius: 10px;
            margin-right: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #ffffff;
        }

        .item-price {
            font-size: 16px;
            color: #6c63ff;
            font-weight: 500;
        }

        .item-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-input {
            width: 60px;
            padding: 8px;
            background: #1a1a2e;
            border: 1px solid #2a2a3e;
            border-radius: 5px;
            color: white;
            text-align: center;
        }

        .remove-btn {
            background: #ff4757;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .remove-btn:hover {
            background: #ff3742;
        }

        .cart-summary {
            background: rgba(26, 26, 46, 0.8);
            border-radius: 15px;
            padding: 30px;
            border: 1px solid #2a2a3e;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .summary-total {
            font-size: 20px;
            font-weight: 600;
            color: #6c63ff;
            border-top: 1px solid #2a2a3e;
            padding-top: 15px;
            margin-top: 15px;
        }

        .cart-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .update-btn {
            background: #2a2a3e;
            color: white;
            border: 1px solid #6c63ff;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            flex: 1;
        }

        .update-btn:hover {
            background: #6c63ff;
        }

        .checkout-btn {
            background: linear-gradient(135deg, #6c63ff, #4a44b5);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            flex: 2;
        }

        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 99, 255, 0.4);
        }
        <?php endif; ?>

        /* Footer Styles */
        footer {
            background-color: #1a1a2e;
            padding: 40px 0 20px;
            margin-top: 40px;
            border-top: 1px solid #2a2a3e;
        }

        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .footer-section {
            flex: 1;
            min-width: 250px;
            margin-bottom: 20px;
            padding: 0 15px;
        }

        .footer-section h3 {
            color: #6c63ff;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .footer-section p {
            color: #b0b0b0;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 8px;
        }

        .footer-section ul li a {
            color: #b0b0b0;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section ul li a:hover {
            color: #6c63ff;
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #2a2a3e;
            color: #b0b0b0;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            nav ul {
                flex-direction: column;
                gap: 15px;
            }
            
            .cart-item {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .item-image {
                margin-right: 0;
            }
            
            .cart-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">Galaxia Store</div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="produtos.php">Produtos</a></li>
                    <li><a href="contato.php">Contato</a></li>
                    <li><a href="carrinho.php">Carrinho (<?php echo $total_itens; ?>)</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="page-title">
                <div class="cart-icon">🛒</div>
                <h1>Meu Carrinho</h1>
            </div>

            <div class="carrinho-container">
                <?php if (empty($_SESSION['carrinho'])): ?>
                    <div class="empty-cart">
                        <p class="empty-cart-message">Seu carrinho está vazio<br>Explore nossa coleção cósmica e adote produtos incríveis</p>
                        <a href="produtos.php" class="explore-btn">Explorar Produtos</a>
                    </div>
                <?php else: ?>
                    <form method="POST" action="carrinho.php">
                        <div class="cart-items">
                            <?php foreach ($_SESSION['carrinho'] as $produto_id => $item): ?>
                                <?php $produto = $item['produto']; ?>
                                <div class="cart-item">
                                    <div class="item-image">🌟</div>
                                    <div class="item-details">
                                        <div class="item-name"><?php echo htmlspecialchars($produto['nome']); ?></div>
                                        <div class="item-price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></div>
                                    </div>
                                    <div class="item-quantity">
                                        <input type="number" 
                                               name="quantidade[<?php echo $produto_id; ?>]" 
                                               value="<?php echo $item['quantidade']; ?>" 
                                               min="1" 
                                               class="quantity-input">
                                        <a href="carrinho.php?remover=<?php echo $produto_id; ?>" 
                                           class="remove-btn" 
                                           onclick="return confirm('Remover este item do carrinho?')">
                                            Remover
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="cart-summary">
                            <div class="summary-row">
                                <span>Total de Itens:</span>
                                <span><?php echo $total_itens; ?></span>
                            </div>
                            <div class="summary-row summary-total">
                                <span>Total:</span>
                                <span>R$ <?php echo number_format($total_preco, 2, ',', '.'); ?></span>
                            </div>
                            
                            <div class="cart-actions">
                                <button type="submit" name="atualizar" class="update-btn">
                                    Atualizar Carrinho
                                </button>
                                <button type="button" class="checkout-btn" onclick="finalizarCompra()">
                                    Finalizar Compra
                                </button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Galaxia Store</h3>
                    <p>Vista-se com a elegância do universo. Revele a assinatura estelar trajada no toque cósmico.</p>
                </div>
                <div class="footer-section">
                    <h3>Links Rápidos</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="produtos.php">Produtos</a></li>
                        <li><a href="contato.php">Contato</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contato</h3>
                    <p>contato@galaxiatino.com</p>
                    <p>(11) 99999-9999</p>
                </div>
            </div>
            <div class="copyright">
                © 2024 Galaxia Store. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    <script>
        function finalizarCompra() {
            if (confirm('Deseja finalizar a compra?')) {
                alert('Compra finalizada com sucesso! Obrigado por comprar na Galaxia Store.');
                window.location.href = 'carrinho.php?limpar=true';
            }
        }

        // Limpar carrinho se solicitado
        <?php if (isset($_GET['limpar']) && $_GET['limpar'] == 'true'): ?>
            <?php $_SESSION['carrinho'] = []; ?>
            window.location.href = 'carrinho.php';
        <?php endif; ?>
    </script>
</body>
</html>