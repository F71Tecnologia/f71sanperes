<input type="button" class="button" value="Novo Cadastro" id="show_form_cad_<?= $key; ?>" >
<input type="button" class="button" value="Filtrar" onclick="get_table_<?= $key; ?>()" />
<br><br>
<fieldset id="form_cad_<?= $key; ?>" >
    <legend>Cadastrar Dias Úteis</legend>
    
    <p><label class="first">Tipo:</label> 
        <?php echo montaSelect(array('1'=>'Pelas Funções','2'=>'Pelos Horários'), NULL, " id='tipo_fdias' onchange='mudatipo_diasuteis()' ") ?>
    </p> 
    
    
    <div class="box_tp6_1" >
        <p ><label class="first">Grupo (CBO):</label> 
            <?php 


            $arr_c = array('-1'=>' Filtrar por CBO ');
            foreach($arr_cbo as $k=>$v){
                $arr_c[$k] = $v;
            }

            echo montaSelect($arr_c, NULL, "id='cbo_" . $key . "'");

                    ?>
        </p>       

        <p><label class="first">Funções inclusas:</label> 
            <?php echo montaSelect(array(), isset($post_projeto) ? $post_projeto : $usuario['id_projeto'], " id='cursoscbo' style='height: 300px;' multiple  ") ?>
            <span><a href="javascript:;" onclick="add_fn_cbo(2);" title="Adicionar Funções"><img src="../../img_menu_principal/2rightarrow.png" alt="Adicionar" width="40px" ></a></span>
            <span style="margin-left: 132px;"><a href="javascript:;" onclick="add_fn_cbo(1);" title="Remover Funções"><img src="../../img_menu_principal/2leftarrow.png" alt="Remover" width="40px" ></a></span>
            <select id="new_cursos" style="height: 300px;" multiple >
                <option></option>
            </select>
            <br>
            <span style="margin-left: 134px"><img src="../../imagens/icones/ico-amarelo.png"> Segure a tecla ctrl ao clicar para selecionar mais de 1 aleatóriamente.</span>
        </p>  
    </div>
    <div class="box_tp6_2" style="display: none; font-style: italic;" >
        <p><label class="first">Funções:</label> 
            <?php echo montaSelect($cursos, NULL, " id='curso_$key' ") ?>
        </p> 
        <p><label class="first">Horário:</label> 
            <?php echo montaSelect(array('-1'=>'Todos os Horários'), isset($post_projeto) ? $post_projeto : $usuario['id_projeto'], "id='horario_" . $key . "'") ?>
        </p>
    </div>
    <p><label class="first">Referência:</label> 
        <label><input type="radio" name="referencia" value="1" data-key="<?= $key; ?>" >Sempre</label>
        <label><input type="radio" name="referencia" value="2" data-key="<?= $key; ?>" checked="checked" >Competência</label>
        <?php echo montaSelect($meses, date('m'), "id='mes_" . $key . "'") ?>        
        <?php echo montaSelect($anos,date('Y'), "id='ano_" . $key . "'") ?>  
    </p>
    <p>
        <label class="first" for="dias_uteis_<?= $key ?>">Dias Úteis</label>
        <input type="text" id="dias_uteis_<?= $key ?>" /> 
        
    </p>
    <p class="controls">        
        <input type="hidden" id="referencia_<?= $key; ?>" >
        <input type="button" class="button" value="Cadastrar" onclick="form6()" />     
    </p>
    
</fieldset>
<br><br>
<div id="din_<?= $key; ?>">
    <?php include_once 'table_6.php'; ?>
</div>
