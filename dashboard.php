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
$query_stats = "SELECT COUNT(*) as total_produtos, SUM(preco) as valor_total, AVG(preco) as preco_medio, COUNT(CASE WHEN destaque = 1 THEN 1 END) as produtos_destaque FROM produtos";
$stmt_stats = $db->prepare($query_stats);
$stmt_stats->execute();
$estatisticas = $stmt_stats->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Galaxia Store</title>
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

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-info">
                        <h3><?= $estatisticas['total_produtos'] ?? 0 ?></h3>
                        <p>Total de Produtos</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-info">
                        <h3>R$ <?= number_format($estatisticas['valor_total'] ?? 0, 2, ',', '.') ?></h3>
                        <p>Valor em Estoque</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-info">
                        <h3><?= $estatisticas['produtos_destaque'] ?? 0 ?></h3>
                        <p>Produtos em Destaque</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-info">
                        <h3>R$ <?= number_format($estatisticas['preco_medio'] ?? 0, 2, ',', '.') ?></h3>
                        <p>Preço Médio</p>
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
                            <img src="<?= $produto['imagem'] ?>" alt="<?= $produto['nome'] ?>" width="80" height="80">
                            <div class="produto-info">
                                <h4><?= $produto['nome'] ?></h4>
                                <p>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                                <span class="categoria"><?= ucfirst($produto['categoria']) ?></span>
                                <?php if ($produto['destaque']): ?>
                                    <span class="destaque-badge">⭐ Destaque</span>
                                <?php endif; ?>
                            </div>
                            <div class="produto-actions">
                                <button class="btn-editar" onclick="editarProduto(<?= $produto['id'] ?>)">✏️ Editar</button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="acao" value="remover">
                                    <input type="hidden" name="id" value="<?= $produto['id'] ?>">
                                    <button type="submit" class="btn-remover" onclick="return confirm('Tem certeza que deseja remover este produto?')">🗑️ Remover</button>
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const pageTransition = document.querySelector('.page-transition');
            if (pageTransition) {
                pageTransition.style.opacity = '0';
            }
        }, 500);
        
        // Tornar a função global para o HTML poder chamar
        window.editarProduto = function(id) {
            console.log('Editando produto ID:', id);
            
            // Buscar dados do produto via AJAX
            fetch('api_produtos.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na resposta da API');
                    }
                    return response.json();
                })
                .then(produtos => {
                    const produto = produtos.find(p => p.id == id);
                    if (produto) {
                        console.log('Produto encontrado:', produto);
                        
                        document.getElementById('form-titulo').textContent = '✏️ Editar Produto';
                        document.getElementById('acao').value = 'editar';
                        document.getElementById('produto-id').value = produto.id;
                        document.getElementById('nome').value = produto.nome;
                        document.getElementById('preco').value = produto.preco;
                        document.getElementById('categoria').value = produto.categoria;
                        document.getElementById('descricao').value = produto.descricao;
                        document.getElementById('imagem').value = produto.imagem;
                        document.getElementById('destaque').checked = produto.destaque == 1;
                        
                        const btnCancelar = document.getElementById('btn-cancelar');
                        if (btnCancelar) {
                            btnCancelar.style.display = 'inline-block';
                        }
                        
                        // Scroll para o formulário
                        const formContainer = document.querySelector('.form-container');
                        if (formContainer) {
                            formContainer.scrollIntoView({ behavior: 'smooth' });
                        }
                    } else {
                        console.error('Produto não encontrado');
                        alert('Erro: Produto não encontrado');
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar produto:', error);
                    alert('Erro ao carregar dados do produto');
                });
        };
        
        const btnCancelar = document.getElementById('btn-cancelar');
        if (btnCancelar) {
            btnCancelar.addEventListener('click', function() {
                document.getElementById('produto-form').reset();
                document.getElementById('form-titulo').textContent = '🆕 Adicionar Novo Produto';
                document.getElementById('acao').value = 'adicionar';
                this.style.display = 'none';
            });
        }
    });
    
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.href && !this.href.includes('javascript')) {
                e.preventDefault();
                const pageTransition = document.querySelector('.page-transition');
                if (pageTransition) {
                    pageTransition.style.opacity = '1';
                }
                setTimeout(() => {
                    window.location.href = this.href;
                }, 500);
            }
        });
    });
    </script>
</body>
</html>