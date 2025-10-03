<?php
// Inclui a configuração para iniciar a sessão e a conexão com o banco de dados
require_once __DIR__ . '/../includes/config.php';

// 1. VERIFICAÇÃO DE SESSÃO
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$primeiro_nome = explode(' ', $_SESSION["full_name"])[0];
$mensagem_sucesso = "";

// 2. LÓGICA DE SIMULAÇÃO: Adicionar Nova Conta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['adicionar_conta'])) {
    $nome_conta = trim($_POST['nome_conta']);
    $saldo_inicial = trim($_POST['saldo_inicial']);
    $tipo_conta = trim($_POST['tipo_conta']);

    // Validação básica (em um sistema real, haveria validação de dados e inserção no banco)
    if (!empty($nome_conta) && is_numeric($saldo_inicial)) {
        // Simulação de sucesso
        $mensagem_sucesso = "Conta '{$nome_conta}' (R$ " . number_format($saldo_inicial, 2, ',', '.') . ") adicionada com sucesso! (Simulação)";
    } else {
        $mensagem_sucesso = "Erro: Por favor, preencha todos os campos corretamente.";
    }
}


// 3. DADOS SIMULADOS PARA AS CONTAS
// Baseado na estrutura do seu app original, simulamos as contas do usuário.
$contas = [
    [
        'id' => 1,
        'nome' => 'Conta Corrente Principal',
        'tipo' => 'Banco Digital',
        'saldo' => 5800.50,
        'icon' => 'card-outline' 
    ],
    [
        'id' => 2,
        'nome' => 'Cartão de Crédito Nubank',
        'tipo' => 'Cartão de Crédito',
        'saldo' => -1200.00, // Saldo negativo para dívida/limite usado
        'icon' => 'credit-card-outline'
    ],
    [
        'id' => 3,
        'nome' => 'Carteira Pessoal (Dinheiro)',
        'tipo' => 'Dinheiro',
        'saldo' => 450.00,
        'icon' => 'wallet-outline'
    ],
    [
        'id' => 4,
        'nome' => 'Investimentos XP',
        'tipo' => 'Corretora',
        'saldo' => 15000.00,
        'icon' => 'trending-up-outline'
    ]
];

// Cálculo do Saldo Total
$saldo_total = array_sum(array_column($contas, 'saldo'));
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contas | FinanceFlow</title>
    <link rel="stylesheet" href="../public/css/dashboard-styles.css"> 
    <link rel="stylesheet" href="../public/css/accounts-styles.css"> 
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
                <li><a href="accounts.php" class="menu-item active"><ion-icon name="wallet-outline"></ion-icon> Contas</a></li>
                <li><a href="metas.php" class="menu-item"><ion-icon name="ribbon-outline"></ion-icon> Metas</a></li>
                <li><a href="transacoes.php" class="menu-item"><ion-icon name="swap-horizontal-outline"></ion-icon> Transações</a></li>
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
                <h1>Minhas Contas</h1>
                <div class="user-widget">
                    <p>Olá, <?php echo htmlspecialchars($primeiro_nome); ?>!</p>
                    <ion-icon name="person-circle-outline" class="user-icon"></ion-icon>
                </div>
            </header>

            <div class="page-content">
                
                <?php if (!empty($mensagem_sucesso)): ?>
                    <div class="alert success-message"><?php echo $mensagem_sucesso; ?></div>
                <?php endif; ?>

                <section class="total-balance-card">
                    <h2>Saldo Total Consolidado</h2>
                    <p class="balance-value <?php echo $saldo_total >= 0 ? 'positive' : 'negative'; ?>">
                        R$ <?php echo number_format($saldo_total, 2, ',', '.'); ?>
                    </p>
                </section>

                <section class="add-account-section">
                    <h2>Adicionar Nova Conta</h2>
                    <form action="accounts.php" method="post" class="add-account-form">
                        <input type="hidden" name="adicionar_conta" value="1">

                        <div class="form-group">
                            <label for="nome_conta">Nome da Conta (Ex: Banco do Brasil, Poupança, Dinheiro)</label>
                            <input type="text" id="nome_conta" name="nome_conta" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="saldo_inicial">Saldo Inicial (R$)</label>
                            <input type="number" id="saldo_inicial" name="saldo_inicial" step="0.01" value="0.00" required>
                        </div>

                        <div class="form-group">
                            <label for="tipo_conta">Tipo de Conta</label>
                            <select id="tipo_conta" name="tipo_conta" required>
                                <option value="Banco Digital">Banco Digital</option>
                                <option value="Banco Tradicional">Banco Tradicional</option>
                                <option value="Cartão de Crédito">Cartão de Crédito</option>
                                <option value="Dinheiro">Dinheiro</option>
                                <option value="Investimentos">Investimentos</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-add-account">
                            <ion-icon name="add-circle-outline"></ion-icon> Adicionar Conta
                        </button>
                    </form>
                </section>

                <section class="account-list">
                    <h2>Contas Registradas</h2>
                    <?php if (empty($contas)): ?>
                        <p class="no-data-message">Nenhuma conta cadastrada. Adicione uma para começar!</p>
                    <?php else: ?>
                        <div class="accounts-grid">
                            <?php foreach ($contas as $conta): 
                                $is_negative = $conta['saldo'] < 0;
                            ?>
                                <div class="account-card">
                                    <div class="account-header">
                                        <ion-icon name="<?php echo htmlspecialchars($conta['icon']); ?>"></ion-icon>
                                        <h3><?php echo htmlspecialchars($conta['nome']); ?></h3>
                                    </div>
                                    <p class="account-type"><?php echo htmlspecialchars($conta['tipo']); ?></p>
                                    <p class="account-balance">
                                        Saldo Atual: 
                                        <span class="<?php echo $is_negative ? 'negative' : 'positive'; ?>">
                                            R$ <?php echo number_format($conta['saldo'], 2, ',', '.'); ?>
                                        </span>
                                    </p>
                                    <div class="account-actions">
                                        <button class="btn-action edit"><ion-icon name="create-outline"></ion-icon> Editar</button>
                                        <button class="btn-action delete"><ion-icon name="trash-outline"></ion-icon> Excluir</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

            </div>
        </main>
    </div>
    
</body>
</html>