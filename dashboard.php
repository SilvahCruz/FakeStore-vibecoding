<?php
session_start();
require_once 'conexao.php';

// Verificar se usuário está logado e é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_nivel'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Processar formulário de produtos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        $acao = $_POST['acao'];
        
        if ($acao === 'adicionar') {
            $query = "INSERT INTO produtos (nome, preco, categoria, descricao, imagem, destaque) VALUES (:nome, :preco, :categoria, :descricao, :imagem, :destaque)";
            $stmt = $db->prepare($query);
            $stmt->bindValue(':nome', $_POST['nome']);
            $stmt->bindValue(':preco', $_POST['preco']);
            $stmt->bindValue(':categoria', $_POST['categoria']);
            $stmt->bindValue(':descricao', $_POST['descricao']);
            $stmt->bindValue(':imagem', $_POST['imagem']);
            $stmt->bindValue(':destaque', isset($_POST['destaque']) ? 1 : 0);
            $stmt->execute();
            
        } elseif ($acao === 'editar') {
            $query = "UPDATE produtos SET nome = :nome, preco = :preco, categoria = :categoria, descricao = :descricao, imagem = :imagem, destaque = :destaque WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindValue(':nome', $_POST['nome']);
            $stmt->bindValue(':preco', $_POST['preco']);
            $stmt->bindValue(':categoria', $_POST['categoria']);
            $stmt->bindValue(':descricao', $_POST['descricao']);
            $stmt->bindValue(':imagem', $_POST['imagem']);
            $stmt->bindValue(':destaque', isset($_POST['destaque']) ? 1 : 0);
            $stmt->bindValue(':id', $_POST['id']);
            $stmt->execute();
            
        } elseif ($acao === 'remover') {
            $query = "DELETE FROM produtos WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindValue(':id', $_POST['id']);
            $stmt->execute();
        }
        
        header('Location: dashboard.php');
        exit;
    }
}

// Buscar produtos
$query = "SELECT * FROM produtos ORDER BY data_criacao DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar estatísticas
$query_stats = "SELECT 
    COUNT(*) as total_produtos, 
    SUM(preco) as valor_total, 
    AVG(preco) as preco_medio, 
    COUNT(CASE WHEN destaque = 1 THEN 1 END) as produtos_destaque 
    FROM produtos";
$stmt_stats = $db->prepare($query_stats);
$stmt_stats->execute();
$estatisticas = $stmt_stats->fetch(PDO::FETCH_ASSOC);

// Buscar dados para gráficos
$query_vendas = "SELECT 
    DATE(data_pedido) as data,
    COUNT(*) as total_pedidos,
    SUM(total) as total_vendas
    FROM pedidos 
    WHERE data_pedido >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(data_pedido)
    ORDER BY data DESC
    LIMIT 30";
$stmt_vendas = $db->prepare($query_vendas);
$stmt_vendas->execute();
$vendas_diarias = $stmt_vendas->fetchAll(PDO::FETCH_ASSOC);

// Produtos mais vendidos
$query_produtos_vendidos = "SELECT 
    p.nome,
    SUM(pi.quantidade) as total_vendido,
    SUM(pi.quantidade * pi.preco_unitario) as receita_total
    FROM pedido_itens pi
    JOIN produtos p ON pi.produto_id = p.id
    JOIN pedidos ped ON pi.pedido_id = ped.id
    WHERE ped.data_pedido >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY p.id, p.nome
    ORDER BY total_vendido DESC
    LIMIT 10";
$stmt_produtos = $db->prepare($query_produtos_vendidos);
$stmt_produtos->execute();
$produtos_vendidos = $stmt_produtos->fetchAll(PDO::FETCH_ASSOC);

// Vendas por categoria
$query_categorias = "SELECT 
    p.categoria,
    COUNT(pi.id) as total_itens,
    SUM(pi.quantidade * pi.preco_unitario) as receita
    FROM pedido_itens pi
    JOIN produtos p ON pi.produto_id = p.id
    JOIN pedidos ped ON pi.pedido_id = ped.id
    WHERE ped.data_pedido >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY p.categoria
    ORDER BY receita DESC";
$stmt_categorias = $db->prepare($query_categorias);
$stmt_categorias->execute();
$vendas_categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);

// Total de pedidos e vendas
$query_totais = "SELECT 
    COUNT(*) as total_pedidos,
    SUM(total) as total_vendas,
    AVG(total) as ticket_medio
    FROM pedidos
    WHERE data_pedido >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$stmt_totais = $db->prepare($query_totais);
$stmt_totais->execute();
$totais_vendas = $stmt_totais->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Galaxia Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js">
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
                    Dashboard Admin
                </a>
                <ul class="nav-menu">
                    <li><a href="index.php">🏠 Loja</a></li>
                    <li><a href="produtos.php">🛍️ Produtos</a></li>
                    <li><a href="carrinho.php">🛒 Carrinho</a></li>
                    <li class="user-info">
                        <span>👨‍💼 <?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
                    </li>
                    <li><a href="logout.php" class="logout-btn">🚪 Sair</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <section class="dashboard">
            <div class="dashboard-header">
                <h2>Painel de Controle Administrativo</h2>
                <p>Gerencie seus produtos e estoque cósmico</p>
            </div>

            <!-- Estatísticas de Vendas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-info">
                        <h3>R$ <?= number_format($totais_vendas['total_vendas'] ?? 0, 2, ',', '.') ?></h3>
                        <p>Vendas (30 dias)</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-info">
                        <h3><?= $totais_vendas['total_pedidos'] ?? 0 ?></h3>
                        <p>Pedidos (30 dias)</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🎫</div>
                    <div class="stat-info">
                        <h3>R$ <?= number_format($totais_vendas['ticket_medio'] ?? 0, 2, ',', '.') ?></h3>
                        <p>Ticket Médio</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-info">
                        <h3><?= $estatisticas['total_produtos'] ?? 0 ?></h3>
                        <p>Total de Produtos</p>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="graficos-grid">
                <div class="grafico-card">
                    <h3>📈 Vendas Diárias (Últimos 30 dias)</h3>
                    <canvas id="vendasChart" width="400" height="200"></canvas>
                </div>
                
                <div class="grafico-card">
                    <h3>🏆 Produtos Mais Vendidos</h3>
                    <canvas id="produtosChart" width="400" height="200"></canvas>
                </div>
                
                <div class="grafico-card">
                    <h3>📁 Vendas por Categoria</h3>
                    <canvas id="categoriasChart" width="400" height="200"></canvas>
                </div>
                
                <div class="grafico-card">
                    <h3>📋 Últimos Pedidos</h3>
                    <div class="pedidos-lista">
                        <?php
                        $query_ultimos_pedidos = "SELECT p.*, u.nome as cliente 
                                                 FROM pedidos p 
                                                 JOIN usuarios u ON p.usuario_id = u.id 
                                                 ORDER BY p.data_pedido DESC 
                                                 LIMIT 5";
                        $stmt_pedidos = $db->prepare($query_ultimos_pedidos);
                        $stmt_pedidos->execute();
                        $ultimos_pedidos = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach($ultimos_pedidos as $pedido):
                        ?>
                        <div class="pedido-item">
                            <div class="pedido-info">
                                <strong>Pedido #<?= $pedido['id'] ?></strong>
                                <span><?= $pedido['cliente'] ?></span>
                            </div>
                            <div class="pedido-valor">
                                R$ <?= number_format($pedido['total'], 2, ',', '.') ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="dashboard-content">
                <div class="form-container">
                    <h3 id="form-titulo">🆕 Adicionar Novo Produto</h3>
                    <form id="produto-form" method="POST">
                        <input type="hidden" name="acao" id="acao" value="adicionar">
                        <input type="hidden" name="id" id="produto-id">
                        
                        <div class="form-group">
                            <label for="nome">🌠 Nome do Produto:</label>
                            <input type="text" id="nome" name="nome" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="preco">💲 Preço (R$):</label>
                            <input type="number" id="preco" name="preco" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="categoria">📁 Categoria:</label>
                            <select id="categoria" name="categoria" required>
                                <option value="camisetas">Camisetas</option>
                                <option value="moletons">Moletons</option>
                                <option value="acessorios">Acessórios</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="descricao">📝 Descrição:</label>
                            <textarea id="descricao" name="descricao" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="imagem">🖼️ URL da Imagem:</label>
                            <input type="url" id="imagem" name="imagem" required>
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <label>
                                <input type="checkbox" name="destaque" id="destaque">
                                ⭐ Produto em Destaque
                            </label>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Salvar Produto</button>
                            <button type="button" id="btn-cancelar" class="btn-secondary" style="display: none;">Cancelar</button>
                        </div>
                    </form>
                </div>
                
                <div class="produtos-list">
                    <h3>📦 Produtos Cadastrados</h3>
                    <div class="dashboard-produtos">
                        <?php foreach ($produtos as $produto): ?>
                        <div class="dashboard-produto">
                            <img src="<?= $produto['imagem'] ?>" alt="<?= $produto['nome'] ?>">
                            <div class="produto-info">
                                <h4><?= $produto['nome'] ?></h4>
                                <p>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                                <span class="categoria"><?= ucfirst($produto['categoria']) ?></span>
                                <?php if ($produto['destaque']): ?>
                                    <span class="destaque-badge">Destaque</span>
                                <?php endif; ?>
                            </div>
                            <div class="produto-actions">
                                <button class="btn-editar" onclick="editarProduto('<?= $produto['id'] ?>')">Editar</button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="acao" value="remover">
                                    <input type="hidden" name="id" value="<?= $produto['id'] ?>">
                                    <button type="submit" class="btn-remover" onclick="return confirm('Tem certeza?')">Remover</button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
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
            <p>&copy; 2024 Galaxia Store - Painel Administrativo | Usuário: <?= htmlspecialchars($_SESSION['usuario_nome']) ?></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            document.querySelector('.page-transition').style.opacity = '0';
        }, 500);
        
        // Gráfico de Vendas Diárias
        const vendasCtx = document.getElementById('vendasChart').getContext('2d');
        const vendasChart = new Chart(vendasCtx, {
            type: 'line',
            data: {
                labels: [<?php 
                    $labels = [];
                    foreach(array_reverse($vendas_diarias) as $venda) {
                        $labels[] = "'" . date('d/m', strtotime($venda['data'])) . "'";
                    }
                    echo implode(', ', $labels);
                ?>],
                datasets: [{
                    label: 'Vendas (R$)',
                    data: [<?php 
                        $data = [];
                        foreach(array_reverse($vendas_diarias) as $venda) {
                            $data[] = $venda['total_vendas'];
                        }
                        echo implode(', ', $data);
                    ?>],
                    borderColor: '#a78bfa',
                    backgroundColor: 'rgba(167, 139, 250, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        
        // Gráfico de Produtos Mais Vendidos
        const produtosCtx = document.getElementById('produtosChart').getContext('2d');
        const produtosChart = new Chart(produtosCtx, {
            type: 'bar',
            data: {
                labels: [<?php 
                    $labels = [];
                    foreach($produtos_vendidos as $produto) {
                        $labels[] = "'" . substr($produto['nome'], 0, 15) . "'";
                    }
                    echo implode(', ', $labels);
                ?>],
                datasets: [{
                    label: 'Unidades Vendidas',
                    data: [<?php 
                        $data = [];
                        foreach($produtos_vendidos as $produto) {
                            $data[] = $produto['total_vendido'];
                        }
                        echo implode(', ', $data);
                    ?>],
                    backgroundColor: '#fbbf24'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        
        // Gráfico de Vendas por Categoria
        const categoriasCtx = document.getElementById('categoriasChart').getContext('2d');
        const categoriasChart = new Chart(categoriasCtx, {
            type: 'doughnut',
            data: {
                labels: [<?php 
                    $labels = [];
                    foreach($vendas_categorias as $categoria) {
                        $labels[] = "'" . ucfirst($categoria['categoria']) . "'";
                    }
                    echo implode(', ', $labels);
                ?>],
                datasets: [{
                    data: [<?php 
                        $data = [];
                        foreach($vendas_categorias as $categoria) {
                            $data[] = $categoria['receita'];
                        }
                        echo implode(', ', $data);
                    ?>],
                    backgroundColor: [
                        '#a78bfa',
                        '#fbbf24',
                        '#68d391',
                        '#f56565'
                    ]
                }]
            },
            options: {
                responsive: true
            }
        });
        
        function editarProduto(id) {
            document.getElementById('form-titulo').textContent = '✏️ Editar Produto';
            document.getElementById('acao').value = 'editar';
            document.getElementById('produto-id').value = id;
            document.getElementById('btn-cancelar').style.display = 'inline-block';
        }
        
        document.getElementById('btn-cancelar').addEventListener('click', function() {
            document.getElementById('produto-form').reset();
            document.getElementById('form-titulo').textContent = '🆕 Adicionar Novo Produto';
            document.getElementById('acao').value = 'adicionar';
            this.style.display = 'none';
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