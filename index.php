<?php
// Inicia a sessão para checar o status de login
session_start();

// Inclui o arquivo de configuração (se houver, para conexão com o DB)
// include_once 'config/config.php'; 

// Variável para simular o status de login
// Se você já tem a lógica de SESSION, use-a.
// Exemplo: if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true)

// Por enquanto, vamos simular que o usuário NÃO está logado.
$usuario_logado = false; 

// --- Lógica de Redirecionamento ---

if ($usuario_logado) {
    // Se estiver logado, redireciona para o Dashboard
    header('Location: views/dashboard.php');
    exit;
} else {
    // Se não estiver logado, redireciona para a página de Boas-Vindas
    header('Location: views/bemvindo.php');
    exit;
}
?>