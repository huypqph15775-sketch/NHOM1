DROP DATABASE IF EXISTS smartphone;
CREATE DATABASE smartphone CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smartphone;


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
  `admin_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
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
(1, 'Hoang Tiep', 'tiep184@gmail.com', 'admin', '1234', 'avatar1.jpg', 'Hanoi', '0123456789', 'Đại Ca', 4);

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

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cartegory`
--

CREATE TABLE `cartegory` (
  `cartegory_id` int(10) NOT NULL,
  `cartegory_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `cartegory_img` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cartegory`
--

INSERT INTO `cartegory` (`cartegory_id`, `cartegory_name`, `cartegory_img`) VALUES
(1, 'SamSung', 'logo-samsung.png'),
(2, 'Motorola', 'Motorola-Logo.png'),
(3, 'Nokia', ''),
(5, 'Vivo', ''),
(6, 'Xiaomi', ''),
(7, 'Realme', ''),
(8, 'Oppo', 'logo-oppo.jpg'),
(9, 'Apple', 'Apple-Logo.jpg');

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
  `customer_password` int(11) NOT NULL,
  `customer_img` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `account_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `role_id` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `customer`
--

INSERT INTO `customer` (`customer_id`, `customer_name`, `customer_sex`, `customer_email`, `customer_phone`, `customer_address`, `customer_user_name`, `customer_password`, `customer_img`, `account_status`, `role_id`) VALUES
(1, 'Nguyễn Quang Ninh', 'Nam', 'anhnguyenluongxuan@gmail.com', '0377238169', 'Hải Phòng', 'ninh', 1234, 'avatar1.jpg', 'Locked', 1),
(2, 'Phạm Khang Duy', 'Nam', 'duy@gmail.com', '0123456789', 'Ninh Bình', 'duy', 1234, '1.png', 'Locked', 1),
(3, 'Đạt', 'Nam', 'dat@example.com', '0911001100', 'Hà Nội', 'dat', '1234', 'avatar3.jpg', 'Active', 1),
(4, 'Tiệp', 'Nam', 'tiep@example.com', '0933002200', 'Hà Nội', 'tiep', '1234', 'avatar4.jpg', 'Active', 1),
(5, 'Huy', 'Nam', 'huy@example.com', '0988999911', 'Hà Nội', 'huy', '1234', 'avatar5.jpg', 'Active', 1),
(6, 'Vĩnh', 'Nam', 'vinh@example.com', '0977003300', 'Hà Nội', 'vinh', '1234', 'avatar6.jpg', 'Active', 1),
(7, 'Long', 'Nam', 'long@example.com', '0901122334', 'Đà Nẵng', 'long', '1234', 'avatar7.jpg', 'Active', 1),
(8, 'Tùng', 'Nam', 'tung@example.com', '0909988776', 'Huế', 'tung', '1234', 'avatar8.jpg', 'Active', 1),
(9, 'Kiên', 'Nam', 'kien@example.com', '0905566778', 'Hải Dương', 'kien', '1234', 'avatar9.jpg', 'Active', 1),
(10, 'Minh', 'Nam', 'minh@example.com', '0933445566', 'Quảng Ninh', 'minh', '1234', 'avatar10.jpg', 'Active', 1);
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
  `order_no` int(10) NOT NULL,
  `receiver` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `receiver_sex` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `receiver_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `delivery_location` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `payment_type` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `received_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `customer_orders`
--

INSERT INTO `customer_orders` 
(`order_id`, `customer_id`, `total_price`, `order_date`, `status`, `order_no`, `receiver`, `receiver_sex`, `receiver_phone`, `delivery_location`, `payment_type`, `received_date`) VALUES
(31, 1, 9990000, '2023-08-29', 'Đã giao', 391763615, 'Nguyễn Quang Ninh', 'Nam', '0377238169', 'Hải Phòng', 'Thanh toán tiền mặt khi nhận hàng', '2023-08-29'),
(32, 2, 9990000, '2023-08-29', 'Đang giao', 56293775, 'Phạm Khang Duy', 'Nam', '0123456789', 'Ninh Bình', 'Cà thẻ khi nhận hàng', NULL),

(40, 1, 9990000, '2023-08-31', 'Đang giao', 2050707107, 'Nguyễn Quang Ninh', 'Nam', '0377238169', 'Hải Phòng', 'Thanh toán tiền mặt khi nhận hàng', NULL),
(43, 1, 4490000, '2023-08-31', 'Đã giao', 1811275033, 'Nguyễn Quang Ninh', 'Nam', '0377238169', 'Hải Phòng', 'Đã thanh toán qua MOMO ATM', '2023-08-31'),

(44, 5, 4490000, '2024-02-27', 'Đã hủy', 291819124, 'Huy', 'Nam', '0988999911', 'Hà Nội', 'Thanh toán tiền mặt khi nhận hàng', NULL),

(45, 6, 4490000, '2024-02-28', 'Đang giao', 1425441566, 'Vĩnh', 'Nam', '0977003300', 'Hà Nội', 'Thanh toán tiền mặt khi nhận hàng', NULL),
(46, 6, 11490000, '2024-02-28', 'Đang chờ', 1407814012, 'Vĩnh', 'Nam', '0977003300', 'Hà Nội', 'Thanh toán tiền mặt khi nhận hàng', NULL),

(47, 5, 16480000, '2024-03-07', 'Đang chờ', 814977624, 'Huy', 'Nam', '0988999911', 'Hà Nội', 'Thanh toán tiền mặt khi nhận hàng', NULL),
(48, 5, 5990000, '2024-03-07', 'Đã hủy', 1910602922, 'Huy', 'Nam', '0988999911', 'Hà Nội', 'Thanh toán tiền mặt khi nhận hàng', NULL),

(49, 5, 9990000, '2024-03-07', 'Đã giao', 331347326, 'Huy', 'Nam', '0988999911', 'Hà Nội', 'Thanh toán tiền mặt khi nhận hàng', '2024-03-07'),
(50, 5, 5990000, '2024-03-07', 'Đã giao', 100672171, 'Huy', 'Nam', '0988999911', 'Hà Nội', 'Thanh toán tiền mặt khi nhận hàng', '2024-03-07'),

(51, 5, 35970000, '2024-09-08', 'Đang chờ', 2077666649, 'Huy', 'Nam', '0988999911', 'Hà Nội', 'Thanh toán tiền mặt khi nhận hàng', NULL),
(52, 5, 5990000, '2024-09-08', 'Đã hủy', 658122957, 'Huy', 'Nam', '0988999911', 'Hà Nội', 'Thanh toán tiền mặt khi nhận hàng', NULL),

(53, 8, 11990000, '2024-09-22', 'Đang chờ', 1188364144, 'Tùng', 'Nam', '0909988776', 'Huế', 'Thanh toán tiền mặt khi nhận hàng', NULL);


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
(31, 28, 'Xanh da trời', 1),
(32, 28, 'Xanh da trời', 1),
(40, 28, 'Xanh da trời', 1),
(43, 21, 'Đen', 1),
(44, 21, 'Đen', 1),
(45, 21, 'Đen', 1),
(46, 24, 'Tím', 1),
(47, 21, 'Đen', 1),
(47, 25, 'Xám', 1),
(48, 27, 'xanh lá cây', 1),
(49, 28, 'Xanh da trời', 1),
(50, 27, 'xanh lá cây', 1),
(51, 25, 'Xám', 3),
(52, 27, 'xanh lá cây', 1),
(53, 25, 'Xám', 1);

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

--
-- Đang đổ dữ liệu cho bảng `momo`
--

INSERT INTO `momo` (`id_momo`, `partner_code`, `order_id`, `amount`, `order_info`, `order_type`, `trans_id`, `pay_type`, `code_cart`) VALUES
(2, 'MOMOBKUN20180529', 1693425833, '10000', 'Thanh toán qua MoMo ATM', 'momo_wallet', 2147483647, 'napas', '9979'),
(3, 'MOMOBKUN20180529', 1693431829, '4490000', 'Thanh toán qua MoMo', 'momo_wallet', 2147483647, 'napas', '9219'),
(4, 'MOMOBKUN20180529', 1693432066, '4490000', 'Thanh toán qua MoMo', 'momo_wallet', 2147483647, 'napas', '6316'),
(5, 'MOMOBKUN20180529', 1693432066, '4490000', 'Thanh toán qua MoMo', 'momo_wallet', 2147483647, 'napas', '3950'),
(6, 'MOMOBKUN20180529', 1693432324, '4490000', 'Thanh toán qua MoMo', 'momo_wallet', 2147483647, 'napas', '9208'),
(7, 'MOMOBKUN20180529', 1693432324, '4490000', 'Thanh toán qua MoMo', 'momo_wallet', 2147483647, 'napas', '478'),
(8, 'MOMOBKUN20180529', 1693432324, '4490000', 'Thanh toán qua MoMo', 'momo_wallet', 2147483647, 'napas', '5306'),
(9, 'MOMOBKUN20180529', 1693432324, '4490000', 'Thanh toán qua MoMo', 'momo_wallet', 2147483647, 'napas', '5989'),
(10, 'MOMOBKUN20180529', 1693432324, '4490000', 'Thanh toán qua MoMo', 'momo_wallet', 2147483647, 'napas', '9122'),
(11, 'MOMOBKUN20180529', 1693432324, '4490000', 'Thanh toán qua MoMo', 'momo_wallet', 2147483647, 'napas', '9972');

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
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(28, 8, '2023-08-29 17:07:41', 'Reno10 5G 128GB', 'Đến hẹn lại lên, hãng điện thoại OPPO tiếp tục cho ra mắt sản phẩm OPPO Reno10 5G 128GB tại thị trường Việt Nam trong năm 2023. Điện thoại mang trong mình lối thiết kế đẹp mắt, hiệu năng mượt mà xử lý tốt mọi tác vụ, đi cùng bộ ba camera mang đến những bức ảnh đẹp mắt.', 'AMOLED 6.7\"Full HD+', 'Android 13', 'Chính 64 MP & Phụ 32 MP, 8 MP', ' 32 MP', 'MediaTek Dimensity 7050 5G 8 nhân', '8 GB', '128 GB', '2 Nano SIM (SIM 2 chung khe thẻ nhớ)Hỗ trợ 5G', '5000mAh, 67 W');

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
(5, 1, 4, 'samsung-galaxy-a52s-5g-black.jpg', 10000000, 1000000, 0, 'Đang bán'),
(6, 3, 4, 'Galaxy-S22-Ultra-Black.jpg', 4990000, 3990000, 20, 'Đang bán'),
(7, 3, 2, 'Galaxy-S22-Ultra-Green.jpg', 5000000, 5000000, 10, 'Đang bán'),
(8, 3, 3, 'Galaxy-S22-Ultra-White.jpg', 8490000, 7990000, 10, 'Đang bán'),
(9, 3, 5, 'Galaxy-S22-Ultra-Burgundy.jpg', 10000000, 10000000, 20, 'Đang bán'),
(10, 4, 4, 'iphone-12-den.jpg', 18900000, 18000000, 40, 'Đang bán'),
(11, 4, 1, 'iphone-12-do.jpg', 20000000, 18000000, 10, 'Đang bán'),
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
(24, 7, 1, 'iphone-xi-do.jpg', 8900000, 8400000, 28, 'Ngừng bán'),
(25, 7, 9, 'iphone-xi-tim.jpg', 7990000, 7290000, 18, 'Ngừng bán'),
(26, 7, 6, 'iphone-xi-vang.jpg', 13990000, 12490000, 10, 'Ngừng bán'),
(27, 7, 7, 'iphone-xi-xanhla.jpg', 10000000, 8900000, 20, 'Ngừng bán'),
(28, 7, 4, 'iphone-xi-den.jpg', 6990000, 6490000, 20, 'Ngừng bán'),
(29, 8, 4, 'samsung-galaxy-a52s-5g-black.jpg', 8490000, 7990000, 9, 'Đang bán'),
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
(49, 20, 9, 'iphone-14-promax-tim.jpg', 29990000, 26990000, 50, 'Đang bán'),
(50, 21, 4, 'samsung-galaxy-a14-den.jpg', 4990000, 4490000, 48, 'Đang bán'),
(51, 24, 9, 'samsung-galaxy-a54-tim.jpg', 11490000, 11490000, 50, 'Đang bán'),
(52, 25, 8, 'Xiaomi-12-xam.jpg', 19900000, 11990000, 60, 'Đang bán'),
(55, 27, 7, 'vivo-y36-xanh.jpg', 6290000, 5990000, 39, ''),
(56, 28, 2, 'oppo-reno10-blue.jpg', 9990000, 9990000, 0, 'Đang bán');

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
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT cho bảng `cartegory`
--
ALTER TABLE `cartegory`
  MODIFY `cartegory_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `customer_addresses`
--
ALTER TABLE `customer_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `customer_orders`
--
ALTER TABLE `customer_orders`
  MODIFY `order_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'mã order', AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT cho bảng `flash_sale`
--
ALTER TABLE `flash_sale`
  MODIFY `flash_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `momo`
--
ALTER TABLE `momo`
  MODIFY `id_momo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notify_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `product_color`
--
ALTER TABLE `product_color`
  MODIFY `product_color_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `product_img`
--
ALTER TABLE `product_img`
  MODIFY `product_color_img_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

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
-- AUTO_INCREMENT cho bảng `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `voucher_id` int(11) NOT NULL AUTO_INCREMENT;

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
-- Bổ sung khóa chính và ràng buộc cho bảng `favorite_product`
--
ALTER TABLE `favorite_product`
  ADD PRIMARY KEY (`customer_id`, `product_id`, `product_color_id`);

ALTER TABLE `favorite_product`
  ADD CONSTRAINT `favorite_product_ibfk_3` FOREIGN KEY (`product_color_id`) REFERENCES `product_color` (`product_color_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Bổ sung khóa chính cho bảng `customer_order_products`
--
ALTER TABLE `customer_order_products`
  ADD PRIMARY KEY (`order_id`, `product_id`, `color`);

--


COMMIT;
