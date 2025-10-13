<?php
require_once 'conexao.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT * FROM produtos ORDER BY data_criacao DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($produtos);
} catch(PDOException $exception) {
    echo json_encode(['error' => $exception->getMessage()]);
}
?>