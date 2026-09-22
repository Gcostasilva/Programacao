-- Comentários por entrada dos pedidos da indústria.
-- Execute este script uma única vez no banco producao_app.

CREATE TABLE IF NOT EXISTS pedidos_industria_comentarios (
    id INT(11) NOT NULL AUTO_INCREMENT,
    pedido_industria_id INT(11) NOT NULL,
    comentario TEXT NOT NULL,
    usuario VARCHAR(100) NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_pedido_industria_id (pedido_industria_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
