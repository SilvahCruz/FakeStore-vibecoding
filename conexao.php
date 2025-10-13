<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'loja_galaxia';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Erro de conexão: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

// Criar tabelas se não existirem
function criarTabelas($db) {
    // Tabela de usuários
    $query = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        senha VARCHAR(255) NOT NULL,
        nome VARCHAR(255) NOT NULL,
        nivel ENUM('admin', 'usuario') DEFAULT 'usuario',
        token_recuperacao VARCHAR(255) DEFAULT NULL,
        token_expiracao DATETIME DEFAULT NULL,
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $db->exec($query);

    // Tabela de produtos
    $query = "CREATE TABLE IF NOT EXISTS produtos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        preco DECIMAL(10,2) NOT NULL,
        categoria ENUM('camisetas', 'moletons', 'acessorios') NOT NULL,
        descricao TEXT NOT NULL,
        imagem VARCHAR(500) NOT NULL,
        destaque BOOLEAN DEFAULT FALSE,
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $db->exec($query);

    // Tabela do carrinho (atualizada com cor e tamanho)
    $query = "CREATE TABLE IF NOT EXISTS carrinho (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT,
        produto_id INT,
        quantidade INT DEFAULT 1,
        cor VARCHAR(50) DEFAULT 'roxo',
        tamanho VARCHAR(10) DEFAULT 'M',
        data_adicionado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
        FOREIGN KEY (produto_id) REFERENCES produtos(id)
    )";
    $db->exec($query);

    // Tabela de pedidos
    $query = "CREATE TABLE IF NOT EXISTS pedidos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT,
        total DECIMAL(10,2) NOT NULL,
        status ENUM('pendente', 'confirmado', 'enviado', 'entregue') DEFAULT 'pendente',
        data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    )";
    $db->exec($query);

    // Tabela de itens do pedido
    $query = "CREATE TABLE IF NOT EXISTS pedido_itens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pedido_id INT,
        produto_id INT,
        quantidade INT NOT NULL,
        preco_unitario DECIMAL(10,2) NOT NULL,
        cor VARCHAR(50) DEFAULT 'roxo',
        tamanho VARCHAR(10) DEFAULT 'M',
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
        FOREIGN KEY (produto_id) REFERENCES produtos(id)
    )";
    $db->exec($query);

    // Inserir admin padrão se não existir (senha: Admin123!)
    $query = "SELECT COUNT(*) FROM usuarios WHERE email = 'admin@galaxiastore.com'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    if ($stmt->fetchColumn() == 0) {
        $senha_hash = password_hash('Admin123!', PASSWORD_DEFAULT);
        $query = "INSERT INTO usuarios (email, senha, nome, nivel) VALUES ('admin@galaxiastore.com', '$senha_hash', 'Administrador', 'admin')";
        $db->exec($query);
    }

    // Inserir produtos de exemplo se não existirem
    $query = "SELECT COUNT(*) FROM produtos";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    if ($stmt->fetchColumn() == 0) {
        $produtos = [
            ['Camiseta Nebulosa', 79.90, 'camisetas', 'Camiseta premium com estampa de nebulosa cósmica em alta definição', 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=500', 1],
            ['Moleton Via Láctea', 149.90, 'moletons', 'Moleton comfort com estampa detalhada da via láctea', 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=500', 1],
            ['Boné Astronauta', 49.90, 'acessorios', 'Boné estilo baseball com logo espacial exclusivo', 'https://images.unsplash.com/photo-1588850561407-5d44d8cdea49?w=500', 0],
            ['Camiseta Constelações', 69.90, 'camisetas', 'Camiseta com mapa de constelações brilhantes', 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=500', 1],
            ['Blusa Universo', 129.90, 'moletons', 'Blusa com estampa do sistema solar completo', 'https://images.unsplash.com/photo-1574180045827-681f8a1a9622?w=500', 0],
            ['Touca Espacial', 39.90, 'acessorios', 'Touca beanie com estampa de planetas', 'https://images.unsplash.com/photo-1576871337632-b9aef4c17ab9?w=500', 0]
        ];

        foreach ($produtos as $produto) {
            $query = "INSERT INTO produtos (nome, preco, categoria, descricao, imagem, destaque) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->execute($produto);
        }
    }
}

// Inicializar banco
try {
    $database = new Database();
    $db = $database->getConnection();
    criarTabelas($db);
} catch(Exception $e) {
    // Silencioso - não quebra a aplicação
}
?>