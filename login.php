<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Galaxia Store</title>
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
                    <li><a href="login.php" class="active">Login</a></li>
                    <li><a href="cadastro.php">Cadastro</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2>🌌 Login Galáxia</h2>
                <p>Acesse sua conta cósmica</p>
            </div>

            <form id="login-form" action="auth.php" method="POST" class="auth-form">
                <input type="hidden" name="acao" value="login">
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required placeholder="seu@email.com">
                </div>

                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required placeholder="Sua senha">
                </div>

                <button type="submit" class="btn-auth">Entrar no Universo</button>
            </form>

            <div class="auth-links">
                <a href="cadastro.php">Criar conta estelar</a>
                <a href="recuperacao.html">Esqueci minha senha</a>
                <a href="index.php">Voltar para a loja</a>
            </div>

            <?php if(isset($_GET['erro'])): ?>
            <div class="mensagem error">
                <?= htmlspecialchars($_GET['erro']) ?>
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['sucesso'])): ?>
            <div class="mensagem success">
                <?= htmlspecialchars($_GET['sucesso']) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            document.querySelector('.page-transition').style.opacity = '0';
        }, 500);
    });
    </script>
</body>
</html>