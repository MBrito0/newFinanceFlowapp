<?php
require_once __DIR__ . '/../../includes/verifica_admin.php';
require_once __DIR__ . '/../../includes/config.php';

// Valores dos filtros
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$role   = $_GET['role'] ?? '';

// Query base
$sql = "SELECT id, full_name, email, phone_number, date_of_birth, gender, status, role, last_login, created_at 
        FROM users
        WHERE 1=1";

// Aplicar filtros
if (!empty($search)) {
    $searchEsc = $conn->real_escape_string($search);
    $sql .= " AND (full_name LIKE '%$searchEsc%' OR email LIKE '%$searchEsc%')";
}

if (!empty($status)) {
    $statusEsc = $conn->real_escape_string($status);
    $sql .= " AND status = '$statusEsc'";
}

if (!empty($role)) {
    $roleEsc = $conn->real_escape_string($role);
    $sql .= " AND role = '$roleEsc'";
}

$sql .= " ORDER BY id DESC";

$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Usuários</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f4f7;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        /* Caixa dos filtros */
        .filter-box {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .filter-box input, 
        .filter-box select {
            padding: 8px 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .filter-box button {
            background-color: #0066ff;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
        }

        .filter-box button:hover {
            background-color: #0049b8;
        }

        /* Tabela */
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        th {
            background-color: #eef1f5;
            padding: 12px;
            font-size: 14px;
            text-align: left;
            color: #333;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        tr:hover {
            background-color: #f7f9fc;
        }

        .center {
            text-align: center;
            padding: 20px;
        }

        /* Ações */
        .btn-edit {
            color: blue;
            text-decoration: none;
            margin-right: 10px;
        }

        .btn-delete {
            color: red;
            text-decoration: none;
            font-weight: bold;
        }

        @media(max-width: 800px) {
            th, td {
                font-size: 12px;
                padding: 6px;
            }

            .filter-box {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<h2>Gerenciamento de Usuários</h2>

<!-- FILTROS -->
<form method="GET">
    <div class="filter-box">
        <input type="text" name="search" placeholder="Buscar nome ou email..."
               value="<?= htmlspecialchars($search) ?>">

        <select name="status">
            <option value="">Status</option>
            <option value="ativo"     <?= $status=="ativo" ? "selected" : "" ?>>Ativo</option>
            <option value="inativo"   <?= $status=="inativo" ? "selected" : "" ?>>Inativo</option>
            <option value="bloqueado" <?= $status=="bloqueado" ? "selected" : "" ?>>Bloqueado</option>
        </select>

        <select name="role">
            <option value="">Perfil</option>
            <option value="usuario" <?= $role=="usuario" ? "selected" : "" ?>>Usuário</option>
            <option value="admin"   <?= $role=="admin" ? "selected" : "" ?>>Admin</option>
        </select>

        <button type="submit">Filtrar</button>
    </div>
</form>

<!-- TABELA -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome Completo</th>
            <th>Email</th>
            <th>Telefone</th>
            <th>Nascimento</th>
            <th>Gênero</th>
            <th>Status</th>
            <th>Perfil</th>
            <th>Último Login</th>
            <th>Criado em</th>
            <th>Ações</th>
        </tr>
    </thead>

    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= $row['phone_number'] ?: "-" ?></td>
                <td><?= $row['date_of_birth'] ? date("d/m/Y", strtotime($row['date_of_birth'])) : "-" ?></td>
                <td><?= $row['gender'] ?: "-" ?></td>
                <td><?= $row['status'] ?></td>
                <td><?= $row['role'] ?></td>
                <td><?= $row['last_login'] ?: "Nunca logou" ?></td>
                <td><?= date("d/m/Y H:i", strtotime($row['created_at'])) ?></td>

                <td>
                    <a class="btn-delete"
                       href="delete_usuario.php?id=<?= $row['id'] ?>"
                       onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                        Excluir
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="11" class="center">Nenhum usuário encontrado.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
