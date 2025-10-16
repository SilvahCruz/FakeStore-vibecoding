<?php
require_once 'conexao.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Lidar com requisições OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$database = new Database();
$db = $database->getConnection();

try {
    if ($db === null) {
        throw new Exception('Não foi possível conectar ao banco de dados');
    }
    
    $query = "SELECT * FROM produtos ORDER BY data_criacao DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Converter tipos numéricos
    foreach ($produtos as &$produto) {
        $produto['id'] = (int)$produto['id'];
        $produto['preco'] = (float)$produto['preco'];
        $produto['destaque'] = (bool)$produto['destaque'];
    }
    
    echo json_encode($produtos, JSON_NUMERIC_CHECK);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Erro no banco de dados: ' . $exception->getMessage()
    ]);
} catch(Exception $exception) {
    http_response_code(500);
    echo json_encode([
        'error' => true, 
        'message' => $exception->getMessage()
    ]);
}
?>