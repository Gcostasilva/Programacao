-- Registra o usuário que realizou cada estorno.
-- Execute uma única vez no banco producao_app.

ALTER TABLE estornos
    ADD COLUMN usuario_estorno VARCHAR(100) NULL AFTER data_estorno;

CREATE INDEX idx_estornos_usuario_estorno ON estornos (usuario_estorno);
