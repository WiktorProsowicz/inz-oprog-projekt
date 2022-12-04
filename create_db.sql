CREATE TABLE IF NOT EXISTS users (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(20) NOT NULL,
    `email` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `created_account` DATE NOT NULL,
    `blocked` BOOL NOT NULL,
    `blocked_until` DATE,
    `admin` BOOL NOT NULL,
    `profile_img` LONGBLOB,
    `description` TEXT(500),

    PRIMARY KEY (`id`)
) AUTO_INCREMENT=10;

CREATE TABLE IF NOT EXISTS categories (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` TEXT(50) NOT NULL UNIQUE,

    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS posts (
    `id` INT NOT NULL AUTO_INCREMENT,
    `author_id` INT NOT NULL,
    `title` VARCHAR(100) NOT NULL,
    `content` TEXT(20000) NOT NULL,
    `created` DATETIME NOT NULL,
    `modified` DATETIME,
    `category_id` INT NOT NULL,

    PRIMARY KEY (`id`),
    FOREIGN KEY (`author_id`) REFERENCES users(`id`),
    FOREIGN KEY (`category_id`) REFERENCES categories(`id`)
);

CREATE TABLE IF NOT EXISTS ratings (
    `user_id` INT NOT NULL,
    `post_id` INT NOT NULL,
    `is_like` BOOLEAN NOT NULL,

    FOREIGN KEY (`user_id`) REFERENCES users(`id`),
    FOREIGN KEY (`post_id`) REFERENCES posts(`id`)
);

CREATE TABLE IF NOT EXISTS comments (
    `id` INT NOT NULL AUTO_INCREMENT,
    `author_id` INT NOT NULL,
    `post_id` INT NOT NULL,
    `content` TEXT(1500),
    `created` DATETIME NOT NULL,

    PRIMARY KEY (`id`),
    FOREIGN KEY (`author_id`) REFERENCES users(`id`),
    FOREIGN KEY (`post_id`) REFERENCES posts(`id`)
);

CREATE TABLE IF NOT EXISTS comments_ratings (
    `user_id` INT NOT NULL,
    `comment_id` INT NOT NULL,
    `is_like` BOOLEAN NOT NULL,

    FOREIGN KEY (`user_id`) REFERENCES users(`id`),
    FOREIGN KEY (`comment_id`) REFERENCES comments(`id`)
);

CREATE TABLE IF NOT EXISTS tags (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(20) NOT NULL UNIQUE,

    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS tags_in_posts (
    `tag_id` INT NOT NULL,
    `post_id` INT NOT NULL,

    FOREIGN KEY (`tag_id`) REFERENCES tags(`id`),
    FOREIGN KEY (`post_id`) REFERENCES posts(`id`),

    CONSTRAINT `tag_post` UNIQUE (`tag_id`, `post_id`)
);

CREATE TABLE IF NOT EXISTS watchers (
    `user_id` INT NOT NULL,
    `watcher_id` INT NOT NULL,

    FOREIGN KEY (`user_id`) REFERENCES users(`id`),
    FOREIGN KEY (`watcher_id`) REFERENCES users(`id`)
);

CREATE TABLE IF NOT EXISTS search_queries (
    `id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT,
    `date` DATETIME NOT NULL,
    `query` VARCHAR(100) NOT NULL,

    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES users(`id`)
);

CREATE TABLE IF NOT EXISTS post_views (
    `post_id` INT NOT NULL,
    `user_id` INT,
    `date` DATETIME NOT NULL,

    FOREIGN KEY (`post_id`) REFERENCES posts(`id`),
    FOREIGN KEY (`user_id`) REFERENCES users(`id`)
);

CREATE TABLE IF NOT EXISTS notifications (
    `id` INT NOT NULL AUTO_INCREMENT,
    `author_id` INT NOT NULL,
    `recipient_id` INT NOT NULL,
    `content` VARCHAR(500) NOT NULL,
    `high_priority` BOOLEAN NOT NULL,
    `watched` BOOLEAN NOT NULL,
    `date` DATETIME NOT NULL,

    PRIMARY KEY (`id`),
    FOREIGN KEY (`author_id`) REFERENCES users(`id`),
    FOREIGN KEY (`recipient_id`) REFERENCES users(`id`)  
);

CREATE TABLE IF NOT EXISTS reports (
    `id` INT NOT NULL AUTO_INCREMENT,
    `author_id` INT NOT NULL,
    `post_id` INT,
    `comment_id` INT,
    `user_id` INT,
    `content` VARCHAR(200) NOT NULL,
    `date` DATETIME NOT NULL,

    PRIMARY KEY (`id`),
    FOREIGN KEY (`author_id`) REFERENCES users(`id`),
    FOREIGN KEY (`comment_id`) REFERENCES comments(`id`),
    FOREIGN KEY (`user_id`) REFERENCES users(`id`)
);

-- reset the catehories table
-- DELETE FROM categories;
-- ALTER TABLE categories AUTO_INCREMENT = 1;

-- add hardcoded categories
INSERT IGNORE INTO categories (`name`) VALUES 
('Opowiadania erotyczne'),
('Fanfiki'),
('Kawały'),
('Informacje'),
('Poezja'),
('Proza'),
('Gore'),
('Gnioooo'),
('Szniooooo'),
('Tutoriale'),
('Lifehacki'),
('Inne');