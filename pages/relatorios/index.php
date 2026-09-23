<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Relatórios</h2>
            <div class="text-muted">Relatórios operacionais da programação e produção</div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="fs-2 text-warning"><i class="bi bi-calendar-day"></i></div>
                        <div><h5 class="card-title mb-1">Programação Diária</h5><div class="text-muted small">Modelo RP 09.</div></div>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Modelo separado para uma data e equipamento, seguindo a estrutura do documento diário fornecido.</p>
                    <a href="?page=relatorio_programacao_diaria" class="btn btn-warning"><i class="bi bi-file-earmark-text"></i> Abrir relatório</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="fs-2 text-primary"><i class="bi bi-calendar-week"></i></div>
                        <div><h5 class="card-title mb-1">Programação Semanal</h5><div class="text-muted small">Modelo atualmente em uso.</div></div>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Programação organizada por equipamento, dia, produto, demanda, quantidade e peso estimado.</p>
                    <a href="?page=relatorio_programacao" class="btn btn-primary"><i class="bi bi-file-earmark-text"></i> Abrir relatório</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="fs-2 text-success"><i class="bi bi-calendar2-range"></i></div>
                        <div><h5 class="card-title mb-1">Programação Quinzenal</h5><div class="text-muted small">Modelo RP 05.</div></div>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Modelo separado para o período quinzenal, seguindo a estrutura do documento fornecido.</p>
                    <a href="?page=relatorio_programacao_quinzenal" class="btn btn-success"><i class="bi bi-file-earmark-text"></i> Abrir relatório</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="fs-2 text-danger"><i class="bi bi-arrow-counterclockwise"></i></div>
                        <div><h5 class="card-title mb-1">Estornos</h5><div class="text-muted small">Registro de estornos dos pedidos.</div></div>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Consulte estornos por pedido, vendedor, tipo e período, com opção de impressão.</p>
                    <a href="?page=relatorio_estornos" class="btn btn-danger"><i class="bi bi-file-earmark-text"></i> Abrir relatório</a>
                </div>
            </div>
        </div>

    </div>
</div>
