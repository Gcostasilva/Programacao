<?php

/**
 * Retorna o usuário Windows autenticado no acesso web.
 *
 * Não usa getenv('USERNAME'), pois esse valor representa o usuário
 * do processo Apache/PHP quando o servidor roda como serviço.
 */
function usuarioAtual(): string
{
    $usuario = $_SERVER['REMOTE_USER']
        ?? $_SERVER['AUTH_USER']
        ?? $_SERVER['REDIRECT_REMOTE_USER']
        ?? '';

    $usuario = trim((string) $usuario);

    if ($usuario === '') {
        return 'USUARIO_NAO_IDENTIFICADO';
    }

    if (str_contains($usuario, '\\')) {
        $usuario = substr($usuario, strrpos($usuario, '\\') + 1);
    }

    if (str_contains($usuario, '@')) {
        $usuario = substr($usuario, 0, strpos($usuario, '@'));
    }

    return strtoupper(trim($usuario));
}
