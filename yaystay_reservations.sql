-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 02 Nis 2025, 09:06:23
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `yaystay_reservations`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '123', '2025-03-16 16:55:15');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `amenities`
--

CREATE TABLE `amenities` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `amenities`
--

INSERT INTO `amenities` (`id`, `name`) VALUES
(1, 'Безплатен Wi-Fi'),
(2, 'Безплатен паркинг'),
(3, 'Приема домашни любимци'),
(4, 'Барбекю'),
(5, 'Отопление'),
(6, 'Камина'),
(7, 'Подходящо за събития'),
(8, 'Подходящо за деца'),
(9, 'Механа'),
(10, 'Хладилник'),
(11, 'Печка'),
(12, 'Телевизор'),
(13, 'Микровълнува'),
(14, 'Кафемашина'),
(15, 'Пералня'),
(16, 'Кухня'),
(17, 'Сушилня'),
(18, 'Термокана'),
(19, 'Тостер'),
(20, 'Ютия'),
(21, 'Кабелна телевизия');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `houses`
--

CREATE TABLE `houses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `type` enum('Море','Планина') NOT NULL,
  `image_url` varchar(255) DEFAULT 'images/default.jpg',
  `winter_price` decimal(10,2) DEFAULT NULL,
  `summer_price` decimal(10,2) DEFAULT NULL,
  `max_guests` int(11) DEFAULT NULL,
  `image1_url` varchar(255) DEFAULT NULL,
  `image2_url` varchar(255) DEFAULT NULL,
  `image3_url` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `houses`
--

INSERT INTO `houses` (`id`, `name`, `location`, `type`, `image_url`, `winter_price`, `summer_price`, `max_guests`, `image1_url`, `image2_url`, `image3_url`, `phone`, `email`) VALUES
(36, 'meco', 'varna', '', 'images/67d9e70f625ce.jpeg', 24.00, 65.00, 4, 'images/67d9e70f62b82.jpeg', 'images/67d9e70f62f28.jpeg', 'images/67d9e70f63449.jpeg', NULL, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `house_amenities`
--

CREATE TABLE `house_amenities` (
  `house_id` int(11) NOT NULL,
  `amenity_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `house_amenities`
--

INSERT INTO `house_amenities` (`house_id`, `amenity_id`) VALUES
(36, 1),
(36, 2),
(36, 4),
(36, 5),
(36, 7),
(36, 8),
(36, 10),
(36, 11),
(36, 13),
(36, 14),
(36, 16),
(36, 17),
(36, 19),
(36, 20);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `messages`
--

INSERT INTO `messages` (`id`, `name`, `email`, `phone`, `message`, `created_at`) VALUES
(1, 'meco', 'syuleyman88ilker@gmail.com', '0893773891', 'altan ako go hvana', '2025-03-18 21:59:49');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `house_id` int(11) NOT NULL,
  `checkin_date` date NOT NULL,
  `checkout_date` date NOT NULL,
  `guest_name` varchar(100) NOT NULL,
  `guest_email` varchar(100) NOT NULL,
  `guest_phone` varchar(50) DEFAULT NULL,
  `num_guests` int(11) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `reservations`
--

INSERT INTO `reservations` (`id`, `house_id`, `checkin_date`, `checkout_date`, `guest_name`, `guest_email`, `guest_phone`, `num_guests`, `payment_method`, `total_price`) VALUES
(30, 36, '2025-04-10', '2025-04-25', 'hdfhdfhd', 'syuleyman88ilker@gmail.com', '21341234', 2, NULL, 360.00),
(31, 36, '2025-05-07', '2025-05-29', 'q', 'syuleyman88ilker@gmail.com', '235235', 2, 'card', 528.00);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Tablo için indeksler `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `houses`
--
ALTER TABLE `houses`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `house_amenities`
--
ALTER TABLE `house_amenities`
  ADD PRIMARY KEY (`house_id`,`amenity_id`),
  ADD KEY `amenity_id` (`amenity_id`);

--
-- Tablo için indeksler `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `house_id` (`house_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `amenities`
--
ALTER TABLE `amenities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Tablo için AUTO_INCREMENT değeri `houses`
--
ALTER TABLE `houses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Tablo için AUTO_INCREMENT değeri `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `house_amenities`
--
ALTER TABLE `house_amenities`
  ADD CONSTRAINT `house_amenities_ibfk_1` FOREIGN KEY (`house_id`) REFERENCES `houses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `house_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`house_id`) REFERENCES `houses` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
