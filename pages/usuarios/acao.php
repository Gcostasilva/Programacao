<?php
require_once __DIR__ . '/../../config/auth.php';
authExigirPerfil(['admin']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !authValidarCsrf()) {
    http_response_code(400);
    exit('Requisição inválida ou sessão expirada. Volte e tente novamente.');
}
$pdo = authPdo();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$acao = (string)($_POST['acao'] ?? '');
$perfil = (string)($_POST['perfil'] ?? 'visitante');
$perfisValidos = ['admin','analista_pcp','visitante'];
if (!$id || !in_array($perfil, $perfisValidos, true)) {
    header('Location: index.php?page=usuarios');
    exit;
}
$atual = authUsuario();
try {
    if ($acao === 'aprovar' || $acao === 'salvar_perfil') {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('SELECT id,perfil,status FROM usuarios_acesso WHERE id = :id FOR UPDATE');
        $stmt->execute([':id'=>$id]);
        $alvo = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$alvo) {
            throw new RuntimeException('Conta não encontrada.');
        }
        if ($acao === 'salvar_perfil' && $id === (int)($atual['id'] ?? 0) && $perfil !== 'admin') {
            throw new RuntimeException('Você não pode remover seu próprio perfil de administrador.');
        }
        $status = $acao === 'aprovar' ? 'ativo' : $alvo['status'];
        $stmt = $pdo->prepare('UPDATE usuarios_acesso SET perfil=:perfil,status=:status,aprovado_por=:admin,aprovado_em=IF(:status2 = "ativo", NOW(), aprovado_em) WHERE id=:id');
        $stmt->execute([':perfil'=>$perfil,':status'=>$status,':admin'=>$atual['id'],':status2'=>$status,':id'=>$id]);
        $pdo->commit();
    } elseif ($acao === 'bloquear') {
        if ($id === (int)($atual['id'] ?? 0)) throw new RuntimeException('Você não pode bloquear sua própria conta.');
        $stmt = $pdo->prepare("UPDATE usuarios_acesso SET status='bloqueado' WHERE id=:id");
        $stmt->execute([':id'=>$id]);
    } elseif ($acao === 'excluir') {
        if ($id === (int)($atual['id'] ?? 0)) throw new RuntimeException('Você não pode excluir sua própria conta.');
        $stmt = $pdo->prepare('SELECT perfil,status FROM usuarios_acesso WHERE id=:id');
        $stmt->execute([':id'=>$id]);
        $alvo = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($alvo && $alvo['perfil'] === 'admin' && $alvo['status'] === 'ativo') {
            $ativos = (int)$pdo->query("SELECT COUNT(*) FROM usuarios_acesso WHERE perfil='admin' AND status='ativo'")->fetchColumn();
            if ($ativos <= 1) throw new RuntimeException('Não é permitido excluir o último administrador ativo.');
        }
        $pdo->prepare('DELETE FROM usuarios_acesso WHERE id=:id')->execute([':id'=>$id]);
    }
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
}
header('Location: index.php?page=usuarios');
exit;
