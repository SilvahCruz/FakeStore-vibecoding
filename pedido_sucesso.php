<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$pedido_id = $_GET['id'] ?? 0;

if (!$pedido_id) {
    header('Location: index.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Buscar detalhes do pedido
$query = "SELECT p.*, pi.quantidade, pi.preco_unitario, pi.cor, pi.tamanho, prod.nome, prod.imagem 
          FROM pedidos p 
          JOIN pedido_itens pi ON p.id = pi.pedido_id 
          JOIN produtos prod ON pi.produto_id = prod.id 
          WHERE p.id = ? AND p.usuario_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$pedido_id, $_SESSION['usuario_id']]);
$itens_pedido = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($itens_pedido)) {
    header('Location: index.php');
    exit;
}

$pedido = $itens_pedido[0];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado - Galaxia Store</title>
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
        <section class="sucesso-pedido">
            <div class="sucesso-container">
                <div class="sucesso-icon">🎉</div>
                <h2>Pedido Confirmado!</h2>
                <p class="sucesso-mensagem">Seu pedido #<?= $pedido_id ?> foi processado com sucesso.</p>
                
                <div class="pedido-info">
                    <div class="info-card">
                        <h3>📦 Detalhes do Pedido</h3>
                        <p><strong>Número do Pedido:</strong> #<?= $pedido_id ?></p>
                        <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></p>
                        <p><strong>Status:</strong> <span class="status-confirmado">Confirmado</span></p>
                        <p><strong>Total:</strong> R$ <?= number_format($pedido['total'], 2, ',', '.') ?></p>
                    </div>
                    
                    <div class="info-card">
                        <h3>📋 Itens do Pedido</h3>
                        <div class="itens-pedido">
                            <?php foreach($itens_pedido as $item): ?>
                            <div class="item-pedido">
                                <img src="<?= htmlspecialchars($item['imagem']) ?>" alt="<?= htmlspecialchars($item['nome']) ?>">
                                <div class="item-info">
                                    <h4><?= htmlspecialchars($item['nome']) ?></h4>
                                    <p>Quantidade: <?= $item['quantidade'] ?></p>
                                    <p>Cor: <?= htmlspecialchars($item['cor']) ?> | Tamanho: <?= htmlspecialchars($item['tamanho']) ?></p>
                                    <p class="preco">R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="sucesso-actions">
                    <a href="index.php" class="btn-continuar">🏠 Continuar Comprando</a>
                    <a href="produtos.php" class="btn-ver-produtos">🛍️ Ver Mais Produtos</a>
                </div>
                
                <div class="agradecimento">
                    <p>Obrigado por comprar na <strong>Galaxia Store</strong>! 🌌</p>
                    <p>Seu pedido será processado e enviado em breve.</p>
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