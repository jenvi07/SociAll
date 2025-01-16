-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2024 at 04:28 AM
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
-- Database: `sociall`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(225) NOT NULL,
  `password` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `post_id`, `user_id`, `comment_text`, `created_at`) VALUES
(2, 2, 1, 'good morning', '2024-10-01 15:02:38'),
(3, 10, 8, 'great', '2024-10-17 12:00:05'),
(4, 9, 1, 'Good', '2024-10-21 21:00:05');

-- --------------------------------------------------------

--
-- Table structure for table `friend_requests`
--

CREATE TABLE `friend_requests` (
  `request_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friend_requests`
--

INSERT INTO `friend_requests` (`request_id`, `sender_id`, `receiver_id`, `status`, `created_at`) VALUES
(11, 4, 1, 'accepted', '2024-10-21 20:25:26');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `like_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`like_id`, `post_id`, `user_id`, `created_at`) VALUES
(1, 0, 1, '2024-09-30 16:54:35'),
(3, 8, 1, '2024-10-01 16:12:26'),
(4, 8, 6, '2024-10-03 17:26:12'),
(5, 2, 1, '2024-10-05 02:40:52'),
(6, 9, 7, '2024-10-06 17:03:49'),
(14, 10, 5, '2024-10-21 11:26:25'),
(16, 9, 1, '2024-10-21 15:38:24'),
(18, 10, 4, '2024-10-21 20:27:22'),
(20, 11, 1, '2024-10-22 00:24:53');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `read_status` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `created_at`, `read_status`) VALUES
(1, 4, 1, 'hi', '2024-09-25 20:20:39', 1),
(2, 4, 1, 'jensi', '2024-09-25 20:20:50', 1),
(4, 1, 4, 'hello', '2024-09-25 21:08:27', 1),
(5, 1, 4, 'abc', '2024-09-25 21:08:37', 1),
(7, 8, 4, 'heyyy ', '2024-10-17 17:37:12', 0),
(8, 8, 4, 'abc', '2024-10-17 17:37:22', 0),
(9, 8, 4, 'go to hell', '2024-10-17 17:51:26', 0),
(10, 1, 4, 'how are you?', '2024-10-19 18:04:10', 0),
(11, 4, 1, 'I am good', '2024-10-19 18:06:01', 0),
(12, 1, 4, 'nice', '2024-10-19 18:06:26', 0),
(13, 1, 8, 'janvi', '2024-10-19 18:29:44', 0),
(14, 1, 8, 'hello', '2024-10-19 18:29:54', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_text` enum('likes','comments','follow') NOT NULL,
  `seen` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `id` int(100) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `text_content` text DEFAULT NULL,
  `media_file` varchar(255) DEFAULT NULL,
  `media_type` enum('image','video','text') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`id`, `user_id`, `text_content`, `media_file`, `media_type`, `created_at`) VALUES
(2, 1, 'have a nice day!', 'uploads/1727351429_flowers.jpeg', 'image', '2024-09-26 11:50:29'),
(4, 0, 'Good thing takes time........', '', 'text', '2024-09-30 17:23:07'),
(5, 0, 'Good thing takes time!!', '', 'text', '2024-09-30 17:23:31'),
(6, 0, '', '', 'text', '2024-09-30 17:23:41'),
(7, 0, 'this video is for testing purpose only', 'uploads/1727717193_VN20220820_211235.mp4', 'video', '2024-09-30 17:26:33'),
(8, 1, '', 'uploads/1727798825_7c52700c760c4c5585a9a3c3d6018442.mp4', 'video', '2024-10-01 16:07:05'),
(9, 7, 'hey! I have made a project of quiz website', '', 'text', '2024-10-06 17:03:46'),
(10, 8, '♥🌟♥ ', 'uploads/1728820504_quote.jpeg', 'image', '2024-10-13 11:55:04'),
(11, 1, 'how are you?', 'uploads/1729545071_609bf8ff6359d25bf7f9258bf86786e1.jpg', 'image', '2024-10-21 21:11:11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT 'img/profile_picture/default_dp.jpeg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `theme` enum('light','dark') DEFAULT 'light',
  `visibility` enum('public','private') DEFAULT 'public',
  `bio` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `date_of_birth`, `gender`, `phone`, `profile_pic`, `created_at`, `theme`, `visibility`, `bio`) VALUES
(1, 'jenypatell', 'jency96', 'jjp@gmail.com', '123456', '2005-07-22', 'female', '9723258677', 'img/profile_picture/e32b0b7123ab0c4a3924b43d43bc4e21.jpg', '2024-09-25 11:52:25', 'dark', 'private', 'I am jensi parsana,BCA student'),
(4, 'abc', 'abc7', 'abc@gmail.com', 'abc123', '2011-11-11', 'male', '4366781298', 'img/profile_picture/default_dp.jpeg', '2024-09-25 13:34:20', 'light', 'public', NULL),
(5, 'jenvi', 'jnv', 'jenvi@gmail.com', '123456', '2005-07-22', 'female', '4506942547', 'img/profile_picture/default_dp.jpeg', '2024-09-30 16:34:34', 'dark', 'public', 'life is beautiful'),
(6, 'aelvish', 'aelvish7', 'ael20@gmail.com', 'L20', '2009-03-28', 'male', '9737510509', 'img/profile_picture/default_dp.jpeg', '2024-10-03 17:25:46', 'light', 'public', 'radhesyam'),
(7, 'happy', 'hppyds', 'hd@gmail.com', '123', '2004-07-22', 'other', '6244568746', 'img/profile_picture/default_dp.jpeg', '2024-10-06 17:01:44', 'light', 'public', NULL),
(8, 'janvi', 'jj', 'jj@gmail.com', '1234', '2004-10-22', 'male', '34948190199', 'img/profile_picture/ur_beautiful.jpeg', '2024-10-13 11:52:19', 'light', 'public', 'BTS ARMY ♥ ');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`like_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `friend_requests`
--
ALTER TABLE `friend_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
