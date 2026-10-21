-- Execute uma única vez no banco de dados da aplicação, para instalações que já possuem usuarios_acesso.
ALTER TABLE usuarios_acesso
    ADD COLUMN trocar_senha TINYINT(1) NOT NULL DEFAULT 0;
