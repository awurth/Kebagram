CREATE TABLE IF NOT EXISTS 'photos' (
 'photo_id' int(11) NOT NULL AUTO_INCREMENT,
 'user_id' int(11) NOT NULL,
 'description' TEXT,
 'created_at' timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 'updated_at' timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 PRIMARY KEY ('photos_id'),
 UNIQUE KEY 'user_name' ('user_name'),
 UNIQUE KEY 'user_email' ('user_email')
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/** FOREIGN KEY A FAIRE , J'AI PLUS LA SYNTAXE MYSQL EN TETE */
