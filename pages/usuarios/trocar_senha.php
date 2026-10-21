<?php
require_once __DIR__ . '/../../config/auth.php';
authExigirLogin();

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!authValidarCsrf()) {
        $erro = 'A sessão expirou. Atualize a página e tente novamente.';
    } else {
        $senha = (string)($_POST['senha'] ?? '');
        $confirmacao = (string)($_POST['confirmacao'] ?? '');
        if (strlen($senha) < 12) {
            $erro = 'A nova senha deve ter pelo menos 12 caracteres.';
        } elseif ($senha !== $confirmacao) {
            $erro = 'As senhas não coincidem.';
        } else {
            $pdo = authPdo();
            $stmt = $pdo->prepare('SELECT senha_hash FROM usuarios_acesso WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => (int)authUsuario()['id']]);
            $hashAtual = (string)$stmt->fetchColumn();
            if ($hashAtual !== '' && password_verify($senha, $hashAtual)) {
                $erro = 'A nova senha deve ser diferente da senha temporária.';
            } else {
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE usuarios_acesso SET senha_hash = :hash, trocar_senha = 0 WHERE id = :id');
                $stmt->execute([':hash' => $hash, ':id' => (int)authUsuario()['id']]);
                $_SESSION['auth_usuario']['trocar_senha'] = 0;
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                session_regenerate_id(true);
                header('Location: index.php?senha_alterada=1');
                exit;
            }
        }
    }
}
?>
<div class="container-fluid py-4">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <div class="text-center mb-3"><i class="bi bi-shield-lock text-primary" style="font-size:2.5rem"></i><h1 class="h4 mt-2">Defina sua nova senha</h1></div>
          <div class="alert alert-warning">Por segurança, a senha temporária precisa ser substituída antes de continuar usando o sistema.</div>
          <?php if ($erro !== ''): ?><div class="alert alert-danger"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
          <form method="post" autocomplete="off">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(authCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
            <div class="mb-3"><label class="form-label" for="senha">Nova senha (mínimo 12 caracteres)</label><input class="form-control" type="password" id="senha" name="senha" minlength="12" autocomplete="new-password" required autofocus></div>
            <div class="mb-3"><label class="form-label" for="confirmacao">Confirme a nova senha</label><input class="form-control" type="password" id="confirmacao" name="confirmacao" minlength="12" autocomplete="new-password" required></div>
            <button class="btn btn-primary w-100" type="submit">Salvar nova senha</button>
          </form>
          <form method="post" action="index.php?page=logout" class="mt-2"><input type="hidden" name="_csrf" value="<?= htmlspecialchars(authCsrfToken(), ENT_QUOTES, 'UTF-8') ?>"><button class="btn btn-outline-secondary w-100" type="submit">Sair</button></form>
        </div>
      </div>
    </div>
  </div>
</div>
