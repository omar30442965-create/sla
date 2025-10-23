<?php
// Define o charset para garantir que caracteres especiais sejam exibidos corretamente
header('Content-Type: text/html; charset=UTF-8');

// ===========================================
// 1. CONFIGURAÇÃO DA CONEXÃO - CORRIGIDA PARA RAILWAY
// ===========================================
$servername = "metro.proxy.rlwy.net"; // Host (servidor) do Railway (Proxy TCP)
$username = "root";                     // Usuário do MySQL (Geralmente root)
$password = "QZGKpSwJXkMrLuyzceQXcrZuYuNbOdaz"; // << SUA SENHA REAL DO RAILWAY
$dbname = "railway";                    // Nome completo do Banco de Dados
$port = 32641;                          // Porta Pública do Proxy TCP do Railway

// Cria a conexão (incluindo a porta)
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verifica a conexão
if ($conn->connect_error) {
    // Exibe um erro amigável se a conexão falhar
    die("<h1>❌ ERRO CRÍTICO DE CONEXÃO!</h1><p>Não foi possível conectar ao banco de dados: " . $conn->connect_error . "</p><p>Verifique se o Host: {$servername} e a Porta: {$port} estão corretos.</p>");
}

// ===========================================
// 2. RECEBER E TRATAR OS DADOS DO FORMULÁRIO (POST)
// ===========================================

// Verifica se os campos obrigatórios foram enviados pelo formulário
if (isset($_POST['nome_cliente'], $_POST['telefone_contacto'], $_POST['produto_solicitado'], $_POST['quantidade_solicitada'])) {
    
    // Filtra e armazena os dados, prevenindo XSS (Cross-Site Scripting)
    $nome = htmlspecialchars($_POST['nome_cliente']);
    $telefone = htmlspecialchars($_POST['telefone_contacto']);
    $produto = htmlspecialchars($_POST['produto_solicitado']);
    
    // Converte a quantidade para inteiro, garantindo que seja um número válido
    $quantidade = intval($_POST['quantidade_solicitada']);
    
    // Trata o campo opcional de notas
    $notas = isset($_POST['notas_pedido']) ? htmlspecialchars($_POST['notas_pedido']) : '';

    // ===========================================
    // 3. PREPARAR E EXECUTAR A INSERÇÃO SEGURA (Prepared Statement)
    // ===========================================

    // SQL com placeholders (?) - O MySQL se encarrega de preencher de forma segura
    // Nota: Supondo que sua tabela 'pedidos' foi criada com sucesso no Railway.
    $sql = "INSERT INTO pedidos (nome_cliente, telefone_contacto, produto_solicitado, quantidade_solicitada, notas_pedido) VALUES (?, ?, ?, ?, ?)";
    
    // Prepara a declaração (o comando SQL)
    $stmt = $conn->prepare($sql);

    // Verifica se a preparação da query falhou (pode ser problema na estrutura da tabela)
    if ($stmt === false) {
        echo "<h1>❌ ERRO INTERNO!</h1><p>Falha ao preparar a query SQL. Verifique se a tabela 'pedidos' e suas colunas existem em '{$dbname}'.</p><p>Detalhes: " . $conn->error . "</p>";
    } else {
        // Liga as variáveis aos placeholders (s = string, i = integer)
        // "sssis" => 3 strings, 1 integer, 1 string
        $stmt->bind_param("sssis", $nome, $telefone, $produto, $quantidade, $notas);

        // Executa a declaração
        if ($stmt->execute()) {
            // Sucesso: feedback visualmente agradável
            echo "
            <style>
                body { font-family: sans-serif; text-align: center; padding: 50px; background-color: #f7f7f7; }
                h1 { color: #28a745; }
                .pedido-info { border: 1px solid #ddd; padding: 20px; max-width: 400px; margin: 20px auto; background-color: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
            </style>
            <h1>✅ Pedido Recebido com Sucesso!</h1>
            <p>Obrigado, <strong>{$nome}</strong>! Seu pedido foi registrado.</p>
            <div class='pedido-info'>
                <p><strong>Produto:</strong> {$produto}</p>
                <p><strong>Quantidade:</strong> {$quantidade} caixas</p>
                <p>Entraremos em contato no: <strong>{$telefone}</strong></p>
            </div>
            <p>A Distribuidora Myx Beer agradece a preferência! 🍻</p>
            <a href='index.html'>Fazer novo pedido</a>
            ";
        } else {
            // Erro de Execução (se o SQL falhar por algum motivo)
            echo "<h1>❌ ERRO AO REGISTRAR!</h1><p>Detalhes do erro: " . $stmt->error . "</p>";
        }

        // Fecha a declaração preparada
        $stmt->close();
    }
    
} else {
    // Caso o usuário acesse guardar.php diretamente sem enviar dados
    echo "
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; background-color: #f7f7f7; }
        h1 { color: #dc3545; }
    </style>
    <h1>🚫 Acesso Inválido</h1>
    <p>Por favor, envie o pedido através do <a href='index.html'>formulário principal</a>.</p>
    ";
}

// Fecha a conexão com o banco de dados
$conn->close();
?>