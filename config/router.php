<?php

require_once 'rotas.php';

class Router
{
    private static array $rotas = [];
    private static string $base;

    public static function add(string $nome, string $arquivo, string $titulo): void
    {
        self::$rotas[$nome] = [
            'arquivo' => $arquivo,
            'titulo'  => $titulo,
        ];
    }

    public static function getArquivo(): string
    {
        $base = self::getBase();
        $nome = $_GET['page'] ?? 'dashboard';

        if (!isset(self::$rotas[$nome])) {
            return $base . 'pages/404.php';
        }

        $arquivo = $base . self::$rotas[$nome]['arquivo'];

        if (!file_exists($arquivo)) {
            return $base . 'pages/404.php';
        }

        return $arquivo;
    }

    private static function getBase(): string
    {
        // raiz do projeto = uma pasta acima de onde router.php está,
        // AJUSTAR conforme sua estrutura real de pastas
        return dirname(__DIR__) . '/';
    }

    public static function titulo(): string
    {
        $nome = $_GET['page'] ?? 'dashboard';
        return self::$rotas[$nome]['titulo'] ?? 'Página não encontrada';
    }

    public static function carregar(): void
    {
        include self::getArquivo();
    }
}