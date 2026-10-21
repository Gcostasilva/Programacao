<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

function authPdo(): PDO
{
    static $pdo = null;
    if (!$pdo instanceof PDO) {
        $pdo = require __DIR__ . '/banco.php';
    }
    return $pdo;
}

function authUsuario(): ?array
{
    return isset($_SESSION['auth_usuario']) && is_array($_SESSION['auth_usuario'])
        ? $_SESSION['auth_usuario'] : null;
}

function authLogado(): bool
{
    return authUsuario() !== null;
}

function authTemPerfil(array $perfis): bool
{
    $usuario = authUsuario();
    return $usuario !== null && in_array($usuario['perfil'] ?? '', $perfis, true);
}

function authExigirLogin(): void
{
    if (!authLogado()) {
        header('Location: index.php?page=login');
        exit;
    }
}

function authExigirPerfil(array $perfis): void
{
    authExigirLogin();
    if (!authTemPerfil($perfis)) {
        http_response_code(403);
        if (stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['sucesso' => false, 'erro' => 'Você não tem permissão para esta operação.'], JSON_UNESCAPED_UNICODE);
        } else {
            echo '<!doctype html><html lang="pt-br"><meta charset="utf-8"><title>Acesso negado</title><body style="font-family:Arial;padding:3rem"><h1>Acesso negado</h1><p>Seu perfil não permite executar esta operação.</p><a href="index.php">Voltar ao sistema</a></body></html>';
        }
        exit;
    }
}

function authCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function authValidarCsrf(): bool
{
    $token = (string) ($_POST['_csrf'] ?? '');
    return $token !== '' && isset($_SESSION['csrf_token'])
        && hash_equals((string) $_SESSION['csrf_token'], $token);
}

function authNormalizarUsuario(string $usuario): string
{
    return strtolower(trim($usuario));
}
