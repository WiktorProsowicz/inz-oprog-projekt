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
    `name` TEXT(50) NOT NULL,

    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS posts (
    `id` INT NOT NULL AUTO_INCREMENT,
    `author_id` INT NOT NULL,
    `content` TEXT(40000) NOT NULL,
    `created` DATE NOT NULL,
    `modified` DATE,
    `likes` INT NOT NULL,
    `dislikes` INT NOT NULL,
    `category_id` INT NOT NULL,

    PRIMARY KEY (`id`),
    FOREIGN KEY (`author_id`) REFERENCES users(`id`),
    FOREIGN KEY (`category_id`) REFERENCES categories(`id`)
);


CREATE TABLE IF NOT EXISTS comments (
    `id` INT NOT NULL AUTO_INCREMENT,
    `author_id` INT NOT NULL,
    `post_id` INT NOT NULL,
    `content` TEXT(3000),
    `created` DATETIME NOT NULL,
    `likes` INT NOT NULL,
    `dislikes` INT NOT NULL,

    PRIMARY KEY (`id`),
    FOREIGN KEY (`author_id`) REFERENCES users(`id`),
    FOREIGN KEY (`post_id`) REFERENCES posts(`id`)
);

CREATE TABLE IF NOT EXISTS tags (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(20) NOT NULL,

    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS tags_in_posts (
    `tag_id` INT NOT NULL,
    `post_id` INT NOT NULL,

    FOREIGN KEY (`tag_id`) REFERENCES tags(`id`),
    FOREIGN KEY (`post_id`) REFERENCES posts(`id`)
);

CREATE TABLE IF NOT EXISTS watchers (
    `user_id` INT NOT NULL,
    `watcher_id` INT NOT NULL,

    FOREIGN KEY (`user_id`) REFERENCES users(`id`),
    FOREIGN KEY (`watcher_id`) REFERENCES users(`id`)
);
