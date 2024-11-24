-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2024 at 01:41 AM
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
-- Database: `shop_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `comment_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `product_id`, `user_id`, `comment_text`, `comment_date`) VALUES
(1, 1, 1, 'comment', '2024-11-06 10:25:55'),
(2, 1, 1, 'comment 2', '2024-11-06 10:37:11'),
(3, 2, 1, 'WAH!', '2024-11-06 10:37:45');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `order_cost` decimal(6,2) NOT NULL,
  `order_status` varchar(100) NOT NULL DEFAULT 'on_hold',
  `user_id` int(11) NOT NULL,
  `user_phone` int(11) NOT NULL,
  `user_city` varchar(255) NOT NULL,
  `user_address` varchar(255) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_cost`, `order_status`, `user_id`, `user_phone`, `user_city`, `user_address`, `order_date`) VALUES
(1, 874.00, 'on_hold', 1, 2147483647, 'Taguig City', 'Block 63 Lot 52 Upper Bicutan Taguig City', '2024-11-03 03:20:19'),
(2, 874.00, 'on_hold', 1, 2147483647, 'Taguig City', 'Block 63 Lot 52 Upper Bicutan Taguig City', '2024-11-03 03:28:14'),
(3, 874.00, 'on_hold', 1, 2147483647, 'Taguig City', 'Block 63 Lot 52 Upper Bicutan Taguig City', '2024-11-03 03:34:47'),
(4, 874.00, 'on_hold', 1, 2147483647, 'Taguig City', 'Block 63 Lot 52 Upper Bicutan Taguig City', '2024-11-03 03:34:55'),
(5, 874.00, 'on_hold', 1, 2147483647, 'Taguig City', 'Block 63 Lot 52 Upper Bicutan Taguig City', '2024-11-03 03:35:16'),
(6, 874.00, 'on_hold', 1, 2147483647, 'Taguig City', 'Block 63 Lot 52 Upper Bicutan Taguig City', '2024-11-03 03:35:25'),
(7, 874.00, 'on_hold', 1, 2147483647, 'Taguig City', 'Block 63 Lot 52 Upper Bicutan Taguig City', '2024-11-03 03:36:33'),
(8, 874.00, 'on_hold', 1, 2147483647, 'Taguig City', 'Block 63 Lot 52 Upper Bicutan Taguig City', '2024-11-03 03:36:36'),
(9, 155.00, 'on_hold', 1, 2147483647, 'Taguig City', 'Block 1 Lot 2 White House Manila', '2024-11-03 03:57:05'),
(10, 155.00, 'on_hold', 1, 2147483647, 'Taguig City', 'Block 1 Lot 2 White House Manila', '2024-11-03 03:58:26'),
(11, 155.00, 'on_hold', 1, 2147483647, 'Taguig City', 'Block 1 Lot 2 White House Manila', '2024-11-03 03:58:57'),
(12, 155.00, 'on_hold', 1, 2147483647, 'Taguig City', 'Block 1 Lot 2 White House Manila', '2024-11-03 04:01:17'),
(13, 155.00, 'on_hold', 1, 2147483647, 'Taguig City', 'Block 1 Lot 2 White House Manila', '2024-11-03 04:08:31'),
(14, 155.00, 'on_hold', 1, 2147483647, 'Taguig City', 'Block 63 Lot 52 Upper Bicutan Taguig City', '2024-11-03 04:12:58'),
(15, 155.00, 'on_hold', 1, 123123, 'Taguig City', 'Block 63 Lot 52 Upper Bicutan Taguig City', '2024-11-03 04:20:50'),
(16, 155.00, 'on_hold', 1, 123123, 'Taguig City', 'Block 63 Lot 52 Upper Bicutan Taguig City', '2024-11-03 04:21:21'),
(17, 155.00, 'on_hold', 1, 123123, 'Taguig City', 'Block 63 Lot 52 Upper Bicutan Taguig City', '2024-11-03 04:22:51'),
(18, 375.00, 'on_hold', 1, 123123, 'Taguig City', 'Block 63 Lot 52 Upper Bicutan Taguig City', '2024-11-03 04:26:27');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_price` decimal(6,2) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `product_name`, `product_image`, `product_price`, `product_quantity`, `user_id`, `order_date`) VALUES
(1, 8, '1', 'SuiSei Acrylic stand', 'suiseistand.jpg', 155.00, 5, 1, '2024-11-03 03:36:36'),
(2, 8, '5', 'Lamy Plushie', 'Plushie1.jpg', 99.00, 1, 1, '2024-11-03 03:36:36'),
(3, 9, '4', 'Fuwa Acrylic stand', 'fuwastand.jpg', 155.00, 1, 1, '2024-11-03 03:57:05'),
(4, 10, '4', 'Fuwa Acrylic stand', 'fuwastand.jpg', 155.00, 1, 1, '2024-11-03 03:58:26'),
(5, 11, '4', 'Fuwa Acrylic stand', 'fuwastand.jpg', 155.00, 1, 1, '2024-11-03 03:58:57'),
(6, 12, '4', 'Fuwa Acrylic stand', 'fuwastand.jpg', 155.00, 1, 1, '2024-11-03 04:01:17'),
(7, 13, '4', 'Fuwa Acrylic stand', 'fuwastand.jpg', 155.00, 1, 1, '2024-11-03 04:08:31'),
(8, 14, '4', 'Fuwa Acrylic stand', 'fuwastand.jpg', 155.00, 1, 1, '2024-11-03 04:12:58'),
(9, 15, '4', 'Fuwa Acrylic stand', 'fuwastand.jpg', 155.00, 1, 1, '2024-11-03 04:20:50'),
(10, 16, '4', 'Fuwa Acrylic stand', 'fuwastand.jpg', 155.00, 1, 1, '2024-11-03 04:21:21'),
(11, 17, '4', 'Fuwa Acrylic stand', 'fuwastand.jpg', 155.00, 1, 1, '2024-11-03 04:22:51'),
(12, 18, '4', 'Fuwa Acrylic stand', 'fuwastand.jpg', 155.00, 1, 1, '2024-11-03 04:26:27'),
(13, 18, '15', 'Hololive Gen 1 - Tapestry', 'tapestry3.jpg', 220.00, 1, 1, '2024-11-03 04:26:27');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_category` varchar(108) NOT NULL,
  `product_description` varchar(255) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_image2` varchar(255) DEFAULT NULL,
  `product_image3` varchar(255) DEFAULT NULL,
  `product_image4` varchar(255) DEFAULT NULL,
  `product_price` decimal(6,2) NOT NULL,
  `product_special_offer` int(2) DEFAULT NULL,
  `product_color` varchar(108) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_category`, `product_description`, `product_image`, `product_image2`, `product_image3`, `product_image4`, `product_price`, `product_special_offer`, `product_color`) VALUES
(1, 'SuiSei Acrylic stand', 'acrylic stand', 'very cute acryclic stand', 'suiseistand.jpg', 'suiseistand.jpg', 'suiseistand.jpg', 'suiseistand.jpg', 155.00, 0, 'blue'),
(2, 'Ina Acrylic stand', 'acrylic stand', 'very cute acryclic stand', 'inastand.jpg', 'inastand.jpg', 'inastand.jpg', 'inastand.jpg', 155.00, 0, 'violet'),
(3, 'Towa stand', 'acrylic stand', 'very cute acryclic stand', 'towastand.jpg', 'towastand.jpg', 'towastand.jpg', 'towastand.jpg', 155.00, 0, 'purple'),
(4, 'Fuwa Acrylic stand', 'acrylic stand', 'very cute acryclic stand', 'fuwastand.jpg', 'fuwastand.jpg', 'fuwastand.jpg', 'fuwastand.jpg', 155.00, 0, 'pink'),
(5, 'Lamy Plushie', 'plushies', 'Cute Lamy Plushie', 'Plushie1.jpg', 'Plushie1.jpg', 'Plushie1.jpg', 'Plushie1.jpg', 99.00, 0, 'Sky Blue'),
(6, 'Ao Plushie', 'plushies', 'Cute Ao Plushie', 'Plushie2.jpg', 'Plushie2.jpg', 'Plushie2.jpg', 'Plushie2.jpg', 99.00, 0, 'Grey And Blue'),
(7, 'Shiori Plushie', 'plushies', 'Cute Shiori Plushie', 'Plushie3.jpg', 'Plushie3.jpg', 'Plushie3.jpg', 'Plushie3.jpg', 99.00, 0, 'Dark Purple and White'),
(8, 'Raden Plushie', 'plushies', 'Cute Raden Plushie', 'Plushie4.jpg', 'Plushie4.jpg', 'Plushie4.jpg', 'Plushie4.jpg', 99.00, 0, 'Peach and Dark Grey'),
(9, '2B - Nier Automata', 'games', '2B FROM NIER AUTOMATA', 'game1.jpg', 'game1.jpg', 'game1.jpg', 'game1.jpg', 170.00, 0, 'Black and Grey'),
(10, 'Sepiroth - Final Fantasy', 'games', 'Sepiroth from Final Fantasy', 'game2.jpg', 'game2.jpg', 'game2.jpg', 'game2.jpg', 160.00, 0, 'Dark and Grey'),
(11, 'Sora - Kingdom Hearts', 'games', 'Sora from Kingdom Hearts', 'game3.jpg', 'game3.jpg', 'game3.jpg', 'game3.jpg', 160.00, 0, 'Blue and White'),
(12, 'Tifa - Final Fantasy', 'games', 'Tifa from Final Fantasy', 'game4.jpg', 'game4.jpg', 'game4.jpg', 'game4.jpg', 165.00, NULL, 'Red and White'),
(13, 'Suisei - Tapestry', 'tapestry', 'Suisei\'s Tapestry', 'tapestry1.jpg', 'tapestry1.jpg', 'tapestry1.jpg', 'tapestry1.jpg', 250.00, 0, 'Rouge and Blue'),
(14, 'Hololive Gen 3 - Tapestry', 'tapestry', 'Hololive Gen 3\'s Lovely Tapestry!', 'tapestry2.jpg', 'tapestry2.jpg', 'tapestry2.jpg', 'tapestry2.jpg', 210.00, 0, 'Grey, Green, Blue, Red, and Peach'),
(15, 'Hololive Gen 1 - Tapestry', 'tapestry', 'Hololive Gen1\'s Lovely Tapestry!', 'tapestry3.jpg', 'tapestry3.jpg', 'tapestry3.jpg', 'tapestry3.jpg', 220.00, 0, 'Peach, White, and Brown'),
(16, 'Hololive Meet Tapestry', 'tapestry', 'Hololive Meet Tapestry - See a variety of different Vtubers across generations!', 'tapestry4.jpg', 'tapestry4.jpg', 'tapestry4.jpg', 'tapestry4.jpg', 230.00, 0, 'Sky Blue, Violet, Lilac, White, and Red');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(108) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`) VALUES
(1, 'tapit', 'tapit@gmail.com', '123456');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `UX_Constraint` (`user_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
