<?php
require_once __DIR__ . '/auth.php';

/**
 * Identificação única da conta autenticada no sistema.
 * Usa o login cadastrado em usuarios_acesso, nunca a conta do processo Apache
 * nem variáveis de ambiente do Windows.
 */
function usuarioAtual(): string
{
    authExigirLogin();
    $usuario = trim((string) (authUsuario()['usuario'] ?? ''));

    if ($usuario === '') {
        throw new RuntimeException('Não foi possível identificar o usuário autenticado.');
    }

    // O login é a identificação única da conta; os campos de auditoria legados
    // de pedidos_industria comportam até 60 caracteres.
    return substr($usuario, 0, 60);
}
