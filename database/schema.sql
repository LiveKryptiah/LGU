-- ============================================================
-- Government Workflow OS — Database Schema
-- Step 1 & Step 2: Foundation, Organization & Dashboard Tables
-- ============================================================

CREATE DATABASE IF NOT EXISTS `government_workflow_os`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `government_workflow_os`;

-- Disable foreign key checks during initialization
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- Table: organizations
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `organizations`;
CREATE TABLE `organizations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `organization_type` VARCHAR(100) NOT NULL DEFAULT 'Provincial Government',
  `address` TEXT NULL,
  `logo` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: offices
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `offices`;
CREATE TABLE `offices` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_offices_org` (`organization_id`),
  CONSTRAINT `fk_offices_organization`
    FOREIGN KEY (`organization_id`)
    REFERENCES `organizations` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: employees
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `office_id` INT UNSIGNED NOT NULL,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `position` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'supervisor', 'staff') NOT NULL DEFAULT 'staff',
  `status` ENUM('active', 'inactive', 'on_leave') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_employees_email` (`email`),
  KEY `idx_employees_org` (`organization_id`),
  KEY `idx_employees_office` (`office_id`),
  CONSTRAINT `fk_employees_organization`
    FOREIGN KEY (`organization_id`)
    REFERENCES `organizations` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_employees_office`
    FOREIGN KEY (`office_id`)
    REFERENCES `offices` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: tasks
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `office_id` INT UNSIGNED NULL,
  `assigned_to` INT UNSIGNED NULL,
  `created_by` INT UNSIGNED NULL,

  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,

  `status` ENUM(
    'pending',
    'in_progress',
    'completed',
    'cancelled'
  ) NOT NULL DEFAULT 'pending',

  `priority` ENUM(
    'low',
    'normal',
    'high',
    'urgent'
  ) NOT NULL DEFAULT 'normal',

  `due_date` DATE NULL,

  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  INDEX `idx_tasks_assigned_to` (`assigned_to`),
  INDEX `idx_tasks_status` (`status`),
  INDEX `idx_tasks_due_date` (`due_date`),
  INDEX `idx_tasks_org` (`organization_id`),
  INDEX `idx_tasks_office` (`office_id`),

  CONSTRAINT `fk_tasks_organization`
    FOREIGN KEY (`organization_id`)
    REFERENCES `organizations` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_tasks_office`
    FOREIGN KEY (`office_id`)
    REFERENCES `offices` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,

  CONSTRAINT `fk_tasks_assigned_to`
    FOREIGN KEY (`assigned_to`)
    REFERENCES `employees` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,

  CONSTRAINT `fk_tasks_created_by`
    FOREIGN KEY (`created_by`)
    REFERENCES `employees` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: activity_logs
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `employee_id` INT UNSIGNED NULL,

  `action` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) NOT NULL,

  `entity_type` VARCHAR(50) NULL,
  `entity_id` INT UNSIGNED NULL,

  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  INDEX `idx_activity_employee_id` (`employee_id`),
  INDEX `idx_activity_created_at` (`created_at`),
  INDEX `idx_activity_org` (`organization_id`),

  CONSTRAINT `fk_activity_organization`
    FOREIGN KEY (`organization_id`)
    REFERENCES `organizations` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_activity_employee`
    FOREIGN KEY (`employee_id`)
    REFERENCES `employees` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
