-- Anthem configured for Казначей project
-- SQL file merged for modules: Anthem/Auth, Anthem/Settings, Anthem/Notify, AnthemCM/Pages, AnthemCM/UserProfile, AnthemCM/Feedback, Mockingbird

-- This is a fix for InnoDB in MySQL >= 4.1.x
-- It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.


-- ---------------------------------------------------------------------
-- users
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(160) NOT NULL,
    `algorithm` VARCHAR(16),
    `salt` VARCHAR(160),
    `password` VARCHAR(160),
    `locked` VARCHAR(255),
    `is_superuser` TINYINT(1) DEFAULT 0 NOT NULL,
    `last_login` DATETIME,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `auth_user_email_idx` (`email`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- user_keys
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `user_keys`;

CREATE TABLE `user_keys`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `type` VARCHAR(40) NOT NULL,
    `uniqid` VARCHAR(80) NOT NULL,
    `data` TEXT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `auth_user_keys_idx` (`uniqid`),
    INDEX `user_keys_FI_1` (`user_id`),
    CONSTRAINT `user_keys_FK_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- user_social_accounts
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `user_social_accounts`;

CREATE TABLE `user_social_accounts`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `provider` VARCHAR(40) NOT NULL,
    `remote_user_id` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255),
    PRIMARY KEY (`id`),
    UNIQUE INDEX `auth_user_social_accounts_idx` (`provider`, `remote_user_id`),
    INDEX `user_social_accounts_FI_1` (`user_id`),
    CONSTRAINT `user_social_accounts_FK_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- groups
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `groups`;

CREATE TABLE `groups`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `auth_group_title_idx` (`title`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- user_policies
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `user_policies`;

CREATE TABLE `user_policies`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `policy` VARCHAR(160) NOT NULL,
    `enable` TINYINT(1) DEFAULT 1 NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `user_policies_FI_1` (`user_id`),
    CONSTRAINT `user_policies_FK_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- group_policies
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `group_policies`;

CREATE TABLE `group_policies`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `group_id` INTEGER NOT NULL,
    `policy` VARCHAR(160) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `group_policies_FI_1` (`group_id`),
    CONSTRAINT `group_policies_FK_1`
        FOREIGN KEY (`group_id`)
        REFERENCES `groups` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- ref_users_groups
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `ref_users_groups`;

CREATE TABLE `ref_users_groups`
(
    `user_id` INTEGER NOT NULL,
    `group_id` INTEGER NOT NULL,
    PRIMARY KEY (`user_id`,`group_id`),
    INDEX `ref_user_group_user_id_idx` (`user_id`),
    INDEX `ref_user_group_group_id_idx` (`group_id`),
    CONSTRAINT `ref_users_groups_FK_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `ref_users_groups_FK_2`
        FOREIGN KEY (`group_id`)
        REFERENCES `groups` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier


# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.


-- ---------------------------------------------------------------------
-- notifications
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `notifications`;

CREATE TABLE `notifications`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `uniqid` VARCHAR(40) NOT NULL,
    `message` TEXT NOT NULL,
    `output_class` VARCHAR(40),
    `no_dismiss` TINYINT(1) DEFAULT 0 NOT NULL,
    `user_id` INTEGER,
    `policies` VARCHAR(255),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `uniqid_idx` (`user_id`, `uniqid`),
    CONSTRAINT `notifications_FK_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier


# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.


-- ---------------------------------------------------------------------
-- settings
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(80) NOT NULL,
    `value` TEXT NOT NULL,
    `user_id` INTEGER,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `setting_key_idx` (`user_id`, `key`),
    CONSTRAINT `settings_FK_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier


# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.


-- ---------------------------------------------------------------------
-- blogs
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `blogs`;

CREATE TABLE `blogs`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `url` VARCHAR(255),
    `is_active` TINYINT(1) NOT NULL,
    `is_public` TINYINT(1) DEFAULT 1 NOT NULL,
    `is_news_mode` TINYINT(1) NOT NULL,
    `user_id` INTEGER,
    `group_id` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `blogs_url_idx` (`url`),
    INDEX `blogs_FI_1` (`user_id`),
    INDEX `blogs_FI_2` (`group_id`),
    CONSTRAINT `blogs_FK_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE SET NULL,
    CONSTRAINT `blogs_FK_2`
        FOREIGN KEY (`group_id`)
        REFERENCES `groups` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- blog_posts
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `blog_posts`;

CREATE TABLE `blog_posts`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `blog_id` INTEGER NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `url` VARCHAR(255) NOT NULL,
    `content` TEXT,
    `is_active` TINYINT(1) NOT NULL,
    `user_id` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `blog_posts_url_idx` (`url`),
    INDEX `blog_posts_FI_1` (`blog_id`),
    INDEX `blog_posts_FI_2` (`user_id`),
    CONSTRAINT `blog_posts_FK_1`
        FOREIGN KEY (`blog_id`)
        REFERENCES `blogs` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `blog_posts_FK_2`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier


# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.


-- ---------------------------------------------------------------------
-- feedbacks
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `feedbacks`;

CREATE TABLE `feedbacks`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER,
    `email` VARCHAR(255),
    `content` TEXT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `feedbacks_FI_1` (`user_id`),
    CONSTRAINT `feedbacks_FK_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier


# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.


-- ---------------------------------------------------------------------
-- pages
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `pages`;

CREATE TABLE `pages`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `url` VARCHAR(255) NOT NULL,
    `content` TEXT,
    `is_active` TINYINT(1) NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier


# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.


-- ---------------------------------------------------------------------
-- user_profiles
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `user_profiles`;

CREATE TABLE `user_profiles`
(
    `id` INTEGER NOT NULL,
    `firstname` VARCHAR(255),
    `lastname` VARCHAR(255),
    `nickname` VARCHAR(255),
    `avatar` VARCHAR(255),
    PRIMARY KEY (`id`),
    CONSTRAINT `user_profiles_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier


# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.


-- ---------------------------------------------------------------------
-- accounts
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `accounts`;

CREATE TABLE `accounts`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `currency_id` INTEGER NOT NULL,
    `initial_amount` DECIMAL(10,2) DEFAULT 0 NOT NULL,
    `isclosed` TINYINT(1) DEFAULT 0 NOT NULL,
    `isdebt` TINYINT(1) DEFAULT 0 NOT NULL,
    `iscredit` TINYINT(1) DEFAULT 0 NOT NULL,
    `color` VARCHAR(10) DEFAULT '#000000',
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `accounts_FI_1` (`user_id`),
    INDEX `accounts_FI_2` (`currency_id`),
    CONSTRAINT `accounts_FK_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `accounts_FK_2`
        FOREIGN KEY (`currency_id`)
        REFERENCES `currencies` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- transactions
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `transactions`;

CREATE TABLE `transactions`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `category_id` INTEGER,
    `account_id` INTEGER NOT NULL,
    `target_account_id` INTEGER,
    `counter_transaction_id` INTEGER,
    `counter_party_id` INTEGER,
    `parent_transaction_id` INTEGER,
    `amount` DECIMAL(10,2) DEFAULT 0 NOT NULL,
    `isprojected` TINYINT(1) DEFAULT 0 NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `transaction_title_idx` (`title`),
    INDEX `transaction_category_id_idx` (`category_id`),
    INDEX `transaction_amount_idx` (`amount`),
    INDEX `transaction_isprojected_idx` (`isprojected`),
    INDEX `transaction_account_id_idx` (`account_id`),
    INDEX `transaction_target_account_id_idx` (`target_account_id`),
    INDEX `transaction_counter_transaction_id_idx` (`counter_transaction_id`),
    INDEX `transaction_counter_party_id_idx` (`counter_party_id`),
    INDEX `transaction_parent_transaction_id_idx` (`parent_transaction_id`),
    INDEX `transactions_FI_1` (`user_id`),
    CONSTRAINT `transactions_FK_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `transactions_FK_2`
        FOREIGN KEY (`category_id`)
        REFERENCES `transaction_categories` (`id`),
    CONSTRAINT `transactions_FK_3`
        FOREIGN KEY (`account_id`)
        REFERENCES `accounts` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `transactions_FK_4`
        FOREIGN KEY (`target_account_id`)
        REFERENCES `accounts` (`id`),
    CONSTRAINT `transactions_FK_5`
        FOREIGN KEY (`counter_transaction_id`)
        REFERENCES `transactions` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `transactions_FK_6`
        FOREIGN KEY (`counter_party_id`)
        REFERENCES `counter_parties` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `transactions_FK_7`
        FOREIGN KEY (`parent_transaction_id`)
        REFERENCES `transactions` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- transaction_categories
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `transaction_categories`;

CREATE TABLE `transaction_categories`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `color` VARCHAR(10) DEFAULT '#000000',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `transaction_category_title_idx` (`user_id`, `title`),
    CONSTRAINT `transaction_categories_FK_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- transaction_tags
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `transaction_tags`;

CREATE TABLE `transaction_tags`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `transaction_tag_title_idx` (`user_id`, `title`),
    CONSTRAINT `transaction_tags_FK_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- ref_transactions_tags
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `ref_transactions_tags`;

CREATE TABLE `ref_transactions_tags`
(
    `transaction_id` INTEGER NOT NULL,
    `tag_id` INTEGER NOT NULL,
    PRIMARY KEY (`transaction_id`,`tag_id`),
    INDEX `ref_transaction_tag_transaction_id_idx` (`transaction_id`),
    INDEX `ref_transaction_tag_tag_id_idx` (`tag_id`),
    CONSTRAINT `ref_transactions_tags_FK_1`
        FOREIGN KEY (`transaction_id`)
        REFERENCES `transactions` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `ref_transactions_tags_FK_2`
        FOREIGN KEY (`tag_id`)
        REFERENCES `transaction_tags` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- counter_parties
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `counter_parties`;

CREATE TABLE `counter_parties`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `counter_party_title_idx` (`user_id`, `title`),
    CONSTRAINT `counter_parties_FK_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- budgets
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `budgets`;

CREATE TABLE `budgets`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `year` INTEGER NOT NULL,
    `month` INTEGER,
    `currency_id` INTEGER,
    PRIMARY KEY (`id`),
    INDEX `budgets_FI_1` (`user_id`),
    INDEX `budgets_FI_2` (`currency_id`),
    CONSTRAINT `budgets_FK_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `budgets_FK_2`
        FOREIGN KEY (`currency_id`)
        REFERENCES `currencies` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- budget_entries
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `budget_entries`;

CREATE TABLE `budget_entries`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `budget_id` INTEGER NOT NULL,
    `category_id` INTEGER NOT NULL,
    `amount` DECIMAL(10,2) DEFAULT 0 NOT NULL,
    `when_entry` INTEGER,
    `description` TEXT,
    PRIMARY KEY (`id`),
    INDEX `budget_entry_budget_id_idx` (`budget_id`),
    INDEX `budget_entry_category_id_idx` (`category_id`),
    CONSTRAINT `budget_entries_FK_1`
        FOREIGN KEY (`budget_id`)
        REFERENCES `budgets` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `budget_entries_FK_2`
        FOREIGN KEY (`category_id`)
        REFERENCES `transaction_categories` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- currencies
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `currencies`;

CREATE TABLE `currencies`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(10) NOT NULL,
    `format` VARCHAR(20) NOT NULL,
    `is_primary` TINYINT(1) DEFAULT 0 NOT NULL,
    `rate_to_primary` FLOAT DEFAULT 1.0 NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier

-- This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
