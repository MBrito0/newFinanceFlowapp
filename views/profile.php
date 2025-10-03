<?php
// Inclui a configuração para iniciar a sessão e a conexão com o banco de dados
require_once __DIR__ . '/../includes/config.php';

// 1. VERIFICAÇÃO DE SESSÃO E VARIÁVEIS INICIAIS
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$user_id = $_SESSION["id"];
$full_name = $email = $date_created = ""; // 'username' removido daqui
$profile_err = $profile_success = "";
$password_err = $password_success = "";

// 2. BUSCAR DADOS ATUAIS DO USUÁRIO
// CORREÇÃO: Removida a coluna 'username' da query, pois não existe na tabela.
$sql_select = "SELECT full_name, email, created_at FROM users WHERE id = ?";
if ($stmt_select = $conn->prepare($sql_select)) {
    $stmt_select->bind_param("i", $user_id);
    if ($stmt_select->execute()) {
        // CORREÇÃO: Removida a variável '$username' da bind_result.
        $stmt_select->bind_result($full_name, $email, $date_created);
        $stmt_select->fetch();
        $_SESSION["full_name"] = $full_name; // Atualiza sessão com nome completo, caso tenha sido mudado
    }
    $stmt_select->close();
} else {
    // Erro crítico na recuperação de dados
    $profile_err = "Erro ao buscar dados do perfil. Tente novamente mais tarde.";
}

// 3. LÓGICA PARA ATUALIZAR NOME E EMAIL
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
    $new_full_name = trim($_POST['full_name']);
    $new_email = trim($_POST['email']);
    
    // Validação mínima
    if (empty($new_full_name) || empty($new_email)) {
        $profile_err = "Nome e Email não podem ser vazios.";
    } 

    if (empty($profile_err)) {
        // Prepara a instrução UPDATE
        $sql_update = "UPDATE users SET full_name = ?, email = ? WHERE id = ?";
        if ($stmt_update = $conn->prepare($sql_update)) {
            $stmt_update->bind_param("ssi", $new_full_name, $new_email, $user_id);
            
            if ($stmt_update->execute()) {
                $profile_success = "Perfil atualizado com sucesso!";
                // Atualiza as variáveis PHP e de sessão
                $full_name = $new_full_name;
                $email = $new_email;
                $_SESSION["full_name"] = $full_name;
            } else {
                // Verificação de erro, útil para debug (ex: email duplicado)
                if ($conn->errno == 1062) { 
                    $profile_err = "O email fornecido já está em uso.";
                } else {
                    $profile_err = "Erro ao atualizar perfil. Tente novamente: " . $conn->error;
                }
            }
            $stmt_update->close();
        }
    }
}

// 4. LÓGICA PARA ATUALIZAR SENHA
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_password') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Buscar hash da senha atual
    $sql_pass = "SELECT password FROM users WHERE id = ?";
    $hash_password = "";
    if ($stmt_pass = $conn->prepare($sql_pass)) {
        $stmt_pass->bind_param("i", $user_id);
        $stmt_pass->execute();
        $stmt_pass->bind_result($hash_password);
        $stmt_pass->fetch();
        $stmt_pass->close();
    }

    if (!password_verify($current_password, $hash_password)) {
        $password_err = "A senha atual está incorreta.";
    } elseif (empty($new_password) || strlen($new_password) < 6) {
        $password_err = "A nova senha deve ter pelo menos 6 caracteres.";
    } elseif ($new_password !== $confirm_password) {
        $password_err = "A confirmação da nova senha não confere.";
    }

    if (empty($password_err)) {
        $new_hash_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql_update_pass = "UPDATE users SET password = ? WHERE id = ?";
        
        if ($stmt_update_pass = $conn->prepare($sql_update_pass)) {
            $stmt_update_pass->bind_param("si", $new_hash_password, $user_id);
            if ($stmt_update_pass->execute()) {
                $password_success = "Senha atualizada com sucesso!";
            } else {
                $password_err = "Erro ao atualizar senha: " . $conn->error;
            }
            $stmt_update_pass->close();
        }
    }
}

$conn->close();
$primeiro_nome = explode(' ', $_SESSION["full_name"])[0];

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil | FinanceFlow</title>
    <link rel="stylesheet" href="../public/css/dashboard-styles.css"> 
    <link rel="stylesheet" href="../public/css/profile-styles.css"> 
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
                <li><a href="transacoes.php" class="menu-item"><ion-icon name="swap-horizontal-outline"></ion-icon> Transações</a></li>
                <li><a href="investimentos.php" class="menu-item"><ion-icon name="cash-outline"></ion-icon> Investimentos</a></li>
                <li><a href="reports.php" class="menu-item"><ion-icon name="document-text-outline"></ion-icon> Relatórios</a></li>
                <li><a href="profile.php" class="menu-item active"><ion-icon name="person-circle-outline"></ion-icon> Perfil</a></li>
                <li><a href="settings.php" class="menu-item"><ion-icon name="settings-outline"></ion-icon> Configurações</a></li>
                <li><a href="support.php" class="menu-item"><ion-icon name="help-circle-outline"></ion-icon> Suporte</a></li>
                <li><a href="bot.php" class="menu-item"><ion-icon name="robot-outline"></ion-icon> FinanceBot</a></li>
            </ul>
            
            <a href="logout.php" class="btn-logout"><ion-icon name="log-out-outline"></ion-icon> Sair</a>
        </nav>

        <main class="main-area">
            
            <header class="desktop-header">
                <h1>Meu Perfil</h1>
                <div class="user-widget">
                    <p>Olá, <?php echo htmlspecialchars($primeiro_nome); ?>!</p>
                    <ion-icon name="person-circle-outline" class="user-icon"></ion-icon>
                </div>
            </header>

            <div class="page-content">
                
                <div class="profile-info-column">
                    <div class="profile-card profile-details-card">
                        <div class="profile-picture-section">
                            <ion-icon name="person-circle" class="profile-avatar"></ion-icon>
                            <h2><?php echo htmlspecialchars($full_name); ?></h2>
                            </div>
                        
                        <div class="detail-group">
                            <h4>Detalhes da Conta</h4>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                            <p><strong>Membro desde:</strong> <?php echo date('d/m/Y', strtotime($date_created)); ?></p>
                        </div>
                    </div>
                </div>

                <div class="profile-form-column">
                    
                    <div class="profile-card">
                        <h2>Atualizar Dados Pessoais</h2>
                        
                        <?php if (!empty($profile_err)): ?>
                            <div class="alert error-message"><?php echo $profile_err; ?></div>
                        <?php endif; ?>
                        <?php if (!empty($profile_success)): ?>
                            <div class="alert success-message"><?php echo $profile_success; ?></div>
                        <?php endif; ?>

                        <form action="profile.php" method="post" class="profile-form">
                            <input type="hidden" name="action" value="update_profile">

                            <label for="full_name">Nome Completo</label>
                            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>

                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

                            <button type="submit" class="btn-save-profile">Salvar Alterações</button>
                        </form>
                    </div>

                    <div class="profile-card">
                        <h2>Alterar Senha</h2>
                        
                        <?php if (!empty($password_err)): ?>
                            <div class="alert error-message"><?php echo $password_err; ?></div>
                        <?php endif; ?>
                        <?php if (!empty($password_success)): ?>
                            <div class="alert success-message"><?php echo $password_success; ?></div>
                        <?php endif; ?>

                        <form action="profile.php" method="post" class="profile-form">
                            <input type="hidden" name="action" value="update_password">
                            
                            <label for="current_password">Senha Atual</label>
                            <input type="password" id="current_password" name="current_password" required>

                            <label for="new_password">Nova Senha</label>
                            <input type="password" id="new_password" name="new_password" required>

                            <label for="confirm_password">Confirmar Nova Senha</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>

                            <button type="submit" class="btn-change-password">Alterar Senha</button>
                        </form>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>