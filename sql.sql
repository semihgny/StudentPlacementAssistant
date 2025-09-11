SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `notlar` (
  `not_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `universite_id` int(11) NOT NULL,
  `not_metni` text NOT NULL,
  `kayit_tarihi` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tercih_listeleri` (
  `liste_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `liste_adi` varchar(255) NOT NULL,
  `olusturma_tarihi` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tercih_listesi_ogeleri` (
  `oge_id` int(11) NOT NULL,
  `liste_id` int(11) NOT NULL,
  `universite_id` int(11) NOT NULL,
  `sira` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `universiteler` (
  `id` int(11) NOT NULL,
  `universite_adi` varchar(255) NOT NULL,
  `bolum_adi` varchar(255) NOT NULL,
  `puan_2024` decimal(10,4) NOT NULL,
  `tur` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `yeni_kontenjan_datalari` (
  `id` int(11) NOT NULL,
  `universite_adi` varchar(255) NOT NULL,
  `bolum_adi` varchar(255) NOT NULL,
  `kontenjan` int(4) NOT NULL,
  `tur` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `yeni_kontenjan_listeleri` (
  `liste_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `liste_adi` varchar(255) NOT NULL,
  `olusturma_tarihi` timestamp NULL DEFAULT current_timestamp(),
  `tercihler_json` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `notlar`
  ADD PRIMARY KEY (`not_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `universite_id` (`universite_id`);

ALTER TABLE `tercih_listeleri`
  ADD PRIMARY KEY (`liste_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `tercih_listesi_ogeleri`
  ADD PRIMARY KEY (`oge_id`),
  ADD KEY `liste_id` (`liste_id`),
  ADD KEY `universite_id` (`universite_id`);

ALTER TABLE `universiteler`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `yeni_kontenjan_datalari`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `yeni_kontenjan_listeleri`
  ADD PRIMARY KEY (`liste_id`),
  ADD KEY `user_id` (`user_id`);


ALTER TABLE `notlar`
  MODIFY `not_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `tercih_listeleri`
  MODIFY `liste_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `tercih_listesi_ogeleri`
  MODIFY `oge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `universiteler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `yeni_kontenjan_datalari`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `yeni_kontenjan_listeleri`
  MODIFY `liste_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


ALTER TABLE `notlar`
  ADD CONSTRAINT `notlar_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notlar_ibfk_2` FOREIGN KEY (`universite_id`) REFERENCES `universiteler` (`id`) ON DELETE CASCADE;

ALTER TABLE `tercih_listeleri`
  ADD CONSTRAINT `tercih_listeleri_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `tercih_listesi_ogeleri`
  ADD CONSTRAINT `tercih_listesi_ogeleri_ibfk_1` FOREIGN KEY (`liste_id`) REFERENCES `tercih_listeleri` (`liste_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tercih_listesi_ogeleri_ibfk_2` FOREIGN KEY (`universite_id`) REFERENCES `universiteler` (`id`) ON DELETE CASCADE;

ALTER TABLE `yeni_kontenjan_listeleri`
  ADD CONSTRAINT `yeni_kontenjan_listeleri_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
