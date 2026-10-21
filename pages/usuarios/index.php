<?php
require_once __DIR__ . '/../../config/auth.php';
authExigirPerfil(['admin']);
$pdo = authPdo();
$usuarios = $pdo->query("SELECT u.id,u.nome,u.usuario,u.perfil,u.status,u.criado_em,u.aprovado_em,u.ultimo_login,a.nome AS aprovador FROM usuarios_acesso u LEFT JOIN usuarios_acesso a ON a.id = u.aprovado_por ORDER BY FIELD(u.status,'pendente','ativo','bloqueado','rejeitado'), u.criado_em DESC")->fetchAll(PDO::FETCH_ASSOC);
$csrf = authCsrfToken();
?>
<div class="container-fluid py-3">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div><h1 class="h3 mb-1">Gerenciar usuários</h1><p class="text-secondary mb-0">Aprove solicitações, defina perfis e bloqueie ou remova contas.</p></div>
    <span class="badge text-bg-primary"><?= count($usuarios) ?> conta(s)</span>
  </div>
  <div class="alert alert-info"><strong>Perfis:</strong> Administrador gerencia usuários; Analista PCP pode alterar registros; Visitante tem acesso somente para consulta. Contas pendentes não conseguem entrar.</div>
  <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
  <thead class="table-light"><tr><th>Usuário</th><th>Perfil</th><th>Status</th><th>Solicitado em</th><th>Último acesso</th><th class="text-end">Ações</th></tr></thead><tbody>
  <?php foreach ($usuarios as $u): ?>
  <tr><td><div class="fw-semibold"><?= htmlspecialchars($u['nome'], ENT_QUOTES, 'UTF-8') ?></div><small class="text-secondary">@<?= htmlspecialchars($u['usuario'], ENT_QUOTES, 'UTF-8') ?> · ID <?= (int)$u['id'] ?></small></td>
  <td><span class="badge text-bg-light border"><?= htmlspecialchars(['admin'=>'Administrador','analista_pcp'=>'Analista PCP','visitante'=>'Visitante'][$u['perfil']] ?? $u['perfil'], ENT_QUOTES, 'UTF-8') ?></span></td>
  <td><?php $cores=['pendente'=>'warning','ativo'=>'success','bloqueado'=>'secondary','rejeitado'=>'danger']; ?><span class="badge text-bg-<?= $cores[$u['status']] ?? 'secondary' ?>"><?= htmlspecialchars(ucfirst($u['status']), ENT_QUOTES, 'UTF-8') ?></span></td>
  <td><?= htmlspecialchars((string)$u['criado_em'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)($u['ultimo_login'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
  <td><div class="d-flex justify-content-end gap-2 flex-wrap">
  <?php if ($u['status'] === 'pendente' || $u['status'] === 'ativo' || $u['status'] === 'bloqueado'): ?>
  <form method="post" action="index.php?page=usuarios_acao" class="d-flex gap-1 align-items-center"><input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><input type="hidden" name="acao" value="salvar_perfil">
  <select name="perfil" class="form-select form-select-sm" aria-label="Perfil" style="max-width:150px"><option value="visitante" <?= $u['perfil']==='visitante'?'selected':'' ?>>Visitante</option><option value="analista_pcp" <?= $u['perfil']==='analista_pcp'?'selected':'' ?>>Analista PCP</option><option value="admin" <?= $u['perfil']==='admin'?'selected':'' ?>>Administrador</option></select>
  <button class="btn btn-sm btn-outline-primary">Salvar</button></form>
  <?php if ($u['status'] !== 'ativo'): ?><form method="post" action="index.php?page=usuarios_acao"><input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><input type="hidden" name="acao" value="aprovar"><input type="hidden" name="perfil" value="<?= htmlspecialchars($u['perfil'], ENT_QUOTES, 'UTF-8') ?>"><button class="btn btn-sm btn-success">Aprovar</button></form><?php endif; ?>
  <?php if ($u['status'] === 'ativo'): ?><form method="post" action="index.php?page=usuarios_acao"><input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><input type="hidden" name="acao" value="bloquear"><button class="btn btn-sm btn-outline-secondary">Bloquear</button></form><?php endif; ?>
  <?php if ($u['status'] === 'pendente'): ?><form method="post" action="index.php?page=usuarios_acao" onsubmit="return confirm('Rejeitar esta solicitação de acesso?')"><input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><input type="hidden" name="acao" value="rejeitar"><button class="btn btn-sm btn-outline-danger">Rejeitar</button></form><?php endif; ?>
  <?php endif; ?>
  <?php if ((int)$u['id'] !== (int)(authUsuario()['id'] ?? 0)): ?><form method="post" action="index.php?page=usuarios_acao" onsubmit="return confirm('Excluir definitivamente esta conta?')"><input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><input type="hidden" name="acao" value="excluir"><button class="btn btn-sm btn-outline-danger">Excluir</button></form><?php endif; ?>
  </div></td></tr>
  <?php endforeach; ?>
  <?php if (!$usuarios): ?><tr><td colspan="6" class="text-center text-secondary py-4">Nenhuma conta cadastrada.</td></tr><?php endif; ?>
  </tbody></table></div></div>
</div>
