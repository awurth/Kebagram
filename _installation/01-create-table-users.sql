CREATE TABLE IF NOT EXISTS `users` (
 `user_id` int(11) NOT NULL AUTO_INCREMENT,
 `session_id` varchar(48) DEFAULT NULL,
 `user_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
 `user_slug` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
 `user_password_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
 `user_email` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
 `user_active` tinyint(1) NOT NULL DEFAULT '0',
 `user_deleted` tinyint(1) NOT NULL DEFAULT '0',
 `user_account_type` tinyint(1) NOT NULL DEFAULT '1',
 `user_has_avatar` tinyint(1) NOT NULL DEFAULT '0',
 `user_remember_me_token` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
 `user_suspension_timestamp` bigint(20) DEFAULT NULL,
 `user_last_login_timestamp` bigint(20) DEFAULT NULL,
 `user_failed_logins` tinyint(1) NOT NULL DEFAULT '0',
 `user_last_failed_login` int(10) DEFAULT NULL,
 `user_activation_hash` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
 `user_profile` tinyint(1) NOT NULL DEFAULT '1',
 `user_password_reset_hash` char(40) COLLATE utf8_unicode_ci DEFAULT NULL,
 `user_password_reset_timestamp` bigint(20) DEFAULT NULL,
 `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 PRIMARY KEY (`user_id`),
 UNIQUE KEY `user_name` (`user_name`),
 UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO users (user_name, user_password_hash, user_email, user_account_type) 
VALUES('demo', '$2y$10$w92ibsqyGjlGBL8vQipbJeS9BCUcbC8j0vxIgfC5ShxAdcsXC/s3W', 'john@example.com', '1');

INSERT INTO users (user_name, user_password_hash, user_email) 
VALUES('demo2', '$2y$10$w92ibsqyGjlGBL8vQipbJeS9BCUcbC8j0vxIgfC5ShxAdcsXC/s3W', 'demo@example.com');

INSERT INTO users (user_name, user_password_hash, user_email, user_deleted) 
VALUES('mrSpam', '$2y$10$w92ibsqyGjlGBL8vQipbJeS9BCUcbC8j0vxIgfC5ShxAdcsXC/s3W', 'spam.alot@example.com', '1');