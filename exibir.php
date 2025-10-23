<?php
// Configurações de Conexão - Corrigidas para Railway
$servername = "metro.proxy.rlwy.net"; // Host (servidor) do Railway (Proxy TCP)
$username = "root";       // Usuário do MySQL (Geralmente root)
$password = "QZGKpSwJXkMrLuyzceQXcrZuYuNbOdaz"; // << SUA SENHA REAL DO RAILWAY
$dbname = "railway";      // Nome completo do Banco de Dados
$port = 32641;            // Porta Pública do Proxy TCP do Railway

// Cria a conexão (incluindo a porta)
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro na Conexão: " . $conn->connect_error);
}

// 1. QUERY SQL para selecionar todos os pedidos
$sql = "SELECT id, nome_cliente, telefone_contacto, produto_solicitado, quantidade_solicitada, notas_pedido, status_pedido, data_pedido FROM pedidos ORDER BY data_pedido DESC";

// Executa a query
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Pedidos | Myx Beer</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
        }
        h1 {
            color: #1a1a1a;
            border-bottom: 3px solid #FFC72C; /* Amarelo da Myx Beer */
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #1a1a1a; /* Preto */
            color: white;
            font-weight: bold;
            font-size: 0.9em;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        /* Estilos para o Status (para diferenciar visualmente) */
        .status-Novo { background-color: #ffe0b2; color: #e65100; font-weight: bold;}
        .status-EmProcessamento { background-color: #bbdefb; color: #1565c0; }
        .status-Entregue { background-color: #c8e6c9; color: #2e7d32; font-weight: bold; }
        .status-Cancelado { background-color: #ffcdd2; color: #c62828; }
    </style>
</head>
<body>

    <h1>📋 Pedidos Recentes - Distribuidora Myx Beer</h1>
    <p>Total de pedidos encontrados: <?php echo $result->num_rows; ?></p>
    
    <?php
    // Verifica se há resultados (pedidos) na tabela
    if ($result->num_rows > 0) {
        
        // 2. Inicia a tabela HTML
        echo "<table>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Data/Hora</th>";
        echo "<th>Cliente</th>";
        echo "<th>Contato</th>";
        echo "<th>Produto</th>";
        echo "<th>Qtde.</th>";
        echo "<th>Notas</th>";
        echo "<th>Status</th>";
        echo "</tr>";

        // 3. Loop para ler cada linha (pedido)
        while($row = $result->fetch_assoc()) {
            
            // Cria uma classe dinâmica baseada no status para aplicar cores
            $statusClass = 'status-' . str_replace(' ', '', $row['status_pedido']);
            
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['data_pedido'] . "</td>";
            echo "<td>" . $row['nome_cliente'] . "</td>";
            echo "<td>" . $row['telefone_contacto'] . "</td>";
            echo "<td>" . $row['produto_solicitado'] . "</td>";
            echo "<td>" . $row['quantidade_solicitada'] . "</td>";
            echo "<td>" . (empty($row['notas_pedido']) ? '-' : $row['notas_pedido']) . "</td>";
            // Aplica a classe de status na célula
            echo "<td class='{$statusClass}'>" . $row['status_pedido'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
    } else {
        // Mensagem se não houver pedidos
        echo "<p>Nenhum pedido foi encontrado na base de dados.</p>";
    }

    // Fecha a conexão
    $conn->close();
    ?>

</body>
</html>