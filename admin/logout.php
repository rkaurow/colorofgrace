<?php
require_once __DIR__ . '/../includes/auth.php';
admin_logout();
redirect(BASE_URL . '/admin/login.php');
