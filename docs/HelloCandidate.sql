CREATE TABLE `users` (
  `user_id` INT PRIMARY KEY AUTO_INCREMENT,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `user_type` ENUM ('student', 'spectator') NOT NULL,
  `bio` TEXT,
  `candidature_count` INT DEFAULT 0,
  `rank_id` INT,
  `created_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `applications` (
  `application_id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `company_name` VARCHAR(100) NOT NULL,
  `position` VARCHAR(100) NOT NULL,
  `submission_date` DATE NOT NULL,
  `status` ENUM ('pending', 'interview', 'rejected', 'accepted') DEFAULT 'pending',
  `address` VARCHAR(255),
  `offer_link` VARCHAR(255),
  `description` TEXT,
  `cover_letter_path` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `ranks` (
  `rank_id` INT PRIMARY KEY AUTO_INCREMENT,
  `rank_name` VARCHAR(50) NOT NULL,
  `sub_rank` INT NOT NULL,
  `min_applications` INT NOT NULL
);

CREATE TABLE `groups` (
  `group_id` INT PRIMARY KEY AUTO_INCREMENT,
  `group_name` VARCHAR(50) NOT NULL,
  `created_by` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `group_members` (
  `group_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `added_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP),
  PRIMARY KEY (`group_id`, `user_id`)
);

CREATE TABLE `notifications` (
  `notification_id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` BOOLEAN DEFAULT false,
  `created_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `badges` (
  `badge_id` INT PRIMARY KEY AUTO_INCREMENT,
  `badge_name` VARCHAR(50) NOT NULL,
  `description` TEXT,
  `min_applications` INT
);

CREATE TABLE `user_badges` (
  `user_id` INT NOT NULL,
  `badge_id` INT NOT NULL,
  `earned_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP),
  PRIMARY KEY (`user_id`, `badge_id`)
);

CREATE UNIQUE INDEX `ranks_index_0` ON `ranks` (`rank_name`, `sub_rank`);

ALTER TABLE `users` ADD FOREIGN KEY (`rank_id`) REFERENCES `ranks` (`rank_id`);

ALTER TABLE `applications` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `groups` ADD FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `group_members` ADD FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE;

ALTER TABLE `group_members` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `notifications` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `user_badges` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `user_badges` ADD FOREIGN KEY (`badge_id`) REFERENCES `badges` (`badge_id`) ON DELETE CASCADE;


INSERT INTO ranks (rank_name, sub_rank, min_applications) VALUES
('Bronze', 3, 0),    -- Début, pas de candidatures nécessaires
('Bronze', 2, 10),
('Bronze', 1, 20),
('Argent', 3, 30),   -- Passage à Argent après 3 sous-rangs de Bronze
('Argent', 2, 40),
('Argent', 1, 50),
('Or', 3, 60),
('Or', 2, 70),
('Or', 1, 80),
('Émeraude', 3, 90),
('Émeraude', 2, 100),
('Émeraude', 1, 110),
('Diamant', 3, 120),
('Diamant', 2, 130),
('Diamant', 1, 140),
('Maitre', 3, 150),
('Maitre', 2, 160),
('Maitre', 1, 170),
('Challenger', 3, 180),
('Challenger', 2, 190),
('Challenger', 1, 200),
('Grand Challenger', 3, 210),
('Grand Challenger', 2, 220),
('Grand Challenger', 1, 230);