-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 13, 2025 lúc 07:01 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `ci_ecommerce`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `blog_comments`
--

CREATE TABLE `blog_comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `author_name` varchar(100) NOT NULL,
  `author_email` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `blog_comments`
--

INSERT INTO `blog_comments` (`id`, `post_id`, `customer_id`, `parent_id`, `author_name`, `author_email`, `comment`, `is_approved`, `created_at`, `updated_at`) VALUES
(1, 26, 10, NULL, 'fa fads', 'nicktescake@gmail.com', 'âsasas', 1, '2025-09-21 16:49:19', '2025-09-21 16:49:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `featured_image` varchar(255) NOT NULL,
  `image_alt` varchar(255) DEFAULT NULL,
  `author_id` int(10) UNSIGNED DEFAULT NULL,
  `author_name` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `reading_time` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `title`, `slug`, `excerpt`, `content`, `featured_image`, `image_alt`, `author_id`, `author_name`, `category`, `status`, `published_at`, `meta_title`, `meta_description`, `view_count`, `is_featured`, `reading_time`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'phim hoạt hình', 'phim-hot-hinh', 'Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch ', 'Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch ', 'uploads/blog/featured/1757409540_828701574c30ce9c2ad6.jpg', 'Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch ', NULL, 'fa fads', 'khách hàng', 'published', '2025-09-08 19:18:00', 'Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch ', 'Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch Tập 2] Ta Từ Tận Thế Bắt Đầu Vô Địch ', 14, 0, 1, '2025-09-08 23:25:42', '2025-09-21 22:31:47', NULL),
(26, 'Ghế Gaming Ergonomic XYZ – Lựa chọn hoàn hảo cho game thủ và dân văn phòng', 'gh-gaming-ergonomic-xyz-la-chn-hoan-ho-cho-game-th-va-dan-van-phong', 'Ghế Gaming Ergonomic XYZ được thiết kế tối ưu cho sự thoải mái, hỗ trợ cột sống và mang lại trải nghiệm ngồi lâu không mệt mỏi cho cả game thủ lẫn nhân viên văn phòng', '<p>Ghế Gaming Ergonomic XYZ là dòng sản phẩm cao cấp, được thiết kế để mang lại sự thoải mái và bảo vệ sức khỏe người dùng. Với khung thép chắc chắn, đệm mút dày và da PU cao cấp, sản phẩm vừa bền bỉ vừa sang trọng.</p><h3>Đặc điểm nổi bật:</h3><ul><li>Tựa lưng ngả tới 165 độ</li><li>Gối cổ và đệm lưng hỗ trợ chuẩn ergonomic</li><li>Bánh xe xoay 360° bọc cao su chống trầy sàn</li><li>Tải trọng tối đa 150kg</li></ul><p>Ghế phù hợp cho game thủ chuyên nghiệp, streamer và cả nhân viên văn phòng cần ngồi lâu hàng giờ.</p><h3>Kết luận:</h3><p>Nếu bạn đang tìm một chiếc ghế vừa thoải mái, vừa đẹp, lại tốt cho sức khỏe thì Ergonomic XYZ chính là lựa chọn không thể bỏ qua.</p>', 'uploads/blog/featured/1758446718_7548ce1ce86349b42cf2.jpg', 'ghế gaming', NULL, 'admin', 'gaming', 'published', '2025-09-21 02:30:00', 'Ghế Gaming Ergonomic XYZ – Lựa chọn hoàn hảo cho game thủ và dân văn phòng', 'Ghế Gaming Ergonomic XYZ được thiết kế tối ưu cho sự thoải mái, hỗ trợ cột sống và mang lại trải nghiệm ngồi lâu không mệt mỏi cho cả game thủ lẫn nhân viên văn phòng', 0, 0, 1, '2025-09-21 16:25:18', '2025-10-03 21:00:39', NULL),
(27, 'Giày Sneaker Nam Aranoz – Thời Trang, Năng Động và Thoải Mái', 'giay-sneaker-nam-aranoz-thi-trang-nang-dng-va-thoi-mai', 'Giày Sneaker Nam Aranoz với thiết kế trẻ trung, chất liệu cao cấp và đế êm ái, mang lại sự thoải mái tối đa trong mọi hoạt động hàng ngày.', 'Trong thế giới thời trang nam giới, sneaker luôn giữ vị trí quan trọng bởi tính linh hoạt, dễ phối đồ và sự thoải mái vượt trội. Giày Sneaker Nam Aranoz được thiết kế dành riêng cho những chàng trai yêu thích sự năng động và cá tính.\r\n\r\n1. Thiết kế hiện đại, trẻ trung\r\nMẫu sneaker được lấy cảm hứng từ phong cách đường phố, dễ dàng kết hợp với quần jean, kaki hay short. Đường may tinh tế, form giày ôm vừa vặn, tạo nên vẻ ngoài khỏe khoắn.\r\n\r\n2. Chất liệu cao cấp\r\nThân giày làm từ vải canvas thoáng khí kết hợp da tổng hợp, giúp giữ form lâu và hạn chế bám bẩn. Lót trong mềm mại, hút ẩm tốt, mang lại cảm giác dễ chịu suốt cả ngày.\r\n\r\n3. Đế giày êm ái, chống trượt\r\nĐế cao su cao cấp, đàn hồi tốt và có khả năng chống trơn trượt. Dù đi bộ, chạy nhảy hay vận động nhiều, đôi giày vẫn đảm bảo sự chắc chắn và an toàn.\r\n\r\n4. Ứng dụng đa năng\r\nPhù hợp trong nhiều hoàn cảnh: đi học, đi làm, dạo phố hay du lịch. Đây là item mà mọi chàng trai nên có trong tủ đồ.\r\n\r\n👉 Nếu bạn đang tìm kiếm một đôi sneaker vừa đẹp vừa bền, Sneaker Nam Aranoz chính là lựa chọn hoàn hảo!', 'uploads/blog/featured/1759501581_e81650a98bc0fea3bc8f.jpg', 'Giày Sneaker Nam Aranoz', NULL, 'Admin', 'Giày sneaker', 'published', '2025-10-03 21:45:00', 'Giày Sneaker Nam Aranoz – Thời Trang, Năng Động và Thoải Mái', 'Khám phá Giày Sneaker Nam Aranoz – thiết kế hiện đại, chất liệu cao cấp, đế cao su chống trượt. Lựa chọn hoàn hảo cho phong cách năng động và tự tin.', 0, 0, 2, '2025-10-03 21:26:21', '2025-10-03 21:49:58', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

CREATE TABLE `brands` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

INSERT INTO `brands` (`id`, `name`, `slug`, `description`, `logo_url`, `website`, `country`, `is_active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Samsung', 'samsung', NULL, 'uploads/brand/1757018856_3228d80dc9a357ad11fb.jpg', 'vinfat.com', 'Hàn Quốc', 1, 1, '2025-08-26 22:38:33', '2025-09-05 03:47:36', NULL),
(2, 'Sony', 'sony', NULL, 'uploads/brand/1757018862_e659bc5faac29c92febe.jpg', '', 'Nhật Bản', 1, 2, '2025-08-26 22:38:33', '2025-09-05 22:50:44', NULL),
(3, 'vilova-sia', 'vilova-sia', NULL, 'uploads/brand/1757089549_62a1c22979ef34ddf1dc.webp', 'vinfat.comx', 'Hà Lan', 1, 3, '2025-08-26 22:38:33', '2025-09-27 01:57:30', NULL),
(4, 'vinfat', 'vinfat', NULL, 'uploads/brand/1757018877_9d68aa40c01a6714a2cd.jpg', 'vinfat.com', 'VietNam', 1, 4, '2025-09-05 02:56:16', '2025-09-05 22:21:52', '2025-09-05 22:21:52'),
(5, 'vutrunganh', 'vutrunganh', NULL, 'uploads/brand/1757018848_99490d8a9fb1d6ea4fe8.jpg', 'vinfat.com', 'VietNam', 1, 0, '2025-09-05 03:40:30', '2025-09-27 01:57:04', '2025-09-27 01:57:04'),
(6, 'vu hoang anh', 'vu-hoang-anh', NULL, 'uploads/brand/1757085307_741040c7407f5daaeb16.jpg', 'vinfat.com', '', 1, 0, '2025-09-05 22:15:07', '2025-09-27 01:57:06', '2025-09-27 01:57:06'),
(10, 'giày venno', 'giày-venno', NULL, 'uploads/brand/1757086599_919c6ffca18df326f1eb.jpg', '', '', 1, 0, '2025-09-05 22:36:39', '2025-09-27 01:57:14', '2025-09-27 01:57:14'),
(11, 'vsmart', 'vsmart', NULL, 'uploads/brand/1757089556_2c01bf06d45478131d55.webp', 'vinfat.coms', 'VietNams', 1, 9, '2025-09-05 22:39:47', '2025-09-27 01:57:42', NULL),
(12, 'vivo', 'vivo', NULL, 'uploads/brand/1757089581_7977b76c0ef06614b864.jpg', 'vinfat.comdQWD', 'ƯDqwdWQD', 1, 10, '2025-09-05 23:26:21', '2025-09-27 01:57:49', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image_url`, `parent_id`, `sort_order`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Điện tử', 'điện-tử', 'Các thiết bị điện tử', 'uploads/categories/1759396838_50d0cc014251854d8edf.png', NULL, 1, 1, '2025-08-26 22:38:34', '2025-10-02 16:20:38', NULL),
(2, 'Đồ gỗ', 'đồ-gỗ', 'Các sản phẩm đồ gỗ nội thất', 'uploads/categories/1759397318_f6515c8ca0dc9b3e4e48.png', NULL, 2, 1, '2025-08-26 22:38:34', '2025-10-02 16:28:38', NULL),
(3, 'Nhà bếp', 'nhà-bếp', 'Dụng cụ và thiết bị nhà bếp', 'uploads/categories/1757249412_b5ccaff87c48b1f20661.jpg', NULL, 3, 1, '2025-08-26 22:38:34', '2025-09-07 19:50:12', NULL),
(4, 'SOFA', 'sofa', 'ađsfsfsdfsdfsfwd', 'uploads/categories/1759397247_b87be13ddfb3d0c751bd.png', NULL, 0, 1, '2025-09-07 19:49:39', '2025-10-02 16:28:26', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customers`
--

CREATE TABLE `customers` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `otp` varchar(10) DEFAULT NULL,
  `otp_expiration` datetime DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `password`, `image_url`, `otp`, `otp_expiration`, `is_verified`, `created_at`, `updated_at`, `deleted_at`) VALUES
(10, 'vu trung anh', 'nicktestcake@gmail.com', '0373562881', 'yên phúc ý yên nam dinh', '$2y$10$UfAnz7BHISUt8KZFEFTEc.28NPWgo.OYpmwLETWeMMOeS4W9Uvlkq', 'https://lh3.googleusercontent.com/a/ACg8ocJhBLVccSqBLhcvtZ4u6EZ-QzeHA4QwEkQ5HD4qXcTYpI1fFQ=s96-c', NULL, NULL, 1, '2025-08-26 22:53:44', '2025-10-08 22:52:16', NULL),
(12, 'Vũ Hoàng Anh', 'thanhlong09052002@gmail.com', '0373562881', 'yên cường ý yên nam định', '$2y$10$3/NESRMjB7Kel7Hx7Jc8zOYdTApYw.ciKRJKagnCsq2pU.PFPiGM.', 'uploads/customers/12_1759911278.jpg', NULL, NULL, 1, '2025-10-08 15:09:28', '2025-10-08 15:14:38', NULL),
(13, 'hoang tuan anh', 'trunganhvu59@gmail.com', '0373562881', 'nam định', '$2y$10$KOpWBXEtgjjgsBOLAJQ8z.21I2N/SssKQxarCmwXjMixWcNAN/4Wa', NULL, NULL, NULL, 1, '2025-10-11 19:43:36', '2025-10-11 19:44:12', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `discount_coupons`
--

CREATE TABLE `discount_coupons` (
  `id` int(11) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` enum('percentage','fixed') NOT NULL DEFAULT 'fixed',
  `value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `min_order_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `apply_all` tinyint(1) NOT NULL DEFAULT 1,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `discount_coupons`
--

INSERT INTO `discount_coupons` (`id`, `code`, `type`, `value`, `min_order_amount`, `usage_limit`, `used_count`, `apply_all`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'SUMMER50', 'fixed', 50000.00, 200000.00, 100, 3, 1, '2025-09-01 10:20:00', '2025-11-30 10:21:00', 1, '2025-09-10 17:21:20', '2025-10-08 16:19:15'),
(2, 'SUMMER500', 'percentage', 50.00, 200000.00, 100, 0, 0, '2025-08-31 06:20:00', '2025-10-07 06:21:00', 1, '2025-09-10 17:21:20', '2025-10-07 14:36:50'),
(3, 'SIEUGIAMGIA', 'fixed', 100000.00, 50000.00, 100, 11, 1, '2025-09-01 07:48:00', '2025-10-30 07:48:00', 1, '2025-09-19 14:48:26', '2025-10-11 19:46:52'),
(4, 'SUMMER500_COPY_1759822069', 'percentage', 50.00, 200000.00, 100, 0, 0, '2025-08-31 06:20:00', '2025-10-31 06:21:00', 1, '2025-09-10 17:21:20', '2025-10-11 19:53:32'),
(5, 'SUMMER50_COPY_1759911609', 'fixed', 50000.00, 200000.00, 100, 0, 1, '2025-09-01 10:20:00', '2025-11-30 10:21:00', 0, '2025-09-10 17:21:20', '2025-10-06 18:01:49'),
(6, 'SUMMER500_COPY_1759822069_COPY_1760187223', 'percentage', 50.00, 200000.00, 100, 0, 0, '2025-08-31 06:20:00', '2025-10-31 06:21:00', 0, '2025-09-10 17:21:20', '2025-10-11 19:53:32'),
(7, 'SUMMER500_COPY_1759822069_COPY_1760187228', 'percentage', 50.00, 200000.00, 100, 0, 0, '2025-08-31 06:20:00', '2025-10-31 06:21:00', 0, '2025-09-10 17:21:20', '2025-10-11 19:53:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `discount_coupon_products`
--

CREATE TABLE `discount_coupon_products` (
  `coupon_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `discount_coupon_products`
--

INSERT INTO `discount_coupon_products` (`coupon_id`, `product_id`) VALUES
(2, 3),
(2, 4),
(2, 9),
(4, 3),
(4, 4),
(6, 3),
(6, 4),
(7, 3),
(7, 4);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `groups`
--

CREATE TABLE `groups` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Administrator group'),
(2, 'nhan vien', 'start group');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `group_roles`
--

CREATE TABLE `group_roles` (
  `id` int(11) UNSIGNED NOT NULL,
  `group_id` int(11) UNSIGNED NOT NULL,
  `role_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `group_roles`
--

INSERT INTO `group_roles` (`id`, `group_id`, `role_id`) VALUES
(23, 2, 1),
(24, 2, 2),
(25, 2, 3),
(26, 2, 4),
(27, 2, 5),
(28, 2, 6);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `invoices`
--

CREATE TABLE `invoices` (
  `id` int(10) UNSIGNED NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `subtotal` decimal(12,0) NOT NULL,
  `tax_amount` decimal(12,0) NOT NULL DEFAULT 0,
  `discount_amount` decimal(12,0) NOT NULL DEFAULT 0,
  `shipping_fee` decimal(12,0) NOT NULL DEFAULT 0,
  `total_amount` decimal(12,0) NOT NULL,
  `status` enum('draft','sent','paid','overdue','cancelled') NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `terms` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2025-08-20-092204', 'App\\Database\\Migrations\\CreateCustomersTable', 'default', 'App', 1755682597, 1),
(2, '2025-08-24-065751', 'App\\Database\\Migrations\\Groups', 'default', 'App', 1756020814, 2),
(3, '2025-08-24-065901', 'App\\Database\\Migrations\\Roles', 'default', 'App', 1756020814, 2),
(4, '2025-08-24-065907', 'App\\Database\\Migrations\\User', 'default', 'App', 1756020814, 2),
(5, '2025-08-24-065925', 'App\\Database\\Migrations\\GroupRole', 'default', 'App', 1756020814, 2),
(7, '2025-08-24-083313', 'App\\Database\\Migrations\\AddRoleAndSuperAdminToUsers', 'default', 'App', 1756025054, 3),
(8, '2025-08-26-074124', 'App\\Database\\Migrations\\CreateBrandsTable', 'default', 'App', 1756196052, 4),
(9, '2025-08-26-074400', 'App\\Database\\Migrations\\CreateCategoriesTable', 'default', 'App', 1756196053, 4),
(10, '2025-08-26-074500', 'App\\Database\\Migrations\\CreateProductsTable', 'default', 'App', 1756196053, 4),
(11, '2025-08-26-074600', 'App\\Database\\Migrations\\CreateProductImagesTable', 'default', 'App', 1756196053, 4),
(12, '2025-08-26-074601', 'App\\Database\\Migrations\\CreateWishlistTable', 'default', 'App', 1756196053, 4),
(13, '2025-08-26-074606', 'App\\Database\\Migrations\\CreateShoppingCartTable', 'default', 'App', 1756196053, 4),
(14, '2025-08-26-074607', 'App\\Database\\Migrations\\CreateOrdersTable', 'default', 'App', 1756196053, 4),
(15, '2025-08-26-074608', 'App\\Database\\Migrations\\CreateOrderItemsTable', 'default', 'App', 1756196053, 4),
(16, '2025-08-26-074609', 'App\\Database\\Migrations\\CreateProductReviewsTable', 'default', 'App', 1756196053, 4),
(17, '2025-08-27-074601', 'App\\Database\\Migrations\\CreateProductCommentsTable', 'default', 'App', 1756820776, 5),
(18, '2025-08-27-074602', 'App\\Database\\Migrations\\CreateBlogPostsTable', 'default', 'App', 1756820776, 5),
(19, '2025-08-27-074603', 'App\\Database\\Migrations\\CreateBlogCommentsTable', 'default', 'App', 1756820776, 5),
(20, '2025-08-27-074604', 'App\\Database\\Migrations\\CreateStockMovementsTable', 'default', 'App', 1756820776, 5),
(21, '2025-08-27-074605', 'App\\Database\\Migrations\\CreateInvoicesTable', 'default', 'App', 1756820776, 5),
(22, '2025-08-27-074606', 'App\\Database\\Migrations\\CreatePaymentTransactionsTable', 'default', 'App', 1756820776, 5),
(23, '2025-09-10-091211', 'App\\Database\\Migrations\\CreateDiscountCoupons', 'default', 'App', 1757496201, 6),
(24, '2025-10-06-090300', 'App\\Database\\Migrations\\AddCouponIdToOrders', 'default', 'App', 1759741439, 7);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `payment_method` enum('cod','momo','bank_transfer') NOT NULL DEFAULT 'cod',
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `subtotal` decimal(12,0) NOT NULL,
  `shipping_fee` decimal(12,0) NOT NULL DEFAULT 0,
  `total_amount` decimal(12,0) NOT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(12,2) DEFAULT 0.00,
  `shipping_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`shipping_address`)),
  `billing_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`billing_address`)),
  `notes` text DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `shipped_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `customer_id`, `status`, `payment_method`, `payment_status`, `subtotal`, `shipping_fee`, `total_amount`, `coupon_code`, `discount_amount`, `shipping_address`, `billing_address`, `notes`, `tracking_number`, `shipped_at`, `delivered_at`, `created_at`, `updated_at`) VALUES
(26, 'DH202509190618', 10, 'pending', 'cod', 'pending', 360000, 30000, 340000, NULL, 0.00, '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\"}', '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\"}', '', NULL, NULL, NULL, '2025-09-19 11:30:21', '2025-09-19 11:30:21'),
(34, 'DH202509199323', 10, 'pending', 'cod', 'pending', 310000, 30000, 340000, NULL, 0.00, '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-19 12:15:13', '2025-09-19 12:15:13'),
(35, 'DH202509198437', 10, 'pending', 'cod', 'pending', 150000, 30000, 180000, NULL, 0.00, '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-19 12:34:04', '2025-09-19 12:34:04'),
(36, 'DH202509191720', 10, 'pending', 'cod', 'pending', 80000, 30000, 110000, NULL, 0.00, '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-19 14:49:16', '2025-09-19 14:49:16'),
(37, 'DH202509198186', 10, 'pending', 'cod', 'pending', 80000, 30000, 110000, NULL, 0.00, '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-19 14:53:27', '2025-09-19 14:53:27'),
(38, 'DH202509197097', 10, 'pending', 'cod', 'pending', 80000, 30000, 110000, NULL, 0.00, '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-19 16:29:35', '2025-09-19 16:29:35'),
(39, 'DH202509197437', 10, 'pending', 'cod', 'pending', 260000, 30000, 290000, NULL, 0.00, '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-19 16:35:35', '2025-09-19 16:35:35'),
(40, 'DH202509191971', 10, 'delivered', 'cod', 'paid', 50000, 30000, 80000, NULL, 0.00, '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', '', '2025-09-19 23:21:55', '2025-09-19 23:22:32', '2025-09-19 22:34:27', '2025-09-19 23:22:32'),
(41, 'DH202509192703', 10, 'pending', 'cod', 'pending', 180000, 30000, 210000, NULL, 0.00, '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-19 23:24:19', '2025-09-19 23:24:19'),
(42, 'DH202509193234', 10, 'delivered', 'cod', 'paid', 280000, 30000, 310000, NULL, 0.00, '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', '', NULL, '2025-09-20 00:28:19', '2025-09-19 23:32:38', '2025-09-20 00:28:20'),
(43, 'DH202509208699', 10, 'pending', 'cod', 'pending', 380000, 30000, 410000, NULL, 0.00, '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', 'KHÁCH HÀNG YÊU CẦU THÙNG XỐP', '', NULL, NULL, '2025-09-20 15:04:12', '2025-09-20 15:05:04'),
(44, 'DH202509206506', 10, 'cancelled', 'cod', 'pending', 80000, 30000, 110000, NULL, 0.00, '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"ha anh tuan\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-20 15:05:59', '2025-09-23 18:09:34'),
(45, 'DH202509222908', 10, 'pending', 'cod', 'pending', 180000, 30000, 210000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"\\u00fd yen nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-22 21:01:13', '2025-09-22 21:01:13'),
(46, 'DH202509240698', 10, 'delivered', 'cod', 'paid', 180000, 80000, 260000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', '', '2025-09-24 14:01:38', '2025-09-24 14:01:56', '2025-09-24 14:01:17', '2025-09-24 14:01:56'),
(47, 'DH202509241747', 10, 'pending', 'momo', 'pending', 80000, 30000, 110000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-24 23:04:20', '2025-09-24 23:04:20'),
(48, 'DH202509240120', 10, 'pending', 'momo', 'pending', 180000, 30000, 210000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-24 23:07:11', '2025-09-24 23:07:11'),
(49, 'DH202509243467', 10, 'pending', 'momo', 'pending', 480000, 30000, 510000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-24 23:08:32', '2025-09-24 23:08:32'),
(50, 'DH202509245475', 10, 'pending', 'cod', 'pending', 180000, 30000, 210000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-24 23:29:34', '2025-09-24 23:29:34'),
(51, 'DH202509248156', 10, 'pending', 'momo', 'pending', 180000, 30000, 210000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-24 23:29:41', '2025-09-24 23:29:41'),
(52, 'DH202509249408', 10, 'cancelled', 'momo', 'pending', 180000, 30000, 210000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-24 23:33:51', '2025-09-25 20:21:58'),
(53, 'DH202509244445', 10, 'pending', 'momo', 'pending', 180000, 30000, 210000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-24 23:34:35', '2025-09-24 23:34:35'),
(54, 'DH202509247655', 10, 'pending', 'momo', 'pending', 180000, 30000, 210000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-24 23:50:02', '2025-09-24 23:50:02'),
(55, 'DH202509250635', 10, 'pending', 'momo', 'failed', 180000, 30000, 210000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '\n[Payment] Giao dịch đang được xử lý\n[Payment] Giao dịch đang được xử lý', NULL, NULL, NULL, '2025-09-25 00:40:17', '2025-09-25 00:45:09'),
(56, 'DH202509259035', 10, 'pending', 'momo', 'paid', 180000, 30000, 210000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '\n[Payment] Thanh toán MoMo thành công', NULL, NULL, NULL, '2025-09-25 00:45:22', '2025-09-25 00:46:55'),
(57, 'DH202509258688', 10, 'delivered', 'momo', 'paid', 180000, 30000, 210000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '\n[Payment] Thanh toán MoMo thành công', '', NULL, '2025-09-26 00:00:54', '2025-09-25 23:58:22', '2025-09-26 00:00:54'),
(58, 'DH202509263085', 10, 'pending', 'cod', 'pending', 80000, 30000, 110000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-26 23:23:35', '2025-09-26 23:23:35'),
(59, 'DH202509264427', 10, 'delivered', 'momo', 'paid', 292000, 30000, 322000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '\n[Payment] Thanh toán MoMo thành công', '', '2025-09-26 23:29:26', '2025-09-26 23:29:33', '2025-09-26 23:24:42', '2025-09-26 23:29:33'),
(60, 'DH202509293160', 10, 'pending', 'momo', 'pending', 1850000, 0, 1850000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-09-29 22:23:42', '2025-09-29 22:23:42'),
(61, 'DH202510034829', 10, 'delivered', 'momo', 'paid', 130000, 30000, 160000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '\n[Payment] Thanh toán MoMo thành công', '', '2025-10-03 16:16:12', '2025-10-03 16:16:25', '2025-10-03 16:14:56', '2025-10-03 16:16:25'),
(62, 'DH202510065340', 10, 'delivered', 'cod', 'pending', 350000, 30000, 380000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', '', '2025-10-06 16:01:17', '2025-10-06 16:47:31', '2025-10-06 16:00:38', '2025-10-06 16:47:31'),
(63, 'DH202510067801', 10, 'cancelled', 'momo', 'failed', 350000, 30000, 380000, 'SIEUGIAMGIA', 100000.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '\n[Payment] Giao dịch đang được xử lý\n[Payment] Giao dịch đang được xử lý', '', NULL, NULL, '2025-10-06 16:10:32', '2025-10-06 16:46:57'),
(64, 'DH202510068781', 10, 'cancelled', 'cod', 'pending', 350000, 30000, 380000, 'SIEUGIAMGIA', 100000.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', '', NULL, NULL, '2025-10-06 16:12:51', '2025-10-06 16:47:50'),
(65, 'DH202510064331', 10, 'pending', 'cod', 'pending', 80000, 30000, 110000, 'SIEUGIAMGIA', 100000.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-10-06 16:14:09', '2025-10-06 16:14:09'),
(66, 'DH202510060795', 10, 'pending', 'momo', 'paid', 80000, 30000, 110000, 'SIEUGIAMGIA', 100000.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '\n[Payment] Thanh toán MoMo thành công', NULL, NULL, NULL, '2025-10-06 16:40:39', '2025-10-06 16:41:57'),
(67, 'DH202510063083', 10, 'pending', 'cod', 'pending', 1080000, 0, 1080000, 'SIEUGIAMGIA', 100000.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-10-06 20:10:16', '2025-10-06 20:10:16'),
(68, 'DH202510068513', 10, 'pending', 'momo', 'paid', 540000, 0, 540000, 'SIEUGIAMGIA', 100000.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '\n[Payment] Thanh toán MoMo thành công', NULL, NULL, NULL, '2025-10-06 22:40:05', '2025-10-06 22:41:04'),
(69, 'DH202510068755', 10, 'shipped', 'cod', 'paid', 230000, 30000, 260000, NULL, 0.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', '', '2025-10-06 23:19:29', NULL, '2025-10-06 22:47:55', '2025-10-06 23:19:30'),
(70, 'DH202510062151', 10, 'delivered', 'cod', 'paid', 130000, 30000, 160000, 'SIEUGIAMGIA', 100000.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', '', '2025-10-06 23:18:20', '2025-10-07 00:30:44', '2025-10-06 23:15:58', '2025-10-07 00:30:44'),
(71, 'DH202510075589', 10, 'processing', 'momo', 'paid', 130000, 30000, 160000, 'SIEUGIAMGIA', 100000.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '\n[Payment] Thanh toán MoMo thành công', '', NULL, NULL, '2025-10-07 00:34:57', '2025-10-07 00:36:07'),
(72, 'DH202510079611', 10, 'processing', 'cod', 'paid', 180000, 30000, 210000, 'SUMMER50', 50000.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', '', NULL, NULL, '2025-10-07 00:37:40', '2025-10-07 13:57:24'),
(73, 'DH202510075029', 10, 'cancelled', 'momo', 'refunded', 130000, 30000, 160000, 'SIEUGIAMGIA', 100000.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '\n[Payment] Thanh toán MoMo thành công', '', '2025-10-07 14:08:53', '2025-10-07 14:09:07', '2025-10-07 13:25:14', '2025-10-07 14:09:50'),
(74, 'DH202510073713', 10, 'processing', 'cod', 'pending', 180000, 30000, 210000, 'SUMMER50', 50000.00, '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"vu trung anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean ph\\u00fac \\u00fd y\\u00ean nam dinh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', '', NULL, NULL, '2025-10-07 14:04:09', '2025-10-07 14:04:44'),
(75, 'DH202510082124', 12, 'delivered', 'momo', 'paid', 230000, 30000, 260000, 'SIEUGIAMGIA', 100000.00, '{\"name\":\"V\\u0169 Ho\\u00e0ng Anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"V\\u0169 Ho\\u00e0ng Anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '\n[Payment] Thanh toán MoMo thành công', '', '2025-10-08 15:18:51', '2025-10-08 15:18:59', '2025-10-08 15:12:56', '2025-10-08 15:18:59'),
(76, 'DH202510080552', 12, 'processing', 'cod', 'pending', 130000, 30000, 160000, 'SIEUGIAMGIA', 100000.00, '{\"name\":\"V\\u0169 Ho\\u00e0ng Anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"V\\u0169 Ho\\u00e0ng Anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', '', NULL, NULL, '2025-10-08 15:21:47', '2025-10-08 15:22:17'),
(77, 'DH202510080119', 12, 'processing', 'cod', 'paid', 180000, 30000, 210000, 'SUMMER50', 50000.00, '{\"name\":\"V\\u0169 Ho\\u00e0ng Anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"V\\u0169 Ho\\u00e0ng Anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', '', NULL, NULL, '2025-10-08 15:23:47', '2025-10-08 15:24:25'),
(83, 'DH202510081299', 12, 'pending', 'cod', 'pending', 180000, 30000, 210000, 'SUMMER50', 50000.00, '{\"name\":\"V\\u0169 Ho\\u00e0ng Anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"V\\u0169 Ho\\u00e0ng Anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-10-08 15:59:31', '2025-10-08 15:59:31'),
(84, 'DH202510089535', 12, 'pending', 'cod', 'pending', 230000, 30000, 260000, NULL, 0.00, '{\"name\":\"V\\u0169 Ho\\u00e0ng Anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"V\\u0169 Ho\\u00e0ng Anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-10-08 15:59:38', '2025-10-08 15:59:38'),
(85, 'DH202510088546', 12, 'pending', 'cod', 'pending', 80000, 30000, 110000, 'SIEUGIAMGIA', 100000.00, '{\"name\":\"V\\u0169 Ho\\u00e0ng Anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"V\\u0169 Ho\\u00e0ng Anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-10-08 16:06:27', '2025-10-08 16:06:27'),
(86, 'DH202510088697', 12, 'pending', 'cod', 'pending', 130000, 30000, 160000, 'SIEUGIAMGIA', 100000.00, '{\"name\":\"V\\u0169 Ho\\u00e0ng Anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"V\\u0169 Ho\\u00e0ng Anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-10-08 16:12:25', '2025-10-08 16:12:25'),
(87, 'DH202510088313', 12, 'pending', 'cod', 'pending', 180000, 30000, 210000, 'SUMMER50', 50000.00, '{\"name\":\"V\\u0169 Ho\\u00e0ng Anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"V\\u0169 Ho\\u00e0ng Anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '', NULL, NULL, NULL, '2025-10-08 16:19:15', '2025-10-08 16:19:15'),
(88, 'DH202510115282', 13, 'delivered', 'momo', 'paid', 130000, 30000, 160000, 'SIEUGIAMGIA', 100000.00, '{\"name\":\"hoang tuan anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean  nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '{\"name\":\"hoang tuan anh\",\"phone\":\"0373562881\",\"address\":\"y\\u00ean c\\u01b0\\u1eddng \\u00fd y\\u00ean  nam \\u0111\\u1ecbnh\",\"ward\":\"\",\"district\":\"\",\"city\":\"\",\"postal_code\":\"\"}', '\n[Payment] Lỗi không xác định (7002)', '', '2025-10-11 19:50:30', '2025-10-11 19:51:32', '2025-10-11 19:46:52', '2025-10-11 19:51:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_sku` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(12,0) NOT NULL,
  `total` decimal(12,0) NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_sku`, `quantity`, `price`, `total`, `created_at`) VALUES
(1, 34, 7, 'Máy hút bụi cầm tay không dây', '00198', 2, 180000, 360000, NULL),
(2, 35, 6, 'quat ha', '00195', 4, 50000, 200000, NULL),
(3, 36, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(4, 37, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(5, 38, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(6, 39, 7, 'Máy hút bụi cầm tay không dây', '00198', 2, 180000, 360000, NULL),
(7, 40, 6, 'quat ha', '00195', 1, 50000, 50000, NULL),
(8, 41, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(9, 42, 3, 'giày venno444', '00193', 1, 100000, 100000, NULL),
(10, 42, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(11, 43, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(12, 43, 6, 'quat ha', '00195', 2, 50000, 100000, NULL),
(13, 43, 3, 'giày venno444', '00193', 1, 100000, 100000, NULL),
(14, 43, 2, 'giày venno', '00192', 1, 100000, 100000, NULL),
(15, 44, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(16, 45, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(17, 46, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(18, 47, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(19, 48, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(20, 49, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(21, 49, 6, 'quat ha', '00195', 2, 50000, 100000, NULL),
(22, 49, 3, 'giày venno444', '00193', 1, 100000, 100000, NULL),
(23, 49, 2, 'giày venno', '00192', 1, 100000, 100000, NULL),
(24, 50, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(25, 51, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(26, 52, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(27, 53, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(28, 54, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(29, 55, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(30, 56, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(31, 57, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(32, 58, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(33, 59, 3, 'giày venno444', '00193', 1, 100000, 100000, NULL),
(34, 59, 4, 'giaty ha ka', '00194', 1, 12000, 12000, NULL),
(35, 59, 2, 'giày venno', '00192', 1, 100000, 100000, NULL),
(36, 59, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(37, 60, 11, 'Máy rửa bát Kaff KF-SBL775B New Plus', '00895', 1, 1900000, 1900000, NULL),
(38, 61, 12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', '00678', 1, 230000, 230000, NULL),
(39, 62, 9, 'Tủ lạnh Hitachi Inverter 374 lít HRTN6408SGBK', '00999', 1, 450000, 450000, NULL),
(40, 63, 9, 'Tủ lạnh Hitachi Inverter 374 lít HRTN6408SGBK', '00999', 1, 450000, 450000, NULL),
(41, 64, 9, 'Tủ lạnh Hitachi Inverter 374 lít HRTN6408SGBK', '00999', 1, 450000, 450000, NULL),
(42, 65, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(43, 66, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(44, 67, 8, 'Tủ lạnh Aqua AQR-T220NE(HB) Inverter 189 lít', '00199', 1, 500000, 500000, NULL),
(45, 67, 9, 'Tủ lạnh Hitachi Inverter 374 lít HRTN6408SGBK', '00999', 1, 450000, 450000, NULL),
(46, 67, 12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', '00678', 1, 230000, 230000, NULL),
(47, 68, 12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', '00678', 2, 230000, 460000, NULL),
(48, 68, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(49, 69, 12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', '00678', 1, 230000, 230000, NULL),
(50, 70, 12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', '00678', 1, 230000, 230000, NULL),
(51, 71, 12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', '00678', 1, 230000, 230000, NULL),
(52, 72, 12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', '00678', 1, 230000, 230000, NULL),
(53, 73, 12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', '00678', 1, 230000, 230000, NULL),
(54, 74, 12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', '00678', 1, 230000, 230000, NULL),
(55, 75, 12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', '00678', 1, 230000, 230000, NULL),
(56, 75, 2, 'Gậy Lau nhà-tea', '00192', 1, 100000, 100000, NULL),
(57, 76, 12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', '00678', 1, 230000, 230000, NULL),
(58, 77, 12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', '00678', 1, 230000, 230000, NULL),
(64, 83, 12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', '00678', 1, 230000, 230000, NULL),
(65, 84, 12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', '00678', 1, 230000, 230000, NULL),
(66, 85, 7, 'Máy hút bụi cầm tay không dây', '00198', 1, 180000, 180000, NULL),
(67, 86, 12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', '00678', 1, 230000, 230000, NULL),
(68, 87, 12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', '00678', 1, 230000, 230000, NULL),
(69, 88, 12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', '00678', 1, 230000, 230000, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `payment_method` enum('cod','momo','bank_transfer') NOT NULL DEFAULT 'cod',
  `amount` decimal(12,0) NOT NULL,
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `processed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `brand_id` int(10) UNSIGNED DEFAULT NULL,
  `price` decimal(12,0) NOT NULL,
  `sale_price` decimal(12,0) DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specifications`)),
  `main_image` varchar(255) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `min_stock_level` int(11) NOT NULL DEFAULT 0,
  `stock_status` enum('in_stock','out_of_stock','low_stock','pre_order') NOT NULL DEFAULT 'in_stock',
  `weight` decimal(8,2) DEFAULT NULL,
  `dimensions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dimensions`)),
  `material` varchar(100) DEFAULT NULL,
  `origin_country` varchar(100) DEFAULT NULL,
  `warranty_period` int(11) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `sku`, `category_id`, `brand_id`, `price`, `sale_price`, `short_description`, `description`, `specifications`, `main_image`, `stock_quantity`, `min_stock_level`, `stock_status`, `weight`, `dimensions`, `material`, `origin_country`, `warranty_period`, `is_featured`, `is_active`, `meta_title`, `meta_description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'Gậy Lau nhà-tea', 'gy-lau-nh-tea', '00192', 2, 2, 200000, 100000, 'giày viẹt nam azidat', '', '{\"height\":\"11\",\"width\":\"11\",\"length\":\"11\",\"weight\":\"1\",\"material\":\"11\",\"color\":\"đỏ\",\"power\":\"200\",\"capacity\":\"200\",\"điện áp\":\"220V\"}', 'uploads/products/1758912968_f877d1c4bc9d323ff649.jpg', 11, 0, 'in_stock', 0.00, '{\"length\":100,\"width\":100,\"height\":100}', '', '', 0, 0, 1, '', '', '2025-08-27 22:55:38', '2025-10-08 15:18:13', NULL),
(3, 'Gậy lau nhà -hata', 'gy-lau-nh-hata', '00193', 1, 3, 300000, 100000, 'viết văn ', '', '{\"height\":\"11\",\"width\":\"11\",\"length\":\"11\",\"weight\":\"1\",\"material\":\"11\",\"color\":\"đỏ\",\"power\":\"200\",\"capacity\":\"200\",\"điện áp\":\"220V\"}', 'uploads/products/1758912958_a849698e5cc4a6e71584.jpg', 6, 0, 'in_stock', 0.00, '{\"length\":100,\"width\":100,\"height\":100}', '', '', 0, 0, 1, '', '', '2025-08-28 22:42:03', '2025-09-27 01:59:04', NULL),
(4, 'Gậy lau nhà-virat', 'gy-lau-nh-virat', '00194', 3, 2, 300000, 12000, 'hàng còn đẹp đó anh em ơi ', 'hàng còn đẹp đó anh em ơi ', '{\"height\":\"11\",\"width\":\"11\",\"length\":\"11\",\"weight\":\"1\",\"material\":\"11\",\"color\":\"đỏ\",\"power\":\"200\",\"capacity\":\"200\",\"điện áp\":\"220V\"}', 'uploads/products/1756917014_31c32355c7d4268bd298.jpg', 9, 2, 'in_stock', 100.00, '{\"length\":100,\"width\":100,\"height\":100}', '100', 'Việt Nam', 3, 0, 1, 'hàng hót 2025', 'hàng hót 2025', '2025-08-31 16:00:49', '2025-09-27 01:58:41', '2025-09-02 22:22:04'),
(6, 'quat ha', 'quat-ha', '00195', 1, 2, 50000, 0, 'Quạt tích điện chân quỳ Solar JY-236 cánh rộng pin 25 giờ', 'Quạt tích điện chân quỳ Solar JY-236 cánh rộng pin 25 giờQuạt tích điện chân quỳ Solar JY-236 cánh rộng pin 25 giờQuạt tích điện chân quỳ Solar JY-236 cánh rộng pin 25 giờQuạt tích điện chân quỳ Solar JY-236 cánh rộng pin 25 giờ', '{\"height\":\"22\",\"width\":\"22\",\"length\":\"22\",\"weight\":\"2\",\"material\":\"cv\",\"color\":\"da\",\"power\":\"22\",\"capacity\":\"222\",\"điện\":\"200v\"}', 'uploads/products/1757615397_10fddcd0c3e0fc9707dd.jpg', 1, 0, 'in_stock', 2.00, '{\"length\":200,\"width\":200,\"height\":200}', 'điện ', 'vn', 6, 0, 1, 'Quạt tích điện chân quỳ Solar JY-236 cánh rộng pin 25 giờ', 'Quạt tích điện chân quỳ Solar JY-236 cánh rộng pin 25 giờ', '2025-09-12 01:29:57', '2025-09-24 23:08:32', NULL),
(7, 'Máy hút bụi cầm tay không dây', 'may-hut-bui-cm-tay-khng-dy', '00198', 1, 1, 200000, 180000, 'Máy hút bụi cầm tay không dây', '{Cao Cấp} Máy Hút Bụi Cầm Tay Sạc Điện Ô Tô, Nhà Cửa, Sofa Không Dây R6053 Chính Hãng Công Nghệ Hàng Đầu Nhật Bản, Công Suất Lớn, Độ Bền Cao, Thiết Kế Đẹp Mắt Sang Trọng, Dễ Dàng Thao Tác Sử Dụng, Hút Sofa, Giường Nệm, Nội Thất Ô Tô, Khe Cửa, Bàn Thờ, Quà Tặng Người Thân, Gia Đình, Được Nhiều Người Tin Dùng, Mẫu Mới 2025.', '{\"height\":\"200\",\"width\":\"200\",\"length\":\"200\",\"weight\":\"2\",\"material\":\"\\u0111i\\u1ec7n t\\u1eed\",\"color\":\"\\u0111en\",\"power\":\"222\",\"capacity\":\"222\",\"\\u0111i\\u1ec7n \\u00e1p\":\"220v\"}', 'uploads/products/1758031885_b895053877c92a13fcc3.jpg', 1, 0, 'in_stock', 20.00, '{\"length\":200,\"width\":200,\"height\":200}', 'điện tử ', 'Việt Nam', 6, 0, 1, 'Máy hút bụi cầm tay không dây', '{Cao Cấp} Máy Hút Bụi Cầm Tay Sạc Điện Ô Tô, Nhà Cửa, Sofa Không Dây R6053 Chính Hãng Công Nghệ Hàng Đầu Nhật Bản, Công Suất Lớn, Độ Bền Cao, Thiết Kế Đẹp Mắt Sang Trọng, Dễ Dàng Thao Tác Sử Dụng, Hút Sofa, Giường Nệm, Nội Thất Ô Tô, Khe Cửa, Bàn Thờ, Quà Tặng Người Thân, Gia Đình, Được Nhiều Người Tin Dùng, Mẫu Mới 2025.', '2025-09-16 21:11:25', '2025-10-06 22:40:05', NULL),
(8, 'Tủ lạnh Aqua AQR-T220NE(HB) Inverter 189 lít', 't-lnh-aqua-aqr-t220nehb-inverter-189-lt', '00199', 1, 1, 500000, 0, 'Tủ lạnh Aqua Inverter 189 lít AQR-T220NE(HB) có khả năng làm lạnh đa chiều, giúp bảo quản thực phẩm tối ưu, giảm thiểu tỷ lệ hư hỏng. Ngoài ra, tủ lạnh cũng mang lại hiệu quả tiết kiệm điện nhờ sử dụng công nghệ Twin Inverter.', '- Aqua Inverter 189 lít AQR-T220NE(HB) được thiết kế dạng tủ lạnh ngăn đá trên với mặt cửa tủ làm bằng chất liệu thép bền bỉ, đồng thời phủ sơn màu đen sang trọng nên tủ lạnh phù hợp lắp đặt ở mọi vị trí bên trong khu vực nhà bếp.\r\n\r\n- Dung tích sử dụng của mẫu tủ lạnh Aqua này khoảng 189 lít, đáp ứng khả năng lưu trữ thực phẩm cho gia đình từ 2 - 3 người sử dụng.\r\n\r\nNgăn đá\r\n\r\nNgăn đá có dung tích 55 lít, gồm có kệ chia làm các ngăn và khay chứa (nằm bên cánh cửa tủ). Tủ lạnh sử dụng khay đá kiểu vỉ, giúp bạn chủ động hơn khi đặt vỉ đá ở mọi vị trí mà bạn muốn.\r\n\r\nNgăn lạnh\r\n\r\nNgăn lạnh có dung tích 134 lít, gồm có kệ chia làm các ngăn và hộc đựng rau củ quả. Ngoài ra, cánh cửa tủ còn được thiết kế thêm 3 khay chứa tiện lợi cho việc đặt đồ hộp và các loại đồ uống.', '{\"height\":\"2000\",\"width\":\"2000\",\"length\":\"2000\",\"weight\":\"2000\",\"material\":\"nhôm\",\"color\":\"xám\",\"power\":\"222\",\"capacity\":\"2000\",\"điên áp\":\"220v\"}', 'uploads/products/1759142124_ea0d6df1640ebc916765.webp', 99, 10, 'in_stock', 20.00, '{\"length\":2000,\"width\":1000,\"height\":2000}', 'nhôm', 'Việt Nam', 6, 0, 1, 'Tủ lạnh Aqua AQR-T220NE(HB) Inverter 189 lít', 'Tủ lạnh Aqua Inverter 189 lít AQR-T220NE(HB) có khả năng làm lạnh đa chiều, giúp bảo quản thực phẩm tối ưu, giảm thiểu tỷ lệ hư hỏng. Ngoài ra, tủ lạnh cũng mang lại hiệu quả tiết kiệm điện nhờ sử dụng công nghệ Twin Inverter.', '2025-09-29 17:35:24', '2025-10-06 20:10:16', NULL),
(9, 'Tủ lạnh Hitachi Inverter 374 lít HRTN6408SGBK', 't-lnh-hitachi-inverter-374-lt-hrtn6408sgbk', '00999', 1, 2, 450000, 0, 'Tủ lạnh Hitachi HRTN6408SGBKVN là một sản phẩm mới từ thương hiệu nổi tiếng Hitachi, sở hữu thiết kế cao cấp, sang trọng và dung tích lên đến 374 lít, phục vụ hiệu quả nhu cầu lưu trữ thực phẩm cho gia đình. Được trang bị các công nghệ tiên tiến như cảm biến Dual Sense, làm lạnh vòm cung, bộ lọc Triple Power và ngăn chuyển đổi linh hoạt, tủ lạnh giúp bảo quản thực phẩm tươi ngon lâu dài, đồng thời tiết kiệm năng lượng.', 'Tủ lạnh Hitachi HRTN6408SGBKVN là một sản phẩm mới từ thương hiệu nổi tiếng Hitachi, sở hữu thiết kế cao cấp, sang trọng và dung tích lên đến 374 lít, phục vụ hiệu quả nhu cầu lưu trữ thực phẩm cho gia đình. Được trang bị các công nghệ tiên tiến như cảm biến Dual Sense, làm lạnh vòm cung, bộ lọc Triple Power và ngăn chuyển đổi linh hoạt, tủ lạnh giúp bảo quản thực phẩm tươi ngon lâu dài, đồng thời tiết kiệm năng lượng.', '{\"height\":\"2000\",\"width\":\"2000\",\"length\":\"2000\",\"weight\":\"20\",\"material\":\"nhôm\",\"color\":\"xám\",\"power\":\"220\",\"capacity\":\"222\",\"điện áp\":\"220v\"}', 'uploads/products/1759142979_32f7a8bdd2bf2d7d4df1.jpg', 46, 10, 'in_stock', 2.00, '{\"length\":2000,\"width\":2000,\"height\":2000}', 'nhôm', 'Hàn quốc', 12, 0, 1, 'Tủ lạnh Hitachi Inverter 374 lít HRTN6408SGBKVN', 'Tủ lạnh Hitachi HRTN6408SGBKVN là một sản phẩm mới từ thương hiệu nổi tiếng Hitachi, sở hữu thiết kế cao cấp, sang trọng và dung tích lên đến 374 lít, phục vụ hiệu quả nhu cầu lưu trữ thực phẩm cho gia đình. Được trang bị các công nghệ tiên tiến như cảm biến Dual Sense, làm lạnh vòm cung, bộ lọc Triple Power và ngăn chuyển đổi linh hoạt, tủ lạnh giúp bảo quản thực phẩm tươi ngon lâu dài, đồng thời tiết kiệm năng lượng.', '2025-09-29 17:49:39', '2025-10-06 20:10:16', NULL),
(10, 'Tủ lạnh Hitachi Inverter 374 lít HRTN6408SGBKssss', 't-lnh-hitachi-inverter-374-lt-hrtn6408sgbkssss', '09999', 1, 2, 130000, 0, 'Tủ lạnh Hitachi HRTN6408SGBKVN là một sản phẩm mới từ thương hiệu nổi tiếng Hitachi, sở hữu thiết kế cao cấp, sang trọng và dung tích lên đến 374 lít, phục vụ hiệu quả nhu cầu lưu trữ thực phẩm cho gia đình. Được trang bị các công nghệ tiên tiến như cảm biến Dual Sense, làm lạnh vòm cung, bộ lọc Triple Power và ngăn chuyển đổi linh hoạt, tủ lạnh giúp bảo quản thực phẩm tươi ngon lâu dài, đồng thời tiết kiệm năng lượng.', 'Tủ lạnh Hitachi HRTN6408SGBKVN là một sản phẩm mới từ thương hiệu nổi tiếng Hitachi, sở hữu thiết kế cao cấp, sang trọng và dung tích lên đến 374 lít, phục vụ hiệu quả nhu cầu lưu trữ thực phẩm cho gia đình. Được trang bị các công nghệ tiên tiến như cảm biến Dual Sense, làm lạnh vòm cung, bộ lọc Triple Power và ngăn chuyển đổi linh hoạt, tủ lạnh giúp bảo quản thực phẩm tươi ngon lâu dài, đồng thời tiết kiệm năng lượng.', '{\"height\":\"2000\",\"width\":\"2000\",\"length\":\"2000\",\"weight\":\"20\",\"material\":\"nhôm\",\"color\":\"xám\",\"power\":\"220\",\"capacity\":\"222\",\"điện áp\":\"220v\"}', 'uploads/products/1759143137_ba324e136e3613a191fb.jpg', 100, 10, 'in_stock', 2.00, '{\"length\":2000,\"width\":2000,\"height\":2000}', 'nhôm', 'Hàn quốc', 12, 0, 1, 'Tủ lạnh Hitachi Inverter 374 lít HRTN6408SGBKVN', 'Tủ lạnh Hitachi HRTN6408SGBKVN là một sản phẩm mới từ thương hiệu nổi tiếng Hitachi, sở hữu thiết kế cao cấp, sang trọng và dung tích lên đến 374 lít, phục vụ hiệu quả nhu cầu lưu trữ thực phẩm cho gia đình. Được trang bị các công nghệ tiên tiến như cảm biến Dual Sense, làm lạnh vòm cung, bộ lọc Triple Power và ngăn chuyển đổi linh hoạt, tủ lạnh giúp bảo quản thực phẩm tươi ngon lâu dài, đồng thời tiết kiệm năng lượng.', '2025-09-29 17:52:17', '2025-09-29 17:52:29', NULL),
(11, 'Máy rửa bát Kaff KF-SBL775B New Plus', 'my-ra-bt-kaff-kf-sbl775b-new-plus', '00895', 1, 11, 1900000, 0, '8 Chương trình rửa tích hợp: Rửa thông minh rửa tự động (AI wash), Rửa kỹ (Intensive or Heavy), rửa thông thường (Nomal Wash), rửa tiết kiệm (ECO Wash), rửa ly (Glass wash), rửa nhanh rửa tráng nước nóng (Rapid Wash), rửa hoa quả (Fruit and vegetable wash), chức năng rửa diệt khuẩn rửa đồ trẻ em (Baby care)', 'Thông số kỹ thuật Máy rửa bát Kaff KF-SBL775B New Plus\r\nChất liệu: Thân sơn tĩnh điện màu đen cao cấp\r\n3 dàn rửa tiện lợi\r\nSức chứa: 17 bộ đồ ăn Châu Âu\r\n8 Chương trình rửa tích hợp: Rửa thông minh rửa tự động (AI wash), Rửa kỹ (Intensive or Heavy), rửa thông thường (Nomal Wash), rửa tiết kiệm (ECO Wash), rửa ly (Glass wash), rửa nhanh rửa tráng nước nóng (Rapid Wash), rửa hoa quả (Fruit and vegetable wash), chức năng rửa diệt khuẩn rửa đồ trẻ em (Baby care)\r\nChức năng đặc biệt:\r\nHệ thống PTC sấy khô khí nóng. Sau khi quá trình rửa chén bát hoàn tất, máy sẽ dẫn không khí tự nhiên bên ngoài thông qua bộ gia nhiệt PTC để tạo thành luồng khí nóng thổi vào khoang máy và nhanh chóng lấy đi hơi ẩm còn tồn lại, giúp cho các bộ đồ ăn được sấy khô hoàn toàn\r\nChức năng khử khuẩn lên đến 72 độ giúp diệt khuẩn lên đến 99.99% (Sterilization)\r\nChức năng sấy khí tươi 168h dẫn lưu không khí định kỳ trong khoang máy 5 phút mỗi giờ giúp cho khoang máy và bát đĩa như luôn được làm mới ( 168h Fresh; Dry Function)\r\nTăng cường vòi rửa kép (Dual zone wash function) chức năng xả bổ sung để tăng số bước, có thể chọn thêm 1, 2 hoặc thêm 3 lần xả để làm sạch bộ đồ ăn bẩn hiệu quả hơn.\r\nChức năng xả tăng cường chỉ có thể được chọn cho giặt mạnh, giặt thường, giặt mềm thủy tinh và rửa rau củ quả.\r\nChức năng rửa thông minh (Smart Washing Function Auto): Khi bật chế độ giặt thông minh, máy sẽ tự động nhận biết và lựa chọn nhiệt độ rửa phù hợp nhất, lưu lượng nước vào và thời gian rửa để rửa bộ đồ ăn tùy theo độ đục của nước bên trong máy, sau khi hoàn thành chương trình rửa thông minh, chức năng sấy khô sẽ tự động được bật, làm thế nào để làm cho bộ đồ ăn được tẩy nhờn và khô tốt hơn', '{\"power\":\"1200\",\"capacity\":\"6-11.2L\",\"voltage\":\"Điện áp: 220-240V \",\"frequency\":\"50-60 Hz\",\"screen_size\":\"805 C x 598 R x 568 S mm\",\"color\":\"đen\",\"Kích thước\":\"805 C x 598 R x 568 S mm\",\"Kích thước tháo nắp trên\":\"775 C x 598 R x 568 S mm\",\"Chiều dài dây điện\":\"1.5m\",\"Điện áp\":\"220-240V \\/ 50-60 Hz\",\"Áp suất nước\":\"0.4-10 Bar\",\"Mức tiêu thụ nước\":\"6-11.2L\\/1 lần rửa\",\"Nhiệt độ nước nóng\":\"Lên đến 70⁰C\",\"Mức tiêu thụ điện bình quân\":\"0.92 Kwh\\/lần rửa\",\"Tiêu chuẩn\":\"Năng lượng Châu Âu A+++ ( biến tần tích hợp )\",\"Độ ồn\":\"48dB\"}', 'uploads/products/1759159174_db69de0ecc73c060186d.png', 0, 0, 'out_of_stock', 15.00, '{\"length\":1500,\"width\":500,\"height\":2000}', 'nhôm -sắt', '', 0, 0, 1, 'Máy rửa bát Kaff KF-SBL775B New Plus', '8 Chương trình rửa tích hợp: Rửa thông minh rửa tự động (AI wash), Rửa kỹ (Intensive or Heavy), rửa thông thường (Nomal Wash), rửa tiết kiệm (ECO Wash), rửa ly (Glass wash), rửa nhanh rửa tráng nước nóng (Rapid Wash), rửa hoa quả (Fruit and vegetable wash), chức năng rửa diệt khuẩn rửa đồ trẻ em (Baby care)', '2025-09-29 22:19:34', '2025-09-29 22:23:42', NULL),
(12, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', 'my-ra-bt-c-lp-bosch-sms8yci01e-series-8', '00678', 1, 11, 500000, 230000, 'Máy rửa bát độc lập Bosch SMS8YCI01E là sản phẩm được người tiêu dùng an tâm về chất lượng, mẫu mã và giá thành cũng vô cùng hợp lý.  Sản phẩm được người tiêu dùng tại châu Âu đánh giá cao bởi thiết kế trẻ trung, hiện đại song cũng không kém phần sang trọng. Thiết kế máy đứng độc lập chắc chắn và cứng cáp , công suất rửa mạnh mẽ với 14 bộ chén bát. Nếu đang tìm kiếm sản phẩm bền bỉ, hiệu suất cao và thân thiện môi trường thì Bosch SMS8YCI01E là lựa chọn hoàn hảo cho bạn.', 'Máy rửa bát độc lập Bosch SMS8YCI01E là sản phẩm được người tiêu dùng an tâm về chất lượng, mẫu mã và giá thành cũng vô cùng hợp lý.  Sản phẩm được người tiêu dùng tại châu Âu đánh giá cao bởi thiết kế trẻ trung, hiện đại song cũng không kém phần sang trọng. Thiết kế máy đứng độc lập chắc chắn và cứng cáp , công suất rửa mạnh mẽ với 14 bộ chén bát. Nếu đang tìm kiếm sản phẩm bền bỉ, hiệu suất cao và thân thiện môi trường thì Bosch SMS8YCI01E là lựa chọn hoàn hảo cho bạn.', '{\"power\":\"5v\",\"capacity\":\"2l\",\"voltage\":\"220v\",\"frequency\":\"50 hz\",\"screen_size\":\"3\",\"color\":\"xám\",\"Công suất rửa\":\"14 bộ\",\"Chất liệu\":\"Inox\",\"Tiêu thụ nước\":\"9.5 lít\",\"Số chương trình rửa\":\"8 chương trình rửa\",\"Độ ồn\":\"43dB\",\"Kích thước\":\"84.5x60x60 cm\",\"Trọng lượng\":\"57kg\",\"Bảo hành\":\"Chính hãng 36 Tháng\"}', 'uploads/products/1759477875_6a810d669eb4f2c1273d.jpg', 185, 0, 'in_stock', 5.00, '{\"length\":2000,\"width\":2000,\"height\":2000}', 'nhôm', 'hàn quốc', 36, 0, 1, 'Máy rửa bát độc lập Bosch SMS8YCI01E - Series 8', 'Máy rửa bát độc lập Bosch SMS8YCI01E là sản phẩm được người tiêu dùng an tâm về chất lượng, mẫu mã và giá thành cũng vô cùng hợp lý.  Sản phẩm được người tiêu dùng tại châu Âu đánh giá cao bởi thiết kế trẻ trung, hiện đại song cũng không kém phần sang trọng. Thiết kế máy đứng độc lập chắc chắn và cứng cáp , công suất rửa mạnh mẽ với 14 bộ chén bát. Nếu đang tìm kiếm sản phẩm bền bỉ, hiệu suất cao và thân thiện môi trường thì Bosch SMS8YCI01E là lựa chọn hoàn hảo cho bạn.', '2025-10-03 14:51:15', '2025-10-11 19:50:12', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_comments`
--

CREATE TABLE `product_comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `comment` text NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_comments`
--

INSERT INTO `product_comments` (`id`, `product_id`, `customer_id`, `parent_id`, `comment`, `is_approved`, `created_at`, `updated_at`) VALUES
(3, 7, 10, NULL, 'hàng dùng tốt không', 0, '2025-09-24 19:51:47', '2025-09-24 19:51:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_main` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `alt_text`, `sort_order`, `is_main`, `created_at`, `updated_at`) VALUES
(1, 2, 'uploads/products/1756310138_b61028ebe7a9e5dd94e3.jpg', NULL, 0, 0, '2025-08-27 22:55:38', '2025-08-27 22:55:38'),
(11, 2, 'uploads/products/1756395800_39a7839c0078a4e5df5c.jpg', NULL, 0, 0, '2025-08-28 22:43:20', '2025-08-28 22:43:20'),
(15, 4, 'uploads/products/1756917014_4ebb2312171833044189.jpg', NULL, 1, 0, '2025-09-03 23:30:14', '2025-09-03 23:30:14'),
(16, 4, 'uploads/products/1756917014_9af5fec3834f1311e6ee.jpg', NULL, 2, 0, '2025-09-03 23:30:14', '2025-09-03 23:30:14'),
(17, 3, 'uploads/products/1756917029_770ae14a872404a47be1.jpg', NULL, 1, 0, '2025-09-03 23:30:29', '2025-09-03 23:30:29'),
(22, 3, 'uploads/products/1757003772_7c6c25d3a803196dc228.webp', NULL, 2, 0, '2025-09-04 23:36:12', '2025-09-04 23:36:12'),
(23, 6, 'uploads/products/1757615397_6c9051adc266566f3f6c.jpg', NULL, 1, 0, '2025-09-12 01:29:57', '2025-09-12 01:29:57'),
(24, 6, 'uploads/products/1757615548_d62a2fb4380e67f40e7d.jpg', NULL, 2, 0, '2025-09-12 01:32:28', '2025-09-12 01:32:28'),
(25, 7, 'uploads/products/1758031885_e7af560759ce21594a6a.jpg', NULL, 1, 0, '2025-09-16 21:11:25', '2025-09-16 21:11:25'),
(26, 7, 'uploads/products/1758031885_8a485e0ec2e9ea2e0e0e.jpg', NULL, 2, 0, '2025-09-16 21:11:25', '2025-09-16 21:11:25'),
(27, 7, 'uploads/products/1758031885_d837647b715e814ec207.jpg', NULL, 3, 0, '2025-09-16 21:11:25', '2025-09-16 21:11:25'),
(28, 7, 'uploads/products/1758031885_0914ef4d16216def6d95.jpg', NULL, 4, 0, '2025-09-16 21:11:25', '2025-09-16 21:11:25'),
(29, 7, 'uploads/products/1758031885_9e3ae3f09cbf0871f778.jpg', NULL, 5, 0, '2025-09-16 21:11:25', '2025-09-16 21:11:25'),
(30, 8, 'uploads/products/1759142124_aa821faa58ade9758c4c.webp', NULL, 1, 0, '2025-09-29 17:35:24', '2025-09-29 17:35:24'),
(31, 8, 'uploads/products/1759142124_4d21fd95855a0970cf70.webp', NULL, 2, 0, '2025-09-29 17:35:24', '2025-09-29 17:35:24'),
(32, 8, 'uploads/products/1759142124_2dc607ea0c5735d568e0.webp', NULL, 3, 0, '2025-09-29 17:35:24', '2025-09-29 17:35:24'),
(33, 9, 'uploads/products/1759142979_eb53b4bc9bc3b357af22.jpg', NULL, 1, 0, '2025-09-29 17:49:39', '2025-09-29 17:49:39'),
(34, 9, 'uploads/products/1759142979_35b630e9b0b1bdd08a65.jpg', NULL, 2, 0, '2025-09-29 17:49:39', '2025-09-29 17:49:39'),
(35, 9, 'uploads/products/1759142979_e4ca695fcb6fa5b418e1.jpg', NULL, 3, 0, '2025-09-29 17:49:39', '2025-09-29 17:49:39'),
(36, 9, 'uploads/products/1759142979_670883a7596160173d9d.jpg', NULL, 4, 0, '2025-09-29 17:49:39', '2025-09-29 17:49:39'),
(37, 10, 'uploads/products/1759143137_a7cb89c6bf73d4f15b4e.jpg', NULL, 1, 0, '2025-09-29 17:52:17', '2025-09-29 17:52:17'),
(38, 10, 'uploads/products/1759143137_7210056068cb1885a92b.jpg', NULL, 2, 0, '2025-09-29 17:52:17', '2025-09-29 17:52:17'),
(39, 10, 'uploads/products/1759143137_5fffe26e224048866fce.jpg', NULL, 3, 0, '2025-09-29 17:52:17', '2025-09-29 17:52:17'),
(40, 10, 'uploads/products/1759143137_8515a7e33b9b0e349086.jpg', NULL, 4, 0, '2025-09-29 17:52:17', '2025-09-29 17:52:17'),
(41, 11, 'uploads/products/1759159174_972cc1fe95842040fe5f.jpg', NULL, 1, 0, '2025-09-29 22:19:34', '2025-09-29 22:19:34'),
(42, 11, 'uploads/products/1759159174_6502b2a412acc12bbe6d.jpg', NULL, 2, 0, '2025-09-29 22:19:34', '2025-09-29 22:19:34'),
(43, 11, 'uploads/products/1759159174_fad225dcd06bb0a49ee0.jpg', NULL, 3, 0, '2025-09-29 22:19:34', '2025-09-29 22:19:34'),
(44, 12, 'uploads/products/1759477875_f6cca35190817391e2ab.jpg', NULL, 1, 0, '2025-10-03 14:51:15', '2025-10-03 14:51:15'),
(45, 12, 'uploads/products/1759477875_30f5594b028423a13c8b.jpg', NULL, 2, 0, '2025-10-03 14:51:15', '2025-10-03 14:51:15'),
(46, 12, 'uploads/products/1759477875_8feefc9fe10f02f1f4d9.jpg', NULL, 3, 0, '2025-10-03 14:51:15', '2025-10-03 14:51:15');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 1,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `helpful_count` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `product_id`, `customer_id`, `order_id`, `rating`, `title`, `comment`, `is_verified`, `is_approved`, `helpful_count`, `created_at`, `updated_at`) VALUES
(1, 3, 10, 42, 4, 'dùng tốt ', 'hàng dùng rất tốt', 1, 0, 0, '2025-09-24 00:21:03', '2025-09-24 00:21:03'),
(2, 7, 10, 42, 5, 'dùng tốt', 'thật tốt đó ạ', 1, 0, 0, '2025-09-24 00:22:58', '2025-09-24 00:22:58'),
(3, 6, 10, 40, 5, 'hàng quá là tốt ', 'ae lên mua ửng hộ shop', 1, 0, 0, '2025-09-24 14:17:36', '2025-09-24 14:17:36'),
(4, 7, 10, 46, 5, 'hàng khá là chất lượng ', 'ae lên mua ửng hộ shop nha', 1, 0, 0, '2025-09-24 14:18:15', '2025-09-24 14:18:15'),
(5, 12, 12, 75, 5, 'MẶT HÀNG RẤT TỐT VÀ TIỆN LỢI', 'MẶT HÀNG RẤT TỐT VÀ TIỆN LỢI', 1, 0, 0, '2025-10-08 15:19:31', '2025-10-08 15:19:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` int(11) UNSIGNED NOT NULL,
  `url` text NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `url`, `description`) VALUES
(1, 'Dashboard_table', 'Dashboard access'),
(2, 'Table_Group', 'Manage Groups'),
(3, 'Table_Role', 'Thông tin liên quan đến chức vụ'),
(4, 'Table_GroupRole', 'Thông tin phần quyền'),
(5, 'Table_User', 'Bảng quản lý thông tin nhân viên'),
(6, 'Table_Customers', 'Bảng quản lý thông tin khách hàng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shopping_cart`
--

CREATE TABLE `shopping_cart` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(12,0) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `shopping_cart`
--

INSERT INTO `shopping_cart` (`id`, `customer_id`, `product_id`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
(48, 10, 12, 1, 230000, '2025-10-07 14:03:33', '2025-10-07 14:03:33'),
(54, 12, 7, 1, 180000, '2025-10-08 16:05:07', '2025-10-08 16:05:07'),
(55, 12, 12, 1, 230000, '2025-10-08 16:12:13', '2025-10-08 16:19:04'),
(56, 13, 12, 1, 230000, '2025-10-11 19:44:39', '2025-10-11 19:44:39');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `type` enum('in','out','adjustment') NOT NULL DEFAULT 'in',
  `quantity` int(11) NOT NULL,
  `reason` varchar(100) NOT NULL,
  `reference_id` int(10) UNSIGNED DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `product_id`, `type`, `quantity`, `reason`, `reference_id`, `reference_type`, `notes`, `created_by`, `created_at`) VALUES
(1, 10, 'in', 50, 'initial_stock', NULL, NULL, NULL, NULL, '2025-09-29 17:52:17'),
(2, 11, 'in', 1, 'manual_adjustment', NULL, NULL, NULL, NULL, '2025-09-29 22:21:58'),
(3, 12, 'in', 100, 'initial_stock', NULL, NULL, NULL, NULL, '2025-10-03 14:51:15');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `group_id` int(11) UNSIGNED NOT NULL,
  `role` varchar(50) DEFAULT 'user',
  `super_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `group_id`, `role`, `super_admin`, `created_at`) VALUES
(2, 'HÀ Vân ANh', 'nhanvien@gmail.com', '$2y$10$4YuPhFfMs1Milo0CxSjCh.QAL6dGKgeHxEYMUGkq55p8Qv6IRxhIG', 2, 'user', 0, '2025-08-24 08:47:52'),
(5, 'admin', 'admin@example.com', '$2y$10$64hrPmVEZsHqBLggTvp23ur14kA0ej/g/.L.V1dKt7q54/gZ/GtN6', 2, 'user', 1, '2025-08-24 15:57:16'),
(6, 'Hà Hoàng Hiệp', 'quanly@gmail.com', '$2y$10$WlUCa0VokN5dKcvtTnIDOerctEdamW.n0lErltn00bbVQvALnHRUC', 2, 'user', 0, '2025-08-25 09:03:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `wishlist`
--

INSERT INTO `wishlist` (`id`, `customer_id`, `product_id`, `created_at`) VALUES
(3, 10, 3, '2025-09-15 00:09:20'),
(12, 10, 6, '2025-10-02 23:18:21'),
(14, 10, 7, '2025-10-02 23:40:08'),
(15, 10, 10, '2025-10-02 23:40:14'),
(16, 10, 8, '2025-10-02 23:40:21'),
(17, 12, 2, '2025-10-08 15:12:02'),
(18, 12, 4, '2025-10-08 15:12:04');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blog_comments_post_id_foreign` (`post_id`),
  ADD KEY `blog_comments_customer_id_foreign` (`customer_id`),
  ADD KEY `blog_comments_parent_id_foreign` (`parent_id`);

--
-- Chỉ mục cho bảng `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`);

--
-- Chỉ mục cho bảng `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `discount_coupons`
--
ALTER TABLE `discount_coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `discount_coupon_products`
--
ALTER TABLE `discount_coupon_products`
  ADD PRIMARY KEY (`coupon_id`,`product_id`),
  ADD KEY `discount_coupon_products_product_id_foreign` (`product_id`);

--
-- Chỉ mục cho bảng `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `group_roles`
--
ALTER TABLE `group_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_roles_group_id_foreign` (`group_id`),
  ADD KEY `group_roles_role_id_foreign` (`role_id`);

--
-- Chỉ mục cho bảng `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `invoices_customer_id_foreign` (`customer_id`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `orders_customer_id_foreign` (`customer_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`);

--
-- Chỉ mục cho bảng `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `payment_transactions_order_id_foreign` (`order_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_brand_id_foreign` (`brand_id`);

--
-- Chỉ mục cho bảng `product_comments`
--
ALTER TABLE `product_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_comments_product_id_foreign` (`product_id`),
  ADD KEY `product_comments_customer_id_foreign` (`customer_id`),
  ADD KEY `product_comments_parent_id_foreign` (`parent_id`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_images_product_id_foreign` (`product_id`);

--
-- Chỉ mục cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id_customer_id_order_id` (`product_id`,`customer_id`,`order_id`),
  ADD KEY `product_reviews_customer_id_foreign` (`customer_id`),
  ADD KEY `product_reviews_order_id_foreign` (`order_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_id_product_id` (`customer_id`,`product_id`),
  ADD KEY `shopping_cart_product_id_foreign` (`product_id`);

--
-- Chỉ mục cho bảng `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_movements_product_id_foreign` (`product_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_group_id_foreign` (`group_id`);

--
-- Chỉ mục cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_id_product_id` (`customer_id`,`product_id`),
  ADD KEY `wishlist_product_id_foreign` (`product_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `blog_comments`
--
ALTER TABLE `blog_comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `discount_coupons`
--
ALTER TABLE `discount_coupons`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `group_roles`
--
ALTER TABLE `group_roles`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT cho bảng `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT cho bảng `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `product_comments`
--
ALTER TABLE `product_comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `shopping_cart`
--
ALTER TABLE `shopping_cart`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT cho bảng `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD CONSTRAINT `blog_comments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `blog_comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `blog_comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `blog_comments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `discount_coupon_products`
--
ALTER TABLE `discount_coupon_products`
  ADD CONSTRAINT `discount_coupon_products_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `discount_coupons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `discount_coupon_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `group_roles`
--
ALTER TABLE `group_roles`
  ADD CONSTRAINT `group_roles_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `group_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `invoices_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `payment_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `product_comments`
--
ALTER TABLE `product_comments`
  ADD CONSTRAINT `product_comments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `product_comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_comments_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_reviews_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD CONSTRAINT `shopping_cart_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `shopping_cart_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `wishlist_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
