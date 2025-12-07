<?php

// Caminho correto para chegar no config.php (2 níveis acima)
require_once __DIR__ . "/../../includes/config.php";

// Bloqueia acesso de quem não é admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

/* ================================
   1 — TOTAL DE USUÁRIOS
================================ */
$totalUsuarios = $conn->query("SELECT COUNT(*) AS total FROM users")
                      ->fetch_assoc()['total'];

/* ================================
   2 — CONTAGEM POR STATUS
================================ */
$statusQuery = $conn->query("
    SELECT status, COUNT(*) AS quantidade 
    FROM users 
    GROUP BY status
");

$status = [
    'ativo' => 0,
    'inativo' => 0,
    'bloqueado' => 0
];

while ($row = $statusQuery->fetch_assoc()) {
    $status[$row['status']] = $row['quantidade'];
}

/* ================================
   3 — CONTAGEM POR ROLE
================================ */
$roleQuery = $conn->query("
    SELECT role, COUNT(*) AS quantidade 
    FROM users 
    GROUP BY role
");

$roles = [
    'usuario' => 0,
    'admin'   => 0
];

while ($row = $roleQuery->fetch_assoc()) {
    $roles[$row['role']] = $row['quantidade'];
}

/* ================================
   4 — LOGINS HOJE
================================ */
$loginsHoje = $conn->query("
    SELECT COUNT(*) AS total 
    FROM users 
    WHERE DATE(last_login) = CURDATE()
")->fetch_assoc()['total'];

/* ================================
   5 — ÚLTIMOS USUÁRIOS CADASTRADOS
================================ */
$ultimos = $conn->query("
    SELECT full_name, email, created_at 
    FROM users 
    ORDER BY created_at DESC 
    LIMIT 5
");

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Relatórios Administrativos</title>
<style>
    :root {
        /* Cores Dark Mode Lite */
        --background-color: #55acf3ff; /* Azul escuro/Chumbo */
        --card-background: #fcfcfcff; /* Azul mais escuro que o fundo */
        --text-color: #013574ff; /* Branco suave */
        --highlight-color: #ffffffff; /* Verde-água (Ciano) */
        --secondary-color: #020507ff; /* Cinza claro */
        --success-color: #2ecc71;
        --danger-color: #e74c3c;
        --warning-color: #f1c40f;
    }

    /* Estilos Globais */
    body {
        font-family: 'Roboto', 'Helvetica Neue', Arial, sans-serif;
        background: var(--background-color);
        padding: 40px;
        color: var(--text-color);
        line-height: 1.6;
    }

    h1 {
        color: var(--highlight-color);
        font-weight: 300;
        text-align: center;
        margin-bottom: 40px;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    h2 {
        color: var(--secondary-color);
        font-size: 1.4rem;
        margin-bottom: 15px;
        font-weight: 400;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 5px;
    }

    /* Layout de Cards */
    .dashboard-grid {
    display: grid;
    /* * Minmax agora começa em 200px (menor) em vez de 280px.
     * Isso permite que mais "cards" caibam em uma linha antes de quebrar.
     */
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    
    /* * O 'gap' foi aumentado para 40px, proporcionando mais espaçamento.
     */
    gap: 40px; 
    
    margin-bottom: 40px;
    }

    .card {
        background: var(--card-background);
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
        border: 1px solid var(--highlight-color); /* Borda de destaque */
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px rgba(0, 0, 0, 0.5);
    }

    /* Estilizando os Dados de Resumo */
    .card p {
        margin: 10px 0;
        font-size: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card p strong {
        font-weight: 500;
        color: var(--secondary-color);
    }

    .card span.data-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--highlight-color); /* Destaque principal para números */
        line-height: 1;
    }

    /* Cores Específicas para os Valores (Indicadores de Status) */
    .data-active { color: var(--success-color) !important; }
    .data-blocked { color: var(--danger-color) !important; }
    .data-inactive { color: var(--warning-color) !important; }
    .data-login { color: var(--success-color) !important; }

    /* Tabela de Últimos Usuários */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        border-radius: 6px;
        overflow: hidden;
        background: var(--card-background);
    }

    table th, table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    table th {
        background-color: var(--highlight-color);
        color: var(--background-color);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.85rem;
    }

    table tr:nth-child(even) {
        background-color: #9ebad7ff; /* Linhas levemente mais escuras */
    }

    table tr:hover {
        background-color: #4a657c; /* Destaque ao passar o mouse */
    }

    /* Responsividade */
    @media (max-width: 768px) {
        body {
            padding: 20px;
        }
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
</head>
<body>

<h1>Relatórios Administrativos</h1>

<div class="card">
    <h2>Resumo Geral</h2>
    <p><strong>Total de usuários:</strong> <?= $totalUsuarios ?></p>
    <p><strong>Usuários ativos:</strong> <?= $status['ativo'] ?></p>
    <p><strong>Usuários inativos:</strong> <?= $status['inativo'] ?></p>
    <p><strong>Usuários bloqueados:</strong> <?= $status['bloqueado'] ?></p>
</div>

<div class="card">
    <h2>Tipos de Conta</h2>
    <p><strong>Admins:</strong> <?= $roles['admin'] ?></p>
    <p><strong>Usuários comuns:</strong> <?= $roles['usuario'] ?></p>
</div>

<div class="card">
    <h2>Atividade de Hoje</h2>
    <p><strong>Logins realizados hoje:</strong> <?= $loginsHoje ?></p>
</div>

<div class="card">
    <h2>Últimos usuários cadastrados</h2>

    <table>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Criado em</th>
        </tr>

        <?php while ($row = $ultimos->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= date("d/m/Y H:i", strtotime($row['created_at'])) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
