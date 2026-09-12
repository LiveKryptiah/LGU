-- ============================================================
-- Government Workflow OS — Initial Seed Data
-- Step 1 & Step 2: Organizations, Offices, Employees, Tasks & Activity Logs
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
-- 3. Departmental Personnel / Employees
-- Password for all demo accounts: Password123!
-- ------------------------------------------------------------
INSERT INTO `employees` (`id`, `organization_id`, `office_id`, `first_name`, `last_name`, `position`, `email`, `password`, `role`, `status`, `created_at`)
VALUES
(1, 1, 1, 'Juan', 'Dela Cruz', 'Administrative Officer', 'juan.delacruz@pgov.ph', '$2y$10$bSgFvFNOjaXt7teieTYjsuwn8QNFSZC9y4Un2kH3ic2IDYCEWQrNO', 'admin', 'active', NOW()),
(2, 1, 2, 'Maria', 'Santos', 'Treasury Operations Officer', 'maria.santos@pgov.ph', '$2y$10$bSgFvFNOjaXt7teieTYjsuwn8QNFSZC9y4Un2kH3ic2IDYCEWQrNO', 'supervisor', 'active', NOW()),
(3, 1, 3, 'Pedro', 'Reyes', 'Senior Provincial Engineer', 'pedro.reyes@pgov.ph', '$2y$10$bSgFvFNOjaXt7teieTYjsuwn8QNFSZC9y4Un2kH3ic2IDYCEWQrNO', 'supervisor', 'active', NOW()),
(4, 1, 4, 'Ana', 'Cruz', 'Human Resource Officer', 'ana.cruz@pgov.ph', '$2y$10$bSgFvFNOjaXt7teieTYjsuwn8QNFSZC9y4Un2kH3ic2IDYCEWQrNO', 'staff', 'active', NOW())
ON DUPLICATE KEY UPDATE
  `first_name` = VALUES(`first_name`),
  `last_name` = VALUES(`last_name`),
  `position` = VALUES(`position`),
  `office_id` = VALUES(`office_id`),
  `password` = VALUES(`password`);

-- ------------------------------------------------------------
-- 4. Demo Tasks for Juan Dela Cruz
-- Targets: 12 assigned, 4 due today, 7 in progress, 2 overdue
-- ------------------------------------------------------------
INSERT INTO `tasks` (`id`, `organization_id`, `office_id`, `assigned_to`, `created_by`, `title`, `description`, `status`, `priority`, `due_date`, `created_at`, `updated_at`)
VALUES
-- Task 1: Overdue #1 (in_progress)
(1, 1, 1, 1, 1, 'Review incoming assessment documents', 'Conduct technical verification of incoming land transfer tax assessments and title deeds from district offices.', 'in_progress', 'urgent', DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 2 HOUR)),

-- Task 2: Overdue #2 (in_progress)
(2, 1, 1, 1, 1, 'Coordinate field inspection schedule', 'Synchronize on-site commercial appraisal schedule with Provincial Engineering surveyors.', 'in_progress', 'high', DATE_SUB(CURDATE(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 HOUR)),

-- Task 3: Due Today #1 (in_progress)
(3, 1, 1, 1, 1, 'Prepare monthly office accomplishment report', 'Consolidate real property appraisal statistics for Q3 submission to the Governor\'s Office.', 'in_progress', 'urgent', CURDATE(), DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 1 HOUR)),

-- Task 4: Due Today #2 (in_progress)
(4, 1, 1, 1, 2, 'Verify Tax Declaration supporting documents', 'Review submitted subdivision survey plans and certified true copies of cadastral maps.', 'in_progress', 'high', CURDATE(), DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 30 MINUTE)),

-- Task 5: Due Today #3 (pending)
(5, 1, 1, 1, 4, 'Review pending employee requests', 'Process official overtime authorization and staff travel orders for field appraisers.', 'pending', 'normal', CURDATE(), DATE_SUB(NOW(), INTERVAL 5 HOUR), DATE_SUB(NOW(), INTERVAL 5 HOUR)),

-- Task 6: Due Today #4 (pending)
(6, 1, 1, 1, 1, 'Encode received documents', 'Log newly endorsed assessment appeals into the central municipal record management index.', 'pending', 'normal', CURDATE(), DATE_SUB(NOW(), INTERVAL 4 HOUR), DATE_SUB(NOW(), INTERVAL 4 HOUR)),

-- Task 7: Upcoming #1 (in_progress)
(7, 1, 1, 1, 1, 'Update property assessment records', 'Update zonal valuation roll and tax classifications for Poblacion commercial district.', 'in_progress', 'normal', DATE_ADD(CURDATE(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 45 MINUTE)),

-- Task 8: Upcoming #2 (in_progress)
(8, 1, 1, 1, 3, 'Coordinate boundary synchronization with Engineering Office', 'Resolve parcel discrepancy along provincial road right-of-way boundaries.', 'in_progress', 'high', DATE_ADD(CURDATE(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 6 HOUR)),

-- Task 9: Upcoming #3 (in_progress)
(9, 1, 1, 1, 1, 'Prepare transmittal letter for approved documents', 'Draft formal endorsement transmittal for approved tax declarations to the Provincial Treasurer\'s Office.', 'in_progress', 'normal', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 50 MINUTE)),

-- Task 10: Completed #1
(10, 1, 1, 1, 1, 'Submit quarterly real property assessment statistical report', 'Official compilation submitted to Bureau of Local Government Finance regional directorate.', 'completed', 'high', DATE_SUB(CURDATE(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY)),

-- Task 11: Completed #2
(11, 1, 1, 1, 2, 'Consolidate monthly collection reconciliation report', 'Cross-referenced assessment registry with Treasury collections for previous month.', 'completed', 'normal', DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- Task 12: Completed #3
(12, 1, 1, 1, 1, 'Archive processed assessment appeals for Q2', 'Indexed and transferred physical docket files to the General Services records archive.', 'completed', 'low', DATE_SUB(CURDATE(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY))
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `status` = VALUES(`status`),
  `priority` = VALUES(`priority`),
  `due_date` = VALUES(`due_date`),
  `assigned_to` = VALUES(`assigned_to`);

-- ------------------------------------------------------------
-- 5. Demo Activity Logs
-- ------------------------------------------------------------
INSERT INTO `activity_logs` (`id`, `organization_id`, `employee_id`, `action`, `description`, `entity_type`, `entity_id`, `created_at`)
VALUES
(1, 1, 1, 'TASK_CREATED', 'Juan Dela Cruz created a task: Review incoming assessment documents', 'task', 1, DATE_SUB(NOW(), INTERVAL 10 MINUTE)),
(2, 1, 2, 'STATUS_UPDATED', 'Maria Santos updated a task status to in-progress', 'task', 4, DATE_SUB(NOW(), INTERVAL 45 MINUTE)),
(3, 1, 3, 'REVIEW_COMPLETED', 'Pedro Reyes completed a document review for infrastructure appraisal', 'review', 8, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(4, 1, 4, 'TASK_ASSIGNED', 'Ana Cruz assigned a task to Juan Dela Cruz: Review pending employee requests', 'task', 5, DATE_SUB(NOW(), INTERVAL 5 HOUR)),
(5, 1, 1, 'TRANSMITTAL_SENT', 'Juan Dela Cruz endorsed transmittal documents to Provincial Treasury', 'transmittal', 9, DATE_SUB(NOW(), INTERVAL 1 DAY))
ON DUPLICATE KEY UPDATE
  `action` = VALUES(`action`),
  `description` = VALUES(`description`);
