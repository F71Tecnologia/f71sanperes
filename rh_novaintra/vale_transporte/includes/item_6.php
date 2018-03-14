<input type="button" class="btn btn-success" value="Novo Cadastro" id="show_form_cad_<?= $key; ?>" >
<input type="button" class="btn btn-primary" value="Filtrar" onclick="get_table_<?= $key; ?>()" />
<br><br>
<fieldset id="form_cad_<?= $key; ?>" >
    <legend>Cadastrar Dias Úteis</legend>
    <div class="form-group">
        <label class="col-xs-2 control-label">Tipo:</label> 
        <div class="col-xs-10"><?php echo montaSelect(array('1'=>'Pelas Funções','2'=>'Pelos Horários'), NULL, " id='tipo_fdias' onchange='mudatipo_diasuteis()' class='form-control' ") ?></div>
    </div>
    <div class="box_tp6_1" >
        <div class="form-group">
            <label class="col-xs-2 control-label">Grupo (CBO):</label> 
            <?php 
            $arr_c = array('-1'=>' Filtrar por CBO ');
            foreach($arr_cbo as $k=>$v){
                $arr_c[$k] = $v;
            }?>
            <div class="col-xs-10"><?=montaSelect($arr_c, NULL, "id='cbo_" . $key . "' class='form-control'"); ?></div>
        </div>       
        <div class="form-group">
            <label class="col-xs-12 control-label text-left">Funções inclusas:</label> 
            <div class="col-xs-5"><?=montaSelect(array(), isset($post_projeto) ? $post_projeto : $usuario['id_projeto'], " id='cursoscbo' style='height: 300px;' multiple  class='form-control' ") ?></div>
            <div class="col-xs-2 text-center valign-middle" style="height: 300px; padding: 117px 0 117px 0;">
                <div class="col-xs-12 margin_b10" onclick="add_fn_cbo(2);" title="Adicionar Funções">
                    <!--a href="javascript:;"><img src="../../img_menu_principal/2rightarrow.png" alt="Adicionar" width="40px" ></a-->
                    <i class="fa fa-angle-double-right btn btn-info" alt="Remover"></i>
                </div>
                <div class="col-xs-12" onclick="add_fn_cbo(1);" title="Remover Funções">
                    <i class="fa fa-angle-double-left btn btn-info" alt="Remover"></i>
                </div>
            </div>
            <div class="col-xs-5">
                <select id="new_cursos" style="height: 300px;" multiple class="form-control">
                    <option></option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-12 text-center">
                <img src="../../imagens/icones/ico-amarelo.png"> Segure a tecla ctrl ao clicar para selecionar mais de 1 aleatóriamente.
            </div>
        </div>  
    </div>
    <div class="box_tp6_2" style="display: none; font-style: italic;" >
        <div class="form-group">
            <label class="col-xs-2 control-label">Funções:</label> 
            <div class="col-xs-10"><?=montaSelect($cursos, NULL, " id='curso_$key' class='form-control' ") ?></div>
        </div>
        <div class="form-group">
            <label class="col-xs-2 control-label">Horário:</label> 
            <div class="col-xs-10"><?=montaSelect(array('-1'=>'Todos os Horários'), isset($post_projeto) ? $post_projeto : $usuario['id_projeto'], "id='horario_" . $key . "' class='form-control'") ?></div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-2 control-label">Referência:</label> 
        <label class="col-xs-2 control-label"><input type="radio" name="referencia" value="1" data-key="<?= $key; ?>" >Sempre</label>
        <label class="col-xs-2 control-label"><input type="radio" name="referencia" value="2" data-key="<?= $key; ?>" checked="checked" >Competência</label>
        <div class="col-xs-6">
            <div class="input-group" id="mes_<?=$key?>">
                <?=montaSelect($meses, date('m'), "id='mes_" . $key . "' class='form-control'") ?>
                <div class="input-group-addon"></div>
                <?=montaSelect($anos,date('Y'), "id='ano_" . $key . "' class='form-control'") ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-2 control-label" for="dias_uteis_<?= $key ?>">Dias Úteis:</label>
        <div class="col-xs-10"><input type="text" id="dias_uteis_<?= $key ?>" class='form-control' /></div>
    </div>
    <div class="form-group">        
        <input type="hidden" id="referencia_<?= $key; ?>" >
        <input type="button" class="btn btn-primary" value="Cadastrar" onclick="form6()" />     
    </div>
    
</fieldset>
<br><br>
<div id="din_<?= $key; ?>">
    <?php include_once 'table_6.php'; ?>
</div>
