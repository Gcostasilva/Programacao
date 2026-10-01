<?php
require_once __DIR__ . '/../../models/CadastroMotivoEstornoModel.php';

header('Content-Type: application/json; charset=utf-8');
echo json_encode((new CadastroMotivoEstornoModel())->listarAtivos(), JSON_UNESCAPED_UNICODE);
