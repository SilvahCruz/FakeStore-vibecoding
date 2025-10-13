<?php
// Este arquivo é incluído no dashboard.php
// Todo o processamento está integrado no dashboard.php para simplificação
// Em uma aplicação real, você separaria a lógica de processamento

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produtos_file = 'produtos.json';
    $produtos = json_decode(file_get_contents($produtos_file), true) ?? [];
    
    $acao = $_POST['acao'] ?? '';
    
    switch ($acao) {
        case 'adicionar':
            $novo_produto = [
                'id' => uniqid(),
                'nome' => $_POST['nome'],
                'preco' => floatval($_POST['preco']),
                'categoria' => $_POST['categoria'],
                'descricao' => $_POST['descricao'],
                'imagem' => $_POST['imagem'],
                'destaque' => isset($_POST['destaque'])
            ];
            
            $produtos[] = $novo_produto;
            break;
            
        case 'editar':
            foreach ($produtos as &$produto) {
                if ($produto['id'] === $_POST['id']) {
                    $produto = [
                        'id' => $_POST['id'],
                        'nome' => $_POST['nome'],
                        'preco' => floatval($_POST['preco']),
                        'categoria' => $_POST['categoria'],
                        'descricao' => $_POST['descricao'],
                        'imagem' => $_POST['imagem'],
                        'destaque' => isset($_POST['destaque'])
                    ];
                    break;
                }
            }
            break;
            
        case 'remover':
            $produtos = array_filter($produtos, function($produto) {
                return $produto['id'] !== $_POST['id'];
            });
            $produtos = array_values($produtos);
            break;
    }
    
    file_put_contents($produtos_file, json_encode($produtos, JSON_PRETTY_PRINT));
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
}
?>