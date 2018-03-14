<?php
include_once('../classes/RelatorioClass.php');
$relatorios = new Relatorio(); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="btn-group btn-group-justified margin_b10">
            <?php
            $arr_grupo = $relatorios->carregaGrupos();
            $cont = 0;
            foreach ($arr_grupo as $grupo) { 
                if($cont == 4){ $cont=0; ?>
                    </div>
                    <div class="btn-group btn-group-justified margin_b10">
                <?php } ?>
                <a href="#<?=$grupo['id_grupo']?>" class="btn btn-default"><?=$grupo['nome']?></a>        
                <?php 
                $cont++;
            } ?>
        </div>
        <!--a class="btn disabled"></a-->
    </div>
</div>
<?php if($_COOKIE['logado'] == 257 OR $_COOKIE['logado'] == 5 OR $_COOKIE['logado'] == 82 OR $_COOKIE['logado'] == 178 OR $_COOKIE['logado'] == 209){ ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="note note-warning">
                <h3>Relatório Personalizado</h3>
                <hr class="hr-warning">
                <div class="col-lg-6 col-sm-6 col-xs-12 col-xxs-12">
                    <div class="smallstat pointer" data-url="../relatorios/relatorio_pers.php">
                        <i class="fa fa-file-excel-o warning"></i>
                        <div class="h-50 display-table">
                            <div class="vcenter">
                                <span class="value text-warning">Relatório Personalizado</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
<?php }
$url_rel = "/intranet/relatorios/";
foreach ($arr_grupo as $grupo) {
    $arr_rel = $relatorios->carregaRelatorios($grupo['id_grupo']);
    if ($arr_rel != null) { ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="note note-success">
                <a name="<?=$grupo['id_grupo']?>"></a>
                <h3><?=$grupo['nome']?></h3>
                <hr class="hr-success">
                <?php foreach ($arr_rel as $relatorio) { ?>
                    <div class="col-lg-6 col-sm-6 col-xs-12 col-xxs-12">
                        <div class="smallstat pointer" data-url="<?=$url_rel.array_pop(explode('/',$relatorio['url']));?>" data-id="<?=$relatorio['id_relatorio']?>">
                            <i class="fa fa-file-excel-o success"></i>
                            <div class="h-50 display-table">
                                <div class="vcenter">
                                    <a href="<?=$url_rel.array_pop(explode('/',$relatorio['url']));?>" target="_blank" class="value text-success" style=""><?=$relatorios->relatorioNovo($relatorio['id_relatorio'])?> <?=str_replace('Relatório de ','',$relatorio['nome'])?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <?php
    }
}
?>

