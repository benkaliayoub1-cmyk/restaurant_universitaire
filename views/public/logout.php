<?php
require_once __DIR__ . '/../../index.php';
session_destroy();
header('Location: ' . APP_URL . '/views/public/home.php');
exit;
