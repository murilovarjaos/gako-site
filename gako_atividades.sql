-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 25, 2026 at 01:44 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gako_atividades`
--

-- --------------------------------------------------------

--
-- Table structure for table `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel` enum('admin','editor') DEFAULT 'editor',
  `ultimo_acesso` timestamp NULL DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administradores`
--

INSERT INTO `administradores` (`id`, `nome`, `email`, `senha`, `nivel`, `ultimo_acesso`, `ativo`, `created_at`) VALUES
(1, 'Administrador', 'muri.varjao@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, 1, '2026-02-24 17:37:26'),
(4, 'Administrador', 'admin@gako.com.br', '$2y$10$LENYjnRzORP8ylGgbWHGAOZD7LykVQLplzNWStajNCaYHrhtfKMfm', 'admin', '2026-02-24 21:07:49', 1, '2026-02-24 20:00:21');

-- --------------------------------------------------------

--
-- Table structure for table `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `slug`, `descricao`, `ordem`, `ativo`, `created_at`) VALUES
(1, 'Alfabetização', 'alfabetizacao', 'Atividades de leitura, escrita e formação de palavras', 1, 1, '2026-02-24 17:37:26'),
(2, 'Matemática', 'matematica', 'Números, operações, geometria e raciocínio lógico', 2, 1, '2026-02-24 17:37:26'),
(3, 'Ciências', 'ciencias', 'Natureza, corpo humano, meio ambiente e experimentos', 3, 1, '2026-02-24 17:37:26'),
(4, 'História e Geografia', 'historia-geografia', 'Nosso mundo, sociedade e cultura', 4, 1, '2026-02-24 17:37:26'),
(5, 'Arte e Criatividade', 'arte', 'Desenhos, pinturas e atividades artísticas', 5, 1, '2026-02-24 17:37:26'),
(6, 'Organização Escolar', 'organizacao', 'Calendários, planejamentos e materiais para professores', 6, 1, '2026-02-24 17:37:26');

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `instituicao` varchar(150) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL,
  `newsletter` tinyint(1) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clientes`
--

INSERT INTO `clientes` (`id`, `nome`, `email`, `senha`, `telefone`, `instituicao`, `cidade`, `estado`, `newsletter`, `ativo`, `created_at`) VALUES
(1, 'Murilo Santos', 'muri.varjao@gmail.com', '$2y$10$H1IgcHD5KOXKbEPiKzZp9O34PtJcTtoyhPSgkjVHYsC.NDg.CH7l.', '11 967838627', NULL, NULL, NULL, 1, 1, '2026-02-24 21:25:48');

-- --------------------------------------------------------

--
-- Table structure for table `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL,
  `chave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `descricao` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `chave`, `valor`, `descricao`) VALUES
(1, 'site_nome', 'Gako - Atividades Pedagógicas', 'Nome do site'),
(2, 'site_descricao', 'Atividades prontas e imprimíveis para professoras da Educação Infantil e Fundamental I', 'Descrição SEO'),
(3, 'site_email', 'contato@gako.com.br', 'Email de contato'),
(4, 'moeda', 'BRL', 'Moeda padrão'),
(5, 'itens_por_pagina', '12', 'Produtos por página na loja');

-- --------------------------------------------------------

--
-- Table structure for table `contatos`
--

CREATE TABLE `contatos` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `assunto` varchar(100) DEFAULT NULL,
  `mensagem` text NOT NULL,
  `lido` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `downloads`
--

CREATE TABLE `downloads` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `arquivo` varchar(255) NOT NULL,
  `downloads_permitidos` int(11) DEFAULT 3,
  `downloads_realizados` int(11) DEFAULT 0,
  `ultimo_download` timestamp NULL DEFAULT NULL,
  `expira_em` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `codigo` varchar(20) NOT NULL,
  `status` enum('pendente','pago','processando','enviado','concluido','cancelado') DEFAULT 'pendente',
  `metodo_pagamento` varchar(50) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `desconto` decimal(10,2) DEFAULT 0.00,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pedido_itens`
--

CREATE TABLE `pedido_itens` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `nome_produto` varchar(200) DEFAULT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `descricao_curta` varchar(255) DEFAULT NULL,
  `descricao_completa` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `preco_promocional` decimal(10,2) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `imagem_capa` varchar(255) DEFAULT NULL,
  `arquivo_digital` varchar(255) DEFAULT NULL,
  `paginas` int(11) DEFAULT NULL,
  `serie` varchar(50) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `destaque` tinyint(1) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `visualizacoes` int(11) DEFAULT 0,
  `vendas` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `slug`, `descricao_curta`, `descricao_completa`, `preco`, `preco_promocional`, `categoria_id`, `imagem_capa`, `arquivo_digital`, `paginas`, `serie`, `tags`, `destaque`, `ativo`, `visualizacoes`, `vendas`, `created_at`, `updated_at`) VALUES
(1, 'Kit Alfabetização Completo - Vol. 1', 'kit-alfabetizacao-completo-vol-1', '20 atividades de alfabetização com letras, sílabas e palavras para Educação Infantil', '<p>Este kit completo inclui:</p><ul><li>Flashcards do alfabeto</li><li>Jogos de sílabas</li><li>Atividades de formação de palavras</li><li>Exercícios de leitura</li><li>Desenhos para colorir relacionados a cada letra</li></ul><p>Total de 45 páginas em PDF de alta qualidade, prontas para impressão.</p>', 29.90, NULL, 1, '699e1e0e5058e_1771970062.jpg', NULL, 45, 'Educação Infantil', '', 1, 1, 0, 0, '2026-02-24 17:37:52', '2026-02-24 21:54:22'),
(2, 'Matemática Divertida - 1º Ano', 'matematica-divertida-1-ano', 'Atividades de contagem, operações básicas e raciocínio lógico para o 1º ano', '<p>Conteúdo do material:</p><ul><li>Contagem de 1 a 100</li><li>Adição e subtração simples</li><li>Sequências numéricas</li><li>Formas geométricas</li><li>Problemas do cotidiano</li></ul><p>Material em PDF com 38 páginas coloridas e ilustradas.</p>', 24.90, NULL, 2, NULL, NULL, 38, '1º Ano', NULL, 1, 1, 0, 0, '2026-02-24 17:37:52', '2026-02-24 17:37:52'),
(3, 'Calendário Escolar 2025 - Planejamento', 'calendario-escolar-2025', 'Calendário completo com datas comemorativas e planejamento mensal para professores', '<p>Ideal para organização do ano letivo:</p><ul><li>Calendário mensal de janeiro a dezembro</li><li>Datas comemorativas escolares</li><li>Feriados nacionais</li><li>Planejamento mensal editável</li><li>Lista de presença</li></ul><p>Arquivo em PDF e versão editável em Word.</p>', 15.90, NULL, 6, NULL, NULL, 25, 'Todos os anos', NULL, 1, 1, 0, 0, '2026-02-24 17:37:52', '2026-02-24 17:37:52'),
(4, 'Ciências para Crianças - Natureza', 'ciencias-para-criancas-natureza', 'Experimentos e atividades sobre natureza, animais e meio ambiente', '<p>Explore o mundo natural com:</p><ul><li>Fichas de animais</li><li>Ciclo da água ilustrado</li><li>Partes da planta</li><li>Estações do ano</li><li>Experimentos simples para fazer em sala</li></ul><p>42 páginas de conteúdo rico e ilustrado.</p>', 32.90, NULL, 3, NULL, NULL, 42, 'Educação Infantil ao 2º Ano', NULL, 0, 1, 0, 0, '2026-02-24 17:37:52', '2026-02-24 17:37:52');

-- --------------------------------------------------------

--
-- Table structure for table `produto_imagens`
--

CREATE TABLE `produto_imagens` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `ordem` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recuperacao_senha`
--

CREATE TABLE `recuperacao_senha` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `usuario_tipo` enum('cliente','admin') NOT NULL,
  `token` varchar(64) NOT NULL,
  `usado` tinyint(1) DEFAULT 0,
  `expira_em` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recuperacao_senha`
--

INSERT INTO `recuperacao_senha` (`id`, `usuario_id`, `usuario_tipo`, `token`, `usado`, `expira_em`, `created_at`) VALUES
(1, 4, 'admin', '50a2fe30e39caf1ad062dce96e932e0fbbb76b62a04cef954cac5fb0eddfa0a5', 0, '2026-02-24 22:43:22', '2026-02-24 20:43:22'),
(2, 1, 'admin', 'e521dd7d52a420bca524830904b784567c234208ee5e933177db8aa8173c1a47', 0, '2026-02-24 22:43:33', '2026-02-24 20:43:33'),
(3, 4, 'admin', '3d6fd555e2835e29d3efdbff541407b7921928a6df5f2b4b4ed372feb49f2a85', 0, '2026-02-24 22:54:18', '2026-02-24 20:54:18'),
(4, 4, 'admin', 'babaf177ccd1f1077afbd06e7dd745ac72e079fd29efb0c182d7aa45f4b5d8db', 1, '2026-02-24 23:07:21', '2026-02-24 21:07:21'),
(5, 1, 'cliente', '499139695be95a50164a1166e13a87bd0e048623ba4c460a75a6a7a0cd8c7528', 1, '2026-02-25 00:20:16', '2026-02-24 22:20:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Indexes for table `contatos`
--
ALTER TABLE `contatos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `downloads`
--
ALTER TABLE `downloads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `produto_id` (`produto_id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Indexes for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Indexes for table `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Indexes for table `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indexes for table `produto_imagens`
--
ALTER TABLE `produto_imagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Indexes for table `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expira` (`expira_em`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contatos`
--
ALTER TABLE `contatos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `downloads`
--
ALTER TABLE `downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pedido_itens`
--
ALTER TABLE `pedido_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `produto_imagens`
--
ALTER TABLE `produto_imagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `downloads`
--
ALTER TABLE `downloads`
  ADD CONSTRAINT `downloads_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `downloads_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `downloads_ibfk_3` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD CONSTRAINT `pedido_itens_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_itens_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);

--
-- Constraints for table `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `produto_imagens`
--
ALTER TABLE `produto_imagens`
  ADD CONSTRAINT `produto_imagens_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
