<?php
session_start();
require_once 'conexao.php';

$database = new Database();
$db = $database->getConnection();

echo "<h2>Debug - Produtos</h2>";

if ($db === null) {
    echo "❌ ERRO: Banco de dados não conectado";
    exit;
}

echo "✅ Banco conectado<br>";

// Ver produtos
$query = "SELECT id, nome, imagem FROM produtos";
$stmt = $db->prepare($query);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Produtos no banco:</h3>";
foreach($produtos as $produto) {
    echo "<p><strong>{$produto['nome']}:</strong> {$produto['imagem']}</p>";
    echo "<img src='{$produto['imagem']}' width='100' style='border: 2px solid green'><br><br>";
}
?>