<?php
require_once 'conexao.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    die("Erro na conexão com o banco de dados");
}

// Array com as atualizações CORRETAS
$atualizacoes = [
    ['Camiseta Nebulosa', 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400'],
    ['Moleton Via Láctea', 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=400'],
    ['Boné Astronauta', 'https://images.unsplash.com/photo-1588850561407-5d44d8cdea49?w=400'],
    ['Camiseta Constelações', 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=400'],
    ['Blusa Universo', 'https://images.unsplash.com/photo-1574180045827-681f8a1a9622?w=400'],
    ['Touca Espacial', 'https://images.unsplash.com/photo-1576871337632-b9aef4c17ab9?w=400']
];

echo "<h2>Atualizando Imagens...</h2>";

foreach ($atualizacoes as $atualizacao) {
    $nome = $atualizacao[0];
    $imagem = $atualizacao[1];
    
    $query = "UPDATE produtos SET imagem = ? WHERE nome = ?";
    $stmt = $db->prepare($query);
    
    if ($stmt->execute([$imagem, $nome])) {
        echo "<p style='color: green;'>✅ Atualizado: $nome</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro em: $nome</p>";
    }
}

echo "<h3>Verificando resultados:</h3>";
$query = "SELECT nome, imagem FROM produtos";
$stmt = $db->prepare($query);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($produtos as $produto) {
    echo "<p><strong>{$produto['nome']}:</strong> {$produto['imagem']}</p>";
}
?>