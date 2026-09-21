<div class="container-fluid hidden-print">
    <div class="card card-primary card-outline" id="cardFormSemanal">
        <div class="card-header" style="cursor:pointer;">
            <h3 class="card-title">Nova Programação</h3>
            <div class="card-tools"><button type="button" class="btn btn-tool" id="btnToggleFormSemanal" title="Expandir/Retrair"><i class="bi bi-chevron-down" id="iconeToggleFormSemanal"></i></button></div>
        </div>
        <div class="card-body" style="display:inline;grid-template-columns:1fr 1fr;">
            <form action="index.php?page=prog_semanal_novo" method="POST" style="display:flex;" id="formProgramacaoSemanal">
                <div id="formSemanal" class="form-programacao" style="width:90%;">
                    <div class="row">
                        <div class="col-md-2"><label class="form-label">Semana</label><input type="week" class="form-control" id="semana" name="semana" value="<?php $semanaAtual=date('o-\\WW');echo $semanaAtual;?>" required></div>
                        <div class="col-md-3"><label class="form-label">Recurso</label><select class="form-select" id="recursoSemanal" name="recurso" required><option value="" disabled selected>Selecione...</option><?php foreach($dados['recursos_semanal'] as $linha): ?><option value="<?=htmlspecialchars($linha['id'],ENT_QUOTES)?>"><?=htmlspecialchars($linha['descricao'])?></option><?php endforeach;?></select></div>
                        <div class="col-md-2"><label class="form-label">Data</label><input type="date" class="form-control" id="data" name="data" required></div>
                        <div class="col-lg-2"><label class="form-label">Demanda</label><input class="form-control" name="demanda" id="demanda" required></div>
                        <div></div>
                        <div class="col-md-3"><label class="form-label">Código</label><div class="input-group"><input class="form-control" name="codigo_s" id="codigo" autocomplete="off" required><input type="hidden" id="espessura_prod"><button class="btn btn-primary bt-especial" type="button" id="btn_buscaCodigo" title="Selecionar produto"><i class="bi bi-search"></i></button><button class="btn btn-primary ms-1 bt-especial" type="button" id="btn_buscaDemanda" title="Selecionar demanda"><i class="bi bi-boxes"></i></button></div></div>
                        <div class="col-md-6"><label class="form-label">Descrição</label><input class="form-control" name="descricao" id="descricao_sem" disabled><input type="hidden" name="complemento_descricao" id="complemento_descricao"></div>
                        <div class="col-md-1"><label class="form-label">Quantidade</label><input class="form-control" name="quantidade" id="quantidade" required></div>
                        <div class="col-md-1"><label class="form-label">Peso</label><input class="form-control" name="peso" id="peso"><input type="hidden" name="peso_liquido" id="peso_liquido" disabled></div>
                        <div class="col-md-10"><label class="form-label">Observação</label><input class="form-control" name="observacao" id="observacao"></div>
                    </div>
                </div>
                <div style="grid-column:2/2;flex-direction:column;display:flex;margin-right:50px;"><button class="btn btn-primary mt-2" type="submit"><i class="bi bi-save"></i> Salvar</button><button class="btn btn-primary mt-2" type="reset"><i class="bi bi-x-circle"></i> Desistir</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCodigosProgramacao" tabindex="-1" aria-labelledby="modalCodigosProgramacaoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="modalCodigosProgramacaoLabel"><i class="bi bi-search me-2"></i>Selecionar produto</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div>
        <div class="modal-body">
            <div class="input-group mb-3"><span class="input-group-text"><i class="bi bi-filter"></i></span><input type="text" class="form-control" id="filtroCodigosProgramacao" placeholder="Filtrar por código ou descrição..." autocomplete="off"></div>
            <div class="table-responsive"><table class="table table-sm table-hover align-middle" id="tabelaCodigosProgramacao"><thead><tr><th>Código</th><th>Descrição</th><th>Peso líquido</th><th></th></tr></thead><tbody>
                <?php foreach(($dados['listaCodigos']??[]) as $produto): ?>
                <tr data-busca="<?=htmlspecialchars(strtolower(($produto['codigo']??'').' '.($produto['descricao']??'')),ENT_QUOTES)?>"><td><?=htmlspecialchars($produto['codigo']??'')?></td><td><?=htmlspecialchars($produto['descricao']??'')?></td><td><?=htmlspecialchars($produto['peso_liquido']??'')?></td><td><button type="button" class="btn btn-sm btn-primary btn-selecionar-codigo" data-codigo="<?=htmlspecialchars($produto['codigo']??'',ENT_QUOTES)?>" data-descricao="<?=htmlspecialchars($produto['descricao']??'',ENT_QUOTES)?>" data-peso="<?=htmlspecialchars($produto['peso_liquido']??'',ENT_QUOTES)?>"><i class="bi bi-check-lg"></i></button></td></tr>
                <?php endforeach; ?>
            </tbody></table></div>
        </div>
    </div></div>
</div>

<script>
document.addEventListener('DOMContentLoaded',function(){
 const semana=document.getElementById('semana'),recurso=document.getElementById('recursoSemanal'),data=document.getElementById('data'),form=document.getElementById('formProgramacaoSemanal'),chave='programacaoSemanal_filtros';
 if(semana&&recurso&&data&&form){try{const salvo=JSON.parse(localStorage.getItem(chave)||'{}');if(salvo.semana)semana.value=salvo.semana;if(salvo.recurso)recurso.value=salvo.recurso;if(salvo.data)data.value=salvo.data;}catch(e){} const salvar=()=>localStorage.setItem(chave,JSON.stringify({semana:semana.value,recurso:recurso.value,data:data.value}));semana.addEventListener('change',salvar);recurso.addEventListener('change',salvar);data.addEventListener('change',salvar);form.addEventListener('submit',salvar);}
 const modalEl=document.getElementById('modalCodigosProgramacao'),btn=document.getElementById('btn_buscaCodigo'),filtro=document.getElementById('filtroCodigosProgramacao'),codigo=document.getElementById('codigo'),descricao=document.getElementById('descricao_sem'),peso=document.getElementById('peso');
 if(!modalEl||!btn||!codigo)return; const modal=new bootstrap.Modal(modalEl); btn.addEventListener('click',()=>{modal.show();setTimeout(()=>filtro?.focus(),250);});
 filtro?.addEventListener('input',function(){const termo=this.value.trim().toLowerCase();document.querySelectorAll('#tabelaCodigosProgramacao tbody tr').forEach(l=>l.style.display=!termo||l.dataset.busca.includes(termo)?'':'none');});
 document.querySelectorAll('.btn-selecionar-codigo').forEach(b=>b.addEventListener('click',function(){codigo.value=this.dataset.codigo||'';if(descricao)descricao.value=this.dataset.descricao||'';if(peso&&this.dataset.peso)peso.value=this.dataset.peso;codigo.dispatchEvent(new Event('change',{bubbles:true}));modal.hide();}));
});
</script>
