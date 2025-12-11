-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2025 at 08:56 AM
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
-- Database: `mahatfinalproject`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_Id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(1000) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_Id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$bNNw5SSafSeMrNVH0k2XR.BAcUcrc700ZTpOH5AaivPtvlm1yzh2W', '2025-09-29 12:53:21'),
(2, 'hanna', '$2y$10$3Ufh7NW.FjQqYr6jrKzkZetboYacKDzZqxHUvL4ODMToWGAxXYNUm', '2025-10-08 06:28:30'),
(3, 'test', '$2y$10$Wrb8nf9SYoMHjs2BruLtH.1XodOpflvCy5r2wclrLPIZMXVczjgSq', '2025-10-08 06:28:36');

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`id`, `client_id`, `title`, `description`, `created_at`) VALUES
(2, 1, 'Chocolate', NULL, '2025-09-29 14:29:05'),
(3, 1, 'Gummies & Jellies', NULL, '2025-09-29 14:31:06');

-- --------------------------------------------------------

--
-- Table structure for table `classpage`
--

CREATE TABLE `classpage` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `ClassID` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `img` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `linkText` varchar(255) DEFAULT NULL,
  `priority` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classpage`
--

INSERT INTO `classpage` (`id`, `client_id`, `ClassID`, `title`, `content`, `img`, `link`, `linkText`, `priority`) VALUES
(1, 1, 2, 'Milk Chocolate', 'Smooth, creamy, and classic.', 'Milka_Alpine_Milk_Chocolate_bar_100g_with_chunks_broken_off.jpg', '', '', 1),
(2, 1, 2, 'Dark Chocolate', 'Rich and bold with a deep cocoa taste.', 'images.jpg', '', '', 2),
(3, 1, 3, 'Fruit Gummies', 'Classic fruity flavors like strawberry, orange, and lemon.', 'images (1).jpg', '', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `domin` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `domin`) VALUES
(1, 'localhost');

-- --------------------------------------------------------

--
-- Table structure for table `client_navbar`
--

CREATE TABLE `client_navbar` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `page_link` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `show_item` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE `content` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `pageID` int(11) NOT NULL,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `content` text NOT NULL,
  `img` varchar(500) NOT NULL,
  `link` varchar(200) NOT NULL,
  `linkText` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`id`, `client_id`, `pageID`, `title`, `content`, `img`, `link`, `linkText`, `created_at`, `updated_at`) VALUES
(3, 1, 1, 'Candy Shop', 'Welcome to Candy Shop, the sweetest place in town! We believe every day is better with a little sugar, and that’s why we offer a wide variety of candies, chocolates, lollipops, and gummies for all ages.\r\n\r\nOur mission is simple: to spread joy, one treat at a time. Whether you’re picking up a quick snack, searching for the perfect gift, or planning a party, Candy Shop has something sweet for every occasion.\r\n\r\nStep inside and let your taste buds explore a world of colors, flavors, and fun—because at Candy Shop, happiness is always on the menu.', 'istockphoto-1490797933-612x612.jpg', 'https://mzeget.site', 'mzeget.site', '2025-09-29 14:13:08', '2025-09-29 14:13:23'),
(4, 1, 3, '🎉 New Arrivals!', 'We’ve added exciting new flavors of gummies and chocolates this week—come taste the sweetness!\r\n\r\n🛍️ Special Offer\r\nBuy 2 lollipops and get 1 free – only this weekend!\r\n\r\n🎂 Party Packages\r\nPlanning a birthday or event? Ask us about our custom candy boxes and sweet party bags.\r\n\r\n🌟 Stay Connected\r\nFollow us on Instagram and Facebook for the latest updates, new arrivals, and exclusive deals.', 'istockphoto-522735736-612x612.jpg', '', '', '2025-09-29 14:15:53', '2025-09-29 14:15:53'),
(5, 1, 5, 'qs', '', '', '', '', '2025-09-29 14:55:47', '2025-09-29 14:55:47');

-- --------------------------------------------------------

--
-- Table structure for table `general_elements`
--

CREATE TABLE `general_elements` (
  `id` int(11) NOT NULL,
  `ClientID` int(11) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `email` varchar(255) NOT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `icon` varchar(3000) NOT NULL,
  `background_img1` varchar(255) DEFAULT NULL,
  `background_img2` varchar(255) DEFAULT NULL,
  `background_img3` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `title_page2` varchar(255) DEFAULT NULL,
  `title_page3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `general_elements`
--

INSERT INTO `general_elements` (`id`, `ClientID`, `client_name`, `phone`, `facebook`, `icon`, `background_img1`, `background_img2`, `background_img3`, `description`, `title_page2`, `title_page3`) VALUES
(1, 1, 'Candy Shop', '', 'https://www.facebook.com/TomoCandyshop/', '20250929154536_6ce54b73-7404-419f-94d4-921bde3b6e19.png', '20250929163833_istockphoto-1490797933-612x612.jpg', '20250929163833_istockphoto-1490797933-612x612.jpg', '20250924005634_Screenshot 2025-09-23 004941.png', 'At Candy Shop, we bring you the best selection of chocolates, gummies, lollipops, and classic sweets that everyone loves. Whether you’re looking for a quick treat, a gift, or something to brighten your day, Candy Shop is the place where happiness tastes sweet.', 'test2', 'test3');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `last_attempt` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mainsilderimg`
--

CREATE TABLE `mainsilderimg` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `img` varchar(255) NOT NULL,
  `page` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mainsilderimg`
--

INSERT INTO `mainsilderimg` (`id`, `client_id`, `img`, `page`, `created_at`) VALUES
(8, 1, '1759156859_68da9a7b9bd37.jpg', 2, '2025-09-29 14:40:59'),
(10, 1, '1759156876_68da9a8ce641f.jpg', 2, '2025-09-29 14:41:16'),
(11, 1, '1759156902_68da9aa6efbe3.jpg', 2, '2025-09-29 14:41:42'),
(12, 1, '1759157012_68da9b140ca95.jpg', 2, '2025-09-29 14:43:32'),
(13, 1, '1759157068_68da9b4c25338.jpg', 2, '2025-09-29 14:44:28');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `pageName` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `pageName`) VALUES
(1, 'About Us'),
(2, 'Gallery'),
(3, 'News'),
(4, 'Contact'),
(5, 'Question');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `photo` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `weight` varchar(50) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `material` varchar(100) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `client_id`, `name`, `description`, `price`, `photo`, `category`, `stock_quantity`, `is_active`, `featured`, `weight`, `dimensions`, `material`, `color`, `tags`, `created_at`, `updated_at`) VALUES
(6, 1, 'Battle Bites', 'Battle Bites', 22.60, '1759905842_images.jpg', 'soger canday', 5, 1, 1, '2', NULL, 'soger', '', 'soger canday', '2025-10-08 06:44:02', '2025-10-08 06:44:02'),
(7, 1, 'Trooper Treats', 'Trooper Treats', 5.00, '1759906013_images (1).jpg', 'soger canday', 2, 1, 1, '4', NULL, 'soger', 'white', 'Trooper Treats', '2025-10-08 06:46:53', '2025-10-08 06:46:53'),
(8, 1, 'sugar candy lollipop', 'sugar candy lollipop', 18.00, '1759906148_sugar-candy-lollipop-GJMPFA.jpg', 'candy', 58, 1, 1, '', NULL, 'sugar candy lollipop', 'colors', 'sugar candy lollipop', '2025-10-08 06:49:08', '2025-10-08 06:49:39');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `client_id`, `name`, `description`, `icon`, `is_active`, `sort_order`, `created_at`) VALUES
(9, 1, 'soger canday', 'Rock candy or sugar candy, also called rock sugar or crystal sugar, is a type of confection composed of relatively large sugar crystals.\r\n', 'none', 1, 1, '2025-10-08 06:42:07'),
(10, 1, 'candy', 'candy candy candy candy', 'candy', 1, 2, '2025-10-08 06:48:20');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `option1` varchar(255) NOT NULL,
  `option2` varchar(255) NOT NULL,
  `option3` varchar(255) NOT NULL,
  `correct_option` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `client_id`, `question_text`, `option1`, `option2`, `option3`, `correct_option`, `created_at`) VALUES
(1, 1, 'Why choose Candy Shop?', 'At Candy Shop, we bring you the sweetest treats with top-quality ingredients and a wide variety of flavors. Whether you’re craving chocolate, gummies, or classic candies, we make sure every bite brings a smile!', '', '', '', '2025-09-29 14:52:59'),
(2, 1, 'Who are we?', 'We are a team of candy lovers dedicated to spreading joy and sweetness. Our mission is simple: to make every visit to Candy Shop a fun and delicious experience for kids and adults alike.', '', '', '', '2025-09-29 14:55:14'),
(3, 1, 'What do we offer?', 'From chocolates and truffles to gummies, lollipops, and seasonal treats, Candy Shop has something for everyone. Perfect for gifts, parties, or just a sweet indulgence!', '', '', '', '2025-09-29 14:55:22');

-- --------------------------------------------------------

--
-- Table structure for table `weekly_program`
--

CREATE TABLE `weekly_program` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `location` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_Id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `classpage`
--
ALTER TABLE `classpage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ClassID` (`ClassID`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client_navbar`
--
ALTER TABLE `client_navbar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `general_elements`
--
ALTER TABLE `general_elements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `mainsilderimg`
--
ALTER TABLE `mainsilderimg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `category` (`category`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `featured` (`featured`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `weekly_program`
--
ALTER TABLE `weekly_program`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `classpage`
--
ALTER TABLE `classpage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `client_navbar`
--
ALTER TABLE `client_navbar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content`
--
ALTER TABLE `content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `general_elements`
--
ALTER TABLE `general_elements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mainsilderimg`
--
ALTER TABLE `mainsilderimg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `weekly_program`
--
ALTER TABLE `weekly_program`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classpage`
--
ALTER TABLE `classpage`
  ADD CONSTRAINT `classpage_ibfk_1` FOREIGN KEY (`ClassID`) REFERENCES `class` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `client_navbar`
--
ALTER TABLE `client_navbar`
  ADD CONSTRAINT `client_navbar_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `product_categories_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
CREATE TABLE contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    subject VARCHAR(255) DEFAULT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;