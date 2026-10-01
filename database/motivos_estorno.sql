CREATE TABLE IF NOT EXISTS motivos_estorno (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    motivo VARCHAR(100) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY uk_motivos_estorno_motivo (motivo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
