<?php
/**
 * Government Workflow OS — API Gateway Endpoint (Step 1 Foundation)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

json_response(true, [
    'application' => 'Government Workflow OS',
    'version'     => '1.0.0',
    'status'      => 'operational',
    'step'        => 'Step 1: Foundation & Application Shell'
], 'Government Workflow OS API is ready for future module integrations.');
