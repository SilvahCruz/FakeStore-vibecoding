<?php
session_start();
require_once 'conexao.php';

$database = new Database();
$db = $database->getConnection();

$acao = $_POST['acao'] ?? '';

function redirect($url, $tipo = null, $mensagem = null) {
    if ($tipo && $mensagem) {
        header("Location: $url?$tipo=" . urlencode($mensagem));
    } else {
        header("Location: $url");
    }
    exit;
}

try {
    switch ($acao) {
        case 'login':
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            
            $query = "SELECT * FROM usuarios WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($senha, $usuario['senha'])) {
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_email'] = $usuario['email'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];
                    $_SESSION['usuario_nivel'] = $usuario['nivel'];
                    
                    // Redirecionar para a página original se existir
                    if (isset($_POST['redirect'])) {
                        redirect($_POST['redirect'], 'sucesso', 'Login realizado com sucesso!');
                    } else if ($usuario['nivel'] === 'admin') {
                        redirect('dashboard.php', 'sucesso', 'Login admin realizado com sucesso!');
                    } else {
                        redirect('index.php', 'sucesso', 'Login realizado com sucesso!');
                    }
                } else {
                    redirect('login.php', 'erro', 'Senha incorreta!');
                }
            } else {
                redirect('login.php', 'erro', 'Usuário não encontrado!');
            }
            break;
            
        case 'cadastro':
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            $confirmar_senha = $_POST['confirmar_senha'];
            
            if ($senha !== $confirmar_senha) {
                redirect('cadastro.php', 'erro', 'As senhas não coincidem!');
            }
            
            if (strlen($senha) < 6) {
                redirect('cadastro.php', 'erro', 'A senha deve ter pelo menos 6 caracteres!');
            }
            
            $query = "SELECT id FROM usuarios WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                redirect('cadastro.php', 'erro', 'Este email já está cadastrado!');
            }
            
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $query = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
            $stmt = $db->prepare($query);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':senha', $senha_hash);
            
            if ($stmt->execute()) {
                redirect('login.php', 'sucesso', 'Conta criada com sucesso! Faça login.');
            } else {
                redirect('cadastro.php', 'erro', 'Erro ao criar conta!');
            }
            break;
            
        default:
            redirect('login.php', 'erro', 'Ação inválida!');
    }
} catch(PDOException $exception) {
    error_log("Erro no login: " . $exception->getMessage());
    redirect('login.php', 'erro', 'Erro no servidor! Tente novamente.');
}
?>