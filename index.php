<?php


// Session unique
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// APP_ROOT = dossier racine du projet (là où se trouve index.php)
if (!defined('APP_ROOT')) {
    // __FILE__ pointe toujours sur ce fichier, même en include
    define('APP_ROOT', dirname(__FILE__));
}

// APP_URL détection dynamique — fonctionne depuis n'importe quelle profondeur
if (!defined('APP_URL')) {
    $proto  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // DOCUMENT_ROOT permet de trouver le chemin web du projet
    $docRoot   = rtrim(str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']), '/');
    $appRoot   = rtrim(str_replace('\\','/',APP_ROOT), '/');
    $webPath   = str_replace($docRoot, '', $appRoot);
    define('APP_URL', $proto . '://' . $host . $webPath);
}

// Charger configs
require_once APP_ROOT . '/config/database.php';
require_once APP_ROOT . '/config/constants.php';
require_once APP_ROOT . '/config/helpers.php';

// Auto-chargement models + controllers
spl_autoload_register(function (string $class) {
    foreach ([APP_ROOT.'/models/', APP_ROOT.'/controllers/'] as $dir) {
        $f = $dir . $class . '.php';
        if (file_exists($f)) { require_once $f; return; }
    }
});

// Si on accède directement à index.php depuis la racine → rediriger
$scriptFile = basename($_SERVER['SCRIPT_FILENAME'] ?? '');
$scriptDir  = rtrim(str_replace('\\','/',dirname($_SERVER['SCRIPT_FILENAME'] ?? '')), '/');
$projDir    = rtrim(str_replace('\\','/',APP_ROOT), '/');

if ($scriptFile === 'index.php' && $scriptDir === $projDir) {
    header('Location: ' . APP_URL . '/views/public/home.php');
    exit;
}
