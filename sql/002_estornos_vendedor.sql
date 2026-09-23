-- Adiciona o vendedor ao histórico de estornos.
-- Execute uma única vez no banco producao_app.

ALTER TABLE estornos
    ADD COLUMN vendedor_id INT NULL AFTER atendimento;

CREATE INDEX idx_estornos_vendedor_id ON estornos (vendedor_id);

ALTER TABLE estornos
    ADD CONSTRAINT fk_estornos_vendedor
    FOREIGN KEY (vendedor_id) REFERENCES vendedores(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL;
