<?php
/**
 * Arquivo de Configuração do Banco de Dados MySQL (LOCALHOST)
 */

define('DB_SERVER', 'localhost'); // Servidor local do XAMPP
define('DB_USERNAME', 'root');    // Usuário padrão do XAMPP
define('DB_PASSWORD', '');        // XAMPP geralmente não tem senha
define('DB_NAME', 'financialapp_php'); // Nome do banco local

// Tenta estabelecer a conexão
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verifica a conexão
if ($conn->connect_error) {
    die("ERRO: Não foi possível conectar ao banco de dados LOCAL. Detalhes: " . $conn->connect_error);
}

// Configura o charset para UTF-8
$conn->set_charset("utf8mb4");

// Inicia a sessão
session_start();
?>
