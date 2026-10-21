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
    $sessao = authUsuario();
    if ($sessao === null) {
        header('Location: index.php?page=login');
        exit;
    }

    // Revalida status e perfil no banco em cada requisição protegida para que
    // bloqueios e alterações de perfil tenham efeito imediato.
    $stmt = authPdo()->prepare("SELECT id,nome,usuario,perfil,status,trocar_senha FROM usuarios_acesso WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => (int)($sessao['id'] ?? 0)]);
    $conta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$conta || $conta['status'] !== 'ativo') {
        unset($_SESSION['auth_usuario']);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header('Location: index.php?page=login&motivo=conta_inativa');
        exit;
    }

    $_SESSION['auth_usuario'] = [
        'id' => (int)$conta['id'],
        'nome' => $conta['nome'],
        'usuario' => $conta['usuario'],
        'perfil' => $conta['perfil'],
        'trocar_senha' => (int)$conta['trocar_senha']
    ];
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

/**
 * Camada inicial de proteção por rota. Endpoints de gravação conhecidos são
 * bloqueados para visitantes; endpoints de consulta permanecem liberados.
 */
function authProtegerRota(string $pagina): void
{
    $publicas = ['login', 'logout', 'solicitar_acesso', 'usuarios_setup'];
    if (in_array($pagina, $publicas, true)) {
        return;
    }

    try {
        $total = (int) authPdo()->query('SELECT COUNT(*) FROM usuarios_acesso')->fetchColumn();
    } catch (Throwable $e) {
        http_response_code(503);
        exit('Autenticação ainda não configurada. Importe database/sql/usuarios_acesso.sql no banco da aplicação.');
    }

    if ($total === 0) {
        header('Location: index.php?page=usuarios_setup');
        exit;
    }

    authExigirLogin();

    // Senha temporária: só permite acessar a troca obrigatória ou sair.
    if (!in_array($pagina, ['trocar_senha', 'logout'], true) && (int)(authUsuario()['trocar_senha'] ?? 0) === 1) {
        header('Location: index.php?page=trocar_senha');
        exit;
    }

    if ($pagina === 'trocar_senha') {
        return;
    }

    if (in_array($pagina, ['usuarios', 'usuarios_acao'], true)) {
        authExigirPerfil(['admin']);
    }

    $mutacoes = [
        'prog_diaria_salvar','prog_diaria_baixar','prog_diaria_baixa_salvar',
        'prog_diaria_excluir','prog_diaria_editar','prog_diaria_reordenar',
        'prog_semanal_novo','prog_semanal_editar','prog_semanal_excluir','prog_semanal_reordenar',
        'prog_quinzenal_salvar','prog_quinzenal_editar','prog_quinzenal_excluir','prog_quinzenal_reordenar',
        'pedidos_salvar','salvar_pedido','pedidos_registro_acao','pedidos_comentario','pedidos_estorno',
        'pedidos_motivos_estorno_salvar','pedidos_motivos_estorno_excluir'
    ];

    if (in_array($pagina, $mutacoes, true)) {
        authExigirPerfil(['admin', 'analista_pcp']);
    }

    // Todas as telas do menu Cadastros, inclusive consultas/GET, ficam restritas a admin e Analista PCP.
    $telasCadastro = ['cadastro_aco','cadastro_vendedores','cadastro_equipamentos','cadastro_motivos_estorno','importacao'];
    if (in_array($pagina, $telasCadastro, true)) {
        authExigirPerfil(['admin', 'analista_pcp']);
    }

    // Acesso administrativo a usuários deve ser sempre exclusivo de admin.
    if (str_starts_with($pagina, 'usuarios_')) {
        authExigirPerfil(['admin']);
    }
}
