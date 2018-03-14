<?php
include "../conn.php";
include "../wfunction.php";

$data1 = ConverteData($_REQUEST['data1']);
$data2 = ConverteData($_REQUEST['data2']);

if (isset($_REQUEST['gerar'])) {

    if (isset($data1) && isset($data2)) {
        $where .= " p.data between '$data1' and '$data2' ";
    } else {
        exit();
    }
    if ($_REQUEST['func'] !== '0') {
        $where .= " and t.id_clt = {$_REQUEST['func']} ";
    }


    $sql = "select
		t.nome, 
		p.data, min(hora) as entrada, if(MAX(hora) = MIN(hora), '---', MAX(hora)) AS saida, timediff(max(hora), min(hora)) as total,
		min(p.imagem) as img1, max(p.imagem) as img2
	    from rh_clt t inner join
		terceiro_ponto p on t.numero = p.numero
	    where
		{$where}
	    group 
		    by p.id_terceirizado, t.nome, p.data
	    order by
		    p.data DESC, saida desc, entrada desc;";

    $result = mysql_query($sql);
    echo "sql = [$sql]<br>\n";
    exit();
}

$sql = "select * from rh_clt";
$fun = mysql_query($sql);
?>
<html>
    <head>
        <title>:: Intranet :: Controle de Máquina</title>
        <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />

        <link href="../jquery/css/smoothness/jquery-ui-1.10.0.custom.min.css" rel="stylesheet" type="text/css" />
        <link href="bootstrap.css" rel="stylesheet" type="text/css" />

        <script src="../jquery/js/jquery.js"></script>
        <script src="../jquery/js/jquery-ui.js"></script>
        <script src="../jquery/jquery.mask.min.js"></script>
        <style media="print">
            .noprint{
                display: none;
            }
        </style>
    </head>
    <body class="novaintra" >        
        <div id="content" class="container-fluid">
            <form  name="form" action="" method="post" id="form" class="form-horizontal" style="padding: 0 70px;">
                
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">Controle de Máquina
                            <small>Relatório</small>
                        </h1>
                    </div>
                </div>
                
                <div class="row noprint">
                    
                    <div class="form-group">
                        <label for="exampleInputAmount">Período</label>
                        <div class="input-group">
                            <div class="input-group-addon"> de </div>
                            <input class="data form-control" name="data1" type="text" id="data1" size="11" maxlength="11" value="<?= (!is_null($_REQUEST['data1'])) ? ($_REQUEST['data1']) : '' ?>">
                            <div class="input-group-addon"> até </div>
                            <input class="data form-control" name="data2" type="text" id="data2" size="11" maxlength="11" value="<?= (!is_null($_REQUEST['data2'])) ? ($_REQUEST['data2']) : '' ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="first" style='width: 200px'>Colaborador:</label>
                        <select name="func" id="func" class="form-control">
                            <option value="0">Todos</option>
                            <?php while ($row = mysql_fetch_array($fun)): ?>
                            <option value="<?= $row['id_clt'] ?>" <?= (trim($row['id_clt']) === trim($_REQUEST['func']) ? "selected='selected'" : "") ?>><?= $row['nome'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="gerar" class="btn btn-success" value="Gerar" id="gerar"/>
                    </div>
                </div>
                <div class="row">
                <?php if (isset($_REQUEST['gerar'])): ?>

                    <table id="tbRelatorio" class="table table-hover">
                        <thead>
                            <tr>
                                <th>NOME</th>
                                <th>DATA</th>
                                <th>FOTO REGISTRO INICIAL</th>
                                <th>REGISTRO INICIAL</th>
                                <th>FOTO REGISTRO FINAL</th>
                                <th>REGISTRO FINAL</th>
                                <th>TOTAL DE REGISTROS. (HORAS)</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $tot = 0;
                        while ($row = mysql_fetch_array($result)):
                            ?>

                            <!--tr class="linha_<?php echo ($alternateColor++ % 2 == 0) ? "um" : "dois"; ?>" style="font-size:12px;"-->
                            <tr>
                                <td><?= $row['nome'] ?></td>
                                <td style="text-align: center;"><?= date("d/m/Y", strtotime($row['data'])) ?></td>
                                <td style="text-align: center;"><a href="./fotos/<?= $row['img1'] ?>" target="_blank"><img src="./fotos/<?= $row['img1'] ?>" width="40" height="30"/></a></td>
                                <td style="text-align: center;"><?= $row['entrada'] ?></td>
                                <td style="text-align: center;">
                                    <?if($row['img2']==$row['img1']):?>
                                    ---
                                    <?else:?>
                                    <a href="./fotos/<?= $row['img2'] ?>" target="_blank"><img src="./fotos/<?= $row['img2'] ?>" width="40" height="30"/></a>
                                    <?endif;?>
                                </td>
                                <td style="text-align: center;"><?= $row['saida'] ?></td>
                                <td style="text-align: center;"><?= $row['total'] ?></td>
                            </tr>

                        <?php $tot = $tot + $row['valor'];    endwhile;    ?>
                        </tbody>
                        <!--tr class="linha_<?php echo ($alternateColor++ % 2 == 0) ? "um" : "dois"; ?>" style="font-size:12px;">
                            <td></td>
                            <td style="text-align: center;"></td>
                            <td style="text-align: center;"></td>
                            <td style="text-align: center;"></td>
                            <td style="text-align: center;"></td>
                            <td style="text-align: center;"></td>
                            <td style="text-align: center;"></td>
                        </tr-->		

                    </table>
                <?php endif; ?>
                </div>
            </form>
        </div>
        <script>
            $(function () {
                $(".data").datepicker({
                    maxDate: '+1D',
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'dd/mm/yy',
                    dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'],
                    dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
                    dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                    monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                    monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']
                });
            });
        </script>
    </body>
</html>