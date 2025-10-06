<?php 
// Inclui o arquivo de configuração e inicia a sessão
require_once __DIR__ . '/../includes/config.php';

$email = ""; 
$message = $error = "";

// Lógica de processamento do formulário (mantida)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (empty($email)) {
        $error = "Por favor, insira seu e-mail.";
    } else {
        // 1. Verifica se o e-mail existe no banco de dados
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = $email;

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    // 2. SIMULAÇÃO DE ENVIO DE LINK
                    $message = "Link de recuperação enviado para o seu e-mail (Simulação).";
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

    <div class="central-form-wrapper">
        
        <div class="form-block">
            <h2>Recuperar Senha</h2>
            <p class="form-description">Insira seu e-mail para que possamos enviar as instruções de recuperação.</p>
            
            <?php if (!empty($message)): ?>
                <div class="alert success-message"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    
                <div class="input-group">
                    <label for="email">E-mail Cadastrado</label>
                    <input type="email" name="email" id="email" class="form-input" required value="<?php echo htmlspecialchars($email); ?>" placeholder="example@gmail.com">
                </div>

                <div class="form-actions">
                    <input type="submit" class="btn-submit" value="Enviar Link de Recuperação">
                </div>
            </form>

            <div class="login-link">
                <p><a href="login.php">← Voltar para o Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>