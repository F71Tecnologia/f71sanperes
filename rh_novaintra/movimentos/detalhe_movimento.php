<?php
header('Content-Type: text/html; charset=ISO-8859-1');

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../classes/MovimentoClass.php');
include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes_permissoes/acoes.class.php');

$objAcoes = new Acoes();

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$id_mov = $_REQUEST['id_mov'];
$ano = $_REQUEST['ano'];

$movimento = new Movimentos();

$res = $movimento->getMovimentoId($id_mov);
$incidencia_inss = ($res['incidencia_inss'] == 1) ? "<span class='label label-success'>INSS</span>" : '';
$incidencia_fgts = ($res['incidencia_fgts'] == 1) ? '<span class="label label-warning">FGTS</span>' : '';
$incidencia_ir = ($res['incidencia_irrf'] == 1) ? '<span class="label label-info">IRRF</span>' : '';
$incide_aviso_previo = ($res['incide_aviso_previo'] == 1) ? '<span class="label label-default">Médio para Aviso Prévio</span>' : '';
$incide_base_ferias_media = ($res['incide_base_ferias_media'] == 1) ? '<span class="label label-default">Média para Férias</span>' : '';
$incide_base_13_media = ($res['incide_base_ferias_media'] == 1) ? '<span class="label label-default">Média para 13º</span>' : '';
$incide_dsr = ($res['incide_dsr'] == 1) ? '<span class="label label-default">DSR</span>' : '';
$incide_aviso_previo = ($res['incide_aviso_previo'] == 1) ? '<span class="label label-default">Médio para Aviso Prévio</span>' : '';


$sql_faixa = $movimento->getfaixasMovimento($res['cod'],$ano);
$tot_faixa = mysql_num_rows($sql_faixa);
?>
<div class="page-header box-rh-header" style="margin-top: -12px;">
    <h4><?php echo $res['descicao']; ?></h4>
</div>

<table class="table table-striped table-hover table-condensed table-bordered">    
    <tbody>
        <tr>
            <td class="text-bold">COD</td>
            <td><?php echo $res['cod']; ?></td>
        </tr>        
        <tr>
            <td class="text-bold">CATEGORIA</td>
            <td><?php echo $res['categoria']; ?></td>
        </tr>        
        <tr>
            <td class="text-bold">INCIDÊNCIA</td>
            <td><?php echo "{$incidencia_inss}{$incidencia_fgts}{$incidencia_ir} {$incide_base_ferias_media} {$incide_base_13_media} {$incide_dsr} {$incide_aviso_previo}"; ?></td>
        </tr>        
    </tbody>
</table>

<?php if($tot_faixa > 0){ ?>
<table class="table table-striped table-hover table-condensed table-bordered">
    <thead>
        <tr class="bg-primary valign-middle">
            <td>FAIXA</td>
            <td>INICIAL</td>
            <td>FINAL</td>
            <td>%</td>
            <td>FIXO</td>
            <td>PISO</td>
            <td>TETO</td>
        </tr>
    </thead>
    <tbody>
    <?php while($res_faixa = mysql_fetch_assoc($sql_faixa)){ ?>
        <tr>
            <td><?php echo $res_faixa['faixa']; ?></td>
            <td><?php echo formataMoeda($res_faixa['v_ini'], 1); ?></td>
            <td><?php echo formataMoeda($res_faixa['v_fim'], 1); ?></td>
            <td><?php echo $res_faixa['percentual'] * 100; ?>%</td>
            <td><?php echo formataMoeda($res_faixa['fixo'], 1); ?></td>
            <td><?php echo formataMoeda($res_faixa['piso'], 1); ?></td>
            <td><?php echo formataMoeda($res_faixa['teto'], 1); ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<?php } ?>