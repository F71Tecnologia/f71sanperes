<?php
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

//echo $data->format('d/m/Y') .' é: '. $formatter->format($data);

include "../conn.php";
include "../wfunction.php";

$path_fotos = 'http://f71lagos.com/intranet/fotos/';

$data1 = ConverteData($_REQUEST['data1']);
$data2 = ConverteData($_REQUEST['data2']);

function subHours($h1, $h2)
{
    $row = mysql_fetch_array(mysql_query("select time_format(timediff('{$h1}', '{$h2}'),'%H:%i:%s');"));
    return $row[0];
}

// Recebe vetor com horas a serem somadas
function sumHours($vetor)
{
    $seconds = 0;

    foreach ($vetor as $time)
    {
        list( $g, $i, $s ) = explode(':', $time);
        $seconds += $g * 3600;
        $seconds += $i * 60;
        $seconds += $s;
    }

    $hours = floor($seconds / 3600);
    $seconds -= $hours * 3600;
    $minutes = floor($seconds / 60);
    $seconds -= $minutes * 60;

    $minutes = $minutes < 10 ? '0' . $minutes : $minutes;
    $seconds = $seconds < 10 ? '0' . $seconds : $seconds;

    return $hours . ":" . $minutes . ":" . $seconds;
}

if (isset($_REQUEST['gerar']))
{

    if (isset($data1) && isset($data2))
    {
        $sql = "select p.data 
                from terceiro_ponto p 
                where (p.data between '{$data1}' and '{$data2}') and p.maquina = 'f71'
                group by p.`data` desc;";
    } else
    {
        exit();
    }
    //echo "sql1 = [{$sql}]<br/>\n";
    $result = mysql_query($sql);
}

$sql = "select id_f71, nome from rh_clt where computar = 1 and id_f71 is not null order by nome";
$fun = mysql_query($sql);
?>
<html>
    <head>
        <title>:: Intranet :: Controle de Acesso</title>
        <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
        
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="./jquery/css/smoothness/jquery-ui-1.10.0.custom.min.css" rel="stylesheet" type="text/css" />
        <link href="bootstrap.css" rel="stylesheet" type="text/css" />

        <script src="./jquery/js/jquery.js"></script>
        <script src="./jquery/js/jquery-ui.js"></script>
        <script src="./jquery/jquery.mask.min.js"></script>
        
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
                        <h1 class="page-header">Controle de Acesso
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
                                <option value="<?= $row['id_f71'] ?>" <?= (trim($row['id_f71']) === trim($_REQUEST['func']) ? "selected='selected'" : "") ?>><?= $row['nome'] ?></option>
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
                                <tr style="background-color: #5cb85c; color: #ffffff">
                                    <?= ($_REQUEST['func'] !== '0') ? '<th>DATA</th>' : '' ?>
                                    <th>NOME</th>
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
                                $i = 1;
                                $tot_h = array();
                                while ($row = mysql_fetch_array($result)):
                                    if ($_REQUEST['func'] == '0'):
                                        ?>
                                        <tr style="background-color: #00ADE3; color: #ffffff">
                                            <td><?= strftime("%A", strtotime($row['data'])) ?> <?= date("d/m/Y", strtotime($row['data'])) ?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <?php
                                    endif;
                                    if ($_REQUEST['func'] !== '0')
                                    {
                                        $where = " and c.numero = {$_REQUEST['func']}";
                                        $where_t = " and p.id_f71 = {$_REQUEST['func']}";
                                    } else
                                    {
                                        $where = "";
                                        $where_t = "";
                                    }
                                            
                                    $sql = "select id_clt, id_f71, nome
                                            from 
                                                rh_clt c where c.computar = 1 
                                                and c.id_f71 is not null 
                                                and c.id_f71 not in (select p.id_f71 from terceiro_ponto p where p.`data` = '{$row['data']}' and p.maquina='f71')
                                                {$where}
                                            order by c.nome;";
                                            
                                    //echo "sql2 = [{$sql}]<br/>\n";

                                    $faltas = mysql_query($sql);

                                    while ($f_row = mysql_fetch_array($faltas)):
                                        ?>
                                        <tr style="background-color: #FE5D64; color: #ffffff">
                                            <?= ($_REQUEST['func'] !== '0') ? '<td></td>' : '' ?>
                                            <td><?= $f_row['nome'] ?></td>
                                            <td></td>
                                            <td colspan="2" style="text-align: center;">Sem registro para esta data</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <?php
                                    endwhile;

                                    $sql = "select
                                                p.id_f71,
                                                t.nome, 
                                                p.data, min(hora) as entrada, if(MAX(hora) = MIN(hora), '---', MAX(hora)) AS saida, timediff(max(hora), min(hora)) as total,
                                                min(p.imagem) as img1, max(p.imagem) as img2
                                            from rh_clt t inner join
                                                terceiro_ponto p on t.id_f71 = p.id_f71
                                            where
                                                        p.`data` = '{$row['data']}' {$where_t}
                                            group 
                                                    by p.id_terceirizado, t.nome, p.data
                                            order by
                                                    t.nome, saida desc, entrada desc;";
                                    $dias = mysql_query($sql);
                                    //echo "sql3 = [{$sql}]<br/>\n";

                                    while ($d_row = mysql_fetch_array($dias)):
                                        ?>

                                        <tr>
                                            <?= ($_REQUEST['func'] !== '0') ? '<td style="font-size: 12px">' . strftime("%A", strtotime($row['data'])) . ' ' . date("d/m/Y", strtotime($row['data'])) . '</td>' : '' ?>                                            
                                            <td><?= $d_row['nome'] ?></td>
                                            <td style="text-align: center;"><a href="<?= $path_fotos.$d_row['img1'] ?>" target="_blank"><img src="<?= $path_fotos.$d_row['img1'] ?>" width="40" height="30"/></a></td>
                                            <td style="text-align: center;"><?= $d_row['entrada'] ?></td>
                                            <td style="text-align: center;">
                                                <?if($d_row['img2']==$d_row['img1']):?>
                                                ---
                                                <?else:?>
                                                <a href="<?= $path_fotos.$d_row['img2'] ?>" target="_blank"><img src="<?= $path_fotos.$d_row['img2'] ?>" width="40" height="30"/></a>
                                                <?endif;?>
                                            </td>
                                            <td style="text-align: center;"><?= $d_row['saida'] ?></td>
                                            <td style="text-align: center;"><?= $d_row['total'] ?></td>
                                        </tr>

                                        <?php
                                        $tot_h[$d_row['nome']][$i] = $d_row['total'];
                                    endwhile;
                                    $i++;
                                    $tot = $tot + $row['valor'];
                                endwhile;
                                ?>
                            </tbody>

                        </table>
                        <table id="tbRelatorio" class="table table-hover">
                            <thead>
                                <tr style="background-color: #5cb85c; color: #ffffff">
                                    <th>NOME</th>
                                    <th>TOTAL HORAS REGISTRADAS</th>
                                    <th>TOTAL HORAS NO PERÍODO</th>
                                    <th>DIFERENÇA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($tot_h as $chave => $lin):
                                    $soma = sumHours($tot_h[$chave]);
                                    $horas = explode(':', $soma);
                                    $diffHoras = date('H:i:s',(strtotime($horas) - strtotime(($horas[0] - ($i - 1) * 9))));
                                    $dif = subHours($soma, (($i - 1) * 9).':00:00');
                                    ?>
                                    <tr>
                                        <td><?= $chave ?></td>
                                        <td><?= $soma ?></td>
                                        <td><?= ($i - 1) * 9 ?></td>
                                        <td style="color: <?= ($horas[0] - ($i - 1) * 9) >= 0 ? '#00335e' : '#F40000' ?>"><?= $dif ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
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
