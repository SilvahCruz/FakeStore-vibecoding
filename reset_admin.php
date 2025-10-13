<?php
require_once 'conexao.php';

$database = new Database();
$db = $database->getConnection();

// Resetar senha do admin
$senha_hash = password_hash('Admin123!', PASSWORD_DEFAULT);

// Remover admin existente
$query = "DELETE FROM usuarios WHERE email = 'admin@galaxiastore.com'";
$db->exec($query);

// Inserir novo admin
$query = "INSERT INTO usuarios (email, senha, nome, nivel) VALUES ('admin@galaxiastore.com', '$senha_hash', 'Administrador', 'admin')";
$db->exec($query);

echo "Admin resetado com sucesso!<br>";
echo "Email: admin@galaxiastore.com<br>";
echo "Senha: Admin123!<br>";
echo "<a href='login.php'>Fazer Login</a>";
?>