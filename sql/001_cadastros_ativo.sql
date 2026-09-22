-- Ajustes necessários para os três cadastros.
-- maquinas já possui o campo ativo no banco exportado.

ALTER TABLE vendedores
    ADD COLUMN IF NOT EXISTS ativo TINYINT(1) NOT NULL DEFAULT 1 AFTER nome;

ALTER TABLE produtos
    ADD COLUMN IF NOT EXISTS ativo TINYINT(1) NOT NULL DEFAULT 1 AFTER espessura;

-- Os registros existentes permanecem ativos por padrão.
UPDATE vendedores SET ativo = 1 WHERE ativo IS NULL;
UPDATE produtos SET ativo = 1 WHERE ativo IS NULL;
