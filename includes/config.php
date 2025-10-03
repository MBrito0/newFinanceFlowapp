<?php
/**
 * Arquivo de Configuração do Banco de Dados MySQL
 * (Substitui as configurações do Firebase em src/environments/environment.ts)
 */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');  // <--- MUDAR AQUI
define('DB_PASSWORD', '');    // <--- MUDAR AQUI
define('DB_NAME', 'financialapp_php');

// Tenta estabelecer a conexão
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verifica a conexão
if ($conn->connect_error) {
    die("ERRO: Não foi possível conectar ao banco de dados. Verifique as credenciais no config.php. Detalhes: " . $conn->connect_error);
}

// Configura o charset para UTF-8
$conn->set_charset("utf8mb4");

// Inicia a sessão para gerenciamento de login e estado do usuário
session_start();
?>