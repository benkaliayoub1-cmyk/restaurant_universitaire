<?php
// ============================================================
//  config/constants.php — Constantes globales
// ============================================================

// Prix fixe unique pour tous les menus (0.200 DT)
define('MENU_PRIX_FIXE', 0.200);

// Rôles
define('ROLE_ETUDIANT', 'etudiant');
define('ROLE_ADMIN',    'admin');
define('ROLE_CAISSIER', 'caissier');

// Statuts ticket
define('TICKET_VALIDE',  'valide');
define('TICKET_UTILISE', 'utilise');
define('TICKET_ANNULE',  'annule');

// Attente simulation
define('WAIT_BATCH_SIZE', 10);
define('WAIT_MINUTES',     5);

// Pagination
define('ITEMS_PER_PAGE', 10);
