CREATE TABLE IF NOT EXISTS tipos_aco (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    tipo VARCHAR(50) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY uk_tipos_aco_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tipos_aco (tipo, ativo) VALUES
    ('AZ-120', 1),
    ('AZ-150', 1),
    ('Z-275', 1),
    ('Z-280', 1),
    ('PRE PINTADA', 1);