-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17/11/2025 às 23:57
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `financialapp_php`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `type` varchar(50) DEFAULT 'Outro',
  `currency` varchar(10) DEFAULT 'BRL',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `accounts`
--

INSERT INTO `accounts` (`id`, `user_id`, `name`, `balance`, `type`, `currency`, `created_at`, `updated_at`) VALUES
(5, 6, 'Santander', 5000.00, 'Banco Digital', 'BRL', '2025-10-02 17:52:49', '2025-10-02 17:52:49'),
(6, 6, 'Bradesco', 2250.00, 'Banco Digital', 'BRL', '2025-10-02 17:54:00', '2025-10-02 18:01:50'),
(7, 6, 'Nubank', -820.78, 'Banco Digital', 'BRL', '2025-10-02 17:54:33', '2025-10-02 18:53:53'),
(8, 6, 'XP Investimentos', 500.00, 'Investimentos', 'BRL', '2025-10-02 17:58:22', '2025-10-02 17:58:22'),
(11, 6, 'r2m bank', 50.00, 'Banco Digital', 'BRL', '2025-10-02 22:10:35', '2025-10-02 22:11:16'),
(12, 6, 'XP Investimentos', 500.00, 'Investimentos', 'BRL', '2025-10-02 22:23:31', '2025-10-02 22:23:44'),
(13, 13, 'Bradesco', 4150.00, 'Banco Tradicional', 'BRL', '2025-10-03 17:42:00', '2025-10-03 17:43:13'),
(14, 13, 'Santander', 4000.00, 'Banco Digital', 'BRL', '2025-10-03 17:42:30', '2025-10-03 17:42:30');

-- --------------------------------------------------------

--
-- Estrutura para tabela `goals`
--

CREATE TABLE `goals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `target_value` decimal(10,2) NOT NULL,
  `deadline_type` enum('diario','semanal','mensal','anual') NOT NULL,
  `current_progress` decimal(10,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `goals`
--

INSERT INTO `goals` (`id`, `user_id`, `name`, `target_value`, `deadline_type`, `current_progress`, `created_at`) VALUES
(12, 6, 'Consertar parede do quarto', 220.00, 'semanal', 0.00, '2025-10-01 13:22:48'),
(14, 6, 'Nova casa', 350000.00, 'anual', 0.00, '2025-10-02 19:12:04'),
(15, 13, 'Comprar um carro', 5000.00, 'semanal', 0.00, '2025-10-03 14:45:07');

-- --------------------------------------------------------

--
-- Estrutura para tabela `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('Receita','Despesa') NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `account_id`, `description`, `amount`, `type`, `category`, `transaction_date`, `created_at`) VALUES
(1, 6, 6, 'Reserva de emergência', 500.00, 'Receita', 'Investimentos', '2025-10-02', '2025-10-02 15:01:50'),
(2, 6, 7, 'Conta de luz', -560.00, 'Despesa', 'Moradia', '2025-09-26', '2025-10-02 15:53:53'),
(3, 6, 11, 'Conta de luz', -50.00, 'Despesa', 'Moradia', '2025-10-03', '2025-10-02 19:11:16'),
(4, 13, 13, 'Conta de luz', -850.00, 'Despesa', 'Moradia', '2025-10-03', '2025-10-03 14:43:13');

-- --------------------------------------------------------

--
-- Estrutura para tabela `two_factor_codes`
--

CREATE TABLE `two_factor_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code_hash` varchar(255) NOT NULL,
  `contact_method` enum('email','phone') NOT NULL DEFAULT 'email',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `attempts` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('feminino','masculino','outros') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `phone_number` varchar(20) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('ativo','inativo','bloqueado') DEFAULT 'ativo',
  `role` enum('usuario','admin') DEFAULT 'usuario',
  `password_reset_token` varchar(255) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL,
  `is_2fa_enabled` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `date_of_birth`, `gender`, `created_at`, `phone_number`, `last_login`, `status`, `role`, `password_reset_token`, `token_expires`, `is_2fa_enabled`) VALUES
(6, 'Lorrayne Ramos Da Silva', 'lorrayne.ramosdasilva@gmail.com', '$2y$10$RGJAEVn7gBAcWPbizPwG3uXl20MwaH4EYORKhajf.ViykW.zkr102', '2005-02-17', 'feminino', '2025-10-01 13:19:10', '21993645772', NULL, 'ativo', 'usuario', NULL, NULL, 0),
(7, 'Lorrayne Ramos Da Silva', 'lramosoffice0@gmail.com', '$2y$10$4pUcZ.ycx0sO4INtmNSo6OOtoCzxMLtxvxHSMrkgQsHgpmD2J9i/y', '2005-02-17', 'feminino', '2025-10-01 14:58:14', NULL, NULL, 'ativo', 'usuario', NULL, NULL, 0),
(8, 'Emanuella Brito', 'manubrito322@gmail.com', '$2y$10$d2maa0G2EUET2EecR.2JA.EzRVRtMoU12/Kyf0fPRlcWCdEoxCFsW', '1999-03-19', 'feminino', '2025-10-01 18:49:49', NULL, NULL, 'ativo', 'usuario', NULL, NULL, 0),
(9, 'Papagaio Web', 'papagaio@gmail.com', '$2y$10$XbD5gVtgzj3aFIGhq.o0CuOaCEaekZswP2m.p.RPkWKkJcvvWmX/W', '1590-02-13', 'masculino', '2025-10-01 19:25:55', NULL, NULL, 'ativo', 'usuario', NULL, NULL, 0),
(11, 'RONDINELLI DA SILVA', 'rondinellisilva433@gmail.com', '$2y$10$mre1ySWUyYyIL1UEahg7FuI.5F.ZPk7da18SpS52zhoe9gQM.mRmS', '1973-09-14', 'masculino', '2025-10-02 15:28:56', '(21) 98022-6535', NULL, 'ativo', 'usuario', NULL, NULL, 0),
(12, 'Rafael Monteiro', 'profrafaelribeiro@gmail.com', '$2y$10$GC/UTlQsoUpYfETi2P9myuOYagCnBMGCkrdyZ/aw8t99tN/ak65B2', '1977-01-06', 'masculino', '2025-10-02 18:57:48', '(21) 99540-6867', NULL, 'ativo', 'usuario', NULL, NULL, 0),
(13, 'Rondinelli da Silva', 'ronnyy123@hotmail.com', '$2y$10$7.htQZCC6lj57oT7T3VFM.P5nENyy9RERWo8A7ayO2Z/ZX8HYMsca', '1973-09-14', 'masculino', '2025-10-03 14:38:02', '(21) 98022-6535', NULL, 'ativo', 'usuario', NULL, NULL, 0),
(15, 'Romario Gonzaga', 'romariogonzaga2018@gmail.com', '$2y$10$zBcEwvz1WYC3hvUj/X2vYORKiwcGHHJgftoFzShX4RrOMmX3fUnnq', '2004-04-14', 'masculino', '2025-10-22 20:13:36', '(21) 97921-1842', NULL, 'ativo', 'usuario', NULL, NULL, 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Índices de tabela `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `goals`
--
ALTER TABLE `goals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `goals`
--
ALTER TABLE `goals`
  ADD CONSTRAINT `goals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  ADD CONSTRAINT `two_factor_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
