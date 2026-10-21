CREATE TABLE IF NOT EXISTS usuarios_acesso (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(120) NOT NULL,
    usuario VARCHAR(60) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    trocar_senha TINYINT(1) NOT NULL DEFAULT 0,
    perfil ENUM('admin','analista_pcp','visitante') NOT NULL DEFAULT 'visitante',
    status ENUM('pendente','ativo','rejeitado','bloqueado') NOT NULL DEFAULT 'pendente',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    aprovado_por INT UNSIGNED NULL,
    aprovado_em DATETIME NULL,
    ultimo_login DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_usuarios_acesso_usuario (usuario),
    KEY idx_usuarios_acesso_status_perfil (status, perfil),
    CONSTRAINT fk_usuarios_acesso_aprovador
      FOREIGN KEY (aprovado_por) REFERENCES usuarios_acesso(id)
      ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
