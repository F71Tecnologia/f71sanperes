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
<div class="row">
    <div class="col-lg-12">
        <div class="note note-warning">
            <h3>Relat�rio Personalizado</h3>
            <hr class="hr-warning">
            <div class="col-lg-6 col-sm-6 col-xs-12 col-xxs-12">
                <div class="smallstat pointer" data-url="../relatorios/relatorio_pers.php">
                    <i class="fa fa-file-excel-o warning"></i>
                    <div class="h-50 display-table">
                        <div class="vcenter">
                            <a href="../relatorios/relatorio_pers.php" target="_blank" class="value text-success" style="">Relat�rio Personalizado</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>
<?php
$url_rel = "/intranet/";
foreach ($arr_grupo as $grupo) {
    $arr_rel = $relatorios->carregaRelatorios($grupo['id_grupo']);
    if ($arr_rel != null) { ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="note note-success">
                <a name="<?=$grupo['id_grupo']?>"></a>
                <h3><?=$grupo['nome']?></h3>
                <hr class="hr-success">
                <?php foreach ($arr_rel as $relatorio) { 
                    $rel_link_ar = explode('/',$relatorio['url']);
                    $rel_link = $url_rel.$rel_link_ar[count($rel_link_ar)-2].'/'.$rel_link_ar[count($rel_link_ar)-1]; ?>
                    <div class="col-lg-6 col-sm-6 col-xs-12 col-xxs-12">
                        <div class="smallstat pointer" data-url="<?=$rel_link;?>" data-id="<?=$relatorio['id_relatorio']?>">
                            <i class="fa fa-file-excel-o success"></i>
                            <div class="h-50 display-table">
                                <div class="vcenter">
                                    <a href="<?=$rel_link;?>" target="_blank" class="value text-success" style=""><?=$relatorios->relatorioNovo($relatorio['id_relatorio'])?> <?=str_replace('Relat�rio de ','',$relatorio['nome'])?></a>
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

