<?php
require_once __DIR__ . '/../../config/auth.php';
$mensagem = '';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!authValidarCsrf()) {
        $erro = 'A sessão expirou. Atualize a página e tente novamente.';
    } else {
        $nome = trim((string) ($_POST['nome'] ?? ''));
        $usuario = authNormalizarUsuario((string) ($_POST['usuario'] ?? ''));
        $senha = (string) ($_POST['senha'] ?? '');
        if ($nome === '' || mb_strlen($nome) > 120 || !preg_match('/^[a-z0-9._-]{3,60}$/', $usuario)) {
            $erro = 'Confira o nome e o usuário informado.';
        } elseif (strlen($senha) < 12) {
            $erro = 'A senha deve ter pelo menos 12 caracteres.';
        } else {
            try {
                $stmt = authPdo()->prepare("INSERT INTO usuarios_acesso (nome,usuario,senha_hash,perfil,status) VALUES (:nome,:usuario,:hash,'visitante','pendente')");
                $stmt->execute([':nome'=>$nome, ':usuario'=>$usuario, ':hash'=>password_hash($senha, PASSWORD_DEFAULT)]);
                $mensagem = 'Solicitação recebida. Um administrador precisa aprovar sua conta antes do primeiro acesso.';
            } catch (PDOException $e) {
                $erro = 'Não foi possível registrar a solicitação. Esse nome de usuário pode já estar cadastrado.';
            }
        }
    }
}
?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Solicitar acesso | Programação</title><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css"></head>
<body class="bg-body-tertiary min-vh-100 d-flex align-items-center"><main class="container py-4" style="max-width:520px"><div class="card border-0 shadow-sm"><div class="card-body p-4">
<h1 class="h4">Solicitar acesso</h1><p class="text-secondary">A conta começa como visitante pendente e só poderá entrar após aprovação do administrador.</p>
<?php if ($erro): ?><div class="alert alert-danger"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<?php if ($mensagem): ?><div class="alert alert-success"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></div><a class="btn btn-primary w-100" href="index.php?page=login">Voltar ao login</a>
<?php else: ?><form method="post"><input type="hidden" name="_csrf" value="<?= htmlspecialchars(authCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
<div class="mb-3"><label class="form-label">Nome completo</label><input class="form-control" name="nome" required maxlength="120"></div>
<div class="mb-3"><label class="form-label">Usuário</label><input class="form-control" name="usuario" required minlength="3" maxlength="60" pattern="[a-zA-Z0-9._-]+"></div>
<div class="mb-3"><label class="form-label">Senha (mínimo 12 caracteres)</label><input class="form-control" type="password" name="senha" required minlength="12" autocomplete="new-password"></div>
<button class="btn btn-primary w-100">Enviar solicitação</button></form><?php endif; ?>
<div class="text-center mt-3"><a href="index.php?page=login">Já tenho conta</a></div></div></div></main></body></html>
