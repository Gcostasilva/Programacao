<?php
require_once __DIR__ . '/../../config/auth.php';

if (authLogado()) {
    header('Location: index.php');
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!authValidarCsrf()) {
        $erro = 'A sessão expirou. Atualize a página e tente novamente.';
    } else {
        $usuario = authNormalizarUsuario((string) ($_POST['usuario'] ?? ''));
        $senha = (string) ($_POST['senha'] ?? '');
        $stmt = authPdo()->prepare('SELECT id,nome,usuario,senha_hash,perfil,status FROM usuarios_acesso WHERE usuario = :usuario LIMIT 1');
        $stmt->execute([':usuario' => $usuario]);
        $conta = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($conta && $conta['status'] === 'ativo' && password_verify($senha, $conta['senha_hash'])) {
            session_regenerate_id(true);
            $_SESSION['auth_usuario'] = [
                'id' => (int) $conta['id'],
                'nome' => $conta['nome'],
                'usuario' => $conta['usuario'],
                'perfil' => $conta['perfil']
            ];
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            authPdo()->prepare('UPDATE usuarios_acesso SET ultimo_login = NOW() WHERE id = :id')->execute([':id' => $conta['id']]);
            header('Location: index.php');
            exit;
        }
        $erro = 'Usuário ou senha inválidos, ou conta ainda não aprovada.';
    }
}
?>
<!doctype html>
<html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Entrar | Programação</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"></head>
<body class="bg-body-tertiary min-vh-100 d-flex align-items-center">
<main class="container" style="max-width:440px"><div class="card border-0 shadow-sm"><div class="card-body p-4 p-md-5">
<div class="text-center mb-4"><i class="bi bi-shield-lock text-primary" style="font-size:2.5rem"></i><h1 class="h4 mt-2">Programação</h1><p class="text-secondary">Entre com sua conta</p></div>
<?php if ($erro): ?><div class="alert alert-danger"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<form method="post" autocomplete="on"><input type="hidden" name="_csrf" value="<?= htmlspecialchars(authCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
<div class="mb-3"><label class="form-label" for="usuario">Usuário</label><input class="form-control" id="usuario" name="usuario" autocomplete="username" required autofocus maxlength="60"></div>
<div class="mb-3"><label class="form-label" for="senha">Senha</label><input class="form-control" id="senha" type="password" name="senha" autocomplete="current-password" required></div>
<button class="btn btn-primary w-100" type="submit">Entrar</button></form>
<div class="text-center mt-3"><a href="index.php?page=solicitar_acesso">Solicitar acesso</a></div>
</div></div></main></body></html>
