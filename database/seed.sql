-- ============================================================
-- Government Workflow OS — Initial Seed Data
-- Step 1: Foundation & Application Shell
-- ============================================================

USE `government_workflow_os`;

-- ------------------------------------------------------------
-- 1. Demo Organization: Provincial Government
-- ------------------------------------------------------------
INSERT INTO `organizations` (`id`, `name`, `organization_type`, `address`, `logo`, `created_at`)
VALUES
(1, 'Provincial Government', 'Provincial Government', 'Provincial Capitol Compound, Provincial Government Center', 'assets/images/logo.svg', NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- ------------------------------------------------------------
-- 2. Mandated Departmental Offices
-- ------------------------------------------------------------
INSERT INTO `offices` (`id`, `organization_id`, `name`, `description`, `status`, `created_at`)
VALUES
(1, 1, 'Provincial Assessor\'s Office', 'Responsible for establishing a systematic method of real property assessment and appraisal.', 'active', NOW()),
(2, 1, 'Provincial Treasurer\'s Office', 'Custody and disbursement of local government funds, tax collection, and treasury affairs.', 'active', NOW()),
(3, 1, 'Provincial Engineering Office', 'Planning, construction, and maintenance of provincial infrastructure, roads, and bridges.', 'active', NOW()),
(4, 1, 'Human Resource Management Office', 'Personnel administration, civil service compliance, staff development, and employee welfare.', 'active', NOW()),
(5, 1, 'General Services Office', 'Property and asset management, procurement records, maintenance, and logistical support.', 'active', NOW()),
(6, 1, 'Planning and Development Office', 'Formulation of comprehensive provincial development plans, socio-economic monitoring, and spatial analysis.', 'active', NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `description` = VALUES(`description`);

-- ------------------------------------------------------------
-- 3. Demo Employee: Juan Dela Cruz (Administrative Officer)
-- Password for demo: Password123!
-- ------------------------------------------------------------
INSERT INTO `employees` (`id`, `organization_id`, `office_id`, `first_name`, `last_name`, `position`, `email`, `password`, `role`, `status`, `created_at`)
VALUES
(1, 1, 1, 'Juan', 'Dela Cruz', 'Administrative Officer', 'juan.delacruz@pgov.ph', '$2y$10$bSgFvFNOjaXt7teieTYjsuwn8QNFSZC9y4Un2kH3ic2IDYCEWQrNO', 'admin', 'active', NOW())
ON DUPLICATE KEY UPDATE
  `first_name` = VALUES(`first_name`),
  `last_name` = VALUES(`last_name`),
  `position` = VALUES(`position`),
  `office_id` = VALUES(`office_id`),
  `password` = VALUES(`password`);
