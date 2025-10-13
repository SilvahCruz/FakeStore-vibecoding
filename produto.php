<?php
session_start();
require_once 'conexao.php';

$database = new Database();
$db = $database->getConnection();

$produto_id = $_GET['id'] ?? 0;

// Buscar produto
$query = "SELECT * FROM produtos WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$produto_id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header('Location: produtos.php');
    exit;
}

// Processar adição ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_carrinho'])) {
    if (!isset($_SESSION['usuario_id'])) {
        $redirect_url = 'produto.php?id=' . $produto_id;
        header('Location: login.php?redirect=' . urlencode($redirect_url));
        exit;
    }
    
    $quantidade = $_POST['quantidade'] ?? 1;
    $cor = $_POST['cor'] ?? 'roxo';
    $tamanho = $_POST['tamanho'] ?? 'M';
    
    // Verificar se já está no carrinho
    $query = "SELECT * FROM carrinho WHERE usuario_id = ? AND produto_id = ? AND cor = ? AND tamanho = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['usuario_id'], $produto_id, $cor, $tamanho]);
    
    if ($stmt->rowCount() > 0) {
        // Atualizar quantidade
        $query = "UPDATE carrinho SET quantidade = quantidade + ? WHERE usuario_id = ? AND produto_id = ? AND cor = ? AND tamanho = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$quantidade, $_SESSION['usuario_id'], $produto_id, $cor, $tamanho]);
    } else {
        // Adicionar novo
        $query = "INSERT INTO carrinho (usuario_id, produto_id, quantidade, cor, tamanho) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$_SESSION['usuario_id'], $produto_id, $quantidade, $cor, $tamanho]);
    }
    
    // Se for compra imediata, redirecionar para checkout
    if (isset($_POST['compra_imediata']) && $_POST['compra_imediata'] === '1') {
        $_SESSION['checkout_produtos'] = [[
            'produto_id' => $produto_id,
            'quantidade' => $quantidade,
            'cor' => $cor,
            'tamanho' => $tamanho
        ]];
        header('Location: checkout.php');
        exit;
    } else {
        $_SESSION['sucesso'] = "Produto adicionado ao carrinho!";
        header('Location: carrinho.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($produto['nome']) ?> - Galaxia Store</title>
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
        <section class="produto-detalhe">
            <div class="produto-container">
                <div class="produto-imagem">
                    <img src="<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
                </div>
                
                <div class="produto-info">
                    <div class="produto-header">
                        <h1><?= htmlspecialchars($produto['nome']) ?></h1>
                        <div class="rating">
                            <span class="stars">★★★★★</span>
                            <span class="rating-text">4.8 (851 avaliações)</span>
                        </div>
                    </div>
                    
                    <div class="preco-section">
                        <div class="preco-atual">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></div>
                        <div class="preco-original">R$ <?= number_format($produto['preco'] * 1.5, 2, ',', '.') ?></div>
                        <div class="desconto-badge">-33% OFF</div>
                    </div>
                    
                    <div class="oferta-relampago">
                        <span class="relampago-icon">⚡</span>
                        <strong>OFERTA RELÂMPAGO</strong>
                        <span id="contador-tempo">23:59:59</span>
                    </div>
                    
                    <div class="frete-info">
                        <span class="frete-icon">🚚</span>
                        <div>
                            <strong>Frete grátis</strong>
                            <span>Para São Paulo, São Paulo</span>
                        </div>
                    </div>
                    
                    <div class="opcoes-produto">
                        <div class="opcao-grupo">
                            <label>Cor:</label>
                            <div class="cores">
                                <button type="button" class="cor-option active" data-cor="roxo" style="background: #6b46c1;">Roxo</button>
                                <button type="button" class="cor-option" data-cor="preto" style="background: #2d3748;">Preto</button>
                                <button type="button" class="cor-option" data-cor="branco" style="background: #fff; color: #000;">Branco</button>
                                <button type="button" class="cor-option" data-cor="azul" style="background: #3182ce;">Azul Espacial</button>
                            </div>
                        </div>
                        
                        <div class="opcao-grupo">
                            <label>Tamanho:</label>
                            <div class="tamanhos">
                                <button type="button" class="tamanho-option" data-tamanho="P">P</button>
                                <button type="button" class="tamanho-option active" data-tamanho="M">M</button>
                                <button type="button" class="tamanho-option" data-tamanho="G">G</button>
                                <button type="button" class="tamanho-option" data-tamanho="GG">GG</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="quantidade-section">
                        <label>Quantidade:</label>
                        <div class="quantidade-controller">
                            <button type="button" class="qty-btn" id="decrease-qty">-</button>
                            <input type="number" id="quantidade" name="quantidade" value="1" min="1" max="10">
                            <button type="button" class="qty-btn" id="increase-qty">+</button>
                        </div>
                    </div>
                    
                    <form method="POST" class="comprar-form">
                        <input type="hidden" name="adicionar_carrinho" value="1">
                        <input type="hidden" name="quantidade" id="quantidade-hidden" value="1">
                        <input type="hidden" name="cor" id="cor-hidden" value="roxo">
                        <input type="hidden" name="tamanho" id="tamanho-hidden" value="M">
                        <input type="hidden" name="compra_imediata" id="compra-imediata" value="0">
                        
                        <div class="action-buttons">
                            <button type="submit" class="btn-comprar">🛒 Adicionar ao Carrinho</button>
                            <button type="button" class="btn-comprar-agora">⚡ Comprar Agora</button>
                        </div>
                    </form>
                    
                    <div class="produto-descricao">
                        <h3>Descrição do Produto</h3>
                        <p><?= htmlspecialchars($produto['descricao']) ?></p>
                        <ul>
                            <li>✅ Material 100% algodão</li>
                            <li>✅ Estampa de alta durabilidade</li>
                            <li>✅ Lavagem segura</li>
                            <li>✅ Entrega rápida</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="produtos-relacionados">
            <h2>Produtos Relacionados</h2>
            <div class="produtos-grid">
                <?php
                $query_relacionados = "SELECT * FROM produtos WHERE categoria = ? AND id != ? LIMIT 4";
                $stmt_relacionados = $db->prepare($query_relacionados);
                $stmt_relacionados->execute([$produto['categoria'], $produto_id]);
                $relacionados = $stmt_relacionados->fetchAll(PDO::FETCH_ASSOC);
                
                foreach($relacionados as $relacionado):
                ?>
                <div class="produto-card">
                    <a href="produto.php?id=<?= $relacionado['id'] ?>">
                        <img src="<?= htmlspecialchars($relacionado['imagem']) ?>" alt="<?= htmlspecialchars($relacionado['nome']) ?>">
                        <h4><?= htmlspecialchars($relacionado['nome']) ?></h4>
                        <p class="preco">R$ <?= number_format($relacionado['preco'], 2, ',', '.') ?></p>
                        <span class="categoria"><?= ucfirst($relacionado['categoria']) ?></span>
                    </a>
                </div>
                <?php endforeach; ?>
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
        
        // Contador regressivo
        function iniciarContador() {
            const contador = document.getElementById('contador-tempo');
            let tempo = 23 * 60 * 60 + 59 * 60 + 59; // 23:59:59 em segundos
            
            function atualizarContador() {
                const horas = Math.floor(tempo / 3600);
                const minutos = Math.floor((tempo % 3600) / 60);
                const segundos = tempo % 60;
                
                contador.textContent = 
                    `${horas.toString().padStart(2, '0')}:${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
                
                if (tempo > 0) {
                    tempo--;
                    setTimeout(atualizarContador, 1000);
                } else {
                    contador.textContent = 'Oferta encerrada!';
                }
            }
            
            atualizarContador();
        }
        
        iniciarContador();
        
        // Quantidade controller
        const quantidadeInput = document.getElementById('quantidade');
        const quantidadeHidden = document.getElementById('quantidade-hidden');
        const decreaseBtn = document.getElementById('decrease-qty');
        const increaseBtn = document.getElementById('increase-qty');
        
        decreaseBtn.addEventListener('click', () => {
            if (quantidadeInput.value > 1) {
                quantidadeInput.value = parseInt(quantidadeInput.value) - 1;
                quantidadeHidden.value = quantidadeInput.value;
            }
        });
        
        increaseBtn.addEventListener('click', () => {
            if (quantidadeInput.value < 10) {
                quantidadeInput.value = parseInt(quantidadeInput.value) + 1;
                quantidadeHidden.value = quantidadeInput.value;
            }
        });
        
        // Opções de cor e tamanho
        document.querySelectorAll('.cor-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.cor-option').forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('cor-hidden').value = this.getAttribute('data-cor');
            });
        });
        
        document.querySelectorAll('.tamanho-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.tamanho-option').forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('tamanho-hidden').value = this.getAttribute('data-tamanho');
            });
        });
        
        // Comprar agora
        document.querySelector('.btn-comprar-agora').addEventListener('click', function() {
            document.getElementById('compra-imediata').value = '1';
            document.querySelector('.comprar-form').submit();
        });
        
        // Adicionar ao carrinho (sem compra imediata)
        document.querySelector('.comprar-form').addEventListener('submit', function(e) {
            if (document.getElementById('compra-imediata').value === '0') {
                // Não faz nada extra - submit normal
            }
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