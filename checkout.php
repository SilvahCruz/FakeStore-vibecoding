<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['checkout_produtos']) || empty($_SESSION['checkout_produtos'])) {
    header('Location: carrinho.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$produtos_checkout = $_SESSION['checkout_produtos'];
$subtotal = 0;

// Calcular totais
foreach ($produtos_checkout as $item) {
    if (isset($item['preco'])) {
        $subtotal += $item['preco'] * $item['quantidade'];
    } else {
        // Buscar preço do produto se não estiver no array
        $query = "SELECT preco FROM produtos WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$item['produto_id']]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        $subtotal += $produto['preco'] * $item['quantidade'];
    }
}

$frete = $subtotal > 200 ? 0 : 15.90;
$total = $subtotal + $frete;

// Processar finalização da compra
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_compra'])) {
    try {
        // Iniciar transação
        $db->beginTransaction();
        
        // Criar pedido
        $query = "INSERT INTO pedidos (usuario_id, total, status, data_pedido) VALUES (?, ?, 'confirmado', NOW())";
        $stmt = $db->prepare($query);
        $stmt->execute([$_SESSION['usuario_id'], $total]);
        $pedido_id = $db->lastInsertId();
        
        // Adicionar itens do pedido
        foreach ($produtos_checkout as $item) {
            if (isset($item['produto_id'])) {
                $query = "SELECT preco FROM produtos WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$item['produto_id']]);
                $produto = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $query = "INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario, cor, tamanho) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([
                    $pedido_id, 
                    $item['produto_id'], 
                    $item['quantidade'], 
                    $produto['preco'],
                    $item['cor'] ?? 'roxo',
                    $item['tamanho'] ?? 'M'
                ]);
            }
        }
        
        // Limpar carrinho
        $query = "DELETE FROM carrinho WHERE usuario_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$_SESSION['usuario_id']]);
        
        // Confirmar transação
        $db->commit();
        
        // Limpar sessão de checkout
        unset($_SESSION['checkout_produtos']);
        
        $_SESSION['sucesso_pedido'] = "Pedido #$pedido_id confirmado com sucesso!";
        header('Location: pedido_sucesso.php?id=' . $pedido_id);
        exit;
        
    } catch (Exception $e) {
        $db->rollBack();
        $erro = "Erro ao processar pedido: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Galaxia Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page-transition"></div>
    
    <div class="universe-bg">
        <div class="stars"></div>
        <div class="comet"></div>
        <div class="planet planet-1"></div>
        <div class="planet planet-2"></div>
        <div class="planet planet-3"></div>
    </div>

    <header>
        <nav class="navbar">
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
                    <li><a href="carrinho.php">🛒 Carrinho</a></li>
                    <li class="user-info">
                        <span>👋 <?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
                    </li>
                    <li><a href="logout.php">🚪 Sair</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <section class="checkout-page">
            <h2>🚀 Finalizar Compra</h2>
            
            <?php if(isset($erro)): ?>
                <div class="mensagem error">
                    <?= $erro ?>
                </div>
            <?php endif; ?>
            
            <div class="checkout-container">
                <div class="checkout-form">
                    <h3>Informações de Entrega</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label for="nome">Nome Completo:</label>
                            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($_SESSION['usuario_nome']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['usuario_email']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="endereco">Endereço:</label>
                            <input type="text" id="endereco" name="endereco" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="cidade">Cidade:</label>
                                <input type="text" id="cidade" name="cidade" required>
                            </div>
                            <div class="form-group">
                                <label for="estado">Estado:</label>
                                <input type="text" id="estado" name="estado" required>
                            </div>
                            <div class="form-group">
                                <label for="cep">CEP:</label>
                                <input type="text" id="cep" name="cep" required>
                            </div>
                        </div>
                        
                        <h3>Método de Pagamento</h3>
                        
                        <div class="pagamento-opcoes">
                            <div class="pagamento-opcao">
                                <input type="radio" id="cartao" name="pagamento" value="cartao" checked>
                                <label for="cartao">💳 Cartão de Crédito</label>
                            </div>
                            <div class="pagamento-opcao">
                                <input type="radio" id="pix" name="pagamento" value="pix">
                                <label for="pix">📱 PIX</label>
                            </div>
                            <div class="pagamento-opcao">
                                <input type="radio" id="boleto" name="pagamento" value="boleto">
                                <label for="boleto">🏦 Boleto Bancário</label>
                            </div>
                        </div>
                        
                        <div class="cartao-info" id="cartao-info">
                            <div class="form-group">
                                <label for="numero_cartao">Número do Cartão:</label>
                                <input type="text" id="numero_cartao" name="numero_cartao" placeholder="1234 5678 9012 3456">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="validade">Validade:</label>
                                    <input type="text" id="validade" name="validade" placeholder="MM/AA">
                                </div>
                                <div class="form-group">
                                    <label for="cvv">CVV:</label>
                                    <input type="text" id="cvv" name="cvv" placeholder="123">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="nome_cartao">Nome no Cartão:</label>
                                <input type="text" id="nome_cartao" name="nome_cartao">
                            </div>
                        </div>
                        
                        <button type="submit" name="confirmar_compra" class="btn-confirmar">🪐 Confirmar Pedido</button>
                    </form>
                </div>
                
                <div class="checkout-resumo">
                    <h3>Resumo do Pedido</h3>
                    
                    <div class="resumo-produtos">
                        <?php foreach($produtos_checkout as $item): ?>
                        <div class="resumo-produto">
                            <?php if(isset($item['nome'])): ?>
                                <span><?= htmlspecialchars($item['nome']) ?> x<?= $item['quantidade'] ?></span>
                                <span>R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></span>
                            <?php else: ?>
                                <?php 
                                $query = "SELECT nome, preco FROM produtos WHERE id = ?";
                                $stmt = $db->prepare($query);
                                $stmt->execute([$item['produto_id']]);
                                $produto = $stmt->fetch(PDO::FETCH_ASSOC);
                                ?>
                                <span><?= htmlspecialchars($produto['nome']) ?> x<?= $item['quantidade'] ?></span>
                                <span>R$ <?= number_format($produto['preco'] * $item['quantidade'], 2, ',', '.') ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="resumo-linha">
                        <span>Subtotal:</span>
                        <span>R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                    </div>
                    
                    <div class="resumo-linha">
                        <span>Frete:</span>
                        <span>
                            <?php if($frete == 0): ?>
                                <span class="frete-gratis">Grátis</span>
                            <?php else: ?>
                                R$ <?= number_format($frete, 2, ',', '.') ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <div class="resumo-total">
                        <span>Total:</span>
                        <span class="total-preco">R$ <?= number_format($total, 2, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>🌌 Galaxia Store</h3>
                <p>Vista-se com a elegância do universo. Roupas e acessórios inspirados na beleza cósmica.</p>
            </div>
            <div class="footer-section">
                <h4>Links Rápidos</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="produtos.php">Produtos</a></li>
                    <li><a href="carrinho.php">Carrinho</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contato</h4>
                <p>📧 contato@galaxiastore.com</p>
                <p>📱 (11) 99999-9999</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Galaxia Store - Todos os direitos reservados</p>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            document.querySelector('.page-transition').style.opacity = '0';
        }, 500);
        
        // Mostrar/ocultar informações do cartão
        document.querySelectorAll('input[name="pagamento"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const cartaoInfo = document.getElementById('cartao-info');
                cartaoInfo.style.display = this.value === 'cartao' ? 'block' : 'none';
            });
        });
    });
    
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.href && !this.href.includes('javascript')) {
                e.preventDefault();
                document.querySelector('.page-transition').style.opacity = '1';
                setTimeout(() => {
                    window.location.href = this.href;
                }, 500);
            }
        });
    });
    </script>
</body>
</html>