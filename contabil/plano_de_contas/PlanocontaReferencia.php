<ul class="nav nav-tabs nav-justified hidden-print" style="margin-bottom: 20px;">
    <!--<li class="active"><a class="contabil" href="#contas_cadastro" data-toggle="tab">Cadastro </a></li>-->
    <li class="active"><a class="contabil" href="#contas_empresas" data-toggle="tab">Projetos / Empresas</a></li>
    <!--<li><a class="contabil" href="#contas_sped" data-toggle="tab">SPED</a></li>-->
</ul>
  
<div class="tab-content"> 
 
    <div class="tab-pane active" id="contas_empresas">
        <form action="planodecontas_controle.php" method="post" name="form_planocontas_empresa" id="form_planocontas_empresa" class="form-horizontal top-margin" enctype="multipart/form-data">
            <input type="hidden" name="home" id="home" value="" />
            <div class="panel panel-default hidden-print">
                <div class="panel-body">
                    <label class="col-lg-2 control-label">Projeto</label>
                    <div class="col-lg-6">
                        <?= montaSelect(getProjetos($usuario['id_regiao']), $value, 'name="projeto" id="projeto" class="form-control validate[required]"') ?>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-default" id="empresa_planoconta" name="empresa_planoconta_referencia" value="empresa_planoconta_referencia">
                            <span class="glyphicon glyphicon-search"></span>
                        </button>
                    </div>
                </div> 
            </div>
            <div class="panel-body" id="empreascontas">
            </div>
        </form>
    </div>

    <div class="tab-pane modal fade" id="contas_sped">
	<div class="modal-dialog" role="document">
        <div class="modal-content">
	 <div class="modal-header">            
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <form action="" method="post" name="form_plano_contas" id="form_plano_contas" class="form-horizontal top-margin" enctype="multipart/form-data">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-lg-12">
                        <?php
                        $count = 0;
                        $k = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0 );
                        foreach ($nivel as $value) {
                            
                            list($key1, $key2, $key3, $key4, $key5, $key6, $key7, $key8) = explode('.', $value['classificador']);
                            
                            if ($value['nivel'] == 2 && $k[1] == '') { $key1 = ''; }
                            if ($value['nivel'] == 3 && $k[2] == '') { $key2 = ''; }
                            if ($value['nivel'] == 4 && $k[3] == '') { $key3 = ''; }
                            if ($value['nivel'] == 5 && $k[4] == '') { $key4 = ''; }
                            if ($value['nivel'] == 6 && $k[5] == '') { $key5 = ''; }
                            if ($value['nivel'] == 7 && $k[6] == '') { $key6 = ''; }
                            if ($value['nivel'] == 1) { $cor = 'primary'; }
                            if ($value['nivel'] == 2) { $classeN = "$key1"; $cor = 'info'; } 
                            if ($value['nivel'] == 3) { $classeN = "$key1"; $cor = 'info'; } 
                            if ($value['nivel'] == 4) { $classeN = "$key1"; $cor = 'info'; }
                            if ($value['nivel'] == 5 && $value['tipo'] == "S") { $classeN = "$key1"; $cor = 'info'; }
                            if ($value['nivel'] == 6 && $value['classificacao'] == "S") { $classeN = "$key1"; $cor = 'info'; }
                            if ($value['nivel'] == 7 && $value['classificacao'] == "S") { $classeN = "$key1"; $cor = 'info'; }
                            if ($value['nivel'] > 4 && $value['classificacao'] == "A") { $classeN = "$key1"; $cor = 'warning'; }
                            ?> 
                            <div class="panel panel-<?= $cor ?> margin_b5 <?= ($value['nivel'] == 1) ? 'pointer nivel' : null ?> n<?= $classeN ?>" data-n="<?= $value['nivel'] ?>" data-k1="<?= $key1 ?>" data-k2="<?= $key2 ?>" data-k3="<?= $key3 ?>" data-k4="<?= $key4 ?>" data-k5="<?= $key5 ?>" data-k6="<?= $key6 ?>" data-k7="<?= $key7 ?>" style="display: <?= ($value['nivel'] > 1) ? 'none' : null ?>">
                                <div class="panel-heading " style="padding-left: <?= 30 * $value['nivel'] ?>px;"><div class="listaSpeed" data-id="<?= $value['classificador']?>"> <?= $value['classificador'] . ' - ' . $value['descricao'] ?></div></div>
                            </div>
                            <?php
                            $k[1] = $key1;
                            $k[2] = $key2;
                            $k[3] = $key3;
                            $k[4] = $key4;
                            $k[5] = $key5;
                            $k[6] = $key6;
                            $k[7] = $key7;
                        }
                        ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
    </div>
   </div>

    <!--<div class="tab-pane active" id="contas_cadastro">
        <form action="planodecontas_controle.php" method="post" name="form_nova_conta" id="form_nova_conta" class="form-horizontal top-margin" enctype="multipart/form-data">
            <input type="hidden" name="novaconta" value="Salvar">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group">
                        <label for="" class="col-lg-2 control-label">Projeto</label>
                        <div class="col-lg-6">
                            <?= montaSelect(getProjetos($usuario['id_regiao']), $value, 'name="projeto" id="projeto" class="form-control validate[required]"') ?>
                        </div>
                        <?php echo $id_projeto ?>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-lg-2 control-label">Conta</label>
                        <div class="col-lg-3">
                            <div class="input-group">
                                <input type="text" value="" name="classificador" id="classificador" maxlength="10" class="contaPai col-lg-6 form-control validate[required]">
                                <div class="input-group-addon">
                                    <i id="ico_search" class="fa fa-search valign-middle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <input type="text" name="descricao" id="descricao" placeholder="Descrição" maxlength="70"  class="col-lg-8 text text-left form-control validate[required]"> 
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-lg-2 control-label">Acesso</label>
                        <div class="col-lg-3">
                            <input type="text" name="codigo" id="codigo" maxlength="12" class="text text-uppercase col-lg-4 text text-left form-control"> 
                        </div>
                    </div> <!-- conta_referencia 
                    <div class="form-group">
                        <label for="" class="col-lg-2 control-label">Conta Pai</label>
                        <div class="col-lg-2">
                            <input type="text" value="" name="conta_pai" id="conta_pai" maxlength="10" class="col-lg-6 form-control validate[required]">
                            <input type="hidden" value="" name="id_contapai" id="id_contapai">        
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-lg-2 control-label">Histórico Padrão</label>
                        <div class="col-lg-8">
                            <?= montaSelect($optHistorico, NULL, 'name="id_historico" id="id_historico" class="form-control"') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-lg-2 control-label">Classificação</label>
                        <div class="col-lg-3">
                            <div class="radio-inline">
                                <label class="text-slim"><input type="radio" name="tipo" id="tipo" value="A" class="validate[required]"> Analítica</label>
                            </div> 
                            <div class="radio-inline">
                                <label class="text-slim"><input type="radio" name="tipo" id="tipo" value="S" class="validate[required]"> Sintética</label>
                            </div>
                        </div>
                        <label for="" class="col-lg-2 control-label">Natureza</label>
                        <div class="radio-inline">
                            <label class="text-slim"><input type="radio" name="natureza" id="natureza" value="C" class="validate[required]">Credora</label>
                        </div> 
                        <div class="radio-inline">
                            <label class="text-slim"><input type="radio" name="natureza" id="natureza" value="D" class="validate[required]">Devedora</label>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Salvar</button>
                </div>
            </div>
        </form>
        <div class="col-ms-12">
            <table id="planodecontas" class="table table-striped table-condensed text text-sm valign-middle">
                <thead>
                    <tr>
                        <th>Conta</th>
                        <th>Descrição</th>
                        <th>Classificação</th>
                        <th>Projeto</th>
                    </tr>
                </thead> 
                <tbody></tbody>
            </table>
        </div>
    </div>-->

    <!--<div class="tab-pane" id="rel_planodecontas"></div>-->
</div>
