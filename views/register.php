<?php
// Inclui o arquivo de configuração do banco de dados
require_once __DIR__ . '/../includes/config.php';

$registration_err = $registration_success = "";

// Processa o formulário quando submetido (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // ... (Lógica PHP de validação e inserção no MySQL permanece inalterada) ...
    // ... (Mantive o código de backend original para evitar quebras) ...
    
    if (empty(trim($_POST["fullName"])) || empty(trim($_POST["email"])) || empty(trim($_POST["password"])) || empty(trim($_POST["confirmPassword"])) || empty(trim($_POST["dateOfBirth"])) || empty(trim($_POST["gender"]))) {
        $registration_err = "Por favor, preencha todos os campos obrigatórios.";
    } elseif ($_POST["password"] !== $_POST["confirmPassword"]) {
        $registration_err = "As senhas não coincidem!";
    } elseif (!isset($_POST["termsAccepted"])) {
        $registration_err = "Você precisa aceitar os termos e condições.";
    } elseif (!preg_match('/^[a-zA-Z0-9]{1,6}$/', $_POST["password"])) {
        $registration_err = "A senha deve ter até 6 caracteres e conter apenas letras e números.";
    } else {
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = trim($_POST["email"]);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $registration_err = "Este e-mail já está em uso.";
                } else {
                    $fullName = trim($_POST["fullName"]);
                    $email = trim($_POST["email"]);
                    $dateOfBirth = trim($_POST["dateOfBirth"]);
                    $gender = trim($_POST["gender"]);
                    $password = trim($_POST["password"]);

                    $sql_insert = "INSERT INTO users (full_name, date_of_birth, gender, email, password_hash) VALUES (?, ?, ?, ?, ?)";
                    if ($stmt_insert = $conn->prepare($sql_insert)) {
                        $password_hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt_insert->bind_param("sssss", $fullName, $dateOfBirth, $gender, $email, $password_hash);
                        if ($stmt_insert->execute()) {
                            $registration_success = "Cadastro realizado com sucesso! Redirecionando para o login...";
                            header("refresh:3; url=login.php?success=registered"); 
                        } else {
                            $registration_err = "Erro ao criar a conta. Tente novamente mais tarde.";
                        }
                        $stmt_insert->close();
                    }
                }
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - FinanceFlow</title>
    <link rel="stylesheet" href="../public/css/register-styles.css">
</head>
<body class="register-body">
    <header class="header">
        <h1>Cadastro</h1>
    </header> 

    <div class="split-content-wrapper">
        
        <div class="form-container">
            <h2>Crie sua Conta</h2>
            
            <?php if (!empty($registration_err)): ?>
                <div class="alert error-message"><?php echo $registration_err; ?></div>
            <?php endif; ?>
            <?php if (!empty($registration_success)): ?>
                <div class="alert success-message"><?php echo $registration_success; ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="registration-form">
                
                <div class="form-grid">
                    
                    <div class="form-group span-2">
                        <label for="fullName">Nome Completo</label>
                        <input type="text" id="fullName" name="fullName" placeholder="Digite seu nome completo" required>
                    </div>

                    <div class="form-group">
                        <label for="dateOfBirth">Data de Nascimento</label>
                        <input type="date" id="dateOfBirth" name="dateOfBirth" required>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gênero</label>
                        <select id="gender" name="gender" required>
                            <option value="" disabled selected>Selecione</option>
                            <option value="feminino">Feminino</option>
                            <option value="masculino">Masculino</option>
                            <option value="outros">Outros</option>
                        </select>
                    </div>

                    <div class="form-group span-2">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Senha (Até 6 caracteres)</label>
                        <input type="password" id="password" name="password" placeholder="Digite sua senha" pattern="^[a-zA-Z0-9]{1,6}$" title="A senha deve ter até 6 caracteres e conter apenas letras e números." required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">Confirmar Senha</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirme sua senha" required>
                    </div>
                </div> <div class="checkbox-container span-2">
                    <input type="checkbox" id="termsAccepted" name="termsAccepted" value="1" required>
                    <label for="termsAccepted">Eu concordo com os termos e condições</label>
                </div>

                <button type="submit" class="btn-register">Cadastrar-se</button>
                <p class="login-link">Já tem uma conta? <a href="login.php">Faça login</a></p>
            </form>
        </div> <div class="info-side-panel">
            <img src="../public/assets/logofianceflow.png" alt="Logo" class="side-panel-logo">
            <h2>Por Que se Cadastrar Agora?</h2>
            
            <div class="info-list">
                <div class="info-item">
                    <ion-icon name="trending-up-outline"></ion-icon>
                    <p>Comece a traçar suas metas de **curto e longo prazo** com nosso sistema de projeção.</p>
                </div>
                <div class="info-item">
                    <ion-icon name="shield-outline"></ion-icon>
                    <p>Sua segurança é nossa prioridade. Utilizamos criptografia de ponta para proteger seus dados.</p>
                </div>
                <div class="info-item">
                    <ion-icon name="calculator-outline"></ion-icon>
                    <p>Tenha uma visão de **100%** de todos os seus saldos e dívidas em uma única dashboard intuitiva.</p>
                </div>
            </div>
        </div>
    </div> </body>
</html>