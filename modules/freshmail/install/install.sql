CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_setting`(
    `id_freshmail_setting` INT NOT NULL AUTO_INCREMENT ,
    `id_shop` INT NOT NULL ,
    `api_token` VARCHAR(100) NOT NULL ,
    `smtp` TINYINT(1) NOT NULL ,
    `synchronize` TINYINT(1) NOT NULL ,
    `wizard_completed` TINYINT(1) NOT NULL DEFAULT 0,
    `send_confirmation` TINYINT(1) NOT NULL DEFAULT 0,
    `subscriber_list_hash` char(10) COLLATE utf8_general_ci NOT NULL,
    `id_specific_price_rule` int(10)  UNSIGNED NOT NULL,
    `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    `date_upd` TIMESTAMP NULL,
    KEY `id_freshmail_form` (`subscriber_list_hash`) USING BTREE,
    PRIMARY KEY (`id_freshmail_setting`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_list_email`(
    `id_freshmail_list_email` INT NOT NULL AUTO_INCREMENT ,
    `email` VARCHAR (150),
    `hash_list` char(10) COLLATE utf8_general_ci NOT NULL,
    `last_synchronization` TIMESTAMP,
    `add_date` TIMESTAMP,
    `deletion_date` TIMESTAMP,
    `status`  VARCHAR (20),
    `resigning_reason` VARCHAR (250),
    PRIMARY KEY (`id_freshmail_list_email`),
    CONSTRAINT `PREFIX_freshmail_list_email_ibfk_1` FOREIGN KEY (`hash_list`) REFERENCES `PREFIX_freshmail_setting` (`subscriber_list_hash`) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE (hash_list, email)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_async_job` (
    `id_freshmail_async_job` INT NOT NULL AUTO_INCREMENT ,
    `id_job` INT NOT NULL ,
    `hash_list` CHAR(10) NOT NULL ,
    `parts` INT NOT NULL ,
    `last_sync` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
    `finished` TINYINT(1) DEFAULT 0,
    `job_status` TINYINT(1) DEFAULT 0,
    `filename` VARCHAR (50),
    `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    PRIMARY KEY (`id_freshmail_async_job`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_emails_synchronized` (
    `id_freshmail_emails_synchronized` INT NOT NULL AUTO_INCREMENT ,
    `email` VARCHAR (150),
    `hash_list` CHAR(10) NOT NULL ,
    `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    PRIMARY KEY (`id_freshmail_emails_synchronized`),
    UNIQUE (hash_list, email)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_emails_to_synchronize` (
    `id_freshmail_emails_to_synchronize` INT NOT NULL AUTO_INCREMENT ,
    `email` VARCHAR (150),
    `name` VARCHAR (150),
    `hash_list` CHAR(10) NOT NULL ,
    PRIMARY KEY (`id_freshmail_emails_to_synchronize`),
    UNIQUE (hash_list, email)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_form` (
    `id_freshmail_form` int(11) NOT NULL AUTO_INCREMENT,
    `id_shop` INT NOT NULL ,
    `form_hash` char(10) COLLATE utf8_general_ci NOT NULL,
    `hook` varchar(50) COLLATE utf8_general_ci NOT NULL,
    `position` int(11) NOT NULL,
    `active` tinyint(1) NOT NULL,
    PRIMARY KEY (`id_freshmail_form`),
    KEY `id_freshmail_form` (`form_hash`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_cart_setting`(
    `id_freshmail_cart_setting` INT NOT NULL AUTO_INCREMENT ,
    `id_shop` INT NOT NULL ,
    `enabled` TINYINT(1) NOT NULL DEFAULT 0,
    `discount_type` ENUM ('none', 'percent', 'custom'),
    `discount_percent` INT,
    `discount_code` VARCHAR (50),
    `discount_lifetime` INT,
    `send_after` INT,
    `template` TEXT,
    `template_id_hash` VARCHAR(32),
    `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    `date_upd` TIMESTAMP NULL,
    PRIMARY KEY (`id_freshmail_cart_setting`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_cart_setting_lang`(
    `id_freshmail_cart_setting` INT NOT NULL AUTO_INCREMENT ,
    `id_lang` INT NOT NULL ,
    `email_subject` TEXT,
    `email_preheader` TEXT,
    PRIMARY KEY (`id_freshmail_cart_setting`, `id_lang`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_cart`(
    `id_freshmail_cart` INT NOT NULL AUTO_INCREMENT ,
    `id_cart` INT NOT NULL UNIQUE ,
    `id_cart_rule` INT NULL,
    `discount_code` VARCHAR(50) NULL,
    `cart_token` VARCHAR (64),
    `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    `date_upd` TIMESTAMP NULL,
    PRIMARY KEY (`id_freshmail_cart`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_cart_notify`(
    `id` INT NOT NULL AUTO_INCREMENT ,
    `id_freshmail_cart` INT NOT NULL,
    `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    PRIMARY KEY (`id`),
    CONSTRAINT `PREFIX_freshmail_cart_ibfk_1` FOREIGN KEY (`id_freshmail_cart`) REFERENCES `PREFIX_freshmail_cart` (`id_freshmail_cart`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_birthday`(
    `id_freshmail_birthday` INT NOT NULL AUTO_INCREMENT ,
    `id_shop` INT NOT NULL ,
    `enable` TINYINT(1) NOT NULL DEFAULT 0 ,
    `tpl` VARCHAR(32),
    `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    `date_upd` TIMESTAMP NULL,
    PRIMARY KEY (`id_freshmail_birthday`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `PREFIX_freshmail_birthday_lang`(
   `id_freshmail_birthday` INT NOT NULL AUTO_INCREMENT ,
   `id_lang` INT NOT NULL ,
   `content` TEXT,
   `email_subject` TEXT,
   PRIMARY KEY (`id_freshmail_birthday`, `id_lang`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;