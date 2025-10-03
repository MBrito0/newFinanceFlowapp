<?php 
// Inclui o arquivo de configuração e inicia a sessão
require_once __DIR__ . '/../includes/config.php';

$email = "";
$message = $error = "";

// Lógica de processamento do formulário (onSubmit() em reset-password.page.ts)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (empty($email)) {
        $error = "Por favor, insira seu e-mail.";
    } else {
        // 1. Verifica se o e-mail existe no banco de dados (Simulando auth/user-not-found)
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = $email;

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    // 2. SIMULAÇÃO DE ENVIO DE LINK (Substitui sendPasswordResetEmail do Firebase)
                    $message = "Link de recuperação enviado para o seu e-mail (Simulação).";
                    // Redireciona após o sucesso, assim como no código original
                    header("refresh:3; url=login.php"); 
                } else {
                    $error = "Nenhum usuário encontrado com este e-mail.";
                }
            } else {
                $error = "Erro ao processar a requisição.";
            }
            $stmt->close();
        }
    }
    if ($conn) $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha | FinanceFlow</title>
    <link rel="stylesheet" href="../public/css/reset-password-styles.css">
    <link href="https://unpkg.com/ionicons@5.5.2/dist/css/ionicons.min.css" rel="stylesheet">
</head>
<body class="reset-password-body">

    <div class="split-content-wrapper">
        
        <div class="info-side-panel">
            <img src="../public/assets/logofianceflow.png" alt="Logo" class="side-panel-logo">
            <h2>Recupere Seu Acesso.</h2>
            <p>Sua segurança é nossa prioridade. Insira seu e-mail para receber as instruções e voltar a controlar suas finanças.</p>
            <img src="../public/assets/notas.jpg" alt="Gráfico financeiro" class="side-image">
        </div>
        
        <div class="form-container">
            <div class="form-block">
                <img src="../public/assets/logofianceflow.png" alt="Logo" class="logo-interno">
                <h2>Esqueceu a Senha?</h2>
                
                <?php if (!empty($message)): ?>
                    <div class="alert success-message"><?php echo $message; ?></div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="alert error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    
                    <div class="input-group">
                        <label for="email">E-mail Cadastrado</label>
                        <input type="email" name="email" id="email" class="form-input" required value="<?php echo htmlspecialchars($email); ?>" placeholder="seu.email@dominio.com">
                    </div>

                    <div class="form-actions">
                        <input type="submit" class="btn-submit" value="Enviar Link">
                    </div>
                </form>

                <div class="login-link">
                    <p>Lembrou a senha? <a href="login.php">Faça login</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>