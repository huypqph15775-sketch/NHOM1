-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 31, 2025 lúc 07:47 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `smartphone`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `detail` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `admin_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `admin_user_name` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  `admin_img` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `admin_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `admin_contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `admin_level` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `role_id` int(11) DEFAULT 4
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `admin_email`, `admin_user_name`, `admin_password`, `admin_img`, `admin_address`, `admin_contact`, `admin_level`, `role_id`) VALUES
(1, 'Admin', 'tiep184@gmail.com', 'admin', '123456', 'avatar1.jpg', 'Hanoi', '0123456789', 'Quản lý', 4),
(4, 'nv ban hang', 'huay@gmail.com', 'nvbanhang', '123456', 'anh.png', 'abc', '0987565234', 'Quản lý', 3),
(5, 'nv kho', 'nv@gmail.com', 'nvkho', '123456', '', 'abc', '098758965234', 'Nhân viên', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(10) NOT NULL,
  `product_id` int(10) NOT NULL,
  `color` varchar(255) NOT NULL,
  `quantity` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`cart_id`, `customer_id`, `product_id`, `color`, `quantity`) VALUES
(111, 10, 30, 'Đỏ', 1),
(114, 11, 18, 'Trắng', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cartegory`
--

CREATE TABLE `cartegory` (
  `cartegory_id` int(10) NOT NULL,
  `cartegory_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `cartegory_img` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `cartegory_status` varchar(20) DEFAULT 'visible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cartegory`
--

INSERT INTO `cartegory` (`cartegory_id`, `cartegory_name`, `cartegory_img`, `cartegory_status`) VALUES
(1, 'SamSung', 'logo-samsung.png', 'visible'),
(2, 'Iphone', 'logo-iphone.png', 'visible'),
(3, 'Nokia', 'logo-nokia.jpg', 'visible'),
(5, 'Vivo', 'logo-vivo.png', 'visible'),
(6, 'Xiaomi', 'logo-xiaomi.png', 'visible'),
(7, 'Realme', 'logo-realme.png', 'visible'),
(8, 'Oppo', 'logo-oppo.jpg', 'visible'),
(9, 'Apple', '', 'visible'),
(10, ' Gokaiger', 'logo-ioroi.png', 'visible');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) DEFAULT NULL,
  `sender_name` varchar(100) DEFAULT NULL,
  `sender_email` varchar(100) DEFAULT NULL,
  `message` longtext DEFAULT NULL,
  `sender_type` varchar(50) DEFAULT 'customer',
  `admin_reply` longtext DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `is_read` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `conversation_id`, `sender_name`, `sender_email`, `message`, `sender_type`, `admin_reply`, `status`, `is_read`, `created_at`, `updated_at`) VALUES
(26, 11, 'con khi', 'quaduahau@gmail.com', 'cc', 'customer', NULL, 'pending', 1, '2025-12-31 14:14:03', '2025-12-31 14:14:08'),
(27, 11, 'SmartPhoneStore Bot', 'bot@smartphonestore.com', 'Cảm ơn câu hỏi của bạn! 😊 Chúng tôi sẽ hỗ trợ bạn sớm. Vui lòng cung cấp thêm thông tin nếu cần thiết hoặc liên hệ qua số 1900.8198 để được hỗ trợ nhanh hơn!', 'admin', NULL, 'pending', 1, '2025-12-31 14:14:03', '2025-12-31 14:14:03'),
(28, 11, 'Admin', 'admin@smartphonestore.com', 'há', 'admin', NULL, 'pending', 1, '2025-12-31 14:14:13', '2025-12-31 14:14:13');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT 5,
  `content` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `customer_sex` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `customer_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `customer_phone` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `customer_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `customer_user_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `customer_password` varchar(255) NOT NULL,
  `customer_img` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `account_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `role_id` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `customer`
--

INSERT INTO `customer` (`customer_id`, `customer_name`, `customer_sex`, `customer_email`, `customer_phone`, `customer_address`, `customer_user_name`, `customer_password`, `customer_img`, `account_status`, `role_id`) VALUES
(1, 'Nguyễn Huy Vĩnh', 'Nam', 'nguyenhuyvinh@gmail.com', '0377238169', 'Hải Phòng', 'vinh', '123456', 'avatar1.jpg', 'Locked', 1),
(2, 'Phạm Quang Huy', 'Nam', 'phamhuy@gmail.com', '0123456789', 'Ninh Bình', 'huy', '123456', '1.png', 'Locked', 1),
(5, 'Vũ Ngọc Vỹ', 'Nam', 'vyez184@gmail.com', '+84363811478', 'ha noi', 'vuvy', '123456', '', 'Active', 1),
(6, 'abc', 'Nam', 'ldn@ldn.vn', '12345678', 'hanoi', 'abc', '123456', '', 'Active', 1),
(7, 'abcd', 'Nữ', 'ldnd@ldn.vn', '12345678', 'hanoi', 'abcd', '123456', '', 'Active', 1),
(8, 'Lê Thành Đạt', 'Nam', 'dat123@gmail.com', '123232132', 'hanoi', 'dat', '123456', '', 'Active', 1),
(9, 'Đoàn Văn Sáng', 'Nam', 'sang123@gmail.com', '9348234', 'hanoi', 'sang', '$2y$12$90tpeGDxJ1XVF4O7wUTM..rpmMx4mWzLr6FjDEgEOUcBd0IMLYvxK', '', 'Active', 1),
(10, 'khi dot', 'Nam', 'andang212ma@gmail.com', '0909090544', 'Nghệ An, Cửa Lò, abc dfsdfsd', 'khidot', '$2y$12$90tpeGDxJ1XVF4O7wUTM..rpmMx4mWzLr6FjDEgEOUcBd0IMLYvxK', 'customer_1765970567_2b44d619d2.jpg', 'Active', 1),
(11, 'Nguyễn Xuân Đạt', 'Nam', 'bigchive9@gmail.com', '0943567543', 'Hà Nội, Cầu Giấy, số 27', 'test', '$2y$12$w5VhgY54rbj8nUCVam/MZ.CH8Xlxfxu6fSkhZ8inkgzKS2sEVVsX6', 'customer_1766627092_6826cea71b.jpg', 'Active', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer_addresses`
--

CREATE TABLE `customer_addresses` (
  `address_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `receiver_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address_detail` text DEFAULT NULL,
  `is_default` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer_orders`
--

CREATE TABLE `customer_orders` (
  `order_id` int(10) NOT NULL COMMENT 'mã order',
  `customer_id` int(10) NOT NULL,
  `total_price` int(50) NOT NULL,
  `order_date` date NOT NULL,
  `status` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `tracking_code` varchar(255) DEFAULT NULL,
  `order_no` int(10) NOT NULL,
  `receiver` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `receiver_sex` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `receiver_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `delivery_location` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `payment_type` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `received_date` date NOT NULL,
  `voucher_code` varchar(50) DEFAULT NULL,
  `discount_value` int(11) DEFAULT 0,
  `total_after_discount` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `customer_orders`
--

INSERT INTO `customer_orders` (`order_id`, `customer_id`, `total_price`, `order_date`, `status`, `tracking_code`, `order_no`, `receiver`, `receiver_sex`, `receiver_phone`, `delivery_location`, `payment_type`, `received_date`, `voucher_code`, `discount_value`, `total_after_discount`) VALUES
(77, 11, 218700, '2025-12-31', 'Đã giao', 'VN176719037677', 1462985355, 'Nguyễn Xuân Đạt', '', '0943567543', '', 'Thanh toán tiền mặt khi nhận hàng', '2025-12-31', 'tiep99', 216513, 2187),
(78, 11, 119900, '2025-12-31', 'Đã giao', 'VN176719055578', 869994421, 'Nguyễn Xuân Đạt', '', '0943567543', 'hanoi', 'Thanh toán tiền mặt khi nhận hàng', '2025-12-31', 'tiep99', 118701, 1199),
(79, 11, 200000, '2025-12-31', 'Đang chờ', NULL, 2076196113, 'Nguyễn Xuân Đạt', '', '0943567543', '', 'Thanh toán tiền mặt khi nhận hàng', '0000-00-00', 'tiep99', 198000, 2000),
(80, 9, 4944000, '2026-01-01', 'Đang chờ', NULL, 1378032516, 'Đoàn Văn Sáng', '', '9348234', '', 'Thanh toán tiền mặt khi nhận hàng', '0000-00-00', 'tiepdzz', 2472000, 2472000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer_order_products`
--

CREATE TABLE `customer_order_products` (
  `order_id` int(10) NOT NULL,
  `product_id` int(10) NOT NULL,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `quantity` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `customer_order_products`
--

INSERT INTO `customer_order_products` (`order_id`, `product_id`, `color`, `quantity`) VALUES
(77, 25, 'Xám', 1),
(77, 28, 'Xanh lam', 1),
(78, 25, 'Xám', 1),
(79, 30, 'Đỏ', 1),
(80, 28, 'Xanh lam', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `favorite_product`
--

CREATE TABLE `favorite_product` (
  `customer_id` int(10) NOT NULL,
  `product_id` int(10) NOT NULL,
  `product_color_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `flash_sale`
--

CREATE TABLE `flash_sale` (
  `flash_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `sale_price` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `momo`
--

CREATE TABLE `momo` (
  `id_momo` int(11) NOT NULL,
  `partner_code` varchar(50) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` varchar(50) NOT NULL,
  `order_info` varchar(100) NOT NULL,
  `order_type` varchar(50) NOT NULL,
  `trans_id` int(11) NOT NULL,
  `pay_type` varchar(50) NOT NULL,
  `code_cart` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `news`
--

CREATE TABLE `news` (
  `news_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `content` longtext NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `category` varchar(100) DEFAULT 'Tin tức',
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `news`
--

INSERT INTO `news` (`news_id`, `title`, `slug`, `content`, `thumbnail`, `author_id`, `category`, `status`, `views`, `created_at`, `updated_at`) VALUES
(1, 'iPhone 15 Pro Max - Công nghệ tiên phong 2024', 'iphone-15-pro-max', '<p>Apple vừa ra mắt iPhone 15 Pro Max với những cải tiến đáng kể...</p><p>Chip A17 Pro mạnh mẽ hơn 40%</p>', NULL, 1, 'Tin tức', 'published', 8, '2025-12-07 12:00:52', '2025-12-31 18:39:13'),
(2, 'Cập nhật Thương mại điện tử - Bài số 1', 'c-p-nh-t-th-ng-m-i-i-n-t---b-i-s-1-6942a8444f74d', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_2.svg', 1, 'Tin tức', 'published', 37, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(3, 'Đánh giá Thương mại điện tử - Bài số 2', 'nh-gi-th-ng-m-i-i-n-t---b-i-s-2-6942a8445315b', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm..', 'news_3.svg', 1, 'Tin tức', 'published', 3, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(4, 'Cập nhật Ứng dụng - Bài số 3', 'c-p-nh-t-ng-d-ng---b-i-s-3-6942a844546b1', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_4.svg', 1, 'Tin tức', 'published', 16, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(5, 'Cập nhật Thương mại điện tử - Bài số 4', 'c-p-nh-t-th-ng-m-i-i-n-t---b-i-s-4-6942a8445703d', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_5.svg', 1, 'Tin tức', 'published', 41, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(6, 'Hướng dẫn Android - Bài số 5', 'h-ng-d-n-android---b-i-s-5-6942a84458e35', 'Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_6.svg', 1, 'Tin tức', 'published', 10, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(7, 'Tổng hợp Công nghệ AI - Bài số 6', 't-ng-h-p-c-ng-ngh-ai---b-i-s-6-6942a8445c2b2', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_7.svg', 1, 'Tin tức', 'published', 4, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(8, 'So sánh Công nghệ AI - Bài số 7', 'so-s-nh-c-ng-ngh-ai---b-i-s-7-6942a8445fe9c', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_8.svg', 1, 'Tin tức', 'published', 22, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(9, 'Cập nhật Công nghệ AI - Bài số 8', 'c-p-nh-t-c-ng-ngh-ai---b-i-s-8-6942a844616a6', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng.', 'news_9.svg', 1, 'Tin tức', 'published', 5, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(10, 'So sánh Smartphone - Bài số 9', 'so-s-nh-smartphone---b-i-s-9-6942a844637cf', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng.', 'news_10.svg', 1, 'Tin tức', 'published', 46, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(11, 'Mẹo Thương mại điện tử - Bài số 10', 'm-o-th-ng-m-i-i-n-t---b-i-s-10-6942a8446484b', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_11.svg', 1, 'Tin tức', 'published', 23, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(12, 'Chia sẻ Smartphone - Bài số 11', 'chia-s-smartphone---b-i-s-11-6942a84465cb8', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_12.svg', 1, 'Tin tức', 'published', 11, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(13, 'Hot Màn hình - Bài số 12', 'hot-m-n-h-nh---b-i-s-12-6942a8446895a', 'Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_13.svg', 1, 'Tin tức', 'published', 1, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(14, 'Hot iPhone 15 - Bài số 13', 'hot-iphone-15---b-i-s-13-6942a84469cf4', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_14.svg', 1, 'Tin tức', 'published', 0, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(15, 'Chia sẻ Ứng dụng - Bài số 14', 'chia-s-ng-d-ng---b-i-s-14-6942a8446bd9b', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_15.svg', 1, 'Tin tức', 'published', 0, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(16, 'Chia sẻ Android - Bài số 15', 'chia-s-android---b-i-s-15-6942a8446cd98', 'Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_16.svg', 1, 'Tin tức', 'published', 13, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(17, 'Hot Android - Bài số 16', 'hot-android---b-i-s-16-6942a8446e86a', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_17.svg', 1, 'Tin tức', 'published', 39, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(18, 'Tổng hợp App mới - Bài số 17', 't-ng-h-p-app-m-i---b-i-s-17-6942a8446fca4', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_18.svg', 1, 'Tin tức', 'published', 15, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(19, 'Đánh giá App mới - Bài số 18', 'nh-gi-app-m-i---b-i-s-18-6942a84470c35', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng.', 'news_19.svg', 1, 'Tin tức', 'published', 47, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(20, 'Mới Màn hình - Bài số 19', 'm-i-m-n-h-nh---b-i-s-19-6942a84471c19', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm..', 'news_20.svg', 1, 'Tin tức', 'published', 45, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(21, 'Đánh giá App mới - Bài số 20', 'nh-gi-app-m-i---b-i-s-20-6942a844739ad', 'Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm..', 'news_21.svg', 1, 'Tin tức', 'published', 37, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(22, 'Hướng dẫn iPhone 15 - Bài số 21', 'h-ng-d-n-iphone-15---b-i-s-21-6942a84474a7e', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng.', 'news_22.svg', 1, 'Tin tức', 'published', 45, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(23, 'Hot Smartphone - Bài số 22', 'hot-smartphone---b-i-s-22-6942a844758aa', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_23.svg', 1, 'Tin tức', 'published', 41, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(24, 'Bí quyết Smartphone - Bài số 23', 'b-quy-t-smartphone---b-i-s-23-6942a84477ca4', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng.', 'news_24.svg', 1, 'Tin tức', 'published', 19, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(25, 'Mới Ứng dụng - Bài số 24', 'm-i-ng-d-ng---b-i-s-24-6942a84478d1c', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_25.svg', 1, 'Tin tức', 'published', 34, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(26, 'Mẹo Màn hình - Bài số 25', 'm-o-m-n-h-nh---b-i-s-25-6942a84479d11', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng.', 'news_26.svg', 1, 'Tin tức', 'published', 19, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(27, 'Cập nhật Smartphone - Bài số 26', 'c-p-nh-t-smartphone---b-i-s-26-6942a8447b93b', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_27.svg', 1, 'Tin tức', 'published', 14, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(28, 'Bí quyết iPhone 15 - Bài số 27', 'b-quy-t-iphone-15---b-i-s-27-6942a8447c730', 'Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_28.svg', 1, 'Tin tức', 'published', 17, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(29, 'Chia sẻ Camera - Bài số 28', 'chia-s-camera---b-i-s-28-6942a8447da23', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng.', 'news_29.svg', 1, 'Tin tức', 'published', 26, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(30, 'Cập nhật Smartphone - Bài số 29', 'c-p-nh-t-smartphone---b-i-s-29-6942a8447fd11', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_30.svg', 1, 'Tin tức', 'published', 16, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(31, 'Chia sẻ Công nghệ AI - Bài số 30', 'chia-s-c-ng-ngh-ai---b-i-s-30-6942a84480de6', 'Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng.', 'news_31.svg', 1, 'Tin tức', 'published', 35, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(32, 'Mới Camera - Bài số 31', 'm-i-camera---b-i-s-31-6942a84482ac4', 'Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng.', 'news_32.svg', 1, 'Tin tức', 'published', 20, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(33, 'Đánh giá Công nghệ AI - Bài số 32', 'nh-gi-c-ng-ngh-ai---b-i-s-32-6942a844846ed', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_33.svg', 1, 'Tin tức', 'published', 39, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(34, 'Mới Smartphone - Bài số 33', 'm-i-smartphone---b-i-s-33-6942a8448553e', 'Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_34.svg', 1, 'Tin tức', 'published', 15, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(35, 'Bí quyết Smartphone - Bài số 34', 'b-quy-t-smartphone---b-i-s-34-6942a8448662f', 'Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_35.svg', 1, 'Tin tức', 'published', 43, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(36, 'Hướng dẫn Camera - Bài số 35', 'h-ng-d-n-camera---b-i-s-35-6942a844886d0', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_36.svg', 1, 'Tin tức', 'published', 5, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(37, 'So sánh Công nghệ AI - Bài số 36', 'so-s-nh-c-ng-ngh-ai---b-i-s-36-6942a844894ff', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_37.svg', 1, 'Tin tức', 'published', 47, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(38, 'Mới Camera - Bài số 37', 'm-i-camera---b-i-s-37-6942a8448a622', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm..', 'news_38.svg', 1, 'Tin tức', 'published', 38, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(39, 'Đánh giá App mới - Bài số 38', 'nh-gi-app-m-i---b-i-s-38-6942a8448bd62', 'Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng.', 'news_39.svg', 1, 'Tin tức', 'published', 12, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(40, 'Đánh giá Công nghệ AI - Bài số 39', 'nh-gi-c-ng-ngh-ai---b-i-s-39-6942a8448cd61', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_40.svg', 1, 'Tin tức', 'published', 17, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(41, 'Cập nhật Thương mại điện tử - Bài số 40', 'c-p-nh-t-th-ng-m-i-i-n-t---b-i-s-40-6942a8448d950', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm..', 'news_41.svg', 1, 'Tin tức', 'published', 12, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(42, 'Mới Smartphone - Bài số 41', 'm-i-smartphone---b-i-s-41-6942a8448e8b3', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_42.svg', 1, 'Tin tức', 'published', 32, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(43, 'Hướng dẫn Màn hình - Bài số 42', 'h-ng-d-n-m-n-h-nh---b-i-s-42-6942a8449036a', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_43.svg', 1, 'Tin tức', 'published', 18, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(44, 'Chia sẻ Công nghệ AI - Bài số 43', 'chia-s-c-ng-ngh-ai---b-i-s-43-6942a84491198', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng.', 'news_44.svg', 1, 'Tin tức', 'published', 25, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(45, 'Hot Ứng dụng - Bài số 44', 'hot-ng-d-ng---b-i-s-44-6942a84491f0c', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_45.svg', 1, 'Tin tức', 'published', 3, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(46, 'Cập nhật Thương mại điện tử - Bài số 45', 'c-p-nh-t-th-ng-m-i-i-n-t---b-i-s-45-6942a84493fd5', 'Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_46.svg', 1, 'Tin tức', 'published', 32, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(47, 'Đánh giá Màn hình - Bài số 46', 'nh-gi-m-n-h-nh---b-i-s-46-6942a84494c83', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_47.svg', 1, 'Tin tức', 'published', 37, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(48, 'Cập nhật Thương mại điện tử - Bài số 47', 'c-p-nh-t-th-ng-m-i-i-n-t---b-i-s-47-6942a84495876', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_48.svg', 1, 'Tin tức', 'published', 21, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(49, 'So sánh iPhone 15 - Bài số 48', 'so-s-nh-iphone-15---b-i-s-48-6942a844964e4', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng.', 'news_49.svg', 1, 'Tin tức', 'published', 15, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(50, 'Đánh giá App mới - Bài số 49', 'nh-gi-app-m-i---b-i-s-49-6942a844987d7', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_50.svg', 1, 'Tin tức', 'published', 5, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(51, 'Hot Camera - Bài số 50', 'hot-camera---b-i-s-50', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_51.svg', 1, 'Tin tức', 'published', 12, '2025-12-17 12:55:32', '2025-12-31 18:39:13'),
(52, 'Tổng hợp Màn hình - Bài số 1', 't-ng-h-p-m-n-h-nh---b-i-s-1', 'Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_52.svg', 1, 'Tin tức', 'published', 14, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(53, 'Cập nhật App mới - Bài số 2', 'c-p-nh-t-app-m-i---b-i-s-2-6942a84870b2d', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_53.svg', 1, 'Tin tức', 'published', 43, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(54, 'Hot Pin - Bài số 3', 'hot-pin---b-i-s-3-6942a84873222', 'Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng.', 'news_54.svg', 1, 'Tin tức', 'published', 1, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(55, 'Bí quyết Ứng dụng - Bài số 4', 'b-quy-t-ng-d-ng---b-i-s-4-6942a848748df', 'Công nghệ đang thay đổi nhanh chóng. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm..', 'news_55.svg', 1, 'Tin tức', 'published', 40, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(56, 'Bí quyết Ứng dụng - Bài số 5', 'b-quy-t-ng-d-ng---b-i-s-5-6942a84875fbd', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng.', 'news_56.svg', 1, 'Tin tức', 'published', 25, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(57, 'Hướng dẫn Camera - Bài số 6', 'h-ng-d-n-camera---b-i-s-6-6942a8487847c', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_57.svg', 1, 'Tin tức', 'published', 44, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(58, 'Hướng dẫn Android - Bài số 7', 'h-ng-d-n-android---b-i-s-7-6942a84879e63', 'Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_58.svg', 1, 'Tin tức', 'published', 21, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(59, 'Mẹo Pin - Bài số 8', 'm-o-pin---b-i-s-8-6942a8487c675', 'Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_59.svg', 1, 'Tin tức', 'published', 31, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(60, 'Mẹo Ứng dụng - Bài số 9', 'm-o-ng-d-ng---b-i-s-9-6942a8487da78', 'Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_60.svg', 1, 'Tin tức', 'published', 3, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(61, 'Hot iPhone 15 - Bài số 10', 'hot-iphone-15---b-i-s-10-6942a8488046b', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Công nghệ đang thay đổi nhanh chóng. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm..', 'news_61.svg', 1, 'Tin tức', 'published', 41, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(62, 'Đánh giá Camera - Bài số 11', 'nh-gi-camera---b-i-s-11-6942a848816b4', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm..', 'news_62.svg', 1, 'Tin tức', 'published', 34, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(63, 'Hot Thương mại điện tử - Bài số 12', 'hot-th-ng-m-i-i-n-t---b-i-s-12-6942a84883cfe', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_63.svg', 1, 'Tin tức', 'published', 26, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(64, 'Đánh giá Thương mại điện tử - Bài số 13', 'nh-gi-th-ng-m-i-i-n-t---b-i-s-13-6942a84884c5e', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_64.svg', 1, 'Tin tức', 'published', 9, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(65, 'Tổng hợp Pin - Bài số 14', 't-ng-h-p-pin---b-i-s-14-6942a84885bf9', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng.', 'news_65.svg', 1, 'Tin tức', 'published', 35, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(66, 'Mới App mới - Bài số 15', 'm-i-app-m-i---b-i-s-15-6942a84888052', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_66.svg', 1, 'Tin tức', 'published', 6, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(67, 'Mẹo Pin - Bài số 16', 'm-o-pin---b-i-s-16-6942a8488902f', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_67.svg', 1, 'Tin tức', 'published', 4, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(68, 'So sánh Ứng dụng - Bài số 17', 'so-s-nh-ng-d-ng---b-i-s-17-6942a84889dab', 'Công nghệ đang thay đổi nhanh chóng. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng.', 'news_68.svg', 1, 'Tin tức', 'published', 24, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(69, 'Mẹo Thương mại điện tử - Bài số 18', 'm-o-th-ng-m-i-i-n-t---b-i-s-18-6942a8488c0a2', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Công nghệ đang thay đổi nhanh chóng. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_69.svg', 1, 'Tin tức', 'published', 13, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(70, 'Đánh giá Android - Bài số 19', 'nh-gi-android---b-i-s-19-6942a8488ceb6', 'Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng.', 'news_70.svg', 1, 'Tin tức', 'published', 13, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(71, 'Mẹo Pin - Bài số 20', 'm-o-pin---b-i-s-20-6942a8488dc4d', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng.', 'news_71.svg', 1, 'Tin tức', 'published', 46, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(72, 'Đánh giá Pin - Bài số 21', 'nh-gi-pin---b-i-s-21-6942a8488fe5d', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_72.svg', 1, 'Tin tức', 'published', 24, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(73, 'Chia sẻ Ứng dụng - Bài số 22', 'chia-s-ng-d-ng---b-i-s-22-6942a84890f0a', 'Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng.', 'news_73.svg', 1, 'Tin tức', 'published', 3, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(74, 'Đánh giá Ứng dụng - Bài số 23', 'nh-gi-ng-d-ng---b-i-s-23-6942a84891d69', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_74.svg', 1, 'Tin tức', 'published', 24, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(75, 'So sánh Thương mại điện tử - Bài số 24', 'so-s-nh-th-ng-m-i-i-n-t---b-i-s-24-6942a84893d0d', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_75.svg', 1, 'Tin tức', 'published', 46, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(76, 'Tổng hợp Smartphone - Bài số 25', 't-ng-h-p-smartphone---b-i-s-25-6942a84894e16', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_76.svg', 1, 'Tin tức', 'published', 3, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(77, 'So sánh Ứng dụng - Bài số 26', 'so-s-nh-ng-d-ng---b-i-s-26-6942a84895cae', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_77.svg', 1, 'Tin tức', 'published', 13, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(78, 'Chia sẻ iPhone 15 - Bài số 27', 'chia-s-iphone-15---b-i-s-27-6942a84897c71', 'Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_78.svg', 1, 'Tin tức', 'published', 7, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(79, 'Tổng hợp Màn hình - Bài số 28', 't-ng-h-p-m-n-h-nh---b-i-s-28-6942a84898fda', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm..', 'news_79.svg', 1, 'Tin tức', 'published', 16, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(80, 'Mới Camera - Bài số 29', 'm-i-camera---b-i-s-29-6942a84899f35', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Công nghệ đang thay đổi nhanh chóng. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng.', 'news_80.svg', 1, 'Tin tức', 'published', 33, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(81, 'So sánh App mới - Bài số 30', 'so-s-nh-app-m-i---b-i-s-30-6942a8489bc46', 'Công nghệ đang thay đổi nhanh chóng. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm..', 'news_81.svg', 1, 'Tin tức', 'published', 30, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(82, 'Mới Smartphone - Bài số 31', 'm-i-smartphone---b-i-s-31-6942a8489cb5f', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_82.svg', 1, 'Tin tức', 'published', 2, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(83, 'Cập nhật Ứng dụng - Bài số 32', 'c-p-nh-t-ng-d-ng---b-i-s-32-6942a8489d8b0', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_83.svg', 1, 'Tin tức', 'published', 48, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(84, 'Mới Màn hình - Bài số 33', 'm-i-m-n-h-nh---b-i-s-33-6942a8489e61d', 'Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Công nghệ đang thay đổi nhanh chóng. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm..', 'news_84.svg', 1, 'Tin tức', 'published', 44, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(85, 'So sánh Android - Bài số 34', 'so-s-nh-android---b-i-s-34-6942a848a0864', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm..', 'news_85.svg', 1, 'Tin tức', 'published', 25, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(86, 'Mới Camera - Bài số 35', 'm-i-camera---b-i-s-35-6942a848a1942', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_86.svg', 1, 'Tin tức', 'published', 35, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(87, 'Đánh giá Thương mại điện tử - Bài số 36', 'nh-gi-th-ng-m-i-i-n-t---b-i-s-36-6942a848a2aaf', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_87.svg', 1, 'Tin tức', 'published', 38, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(88, 'Đánh giá Công nghệ AI - Bài số 37', 'nh-gi-c-ng-ngh-ai---b-i-s-37-6942a848a4456', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Công nghệ đang thay đổi nhanh chóng.', 'news_88.svg', 1, 'Tin tức', 'published', 11, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(89, 'Tổng hợp iPhone 15 - Bài số 38', 't-ng-h-p-iphone-15---b-i-s-38-6942a848a50fc', 'Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_89.svg', 1, 'Tin tức', 'published', 27, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(90, 'Bí quyết Android - Bài số 39', 'b-quy-t-android---b-i-s-39-6942a848a5d68', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_90.svg', 1, 'Tin tức', 'published', 49, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(91, 'Bí quyết Android - Bài số 40', 'b-quy-t-android---b-i-s-40-6942a848a6bd5', 'Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_91.svg', 1, 'Tin tức', 'published', 30, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(92, 'Bí quyết Smartphone - Bài số 41', 'b-quy-t-smartphone---b-i-s-41-6942a848a87eb', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng. Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_92.svg', 1, 'Tin tức', 'published', 40, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(93, 'So sánh Pin - Bài số 42', 'so-s-nh-pin---b-i-s-42-6942a848a96b7', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm..', 'news_93.svg', 1, 'Tin tức', 'published', 16, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(94, 'Tổng hợp Thương mại điện tử - Bài số 43', 't-ng-h-p-th-ng-m-i-i-n-t---b-i-s-43-6942a848aa629', 'Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_94.svg', 1, 'Tin tức', 'published', 39, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(95, 'Mẹo Pin - Bài số 44', 'm-o-pin---b-i-s-44-6942a848abf4d', 'Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm..', 'news_95.svg', 1, 'Tin tức', 'published', 21, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(96, 'Bí quyết Camera - Bài số 45', 'b-quy-t-camera---b-i-s-45-6942a848acaff', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_96.svg', 1, 'Tin tức', 'published', 28, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(97, 'So sánh Pin - Bài số 46', 'so-s-nh-pin---b-i-s-46-6942a848ad701', 'Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_97.svg', 1, 'Tin tức', 'published', 35, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(98, 'Cập nhật Android - Bài số 47', 'c-p-nh-t-android---b-i-s-47-6942a848ae3f5', 'Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_98.svg', 1, 'Tin tức', 'published', 26, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(99, 'Tổng hợp Công nghệ AI - Bài số 48', 't-ng-h-p-c-ng-ngh-ai---b-i-s-48-6942a848b05ea', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định.', 'news_99.svg', 1, 'Tin tức', 'published', 48, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(100, 'Tổng hợp Công nghệ AI - Bài số 49', 't-ng-h-p-c-ng-ngh-ai---b-i-s-49-6942a848b1456', 'Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm..', 'news_100.svg', 1, 'Tin tức', 'published', 3, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(101, 'Chia sẻ App mới - Bài số 50', 'chia-s-app-m-i---b-i-s-50-6942a848b2152', 'Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_101.svg', 1, 'Tin tức', 'published', 34, '2025-12-17 12:55:36', '2025-12-31 18:39:13'),
(102, 'Đánh giá Công nghệ AI - Bài số 1', '-nh-gi-c-ng-ngh-ai---b-i-s-1', 'Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Thiết bị mới ra mắt mang nhiều tính năng hữu ích.', 'news_102.svg', 1, 'Tin tức', 'published', 9, '2025-12-17 12:56:07', '2025-12-31 18:39:13'),
(103, 'So sánh Thương mại điện tử - Bài số 2', 'so-s-nh-th-ng-m-i-i-n-t---b-i-s-2', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_103.svg', 1, 'Tin tức', 'published', 21, '2025-12-17 12:56:07', '2025-12-31 18:39:13'),
(104, 'Hướng dẫn Camera - Bài số 3', 'h-ng-d-n-camera---b-i-s-3', 'Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng.', 'news_104.svg', 1, 'Tin tức', 'published', 21, '2025-12-17 12:56:07', '2025-12-31 18:39:13'),
(105, 'Mới App mới - Bài số 4', 'm-i-app-m-i---b-i-s-4', 'Công nghệ đang thay đổi nhanh chóng. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm..', 'news_105.svg', 1, 'Tin tức', 'published', 6, '2025-12-17 12:56:07', '2025-12-31 18:39:13'),
(106, 'So sánh Smartphone - Bài số 5', 'so-s-nh-smartphone---b-i-s-5', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_106.svg', 1, 'Tin tức', 'published', 17, '2025-12-17 12:56:07', '2025-12-31 18:39:13'),
(107, 'Hướng dẫn Pin - Bài số 6', 'h-ng-d-n-pin---b-i-s-6', 'Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Công nghệ đang thay đổi nhanh chóng. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Công nghệ đang thay đổi nhanh chóng.', 'news_107.svg', 1, 'Tin tức', 'published', 7, '2025-12-17 12:56:07', '2025-12-31 18:39:13'),
(108, 'So sánh Ứng dụng - Bài số 7', 'so-s-nh-ng-d-ng---b-i-s-7', 'Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Công nghệ đang thay đổi nhanh chóng.', 'news_108.svg', 1, 'Tin tức', 'published', 32, '2025-12-17 12:56:07', '2025-12-31 18:39:13'),
(109, 'Hướng dẫn Pin - Bài số 8', 'h-ng-d-n-pin---b-i-s-8', 'Công nghệ đang thay đổi nhanh chóng. Người dùng nên cân nhắc nhu cầu trước khi mua. Công nghệ đang thay đổi nhanh chóng. Đánh giá chi tiết sẽ giúp bạn đưa ra quyết định. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_109.svg', 1, 'Tin tức', 'published', 7, '2025-12-17 12:56:07', '2025-12-31 18:39:13');
INSERT INTO `news` (`news_id`, `title`, `slug`, `content`, `thumbnail`, `author_id`, `category`, `status`, `views`, `created_at`, `updated_at`) VALUES
(110, 'Cập nhật Pin - Bài số 9', 'c-p-nh-t-pin---b-i-s-9', 'Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Thiết bị mới ra mắt mang nhiều tính năng hữu ích. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Hướng dẫn sử dụng và mẹo nhỏ giúp tối ưu trải nghiệm.. Người dùng nên cân nhắc nhu cầu trước khi mua. Người dùng nên cân nhắc nhu cầu trước khi mua.', 'news_110.svg', 1, 'Tin tức', 'published', 3, '2025-12-17 12:56:07', '2025-12-31 18:39:13');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `news_comments`
--

CREATE TABLE `news_comments` (
  `comment_id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `notify_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `is_read` tinyint(4) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `type` varchar(100) DEFAULT 'system'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`notify_id`, `customer_id`, `admin_id`, `title`, `content`, `is_read`, `created_at`, `type`) VALUES
(34, NULL, NULL, 'Tin nhắn liên hệ mới', 'Liên hệ từ: sigma boy | SĐT: 0987878676 | Email: vuvy184@gmail.com\n\nNội dung: con khi dot', 0, '2026-01-01 00:55:15', 'contact'),
(35, NULL, NULL, 'Tin nhắn liên hệ mới', 'Liên hệ từ: khi dot | SĐT: 098787864 | Email: quaduahau28@gmail.com\n\nNội dung: mày ở đâu, mày sinh năm bao nhiêu', 0, '2026-01-01 01:22:49', 'contact'),
(36, 1, NULL, 'cc', 'sâccacasca', 0, '2026-01-01 01:38:53', 'admin_message'),
(37, 2, NULL, 'cc', 'sâccacasca', 0, '2026-01-01 01:38:53', 'admin_message'),
(38, 5, NULL, 'cc', 'sâccacasca', 0, '2026-01-01 01:38:53', 'admin_message'),
(39, 6, NULL, 'cc', 'sâccacasca', 0, '2026-01-01 01:38:53', 'admin_message'),
(40, 7, NULL, 'cc', 'sâccacasca', 0, '2026-01-01 01:38:53', 'admin_message'),
(41, 8, NULL, 'cc', 'sâccacasca', 0, '2026-01-01 01:38:53', 'admin_message'),
(42, 9, NULL, 'cc', 'sâccacasca', 0, '2026-01-01 01:38:53', 'admin_message'),
(43, 10, NULL, 'cc', 'sâccacasca', 0, '2026-01-01 01:38:53', 'admin_message'),
(44, 11, NULL, 'cc', 'sâccacasca', 0, '2026-01-01 01:38:53', 'admin_message'),
(45, 7, NULL, 'Bạn nhận mã giảm giá dành riêng cho bạn', 'Xin chúc mừng! Bạn nhận được mã giảm giá dành riêng cho tài khoản của bạn: tiepdz. Hạn sử dụng: 2026-01-01. Vui lòng đăng nhập để sử dụng mã.', 0, '2026-01-01 01:40:51', 'voucher'),
(46, 9, NULL, 'Bạn nhận mã giảm giá dành riêng cho bạn', 'Xin chúc mừng! Bạn nhận được mã giảm giá dành riêng cho tài khoản của bạn: tiepdzz. Hạn sử dụng: 2026-01-02. Vui lòng đăng nhập để sử dụng mã.', 0, '2026-01-01 01:41:11', 'voucher');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_key` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `thumbnail` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `price_history`
--

CREATE TABLE `price_history` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `old_price` int(11) DEFAULT NULL,
  `new_price` int(11) DEFAULT NULL,
  `changed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `product_id` int(10) NOT NULL,
  `cartegory_id` int(10) NOT NULL,
  `date` datetime NOT NULL,
  `product_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `product_des` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `product_screen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `product_os` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `product_rear_cam` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `product_front_cam` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `product_chip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `product_ram` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `product_internal_memory` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `product_sim` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `product_battery` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`product_id`, `cartegory_id`, `date`, `product_name`, `product_des`, `product_screen`, `product_os`, `product_rear_cam`, `product_front_cam`, `product_chip`, `product_ram`, `product_internal_memory`, `product_sim`, `product_battery`) VALUES
(1, 1, '2022-05-27 22:40:51', 'SamSung Galaxy A10', 'Samsung', '14inches', 'android', '64px', '36px', 'Snapp dragon 870', '8GB', '256GB', '2', '4000 mpa'),
(3, 1, '2022-05-27 22:47:15', 'SamSung Galaxy S22 Ultra', 'SamSung Galaxy S22 Ultra', 'Dynamic AMOLED 2X6.8\"Quad HD+ (2K+)', 'Android 12', 'Chính 108 MP & Phụ 12 MP, 10 MP, 10 MP', ' 40 MP', 'Snapdragon 8 Gen 1 8 nhân', '8 GB', '128 GB', '2 Nano SIM hoặc 1 Nano SIM + 1 eSIMHỗ trợ 5G', '5000 mAh45 W'),
(4, 2, '2023-08-29 14:04:36', 'iPhone 12 64GB', 'Iphone 12', 'OLED6.1', ' iOS 15', ' 2 camera 12 MP', '12 MP', 'Apple A14 Bionic', '4 GB', '64 GB', ' 1 Nano SIM & 1 eSIMHỗ trợ 5G', '2815 mAh20 W'),
(5, 6, '2022-05-27 22:56:26', 'Xiaomi 11T', 'Xiaomi 11T', 'AMOLED6.67\"Full HD+', ' Android 11', 'Chính 108 MP & Phụ 8 MP, 5 MP', '16 MP', 'MediaTek Dimensity 1200', '8 GB', ' 256 GB', ' 2 Nano SIMHỗ trợ 5G', ' 5000 mAh67 W'),
(6, 6, '2022-05-27 23:01:57', 'Xiaomi redmi note 11 pro', 'Xiaomi redmi note 11 pro', 'AMOLED6.81\"Quad HD+ (2K+)', 'Android 11', ' Chính 108 MP & Phụ 13 MP, 5 MP', ' 20 MP', ' Snapdragon 888', '8 GB', '256 GB', ' 2 Nano SIMHỗ trợ 5G', '4600 mAh 55 W'),
(7, 2, '2022-06-26 20:44:44', 'Iphone X', 'Iphone X 64 GB', 'OLED5.8\"Super Retina', 'iOS 12', ' 2 camera 12 MP', ' 7 MP', 'Apple A11 Bionic', ' 3 GB', ' 64 GB', '1 Nano SIM', '2716 mAh'),
(8, 1, '2022-06-26 20:53:43', 'Samsung Galaxy A52s', 'Samsung Galaxy A52s', 'Super AMOLED6.5\"Full HD+', 'Android 11', 'Chính 64 MP & Phụ 12 MP, 5 MP, 5 MP', '32 MP', 'Snapdragon 778G 5G 8 nhân', ' 8 GB', ' 128 GB', ' 2 Nano SIM (SIM 2 chung khe thẻ nhớ)Hỗ trợ 5G', '4500 mAh 25 W'),
(9, 7, '2022-06-26 20:59:41', 'Realme C35', 'Realme C35', 'IPS LCD6.6', 'Android 11', 'Chính 50 MP & Phụ 2 MP, 0.3 MP', ' 8 MP', ' Unisoc T616 8 nhân', ' 4 GB', '64 GB', '2 Nano SIMHỗ trợ 4G', '5000 mAh, 18 W'),
(10, 8, '2022-07-01 07:17:29', 'OPPO Reno7 Z 5G', 'OPPO đã trình làng mẫu Reno7 Z 5G với thiết kế OPPO Glow độc quyền, camera mang hiệu ứng như máy DSLR chuyên nghiệp cùng viền sáng kép, máy có một cấu hình mạnh mẽ và đạt chứng nhận xếp hạng A về độ mượt.', 'AMOLED6.43\"Full HD+', 'Android 11', 'Chính 64 MP & Phụ 2 MP, 2 MP', '16 MP', 'Snapdragon 695 5G 8 nhân', '8 GB', '128 GB', '2 Nano SIM (SIM 2 chung khe thẻ nhớ)Hỗ trợ 5G', '4500 mAh33 W'),
(11, 2, '2023-08-29 13:37:41', 'iPhone 11 64GB', 'Apple đã chính thức trình làng bộ 3 siêu phẩm iPhone 11, trong đó phiên bản iPhone 11 64GB có mức giá rẻ nhất nhưng vẫn được nâng cấp mạnh mẽ như iPhone Xr ra mắt trước đó.', 'IPS LCD 6.1\" - Tần số quét 60 Hz', ' iOS 15', '2 camera 12 MP', '12 MP', 'Apple A13 Bionic 6 nhân', ' 4 GB', '64 GB', '1 Nano SIM & 1 eSIM', ' 3110 mAh-18 W'),
(12, 2, '2023-08-29 13:55:57', 'iPhone 11 128GB', 'Được xem là một trong những phiên bản iPhone \"giá rẻ\" của bộ 3 iPhone 11 series nhưng iPhone 11 128GB vẫn sở hữu cho mình rất nhiều ưu điểm mà hiếm có một chiếc smartphone nào khác sở hữu.', 'IPS LCD6.1\"Liquid Retina', 'iOS 15', '2 camera 12 MP', '12 MP', 'Apple A13 Bionic', '4 GB', '128 GB', '1 Nano SIM & 1 eSIM, Hỗ trợ 4G', '3110 mAh,18 W'),
(15, 2, '2023-08-29 14:11:04', 'iPhone 12 128GB', 'Apple đã trình diện đến người dùng mẫu điện thoại iPhone 12 128GB với sự tuyên bố về một kỷ nguyên mới của iPhone 5G, nâng cấp về màn hình và hiệu năng hứa hẹn đây sẽ là smartphone cao cấp đáng để mọi người đầu tư sở hữu. ', 'OLED6.1\"Super Retina XDR', ' iOS 15', '2 camera 12 MP', '12 MP', 'Apple A14 Bionic', '4 GB', '128 GB', '1 Nano SIM & 1 eSIM, Hỗ trợ 5G', '2815mAh 20 W'),
(16, 2, '2023-08-29 14:17:44', 'iPhone 13 128GB', 'Trong khi sức hút đến từ bộ 4 phiên bản iPhone 12 vẫn chưa nguội đi, thì hãng điện thoại Apple đã mang đến cho người dùng một siêu phẩm mới iPhone 13 với nhiều cải tiến thú vị sẽ mang lại những trải nghiệm hấp dẫn nhất cho người dùng.', 'OLED6.1\"Super Retina XDR', 'iOS 15', '2 camera 12 MP', '12 MP', 'Apple A15 Bionic', '4 GB', '128 GB', '1 Nano SIM & 1 eSIM, Hỗ trợ 5G', '3240mAh 20 W'),
(17, 2, '2023-08-29 14:20:21', 'iPhone 13 256GB', 'Apple thỏa mãn sự chờ đón của iFan và người dùng với sự ra mắt của iPhone 13. Dù ngoại hình không có nhiều thay đổi so với iPhone 12 nhưng với cấu hình mạnh mẽ hơn, đặc biệt pin “trâu” hơn và khả năng quay phim chụp ảnh cũng ấn tượng hơn, hứa hẹn mang đến những trải nghiệm thú vị trên phiên bản mới này.', 'OLED6.1\"Super Retina XDR', ' iOS 15', '2 camera 12 MP', '12 MP', 'Apple A15 Bionic', '4 GB', '256GB', '1 Nano SIM & 1 eSIM, Hỗ trợ 5G', '3240mAh 20 W'),
(18, 2, '2023-08-29 14:27:08', 'iPhone 14 128GB', 'iPhone 14 128GB được xem là mẫu smartphone bùng nổ của nhà táo trong năm 2022, ấn tượng với ngoại hình trẻ trung, màn hình chất lượng đi kèm với những cải tiến về hệ điều hành và thuật toán xử lý hình ảnh, giúp máy trở thành cái tên thu hút được đông đảo người dùng quan tâm tại thời điểm ra mắt.', 'OLED6.1\"Super Retina XDR', 'iOS 16', '2 camera 12 MP', '12 MP', 'Apple A15 Bionic', '6 GB', '128 GB', '1 Nano SIM & 1 eSIM, Hỗ trợ 5G', '3279 mAh 20 W'),
(19, 2, '2023-08-29 14:33:24', 'iPhone 14 Pro', 'iPhone 14 Pro 128GB - Mẫu smartphone đến từ nhà Apple được mong đợi nhất năm 2022, lần này nhà Táo mang đến cho chúng ta một phiên bản với kiểu thiết kế hình notch mới, cấu hình mạnh mẽ nhờ con chip Apple A16 Bionic và cụm camera có độ phân giải được nâng cấp lên đến 48 MP.', 'OLED6.1\"Super Retina XDR', 'iOS 16', 'Chính 48 MP & Phụ 12 MP, 12 MP', '12 MP', 'Apple A16 Bionic', '6 GB', '128 GB', '1 Nano SIM & 1 eSIM, Hỗ trợ 5G', '3200 mAh20 W'),
(20, 2, '2023-08-29 14:35:32', 'iPhone 14 Pro Max', 'iPhone 14 Pro Max một siêu phẩm trong giới smartphone được nhà Táo tung ra thị trường vào tháng 09/2022. Máy trang bị con chip Apple A16 Bionic vô cùng mạnh mẽ, đi kèm theo đó là thiết kế hình màn hình mới, hứa hẹn mang lại những trải nghiệm đầy mới mẻ cho người dùng iPhone.', 'OLED 6.7\" Super Retina XDR', 'iOS 16', 'Chính 48 MP & Phụ 12 MP, 12 MP', '12 MP', 'Apple A16 Bionic', '6 GB', '128 GB', '1 Nano SIM & 1 eSIM, Hỗ trợ 5G', '4323mAh 20 W'),
(21, 1, '2023-08-29 14:39:05', 'Samsung Galaxy A14 6GB', 'Samsung tiếp tục chứng tỏ sự nỗ lực của mình trong việc cải thiện dòng sản phẩm phân khúc cấp thấp trong năm 2023 bằng việc ra mắt mẫu smartphone Samsung Galaxy A14 4G. Với thiết kế độc đáo và hiện đại, sản phẩm này đáp ứng đầy đủ tiêu chí \"ngon - bổ - rẻ\" với cấu hình ổn định và giá cả cực kỳ hợp lý.', 'PLS LCD6.6\"Full HD+', 'Android 13', 'Chính 50 MP & Phụ 5 MP, 2 MP', '13 MP', 'Exynos 850', '6 GB', '128 GB', '2 Nano SIM, Hỗ trợ 4G', '5000mAh, 15 W'),
(24, 1, '2023-08-29 14:45:46', 'Samsung Galaxy A54 5G', 'Samsung Galaxy A54 5G là mẫu điện thoại thông minh trong phân khúc tầm trung vừa được hãng cho ra mắt vào tháng 03/2023, máy trang bị màn hình Super AMOLED cùng con chip Exynos 1380 tiên tiến do chính Samsung sản xuất.', 'Super AMOLED 6.4\"Full HD+', 'Android 13', 'Chính 50 MP & Phụ 12 MP, 5 MP', ' 32 MP', 'Exynos 1380 8 nhân', '8 GB', '256 GB', '2 Nano SIM, Hỗ trợ 5G', '5000 mAh, 25 W'),
(25, 6, '2023-08-29 14:49:02', 'Xiaomi 12 5G', 'Điện thoại Xiaomi đang dần khẳng định chỗ đứng của mình trong phân khúc flagship bằng việc ra mắt Xiaomi 12 với bộ thông số ấn tượng, máy có một thiết kế gọn gàng, hiệu năng mạnh mẽ, màn hình hiển thị chi tiết cùng khả năng chụp ảnh sắc nét nhờ trang bị ống kính đến từ Sony', 'AMOLED 6.28\"Full HD+', 'Android 12', 'Chính 50 MP & Phụ 13 MP, 5 MP', '12 MP', 'Snapdragon 8 Gen 1', '8 GB', '256 GB', '2 Nano SIM, Hỗ trợ 5G', '4500 mAh, 67 W'),
(27, 5, '2023-08-29 14:59:05', 'vivo Y36 128GB', 'vivo Y36 128GB là một trong những sản phẩm điện thoại thông minh nổi bật và đáng chú ý của thương hiệu vivo. Với những tính năng và thông số kỹ thuật vượt trội, vivo Y36 hứa hẹn mang đến cho người dùng trải nghiệm di động đỉnh cao.', 'IPS LCD6.64\"Full HD+', 'Android 13', 'Chính 50 MP & Phụ 2 MP', '16 MP', 'Snapdragon 680', '8 GB', '128 GB', '2 Nano SIM, Hỗ trợ 4G', '5000mAh 44 W'),
(28, 8, '2023-08-29 17:07:41', 'Reno10 5G 128GB', 'Đến hẹn lại lên, hãng điện thoại OPPO tiếp tục cho ra mắt sản phẩm OPPO Reno10 5G 128GB tại thị trường Việt Nam trong năm 2023. Điện thoại mang trong mình lối thiết kế đẹp mắt, hiệu năng mượt mà xử lý tốt mọi tác vụ, đi cùng bộ ba camera mang đến những bức ảnh đẹp mắt.', 'AMOLED 6.7\"Full HD+', 'Android 13', 'Chính 64 MP & Phụ 32 MP, 8 MP', ' 32 MP', 'MediaTek Dimensity 7050 5G 8 nhân', '8 GB', '128 GB', '2 Nano SIM (SIM 2 chung khe thẻ nhớ)Hỗ trợ 5G', '5000mAh, 67 W'),
(30, 10, '2025-12-26 21:07:01', 'Điện Thoại Siêu Nhân', '-Dùng để biến hình siêu nhân\r\n-Pin vô cực\r\n-Hỗ trợ sạc bằng năng lượng mặt trời', 'AMOLED 3.2', 'ai ô roi', 'không', 'không', 'MediaTek Dimensity 7050 5G 8 nhân', '8 GB', '128 GB', '2 Nano SIM (SIM 2 chung khe thẻ nhớ)Hỗ trợ 5G', 'vô cực, năng lượng mặt trời');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_color`
--

CREATE TABLE `product_color` (
  `product_color_id` int(10) NOT NULL,
  `product_color_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_color`
--

INSERT INTO `product_color` (`product_color_id`, `product_color_name`) VALUES
(1, 'Đỏ'),
(2, 'Xanh lam'),
(3, 'Trắng'),
(4, 'Đen'),
(5, 'Hồng '),
(6, 'Vàng'),
(7, 'Xanh lục'),
(8, 'Xám'),
(9, 'Tím'),
(10, 'Bạc');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_img`
--

CREATE TABLE `product_img` (
  `product_color_img_id` int(11) NOT NULL,
  `product_id` int(10) NOT NULL,
  `product_color_id` int(10) NOT NULL,
  `product_color_img` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `product_price` int(50) NOT NULL,
  `product_price_des` int(50) NOT NULL,
  `product_quantity` int(50) NOT NULL,
  `product_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_img`
--

INSERT INTO `product_img` (`product_color_img_id`, `product_id`, `product_color_id`, `product_color_img`, `product_price`, `product_price_des`, `product_quantity`, `product_status`) VALUES
(5, 1, 4, 'samsung-galaxy-a52s-5g-black.jpg', 10000000, 1000000, 23, 'Đang bán'),
(6, 3, 4, 'Galaxy-S22-Ultra-Black.jpg', 4990000, 3990000, 20, 'Đang bán'),
(7, 3, 2, 'Galaxy-S22-Ultra-Green.jpg', 5000000, 5000000, 10, 'Đang bán'),
(8, 3, 3, 'Galaxy-S22-Ultra-White.jpg', 8490000, 7990000, 10, 'Đang bán'),
(9, 3, 5, 'Galaxy-S22-Ultra-Burgundy.jpg', 10000000, 10000000, 20, 'Đang bán'),
(10, 4, 4, 'iphone-12-den.jpg', 18900000, 18000000, 39, 'Đang bán'),
(11, 4, 1, 'iphone-12-do.jpg', 20000000, 18000000, 9, 'Đang bán'),
(12, 4, 3, 'iphone-12-white.jpg', 23990000, 22000000, 20, 'Đang bán'),
(13, 4, 2, 'iphone-12-xanh-duong.jpg', 10000000, 10000000, 30, 'Đang bán'),
(14, 4, 7, 'iphone-12-xanh-la.jpg', 6900000, 6000000, 60, 'Đang bán'),
(15, 5, 2, 'Xiaomi-11T-Blue.jpg', 10000000, 9000000, 10, 'Đang bán'),
(16, 5, 8, 'Xiaomi-11T-Grey.jpg', 12390000, 11390000, 30, 'Đang bán'),
(17, 5, 3, 'Xiaomi-11T-White-1-2-3.jpg', 12000000, 10000000, 20, 'Đang bán'),
(18, 6, 4, 'xiaomi-redmi-note-11-pro-den-thumb.jpg', 8890000, 8490000, 20, 'Đang bán'),
(19, 6, 3, 'xiaomi-redmi-note-11-pro-trang-thumb.jpg', 10000000, 8990000, 20, 'Đang bán'),
(20, 6, 2, 'xiaomi-redmi-note-11-pro-xanh-thumb.jpg', 12000000, 10000000, 17, 'Đang bán'),
(22, 6, 5, 'xiaomi-redmi-note-11s-5g-lam-hong-thumb.jpg', 10000000, 9000000, 20, 'Đang bán'),
(24, 7, 1, 'iphone-xi-do.jpg', 8900000, 8400000, 28, 'Đang bán'),
(25, 7, 9, 'iphone-xi-tim.jpg', 7990000, 7290000, 18, 'Đang bán'),
(26, 7, 6, 'iphone-xi-vang.jpg', 13990000, 12490000, 10, 'Đang bán'),
(27, 7, 7, 'iphone-xi-xanhla.jpg', 10000000, 8900000, 20, 'Đang bán'),
(28, 7, 4, 'iphone-xi-den.jpg', 6990000, 6490000, 20, 'Đang bán'),
(29, 8, 4, 'samsung-galaxy-a52s-5g-black.jpg', 8490000, 7990000, 8, 'Đang bán'),
(30, 8, 7, 'samsung-galaxy-a52s-5g-mint.jpg', 9490000, 8990000, 8, 'Đang bán'),
(31, 8, 3, 'samsung-galaxy-a52s-5g-white.jpg', 20490000, 18990000, 10, 'Đang bán'),
(32, 8, 5, 'samsung-galaxy-a52s-5g-violet.jpg', 10000000, 10000000, 40, 'Đang bán'),
(33, 9, 4, 'realme-c35-black-thumb.jpg', 8490000, 8490000, 50, 'Đang bán'),
(34, 9, 7, 'realme-c35-thumb-new.jpg', 9990000, 9490000, 10, 'Đang bán'),
(35, 10, 4, 'oppo-reno7-z-5g-thumb-2-1-200x200.jpg', 10490000, 10490000, 29, 'Đang bán'),
(36, 10, 10, 'oppo-reno7-z-5g-thumb-1-1-200x200.jpg', 10490000, 10490000, 10, 'Đang bán'),
(37, 11, 4, 'iphone-11-64gb-den.jpg', 11990000, 10690000, 50, 'Đang bán'),
(38, 11, 3, 'iphone-11-64gb-trang.jpg', 11990000, 10690000, 50, 'Đang bán'),
(39, 12, 3, 'iphone-11-128gb-trang.jpg', 13990000, 12290000, 50, 'Đang bán'),
(40, 12, 4, 'iphone-11-128gb-den.jpg', 13990000, 12290000, 50, 'Đang bán'),
(41, 15, 9, 'iphone-12-128gb-tim.jpg', 18990000, 16490000, 50, 'Đang bán'),
(42, 15, 3, 'iphone-12-128gb-white .jpg', 18990000, 16490000, 50, 'Đang bán'),
(43, 16, 3, 'iphone-13-128gb-trang.jpg', 18690000, 16690000, 50, 'Đang bán'),
(44, 16, 7, 'iphone-13-128gb-xanh-la.jpg', 18690000, 16690000, 50, 'Đang bán'),
(45, 17, 7, 'iphone-13-256gb-xanh-la.jpg', 20990000, 19990000, 50, 'Đang bán'),
(46, 17, 3, 'iphone-13-256gb-trang.jpg', 20990000, 19990000, 50, 'Đang bán'),
(47, 18, 3, 'iPhone-14-128gb-trang.jpg', 24990000, 22290000, 50, 'Đang bán'),
(48, 19, 6, 'iphone-14pro-vang.jpg', 27990000, 24090000, 50, 'Đang bán'),
(49, 20, 9, 'iphone-14-promax-tim.jpg', 29990000, 26990000, 48, 'Đang bán'),
(50, 21, 4, 'samsung-galaxy-a14-den.jpg', 4990000, 4490000, 48, 'Đang bán'),
(51, 24, 9, 'samsung-galaxy-a54-tim.jpg', 11490000, 11480000, 49, 'Đang bán'),
(52, 25, 8, 'Xiaomi-12-xam.jpg', 19900000, 11990000, 59, 'Đang bán'),
(55, 27, 7, 'vivo-y36-xanh.jpg', 6290000, 5980000, 138, 'Đang bán'),
(56, 28, 2, 'oppo-reno10-blue.jpg', 9990000, 9888000, 512, 'Đang bán'),
(61, 30, 1, 'dienthoaideu1.jpg', 19999999, 19999997, 0, 'Đang bán');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_color_id` int(11) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT 5,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `product_id`, `product_color_id`, `customer_id`, `order_id`, `rating`, `title`, `message`, `created_at`) VALUES
(5, 28, 2, 11, 77, 5, 'gdgdfg', 'dggdgdgf', '2025-12-31 21:13:30'),
(6, 25, 8, 11, 77, 5, '', '', '2025-12-31 21:13:32'),
(7, 25, 8, 11, 78, 5, 'cc', 'xxx', '2025-12-31 21:16:26');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotions`
--

CREATE TABLE `promotions` (
  `promotion_id` int(11) NOT NULL,
  `promotion_name` varchar(255) NOT NULL,
  `promotion_desc` text DEFAULT NULL,
  `discount_percent` int(11) DEFAULT 0,
  `discount_amount` int(11) DEFAULT 0,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(50) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotion_products`
--

CREATE TABLE `promotion_products` (
  `id` int(11) NOT NULL,
  `promotion_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `role_level` tinyint(4) NOT NULL DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `role_level`, `description`, `created_at`) VALUES
(1, 'customer', 1, 'Khách hàng', '2025-11-14 15:29:06'),
(2, 'warehouse', 2, 'Nhân viên kho', '2025-11-14 15:29:06'),
(3, 'staff', 3, 'Nhân viên cửa hàng', '2025-11-14 15:29:06'),
(4, 'admin', 4, 'Quản trị', '2025-11-14 15:29:06'),
(5, 'super_admin', 5, 'Toàn quyền hệ thống', '2025-11-14 15:29:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `slider`
--

CREATE TABLE `slider` (
  `slide_id` int(10) NOT NULL,
  `slide_image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `slider`
--

INSERT INTO `slider` (`slide_id`, `slide_image`) VALUES
(1, 'slider7.png'),
(2, 'slider2.png'),
(3, 'slider3.png'),
(4, 'slider4.png'),
(5, 'slider5.jpg'),
(6, 'slider6.png'),
(7, 'slider1.jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `stock_movements`
--

CREATE TABLE `stock_movements` (
  `movement_id` int(11) NOT NULL,
  `product_color_img_id` int(11) NOT NULL,
  `product_id` int(10) NOT NULL,
  `product_color_id` int(10) NOT NULL,
  `movement_type` enum('import','export','adjust') DEFAULT 'import',
  `quantity` int(11) NOT NULL,
  `import_price` int(11) DEFAULT 0,
  `export_price` int(11) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `stock_movements`
--

INSERT INTO `stock_movements` (`movement_id`, `product_color_img_id`, `product_id`, `product_color_id`, `movement_type`, `quantity`, `import_price`, `export_price`, `notes`, `created_by`, `created_at`) VALUES
(14, 5, 1, 4, 'import', 23, 200000, 0, '', 0, '2025-12-31 21:19:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vouchers`
--

CREATE TABLE `vouchers` (
  `voucher_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_percent` int(11) DEFAULT 0,
  `discount_amount` int(11) DEFAULT 0,
  `quantity` int(11) DEFAULT 1,
  `min_order` int(11) DEFAULT 0,
  `max_discount` int(11) DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `allowed_customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `vouchers`
--

INSERT INTO `vouchers` (`voucher_id`, `code`, `discount_percent`, `discount_amount`, `quantity`, `min_order`, `max_discount`, `start_date`, `end_date`, `status`, `allowed_customer_id`) VALUES
(1, 'giam10', 10, 100000, 0, 1000000, 300000, '2025-11-21', '2025-11-22', 'expired', NULL),
(2, 'tiep90', 90, 0, 2, 0, 0, '2025-12-07', '2025-12-10', 'expired', NULL),
(3, 'tiep99', 99, 0, 95, 0, 0, '2025-12-07', '2027-01-07', 'active', NULL),
(4, 'huy10', 10, 0, 5, 0, 0, '2025-12-08', '2026-12-26', 'active', NULL),
(5, 'Test', 20, 0, 1, 0, 0, '2025-12-08', '2025-12-18', 'expired', 1),
(6, 'test2', 30, 0, 1, 0, 0, '2025-12-09', '2025-12-10', 'expired', 8),
(7, 'tiepdz', 50, 0, 99, 0, 0, '2026-01-01', '2026-01-01', 'inactive', 7),
(8, 'tiepdzz', 50, 0, 98, 0, 0, '2026-01-01', '2026-01-02', 'active', 9);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Chỉ mục cho bảng `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Chỉ mục cho bảng `cartegory`
--
ALTER TABLE `cartegory`
  ADD PRIMARY KEY (`cartegory_id`);

--
-- Chỉ mục cho bảng `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Chỉ mục cho bảng `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Chỉ mục cho bảng `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Chỉ mục cho bảng `customer_orders`
--
ALTER TABLE `customer_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Chỉ mục cho bảng `customer_order_products`
--
ALTER TABLE `customer_order_products`
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `favorite_product`
--
ALTER TABLE `favorite_product`
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `product_color_id` (`product_color_id`);

--
-- Chỉ mục cho bảng `flash_sale`
--
ALTER TABLE `flash_sale`
  ADD PRIMARY KEY (`flash_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `momo`
--
ALTER TABLE `momo`
  ADD PRIMARY KEY (`id_momo`);

--
-- Chỉ mục cho bảng `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`news_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Chỉ mục cho bảng `news_comments`
--
ALTER TABLE `news_comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `idx_news_id` (`news_id`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notify_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Chỉ mục cho bảng `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permission_key` (`permission_key`);

--
-- Chỉ mục cho bảng `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Chỉ mục cho bảng `price_history`
--
ALTER TABLE `price_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `cartegory_id` (`cartegory_id`);

--
-- Chỉ mục cho bảng `product_color`
--
ALTER TABLE `product_color`
  ADD PRIMARY KEY (`product_color_id`);

--
-- Chỉ mục cho bảng `product_img`
--
ALTER TABLE `product_img`
  ADD PRIMARY KEY (`product_color_img_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `product_color_id` (`product_color_id`);

--
-- Chỉ mục cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`promotion_id`);

--
-- Chỉ mục cho bảng `promotion_products`
--
ALTER TABLE `promotion_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promotion_id` (`promotion_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Chỉ mục cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_id` (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Chỉ mục cho bảng `slider`
--
ALTER TABLE `slider`
  ADD PRIMARY KEY (`slide_id`);

--
-- Chỉ mục cho bảng `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`movement_id`),
  ADD KEY `product_color_img_id` (`product_color_img_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_movement_type` (`movement_type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Chỉ mục cho bảng `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`voucher_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT cho bảng `cartegory`
--
ALTER TABLE `cartegory`
  MODIFY `cartegory_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `customer_addresses`
--
ALTER TABLE `customer_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `customer_orders`
--
ALTER TABLE `customer_orders`
  MODIFY `order_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'mã order', AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT cho bảng `flash_sale`
--
ALTER TABLE `flash_sale`
  MODIFY `flash_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `momo`
--
ALTER TABLE `momo`
  MODIFY `id_momo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `news`
--
ALTER TABLE `news`
  MODIFY `news_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT cho bảng `news_comments`
--
ALTER TABLE `news_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notify_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT cho bảng `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `price_history`
--
ALTER TABLE `price_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT cho bảng `product_color`
--
ALTER TABLE `product_color`
  MODIFY `product_color_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `product_img`
--
ALTER TABLE `product_img`
  MODIFY `product_color_img_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `promotions`
--
ALTER TABLE `promotions`
  MODIFY `promotion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `promotion_products`
--
ALTER TABLE `promotion_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `slider`
--
ALTER TABLE `slider`
  MODIFY `slide_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `movement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `voucher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`);

--
-- Các ràng buộc cho bảng `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Các ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Các ràng buộc cho bảng `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Các ràng buộc cho bảng `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD CONSTRAINT `customer_addresses_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Các ràng buộc cho bảng `customer_orders`
--
ALTER TABLE `customer_orders`
  ADD CONSTRAINT `customer_orders_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `customer_order_products`
--
ALTER TABLE `customer_order_products`
  ADD CONSTRAINT `customer_order_products_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `customer_order_products_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `customer_orders` (`order_id`);

--
-- Các ràng buộc cho bảng `favorite_product`
--
ALTER TABLE `favorite_product`
  ADD CONSTRAINT `favorite_product_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `favorite_product_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Các ràng buộc cho bảng `flash_sale`
--
ALTER TABLE `flash_sale`
  ADD CONSTRAINT `flash_sale_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Các ràng buộc cho bảng `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `news_comments`
--
ALTER TABLE `news_comments`
  ADD CONSTRAINT `news_comments_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`news_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_comments_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`);

--
-- Các ràng buộc cho bảng `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `admin` (`admin_id`);

--
-- Các ràng buộc cho bảng `price_history`
--
ALTER TABLE `price_history`
  ADD CONSTRAINT `price_history_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`cartegory_id`) REFERENCES `cartegory` (`cartegory_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `product_img`
--
ALTER TABLE `product_img`
  ADD CONSTRAINT `product_img_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_img_ibfk_2` FOREIGN KEY (`product_color_id`) REFERENCES `product_color` (`product_color_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `promotion_products`
--
ALTER TABLE `promotion_products`
  ADD CONSTRAINT `promotion_products_ibfk_1` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`promotion_id`),
  ADD CONSTRAINT `promotion_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Các ràng buộc cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`product_color_img_id`) REFERENCES `product_img` (`product_color_img_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
