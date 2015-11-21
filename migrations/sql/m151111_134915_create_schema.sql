-- MySQL Script generated by MySQL Workbench
-- 11/21/15 10:52:56
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema homecost
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `bank`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bank` ;

CREATE TABLE IF NOT EXISTS `bank` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `date_operation` DATE NOT NULL,
  `label` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(25,2) NOT NULL,
  `date_created` DATETIME NOT NULL,
  `status` INT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_label` (`label` ASC),
  INDEX `idx_status` (`status` ASC))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `category`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `category` ;

CREATE TABLE IF NOT EXISTS `category` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(45) NULL DEFAULT NULL,
  `tag` VARCHAR(10) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_tag` (`tag` ASC))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cost`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cost` ;

CREATE TABLE IF NOT EXISTS `cost` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `amount` DECIMAL(25,2) NULL,
  `guessed` INT(1) NOT NULL DEFAULT 0,
  `category_id` INT(11) NOT NULL,
  `bank_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `FK_cost_id_category` (`category_id` ASC),
  INDEX `FK_cost_id_bank` (`bank_id` ASC))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `subcategory`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `subcategory` ;

CREATE TABLE IF NOT EXISTS `subcategory` (
  `id` INT(11) NOT NULL,
  `category_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`, `category_id`),
  INDEX `FK_subcategory_id_category` (`category_id` ASC))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rules`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `rules` ;

CREATE TABLE IF NOT EXISTS `rules` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `rule` VARCHAR(255) NOT NULL,
  `category_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_rules_category1_idx` (`category_id` ASC))
  ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
