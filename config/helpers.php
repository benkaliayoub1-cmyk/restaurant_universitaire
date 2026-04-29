<?php


function redirect(string $path): void {
    // Éviter les doubles slashes
    $url = APP_URL . '/' . ltrim($path, '/');
    header('Location: ' . $url);
    exit;
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

function hasRole(string $role): bool {
    return ($_SESSION['role'] ?? '') === $role;
}

function requireAuth(string $role = ''): void {
    if (!isLoggedIn()) {
        redirect('views/public/login.php');
    }
    if ($role && !hasRole($role)) {
        redirect('views/public/403.php');
    }
}

function h(string $str): string {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfVerify(): bool {
    $token = $_POST['csrf_token'] ?? '';
    return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function flash(string $key, string $msg = ''): string {
    if ($msg !== '') {
        $_SESSION['flash'][$key] = $msg;
        return '';
    }
    $v = $_SESSION['flash'][$key] ?? '';
    unset($_SESSION['flash'][$key]);
    return $v;
}

function formatPrix(float $prix): string {
    return number_format($prix, 3, '.', '') . ' DT';
}


 // Estimation du temps d'attente 
 
function estimerAttente(int $nb): string {
    if ($nb === 0) return '< 1 min';
    // 1 batch = WAIT_BATCH_SIZE personnes = WAIT_MINUTES minutes
    // ex : 1-10 → 5 min, 11-20 → 10 min, etc.
    $minutes = (int)ceil($nb / WAIT_BATCH_SIZE) * WAIT_MINUTES;
    if ($minutes <= 5)  return '~5 min';
    if ($minutes <= 10) return '~10 min';
    if ($minutes <= 20) return '~20 min';
    if ($minutes <= 30) return '~30 min';
    if ($minutes <= 45) return '~45 min';
    if ($minutes <= 60) return '~1h';
    return '> 1h';
}

 // Pagination helper
function paginate(int $total, int $page, int $perPage = ITEMS_PER_PAGE): array {
    $totalPages = max(1, (int)ceil($total / $perPage));
    $page       = max(1, min($page, $totalPages));
    return [
        'page'       => $page,
        'totalPages' => $totalPages,
        'offset'     => ($page - 1) * $perPage,
        'perPage'    => $perPage,
    ];
}
