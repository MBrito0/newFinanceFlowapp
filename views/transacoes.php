<?php
// Inclui a configuração para iniciar a sessão e a conexão com o banco de dados
require_once __DIR__ . '/../includes/config.php';

// 1. VERIFICAÇÃO DE SESSÃO
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$user_id = $_SESSION["id"];
$trans_err = $trans_success = "";

// 2. BUSCAR CONTAS ATIVAS PARA O FORMULÁRIO
$contas = [];
$sql_accounts = "SELECT id, name FROM accounts WHERE user_id = ?";
if ($stmt_accounts = $conn->prepare($sql_accounts)) {
    $stmt_accounts->bind_param("i", $user_id);
    $stmt_accounts->execute();
    $result_accounts = $stmt_accounts->get_result();
    while ($row = $result_accounts->fetch_assoc()) {
        $contas[] = $row;
    }
    $stmt_accounts->close();
}

// 3. LÓGICA PARA ADICIONAR NOVA TRANSAÇÃO (INSERT no MySQL)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_transaction') {
    
    $type = trim($_POST['type']); // 'Receita' ou 'Despesa'
    $description = trim($_POST['description']);
    $amount = floatval($_POST['amount']);
    $account_id = intval($_POST['account_id']);
    $category = trim($_POST['category']);
    $date = trim($_POST['date']);

    if (!empty($description) && $amount > 0 && !empty($account_id) && !empty($date)) {
        
        // Ajusta o sinal do valor e o tipo para o DB
        $signed_amount = ($type == 'Despesa') ? -$amount : $amount;
        
        $sql = "INSERT INTO transactions (user_id, account_id, description, amount, type, category, transaction_date) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            // "isdssd" -> Integer, Integer, String, Double, String, String, Date
            $stmt->bind_param("iisdsss", $user_id, $account_id, $description, $signed_amount, $type, $category, $date);
            
            if ($stmt->execute()) {
                $trans_success = "Transação adicionada com sucesso! R$ " . number_format($signed_amount, 2, ',', '.');
                
                // ATUALIZAR SALDO DA CONTA (Lógica crucial)
                $sql_update_balance = "UPDATE accounts SET balance = balance + ? WHERE id = ?";
                if ($stmt_update = $conn->prepare($sql_update_balance)) {
                    $stmt_update->bind_param("di", $signed_amount, $account_id);
                    $stmt_update->execute();
                    $stmt_update->close();
                }
                
            } else {
                $trans_err = "Erro ao adicionar transação: " . $conn->error;
            }
            $stmt->close();
        }
    } else {
        $trans_err = "Por favor, preencha todos os campos obrigatórios.";
    }
}

// 4. BUSCAR E LISTAR TRANSAÇÕES (SELECT do MySQL)
$transacoes = [];
$sql_transacoes = "
    SELECT t.id, t.description, t.amount, t.type, t.transaction_date, a.name AS account_name
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    WHERE t.user_id = ?
    ORDER BY t.transaction_date DESC, t.created_at DESC
";

if ($stmt_transacoes = $conn->prepare($sql_transacoes)) {
    $stmt_transacoes->bind_param("i", $user_id);
    $stmt_transacoes->execute();
    $result_transacoes = $stmt_transacoes->get_result();
    while ($row = $result_transacoes->fetch_assoc()) {
        $transacoes[] = $row;
    }
    $stmt_transacoes->close();
}

$conn->close();
$primeiro_nome = explode(' ', $_SESSION["full_name"])[0];

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transações | FinanceFlow</title>
    <link rel="stylesheet" href="../public/css/dashboard-styles.css">
    <link rel="stylesheet" href="../public/css/transacoes-styles.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link href="https://unpkg.com/ionicons@5.5.2/dist/css/ionicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
</head>
<body>
    
    <div class="web-layout-container">

        <nav class="sidebar">
            <div class="sidebar-header">
                <img src="../public/assets/logofianceflow.png" alt="Logo" class="sidebar-logo">
                <h3>FinanceFlow</h3>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="menu-item"><ion-icon name="stats-chart-outline"></ion-icon> Dashboard</a></li>
                <li><a href="accounts.php" class="menu-item"><ion-icon name="wallet-outline"></ion-icon> Contas</a></li>
                <li><a href="metas.php" class="menu-item"><ion-icon name="ribbon-outline"></ion-icon> Metas</a></li>
                <li><a href="transacoes.php" class="menu-item active"><ion-icon name="swap-horizontal-outline"></ion-icon> Transações</a></li>
                <li><a href="investimentos.php" class="menu-item"><ion-icon name="cash-outline"></ion-icon> Investimentos</a></li>
                <li><a href="reports.php" class="menu-item"><ion-icon name="document-text-outline"></ion-icon> Relatórios</a></li>
                <li><a href="profile.php" class="menu-item"><ion-icon name="person-circle-outline"></ion-icon> Perfil</a></li>
                <li><a href="settings.php" class="menu-item"><ion-icon name="settings-outline"></ion-icon> Configurações</a></li>
                <li><a href="support.php" class="menu-item"><ion-icon name="help-circle-outline"></ion-icon> Suporte</a></li>
                <li><a href="bot.php" class="menu-item"><ion-icon name="robot-outline"></ion-icon> FinanceBot</a></li>
            </ul>
            
            <a href="logout.php" class="btn-logout"><ion-icon name="log-out-outline"></ion-icon> Sair</a>
        </nav>

        <main class="main-area">
            
            <header class="desktop-header">
                <h1>Transações</h1>
                <div class="user-widget">
                    <p>Olá, <?php echo htmlspecialchars($primeiro_nome); ?>!</p>
                    <ion-icon name="person-circle-outline" class="user-icon"></ion-icon>
                </div>
            </header>

            <div class="page-content">
                
                <section class="transactions-grid">
                    
                    <div class="new-transaction-card">
                        <h2>Adicionar Movimentação</h2>
                        
                        <?php if (!empty($trans_err)): ?>
                            <div class="alert error-message"><?php echo $trans_err; ?></div>
                        <?php endif; ?>
                        <?php if (!empty($trans_success)): ?>
                            <div class="alert success-message"><?php echo $trans_success; ?></div>
                        <?php endif; ?>

                        <form action="transacoes.php" method="post" class="transaction-form">
                            <input type="hidden" name="action" value="add_transaction">

                            <div class="form-row">
                                <div class="input-group">
                                    <label for="type">Tipo</label>
                                    <select id="type" name="type" required>
                                        <option value="Despesa">Despesa</option>
                                        <option value="Receita">Receita</option>
                                    </select>
                                </div>
                                
                                <div class="input-group">
                                    <label for="date">Data</label>
                                    <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                            
                            <label for="description">Descrição</label>
                            <input type="text" id="description" name="description" required placeholder="Ex: Pagamento de aluguel">

                            <div class="form-row">
                                <div class="input-group">
                                    <label for="amount">Valor (R$)</label>
                                    <input type="number" id="amount" name="amount" step="0.01" required placeholder="0.00">
                                </div>
                                <div class="input-group">
                                    <label for="account_id">Conta</label>
                                    <select id="account_id" name="account_id" required>
                                        <option value="" disabled selected>Selecione a Conta</option>
                                        <?php foreach ($contas as $conta): ?>
                                            <option value="<?php echo $conta['id']; ?>"><?php echo htmlspecialchars($conta['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <label for="category">Categoria (Opcional)</label>
                            <input type="text" id="category" name="category" placeholder="Ex: Alimentação, Moradia, Salário">

                            <button type="submit" class="btn-add-transacao">Adicionar Transação</button>
                        </form>
                    </div>

                    <div class="transaction-history-card">
                        <h2>Histórico de Transações</h2>

                        <?php if (count($transacoes) > 0): ?>
                            <ul class="transaction-history-list">
                                <?php foreach ($transacoes as $t): ?>
                                    <li class="history-item <?php echo strtolower($t['type']); ?>">
                                        <div class="item-icon"><ion-icon name="<?php echo ($t['type'] == 'Receita' ? 'arrow-up-circle-outline' : 'arrow-down-circle-outline'); ?>"></ion-icon></div>
                                        <div class="item-details">
                                            <h3><?php echo htmlspecialchars($t['description']); ?></h3>
                                            <p class="account-name">Conta: <?php echo htmlspecialchars($t['account_name']); ?></p>
                                        </div>
                                        <div class="item-info">
                                            <span class="item-amount">R$ <?php echo number_format($t['amount'], 2, ',', '.'); ?></span>
                                            <span class="item-date"><?php echo date('d/m/Y', strtotime($t['transaction_date'])); ?></span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Nenhuma transação encontrada. Comece adicionando uma movimentação.</p>
                        <?php endif; ?>
                    </div>
                </section>

            </div>
        </main>
    </div>
</body>
</html>