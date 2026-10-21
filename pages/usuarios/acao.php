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
    if ($acao === 'redefinir_senha') {
        $stmt = $pdo->prepare('SELECT nome,usuario FROM usuarios_acesso WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $alvoSenha = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$alvoSenha) {
            throw new RuntimeException('Conta não encontrada.');
        }
        $alfabeto = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
        $senhaTemporaria = '';
        for ($i = 0; $i < 20; $i++) {
            $senhaTemporaria .= $alfabeto[random_int(0, strlen($alfabeto) - 1)];
        }
        $hash = password_hash($senhaTemporaria, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE usuarios_acesso SET senha_hash = :hash, trocar_senha = 1 WHERE id = :id');
        $stmt->execute([':hash' => $hash, ':id' => $id]);
        $_SESSION['senha_temporaria_reset'] = [
            'usuario' => $alvoSenha['usuario'],
            'senha' => $senhaTemporaria
        ];
    } elseif ($acao === 'aprovar' || $acao === 'salvar_perfil') {
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
        if ($alvo['perfil'] === 'admin' && $alvo['status'] === 'ativo' && $perfil !== 'admin') {
            $ativos = (int)$pdo->query("SELECT COUNT(*) FROM usuarios_acesso WHERE perfil='admin' AND status='ativo'")->fetchColumn();
            if ($ativos <= 1) {
                throw new RuntimeException('Não é permitido rebaixar o último administrador ativo.');
            }
        }
        $status = $acao === 'aprovar' ? 'ativo' : $alvo['status'];
        $stmt = $pdo->prepare('UPDATE usuarios_acesso SET perfil=:perfil,status=:status,aprovado_por=:admin,aprovado_em=IF(:status2 = "ativo", NOW(), aprovado_em) WHERE id=:id');
        $stmt->execute([':perfil'=>$perfil,':status'=>$status,':admin'=>$atual['id'],':status2'=>$status,':id'=>$id]);
        $pdo->commit();
    } elseif ($acao === 'bloquear' || $acao === 'rejeitar') {
        if ($id === (int)($atual['id'] ?? 0)) throw new RuntimeException('Você não pode bloquear ou rejeitar sua própria conta.');
        $stmt = $pdo->prepare('SELECT perfil,status FROM usuarios_acesso WHERE id=:id');
        $stmt->execute([':id'=>$id]);
        $alvo = $stmt->fetch(PDO::FETCH_ASSOC);
        $novoStatus = $acao === 'rejeitar' ? 'rejeitado' : 'bloqueado';
        if ($alvo && $alvo['perfil'] === 'admin' && $alvo['status'] === 'ativo') {
            $ativos = (int)$pdo->query("SELECT COUNT(*) FROM usuarios_acesso WHERE perfil='admin' AND status='ativo'")->fetchColumn();
            if ($ativos <= 1) throw new RuntimeException('Não é permitido bloquear ou rejeitar o último administrador ativo.');
        }
        $stmt = $pdo->prepare('UPDATE usuarios_acesso SET status=:status WHERE id=:id');
        $stmt->execute([':status'=>$novoStatus, ':id'=>$id]);
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
