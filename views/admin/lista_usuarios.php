<?php
// Inclui a configuração do banco de dados
require_once __DIR__ . '/../../includes/config.php';

// Consulta usuários
$sql = "SELECT id, full_name, email, phone_number, date_of_birth, gender, status, role, last_login, created_at 
        FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuários</title>
    <!-- CSS simples para deixar a tabela organizada -->
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px;
            background-color: #f9f9f9;
        }
        h2 {
            text-align: center;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
            background-color: #fff;
        }
        th, td { 
            padding: 10px; 
            border: 1px solid #ccc; 
            text-align: left; 
        }
        th { 
            background-color: #f4f4f4; 
        }
        tr:nth-child(even) {
            background-color: #fefefe;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <h2>Usuários Cadastrados</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome Completo</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Data de Nascimento</th>
                <th>Gênero</th>
                <th>Status</th>
                <th>Perfil</th>
                <th>Último Login</th>
                <th>Criado em</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone_number']) ?></td>
                        <td><?= htmlspecialchars($row['date_of_birth']) ?></td>
                        <td><?= htmlspecialchars($row['gender']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td><?= $row['last_login'] ? $row['last_login'] : 'Nunca logou' ?></td>
                        <td><?= $row['created_at'] ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="10" style="text-align:center;">Nenhum usuário encontrado</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
