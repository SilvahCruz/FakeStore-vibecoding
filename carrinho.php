<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Buscar itens do carrinho
$query = "SELECT c.*, p.nome, p.preco, p.imagem 
          FROM carrinho c 
          JOIN produtos p ON c.produto_id = p.id 
          WHERE c.usuario_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['usuario_id']]);
$itens_carrinho = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular totais
$subtotal = 0;
foreach ($itens_carrinho as $item) {
    $subtotal += $item['preco'] * $item['quantidade'];
}
$frete = $subtotal > 200 ? 0 : 15.90;
$total = $subtotal + $frete;

// Processar remoção
if (isset($_GET['remover'])) {
    $query = "DELETE FROM carrinho WHERE id = ? AND usuario_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['remover'], $_SESSION['usuario_id']]);
    header('Location: carrinho.php');
    exit;
}

// Processar atualização de quantidade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_carrinho'])) {
    foreach ($_POST['quantidade'] as $item_id => $quantidade) {
        if ($quantidade > 0) {
            $query = "UPDATE carrinho SET quantidade = ? WHERE id = ? AND usuario_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$quantidade, $item_id, $_SESSION['usuario_id']]);
        } else {
            $query = "DELETE FROM carrinho WHERE id = ? AND usuario_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$item_id, $_SESSION['usuario_id']]);
        }
    }
    header('Location: carrinho.php');
    exit;
}

// Processar finalização da compra
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar_compra'])) {
    if (count($itens_carrinho) > 0) {
        $_SESSION['checkout_produtos'] = $itens_carrinho;
        header('Location: checkout.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho - Galaxia Store</title>
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
                    <li><a href="carrinho.php" class="active">🛒 Carrinho</a></li>
                    <li class="user-info">
                        <span>👋 <?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
                    </li>
                    <li><a href="logout.php">🚪 Sair</a></li>
                    <?php if($_SESSION['usuario_nivel'] === 'admin'): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <section class="carrinho-page">
            <h2>🛒 Meu Carrinho</h2>
            
            <?php if(isset($_SESSION['sucesso'])): ?>
                <div class="mensagem success">
                    <?= $_SESSION['sucesso'] ?>
                    <?php unset($_SESSION['sucesso']); ?>
                </div>
            <?php endif; ?>
            
            <div class="carrinho-container">
                <div class="carrinho-items">
                    <?php if(count($itens_carrinho) > 0): ?>
                        <form method="POST">
                            <input type="hidden" name="atualizar_carrinho" value="1">
                            
                            <?php foreach($itens_carrinho as $item): ?>
                            <div class="carrinho-item">
                                <img src="<?= htmlspecialchars($item['imagem']) ?>" alt="<?= htmlspecialchars($item['nome']) ?>">
                                
                                <div class="item-info">
                                    <h4><?= htmlspecialchars($item['nome']) ?></h4>
                                    <p class="preco">R$ <?= number_format($item['preco'], 2, ',', '.') ?></p>
                                    <div class="item-detalhes">
                                        <span class="cor-tamanho">Cor: <?= htmlspecialchars($item['cor']) ?> | Tamanho: <?= htmlspecialchars($item['tamanho']) ?></span>
                                    </div>
                                </div>
                                
                                <div class="item-quantity">
                                    <label>Qtd:</label>
                                    <input type="number" name="quantidade[<?= $item['id'] ?>]" 
                                           value="<?= $item['quantidade'] ?>" min="1" max="10">
                                </div>
                                
                                <div class="item-subtotal">
                                    R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?>
                                </div>
                                
                                <div class="item-actions">
                                    <a href="carrinho.php?remover=<?= $item['id'] ?>" class="btn-remover">🗑️</a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <div class="carrinho-actions">
                                <button type="submit" class="btn-atualizar">🔄 Atualizar Carrinho</button>
                                <a href="produtos.php" class="btn-continuar">← Continuar Comprando</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="carrinho-vazio">
                            <div class="empty-icon">🛒</div>
                            <h3>Seu carrinho está vazio</h3>
                            <p>Explore nossa coleção cósmica e adicione produtos incríveis!</p>
                            <a href="produtos.php" class="cta-button">Explorar Produtos</a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if(count($itens_carrinho) > 0): ?>
                <div class="carrinho-resumo">
                    <h3>Resumo do Pedido</h3>
                    
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
                    
                    <?php if($frete > 0): ?>
                    <div class="frete-alerta">
                        🚚 Adicione R$ <?= number_format(200 - $subtotal, 2, ',', '.') ?> para frete grátis!
                    </div>
                    <?php endif; ?>
                    
                    <div class="resumo-total">
                        <span>Total:</span>
                        <span class="total-preco">R$ <?= number_format($total, 2, ',', '.') ?></span>
                    </div>
                    
                    <form method="POST">
                        <input type="hidden" name="finalizar_compra" value="1">
                        <button type="submit" class="btn-finalizar">💳 Finalizar Compra</button>
                    </form>
                    
                    <div class="pagamento-info">
                        <p>🔒 Compra 100% segura</p>
                        <div class="bandeiras">
                            <span>💳</span>
                            <span>📱</span>
                            <span>🏦</span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
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
                <p>📱 (11) 96798-8042</p>
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