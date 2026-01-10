-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost
-- Thời gian đã tạo: Th1 10, 2026 lúc 10:55 AM
-- Phiên bản máy phục vụ: 10.4.28-MariaDB
-- Phiên bản PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `project_mini_web`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `gender` tinyint(1) DEFAULT NULL COMMENT '1=Nam, 2=Nữ...',
  `email` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT 'default_profile.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ac_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=not verified, 1=active, 2=blocked'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`id`, `first_name`, `last_name`, `gender`, `email`, `username`, `password`, `profile_pic`, `created_at`, `updated_at`, `ac_status`) VALUES
(1, 'Admin', 'User', 1, 'admin@instagenz.com', 'admin', 'HASH_PLACEHOLDER', 'default_profile.jpg', '2026-01-09 04:28:28', '2026-01-09 04:29:05', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `comment`, `created_at`) VALUES
(19, 18, 7, 'đẹp', '2026-01-07 10:40:22'),
(20, 18, 7, 'đẹp', '2026-01-07 10:42:38'),
(21, 19, 8, 'ok', '2026-01-07 10:50:37'),
(22, 19, 8, 'đẹp đấy ', '2026-01-07 10:50:54'),
(23, 19, 8, 'ghê', '2026-01-09 04:51:34'),
(24, 19, 8, 'ghê quá', '2026-01-09 04:58:23'),
(25, 19, 7, 'oke bro', '2026-01-09 05:33:07'),
(26, 18, 15, 'xấu', '2026-01-09 05:41:36'),
(27, 18, 8, 'oke thấy rồi', '2026-01-09 05:42:13'),
(28, 18, 8, '@minh86 hay lắm', '2026-01-09 05:42:29'),
(29, 19, 16, 'hello kitty', '2026-01-09 05:46:46'),
(30, 19, 16, 'xin 1 hộp khô gà', '2026-01-09 05:47:17'),
(31, 19, 16, '@tamphuc fl chéo hứa trả liền', '2026-01-09 05:49:19'),
(32, 22, 8, 'xấu v tr', '2026-01-09 05:53:48'),
(33, 22, 15, '@@', '2026-01-09 05:54:24'),
(34, 22, 7, 'im', '2026-01-09 05:56:55'),
(35, 22, 7, 'ghê', '2026-01-09 06:03:08'),
(36, 22, 7, 'ghê thế', '2026-01-09 06:03:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `follow_list`
--

CREATE TABLE `follow_list` (
  `id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `follow_list`
--

INSERT INTO `follow_list` (`id`, `follower_id`, `user_id`) VALUES
(23, 8, 12),
(29, 7, 8),
(30, 8, 7),
(31, 8, 15),
(32, 15, 8),
(33, 16, 15),
(34, 7, 16),
(35, 16, 7),
(36, 15, 16),
(37, 15, 7),
(38, 8, 16),
(39, 7, 17),
(40, 17, 16),
(41, 17, 7),
(42, 17, 12),
(43, 17, 8),
(44, 8, 17),
(45, 7, 15),
(46, 19, 8),
(47, 19, 12);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `likes`
--

INSERT INTO `likes` (`id`, `post_id`, `user_id`) VALUES
(95, 18, 7),
(103, 19, 8),
(104, 20, 16),
(105, 19, 15),
(106, 19, 16),
(107, 20, 15),
(108, 22, 8),
(109, 22, 15),
(110, 18, 17);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `msg` text NOT NULL,
  `read_status` int(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `messages`
--

INSERT INTO `messages` (`id`, `from_user_id`, `to_user_id`, `msg`, `read_status`, `created_at`) VALUES
(52, 8, 7, 'alo', 1, '2026-01-04 05:04:38'),
(53, 8, 7, 'alo', 1, '2026-01-04 05:04:47'),
(54, 15, 8, 'nhìn ảnh giống đồ tể vay', 1, '2026-01-09 05:42:16'),
(55, 8, 15, 'kkk ', 1, '2026-01-09 05:42:49'),
(56, 8, 15, 'đi làm ', 1, '2026-01-09 05:42:50'),
(57, 8, 15, 'm thêm ava ', 1, '2026-01-09 05:42:53'),
(58, 8, 15, 'với đăng gì đí ', 1, '2026-01-09 05:42:56'),
(59, 8, 15, 'để t test ', 1, '2026-01-09 05:43:00'),
(60, 7, 16, 'alo', 1, '2026-01-09 05:45:28'),
(61, 7, 16, 'thấy k ', 1, '2026-01-09 05:45:30'),
(62, 7, 16, 'nó có thông báo ', 1, '2026-01-09 05:45:33'),
(63, 7, 17, 'iu anh', 1, '2026-01-09 05:49:23'),
(64, 8, 15, 'trả tiền t ', 1, '2026-01-09 05:51:30'),
(65, 15, 8, 'méo', 1, '2026-01-09 05:51:45'),
(66, 8, 15, 'm hay ', 1, '2026-01-09 05:52:21'),
(67, 8, 15, 'hẹn 5h cổng trường ', 1, '2026-01-09 05:52:26'),
(68, 15, 8, 'ok đồ tể', 1, '2026-01-09 05:53:55'),
(69, 8, 15, 'cc ', 1, '2026-01-09 05:54:57'),
(70, 8, 15, 'qq', 0, '2026-01-09 06:24:10');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `read_status` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`id`, `to_user_id`, `from_user_id`, `message`, `post_id`, `read_status`, `created_at`) VALUES
(11, 8, 7, 'đã thích bài viết của bạn.', 14, 1, '2026-01-04 04:34:31'),
(12, 7, 8, 'đã thích bài viết của bạn.', 8, 1, '2026-01-04 04:49:31'),
(13, 7, 8, 'đã thích bài viết của bạn.', 8, 1, '2026-01-04 05:34:46'),
(14, 8, 7, 'đã thích bài viết của bạn.', 18, 1, '2026-01-07 10:39:22'),
(15, 8, 7, 'đã bắt đầu theo dõi bạn.', 0, 1, '2026-01-07 10:42:25'),
(16, 8, 7, 'đã bắt đầu theo dõi bạn.', 0, 1, '2026-01-07 10:42:32'),
(17, 8, 7, 'đã bình luận bài viết của bạn.', 18, 1, '2026-01-07 10:42:38'),
(18, 8, 7, 'đã bắt đầu theo dõi bạn.', 0, 1, '2026-01-07 10:42:54'),
(19, 7, 8, 'đã thích bài viết của bạn.', 19, 1, '2026-01-07 10:44:09'),
(20, 8, 7, 'đã bắt đầu theo dõi bạn.', 0, 1, '2026-01-07 10:44:27'),
(21, 7, 8, 'đã bình luận bài viết của bạn.', 19, 1, '2026-01-07 10:50:37'),
(22, 7, 8, 'đã bình luận bài viết của bạn.', 19, 1, '2026-01-07 10:50:54'),
(23, 7, 8, 'đã bình luận bài viết của bạn.', 19, 1, '2026-01-09 04:51:34'),
(24, 7, 8, 'đã thích bài viết của bạn.', 19, 1, '2026-01-09 04:51:43'),
(25, 7, 8, 'đã thích bài viết của bạn.', 19, 1, '2026-01-09 04:51:59'),
(26, 7, 8, 'đã thích bài viết của bạn.', 19, 1, '2026-01-09 04:58:09'),
(27, 7, 8, 'đã bình luận bài viết của bạn.', 19, 1, '2026-01-09 04:58:23'),
(28, 7, 8, 'đã bắt đầu theo dõi bạn.', 0, 1, '2026-01-09 04:59:01'),
(29, 7, 8, 'đã thích bài viết của bạn.', 19, 1, '2026-01-09 05:04:46'),
(30, 7, 8, 'đã thích bài viết của bạn.', 19, 1, '2026-01-09 05:04:55'),
(31, 7, 8, 'đã thích bài viết của bạn.', 19, 1, '2026-01-09 05:04:59'),
(32, 7, 8, 'đã thích bài viết của bạn.', 19, 1, '2026-01-09 05:11:13'),
(33, 8, 7, 'đã trả lời bình luận của bạn.', 19, 1, '2026-01-09 05:33:07'),
(34, 8, 15, 'đã bình luận bài viết của bạn.', 18, 1, '2026-01-09 05:41:36'),
(35, 15, 8, 'đã bắt đầu theo dõi bạn.', 0, 1, '2026-01-09 05:41:59'),
(36, 8, 15, 'đã bắt đầu theo dõi bạn.', 0, 1, '2026-01-09 05:42:00'),
(37, 15, 8, 'đã trả lời bình luận của bạn.', 18, 1, '2026-01-09 05:42:13'),
(38, 15, 8, 'đã nhắc đến bạn trong bình luận.', 18, 1, '2026-01-09 05:42:29'),
(39, 15, 16, 'đã bắt đầu theo dõi bạn.', 0, 1, '2026-01-09 05:44:58'),
(40, 16, 7, 'đã bắt đầu theo dõi bạn.', 0, 1, '2026-01-09 05:45:23'),
(41, 7, 16, 'đã bắt đầu theo dõi bạn.', 0, 1, '2026-01-09 05:45:31'),
(42, 7, 15, 'đã thích bài viết của bạn.', 19, 1, '2026-01-09 05:45:33'),
(43, 16, 15, 'đã bắt đầu theo dõi bạn.', 0, 1, '2026-01-09 05:45:48'),
(44, 7, 15, 'đã bắt đầu theo dõi bạn.', 0, 1, '2026-01-09 05:45:52'),
(45, 7, 16, 'đã thích bài viết của bạn.', 19, 1, '2026-01-09 05:46:31'),
(46, 16, 8, 'đã bắt đầu theo dõi bạn.', 0, 0, '2026-01-09 05:46:42'),
(47, 7, 16, 'đã bình luận bài viết của bạn.', 19, 1, '2026-01-09 05:46:46'),
(48, 8, 16, 'đã trả lời bình luận của bạn.', 19, 1, '2026-01-09 05:47:17'),
(49, 16, 15, 'đã thích bài viết của bạn.', 20, 1, '2026-01-09 05:47:18'),
(50, 17, 7, 'đã bắt đầu theo dõi bạn.', 0, 1, '2026-01-09 05:49:16'),
(51, 7, 16, 'đã nhắc đến bạn trong bình luận.', 19, 1, '2026-01-09 05:49:19'),
(52, 7, 16, 'đã trả lời bình luận của bạn.', 19, 1, '2026-01-09 05:49:19'),
(53, 16, 17, 'đã bắt đầu theo dõi bạn.', 0, 0, '2026-01-09 05:50:34'),
(54, 7, 17, 'đã bắt đầu theo dõi bạn.', 0, 1, '2026-01-09 05:50:34'),
(55, 12, 17, 'đã bắt đầu theo dõi bạn.', 0, 0, '2026-01-09 05:50:35'),
(56, 8, 17, 'đã bắt đầu theo dõi bạn.', 0, 1, '2026-01-09 05:50:35'),
(57, 17, 8, 'đã bắt đầu theo dõi bạn.', 0, 0, '2026-01-09 05:51:41'),
(58, 15, 8, 'đã bình luận bài viết của bạn.', 22, 1, '2026-01-09 05:53:48'),
(59, 15, 8, 'đã thích bài viết của bạn.', 22, 1, '2026-01-09 05:53:49'),
(60, 8, 15, 'đã trả lời bình luận của bạn.', 22, 1, '2026-01-09 05:54:24'),
(61, 15, 7, 'đã bắt đầu theo dõi bạn.', 0, 1, '2026-01-09 05:56:37'),
(62, 15, 7, 'đã trả lời bình luận của bạn.', 22, 1, '2026-01-09 05:56:55'),
(63, 8, 19, 'đã bắt đầu theo dõi bạn.', 0, 1, '2026-01-09 05:57:51'),
(64, 12, 19, 'đã bắt đầu theo dõi bạn.', 0, 0, '2026-01-09 05:57:54'),
(65, 15, 7, 'đã trả lời bình luận của bạn.', 22, 0, '2026-01-09 06:03:08'),
(66, 15, 7, 'đã trả lời bình luận của bạn.', 22, 1, '2026-01-09 06:03:29'),
(67, 8, 17, 'đã thích bài viết của bạn.', 18, 1, '2026-01-09 06:04:26');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_img` text NOT NULL,
  `post_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `post_img`, `post_text`, `created_at`) VALUES
(18, 8, '1767781097_IMG_6897.jpeg', '', '2026-01-07 10:18:17'),
(19, 7, '1767782620_IMG_6813.jpeg', '', '2026-01-07 10:43:40'),
(20, 16, '1767937462_IMG_2676.png', 'FPT.TràỔiHồng', '2026-01-09 05:44:22'),
(21, 8, '1767937934_IMG_6738.jpeg', 'oke\r\n', '2026-01-09 05:52:14'),
(22, 15, '1767938019_DSC01336.JPG', '', '2026-01-09 05:53:39'),
(24, 8, '1767939188_IMG_7077.png', 'lạnh dữ vậy trời\r\n', '2026-01-09 06:13:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `gender` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `ac_status` int(11) NOT NULL DEFAULT 0 COMMENT '0=not verified,1=active,2=blocked'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `gender`, `email`, `username`, `password`, `profile_pic`, `created_at`, `updated_at`, `ac_status`) VALUES
(7, 'Tam', 'Phuc', 2, 'danghuynhtam.phucc@gmail.com', 'tamphuc', '$2y$10$yx6W/gItwZh8QnoMDm3tBupAzjT4GaxjtuE7z24hwkX1WoU15nAt6', '', '2025-12-20 09:33:20', '2026-01-01 07:17:05', 1),
(8, 'Luat', 'Le', 1, 'luatle.111105@gmail.com', 'trongluat', '$2y$10$ZQAgr0.1tZqYrkH5cl4uGuZAp2HuEDOtXLS2DRSYYJBk6OrzYgtpO', '1767782225_IMG_6887.jpeg', '2025-12-26 05:22:40', '2026-01-07 10:37:05', 1),
(12, 'Gia', 'Huy', 1, 'trongluat112005@gmail.com', 'giahuy', '$2y$10$hpPMZpHnjhjR8A1gt0DqBe3w3yHtWeyHOXiRS7zaErmRZXcHmaL.O', NULL, '2026-01-04 03:02:40', '2026-01-04 03:06:46', 1),
(15, 'Tống', 'minh', 1, 'tongbinhminh806@gmail.com', 'minh86', '$2y$10$Gzb6WDzPIN0YldvryVr39e234lYrC1W2h16obp2MWuCi0tlylinkC', '1767937428_Screenshot 2025-10-29 201640.png', '2026-01-09 05:38:54', '2026-01-09 05:58:02', 2),
(16, 'Minh', 'Sang', 1, 'sangminhnguyen210@gmail.com', 'sangminhnguyen210@gmail.com', '$2y$10$nMFiFkU0XoD4WyeWO/ZqeuoTFkaPNUYH3qQiDCxt9UjGszZAgA3F6', '', '2026-01-09 05:42:35', '2026-01-09 05:54:45', 1),
(17, 'Duy', 'Thien', 1, '33doduythien@gmail.com', 'duythien', '$2y$10$AsvQFCwsS5IO60msdSQvEeaSX04zEtrd0YWrkQK06QB9j.oUFtPpa', '1767937825_Ban Đàn - Đỗ Duy Thiên.JPG', '2026-01-09 05:48:08', '2026-01-09 05:50:25', 1),
(18, 'mo', 'mo', 1, 'momo@gmail.com', 'momocute1', '$2y$10$lT7WrWvDJENCBaeShZfIF.QRH5YMrWF8VJCC7BF2vxtJeWveTV1Dm', NULL, '2026-01-09 05:54:29', '2026-01-10 08:22:27', 2),
(19, 'mo', 'mo', 1, 'dangphuongnam.bh@gmail.com', 'momocute2', '$2y$10$CVJfgTeZ29uv4i4x2JhHA.t/gdL0gnxQfgl6DVp1cWVzf0TQeOj.e', NULL, '2026-01-09 05:56:21', '2026-01-09 05:56:55', 1);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `follow_list`
--
ALTER TABLE `follow_list`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_posts_users` (`user_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT cho bảng `follow_list`
--
ALTER TABLE `follow_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT cho bảng `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT cho bảng `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT cho bảng `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
