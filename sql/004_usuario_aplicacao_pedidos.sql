-- Execute uma única vez no banco da aplicação.
-- Amplia os campos de auditoria de entrada/saída para comportar o login do sistema.
ALTER TABLE pedidos_industria
    MODIFY COLUMN user_entrada VARCHAR(60) NULL,
    MODIFY COLUMN user_saida VARCHAR(60) NULL;
