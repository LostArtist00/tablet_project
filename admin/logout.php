<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/config/init.php';

$auth = new Auth(db());
$auth->logout();

redirect('admin/login.php');