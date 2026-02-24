<?php
require_once __DIR__ . '/../includes/config.php';

// Login automático para teste (remova em produção!)
$_SESSION['admin_id'] = 1;
$_SESSION['admin_nome'] = 'Administrador';
$_SESSION['admin_nivel'] = 'admin';

header('Location: ' . BASE_URL . 'admin/index.php');
exit;