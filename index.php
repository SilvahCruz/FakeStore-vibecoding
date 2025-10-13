<?php
session_start();
require_once 'conexao.php';

$database = new Database();
$db = $database->getConnection();

// Buscar produtos em destaque
$query = "SELECT * FROM produtos WHERE destaque = 1 ORDER BY data_criacao DESC LIMIT 4";
$stmt = $db->prepare($query);
$stmt->execute();
$produtos_destaque = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galaxia Store - Moda Universal</title>
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
                    <li><a href="index.php" class="active">Home</a></li>
                    <li><a href="produtos.php">Produtos</a></li>
                    <li><a href="carrinho.php">🛒 Carrinho</a></li>
                    <?php if(isset($_SESSION['usuario_id'])): ?>
                        <li class="user-info">
                            <span>👋 <?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
                        </li>
                        <li><a href="logout.php">🚪 Sair</a></li>
                        <?php if($_SESSION['usuario_nivel'] === 'admin'): ?>
                            <li><a href="dashboard.php">Dashboard</a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="cadastro.php">Cadastro</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h2>Vista-se com a Elegância do Universo</h2>
                <p>Descubra nossa coleção exclusiva de roupas e acessórios inspirados na beleza cósmica</p>
                <a href="produtos.php" class="cta-button">Explorar Produtos</a>
            </div>
        </section>

        <section class="destaques">
            <h3>Produtos em Destaque</h3>
            <div class="produtos-grid">
                <?php if(count($produtos_destaque) > 0): ?>
                    <?php foreach($produtos_destaque as $produto): ?>
                    <div class="produto-card">
                        <a href="produto.php?id=<?= $produto['id'] ?>">
                            <img src="<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
                            <h4><?= htmlspecialchars($produto['nome']) ?></h4>
                            <p class="preco">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                            <p class="descricao"><?= htmlspecialchars($produto['descricao']) ?></p>
                            <span class="categoria"><?= ucfirst($produto['categoria']) ?></span>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <p>Nenhum produto em destaque no momento.</p>
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