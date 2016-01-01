

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema abouda
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `abouda` ;

-- -----------------------------------------------------
-- Schema abouda
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `abouda` DEFAULT CHARACTER SET utf8 ;
USE `abouda` ;

-- -----------------------------------------------------
-- Table `abouda`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `abouda`.`users` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `user_email` VARCHAR(45) NOT NULL,
  `user_password` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE INDEX `user_email_UNIQUE` (`user_email` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `abouda`.`pictures`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `abouda`.`pictures` (
  `picture_id` INT NOT NULL AUTO_INCREMENT,
  `picture_path` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`picture_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `abouda`.`users_info`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `abouda`.`users_info` (
  `user_id` INT NOT NULL,
  `user_fname` VARCHAR(45) NOT NULL,
  `user_lname` VARCHAR(45) NOT NULL,
  `user_mobile` VARCHAR(45) NULL,
  `user_gender` TINYINT(1) NOT NULL,
  `user_birthdate` DATE NOT NULL,
  `user_picture` INT NULL,
  `user_thumbnail` INT NULL,
  `user_hometown` VARCHAR(45) NULL,
  `user_marital` VARCHAR(45) NULL,
  `user_about` TEXT NULL,
  PRIMARY KEY (`user_id`),
  INDEX `fk_users_info_pictures1_idx` (`user_picture` ASC),
  INDEX `fk_users_info_pictures2_idx` (`user_thumbnail` ASC),
  CONSTRAINT `fk_users_info_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `abouda`.`users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_users_info_pictures1`
    FOREIGN KEY (`user_picture`)
    REFERENCES `abouda`.`pictures` (`picture_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_users_info_pictures2`
    FOREIGN KEY (`user_thumbnail`)
    REFERENCES `abouda`.`pictures` (`picture_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `abouda`.`friendships`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `abouda`.`friendships` (
  `user_id` INT NOT NULL,
  `friend_id` INT NOT NULL,
  `friendship_type` VARCHAR(45) NOT NULL,
  `friendship_timestamp` TIMESTAMP NOT NULL DEFAULT NOW(),
  PRIMARY KEY (`user_id`, `friend_id`),
  INDEX `fk_friends_users2_idx` (`friend_id` ASC),
  CONSTRAINT `fk_friends_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `abouda`.`users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_friends_users2`
    FOREIGN KEY (`friend_id`)
    REFERENCES `abouda`.`users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `abouda`.`posts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `abouda`.`posts` (
  `post_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `post_privacy` TINYINT(1) NOT NULL DEFAULT 1,
  `post_timestamp` TIMESTAMP NULL DEFAULT NOW(),
  `post_text` TEXT NULL,
  `post_picture` INT NULL,
  PRIMARY KEY (`post_id`),
  INDEX `fk_posts_pictures1_idx` (`post_picture` ASC),
  INDEX `fk_posts_users1_idx` (`user_id` ASC),
  CONSTRAINT `fk_posts_pictures1`
    FOREIGN KEY (`post_picture`)
    REFERENCES `abouda`.`pictures` (`picture_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_posts_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `abouda`.`users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `abouda`.`likes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `abouda`.`likes` (
  `post_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `like_timestamp` TIMESTAMP NOT NULL DEFAULT NOW(),
  PRIMARY KEY (`post_id`, `user_id`),
  INDEX `fk_likes_users1_idx` (`user_id` ASC),
  CONSTRAINT `fk_likes_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `abouda`.`users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_likes_posts1`
    FOREIGN KEY (`post_id`)
    REFERENCES `abouda`.`posts` (`post_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `abouda`.`comments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `abouda`.`comments` (
  `comment_id` INT NOT NULL AUTO_INCREMENT,
  `post_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `comment_text` TEXT NULL,
  `comment_timestamp` TIMESTAMP NULL DEFAULT NOW(),
  PRIMARY KEY (`comment_id`),
  INDEX `fk_comments_users1_idx` (`user_id` ASC),
  INDEX `fk_comments_posts1_idx` (`post_id` ASC),
  CONSTRAINT `fk_comments_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `abouda`.`users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_comments_posts1`
    FOREIGN KEY (`post_id`)
    REFERENCES `abouda`.`posts` (`post_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `abouda`.`tokens`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `abouda`.`tokens` (
  `user_id` INT NOT NULL,
  `user_token` VARCHAR(45) NOT NULL,
  `token_timestamp` TIMESTAMP NOT NULL DEFAULT NOW(),
  `token_address` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE INDEX `user_id_UNIQUE` (`user_id` ASC),
  CONSTRAINT `fk_tokens_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `abouda`.`users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `abouda`.`tokens`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `abouda`.`tokens` (
  `user_id` INT NOT NULL,
  `user_token` VARCHAR(45) NOT NULL,
  `token_timestamp` TIMESTAMP NOT NULL DEFAULT NOW(),
  `token_address` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE INDEX `user_id_UNIQUE` (`user_id` ASC),
  CONSTRAINT `fk_tokens_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `abouda`.`users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `abouda`.`notifications`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `abouda`.`notifications` (
  `user_id` INT NOT NULL,
  `notification_id` INT NOT NULL AUTO_INCREMENT,
  `notification_text` VARCHAR(200) NULL,
  `notification_timestamp` TIMESTAMP NOT NULL DEFAULT NOW(),
  `notification_read` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`notification_id`),
  INDEX `fk_notifications_users1_idx` (`user_id` ASC),
  CONSTRAINT `fk_notifications_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `abouda`.`users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
