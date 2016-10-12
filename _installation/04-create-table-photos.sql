CREATE TABLE IF NOT EXISTS 'photos' (
 'photo_id' int(11) NOT NULL AUTO_INCREMENT,
 'user_id' int(11) NOT NULL,
 'description' TEXT,
 'created_at' timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 'updated_at' timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 PRIMARY KEY ('photos_id'),
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE photos
ADD CONSTRAINT fk_user
FOREIGN KEY (user_id)
REFERENCES users(user_id)
