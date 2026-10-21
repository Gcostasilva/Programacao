<?php
require_once __DIR__ . '/../../config/auth.php';

$erro = '';
try {
    $total = (int) authPdo()->query('SELECT COUNT(*) FROM usuarios_acesso')->fetchColumn();
} catch (Throwable $e) {
    http_response_code(503);
    exit('Tabela de usuários não encontrada. Importe database/sql/usuarios_acesso.sql no banco da aplicação e tente novamente.');
}
if ($total > 0) {
    header('Location: index.php?page=login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!authValidarCsrf()) {
        $erro = 'A sessão expirou. Atualize a página e tente novamente.';
    } else {
        $nome = trim((string) ($_POST['nome'] ?? ''));
        $usuario = authNormalizarUsuario((string) ($_POST['usuario'] ?? ''));
        $senha = (string) ($_POST['senha'] ?? '');
        $confirmacao = (string) ($_POST['confirmacao'] ?? '');

        if ($nome === '' || mb_strlen($nome) > 120 || !preg_match('/^[a-z0-9._-]{3,60}$/', $usuario)) {
            $erro = 'Informe nome e usuário válido (3 a 60 caracteres: letras, números, ponto, hífen ou sublinhado).';
        } elseif (strlen($senha) < 12) {
            $erro = 'A senha do primeiro administrador deve ter pelo menos 12 caracteres.';
        } elseif ($senha !== $confirmacao) {
            $erro = 'As senhas não coincidem.';
        } else {
            $pdo = authPdo();
            try {
                $lock = (int) $pdo->query("SELECT GET_LOCK('programacao_setup_admin', 10)")->fetchColumn();
                if ($lock !== 1) {
                    throw new RuntimeException('Não foi possível obter o bloqueio de configuração.');
                }
                if ((int) $pdo->query('SELECT COUNT(*) FROM usuarios_acesso')->fetchColumn() !== 0) {
                    throw new RuntimeException('O administrador inicial já foi criado.');
                }
                $stmt = $pdo->prepare("INSERT INTO usuarios_acesso (nome,usuario,senha_hash,perfil,status,aprovado_em) VALUES (:nome,:usuario,:hash,'admin','ativo',NOW())");
                $stmt->execute([':nome'=>$nome, ':usuario'=>$usuario, ':hash'=>password_hash($senha, PASSWORD_DEFAULT)]);
                $pdo->query("SELECT RELEASE_LOCK('programacao_setup_admin')");
                header('Location: index.php?page=login');
                exit;
            } catch (Throwable $e) {
                try { $pdo->query("SELECT RELEASE_LOCK('programacao_setup_admin')"); } catch (Throwable $ignorado) {}
                $erro = 'Não foi possível criar o administrador. Verifique se outro administrador já foi criado e se o usuário está disponível.';
            }
        }
    }
}
?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Configuração inicial | Programação</title><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css"></head>
<body class="bg-body-tertiary min-vh-100 d-flex align-items-center"><main class="container py-4" style="max-width:520px"><div class="card border-0 shadow-sm"><div class="card-body p-4">
<h1 class="h4">Criar primeiro administrador</h1><p class="text-secondary">Esta etapa só funciona enquanto não existir nenhuma conta. Use uma senha exclusiva e guarde-a em local seguro.</p>
<?php if ($erro): ?><div class="alert alert-danger"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<form method="post"><input type="hidden" name="_csrf" value="<?= htmlspecialchars(authCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
<div class="mb-3"><label class="form-label">Nome completo</label><input class="form-control" name="nome" required maxlength="120"></div>
<div class="mb-3"><label class="form-label">Usuário</label><input class="form-control" name="usuario" required minlength="3" maxlength="60" pattern="[a-zA-Z0-9._-]+"></div>
<div class="mb-3"><label class="form-label">Senha (mínimo 12 caracteres)</label><input class="form-control" type="password" name="senha" required minlength="12" autocomplete="new-password"></div>
<div class="mb-3"><label class="form-label">Confirme a senha</label><input class="form-control" type="password" name="confirmacao" required minlength="12" autocomplete="new-password"></div>
<button class="btn btn-primary w-100">Criar administrador</button></form></div></div></main></body></html>
