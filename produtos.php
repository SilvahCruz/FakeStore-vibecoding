<?php
session_start();
require_once 'conexao.php';

$database = new Database();
$db = $database->getConnection();

// Buscar todos os produtos
$query = "SELECT * FROM produtos ORDER BY data_criacao DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filtrar por categoria se especificado
$categoria_filtro = $_GET['categoria'] ?? 'todos';
if ($categoria_filtro !== 'todos') {
    $query = "SELECT * FROM produtos WHERE categoria = ? ORDER BY data_criacao DESC";
    $stmt = $db->prepare($query);
    $stmt->execute([$categoria_filtro]);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Galaxia Store</title>
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
                    <li><a href="produtos.php" class="active">Produtos</a></li>
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
        <section class="produtos-page">
            <h2>Nossa Coleção Cósmica</h2>
            <div class="filtros">
                <a href="produtos.php?categoria=todos" class="filtro-btn <?= $categoria_filtro === 'todos' ? 'active' : '' ?>">Todos</a>
                <a href="produtos.php?categoria=camisetas" class="filtro-btn <?= $categoria_filtro === 'camisetas' ? 'active' : '' ?>">Camisetas</a>
                <a href="produtos.php?categoria=moletons" class="filtro-btn <?= $categoria_filtro === 'moletons' ? 'active' : '' ?>">Moletons</a>
                <a href="produtos.php?categoria=acessorios" class="filtro-btn <?= $categoria_filtro === 'acessorios' ? 'active' : '' ?>">Acessórios</a>
            </div>
            <div class="produtos-grid">
                <?php if(count($produtos) > 0): ?>
                    <?php foreach($produtos as $produto): ?>
                    <div class="produto-card">
                        <a href="produto.php?id=<?= $produto['id'] ?>">
                            <img src="<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
                            <h4><?= htmlspecialchars($produto['nome']) ?></h4>
                            <p class="preco">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                            <p class="descricao"><?= htmlspecialchars($produto['descricao']) ?></p>
                            <span class="categoria"><?= ucfirst($produto['categoria']) ?></span>
                            <?php if($produto['destaque']): ?>
                                <span class="destaque-badge">⭐ Destaque</span>
                            <?php endif; ?>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <p>Nenhum produto encontrado nesta categoria.</p>
                        <a href="produtos.php?categoria=todos" class="cta-button">Ver Todos os Produtos</a>
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