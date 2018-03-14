<?php
header('Content-Type: text/html; charset=ISO-8859-1');

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../../conn.php');
include('../../../classes/global.php');
include("../../../classes/InformeRendimentoClass.php");
include('../../../funcoes.php');
include('../../../wfunction.php');

$usuario = carregaUsuario();

$dirf = new InformeRendimentoClass($usuario['id_master']);
$dirf->setAnoBase($_REQUEST['ano']);
$dirf->setTipo($_REQUEST['tipo']);

$idClt = $_REQUEST['id'];
$exibeFeriasResci = true;
switch ($_REQUEST['tipo']){
    case 1:
    case 3:
        $query = "SELECT A.id_autonomo,A.nome,A.id_projeto,A.status,A.id_regiao,B.nome as funcao
                    FROM autonomo AS A
               LEFT JOIN curso AS B ON (A.id_curso = B.id_curso)
                   WHERE A.id_autonomo IN ($idClt)";
        $rsClt = mysql_query($query);
        $exibeFeriasResci = false;
        break;
    case 2:
        $query = "SELECT A.id_clt,A.nome,A.cpf,A.id_projeto,A.status,A.id_regiao,B.nome as funcao,B.salario,
                         DATE_FORMAT(A.data_entrada, '%d/%m/%Y') as ini, 
                         DATE_FORMAT(A.data_demi, '%d/%m/%Y') as fim
                    FROM rh_clt AS A
               LEFT JOIN curso AS B ON (A.id_curso = B.id_curso)
                   WHERE A.id_clt IN ($idClt)";
        $rsClt = mysql_query($query);
        break;
}

$rsProjetos = montaQuery("projeto","id_projeto,nome","id_master = {$usuario['id_master']}");
$pros = array();
foreach($rsProjetos as $val){
    $pros[$val['id_projeto']] = $val['nome'];
}

$clt['projetos'] = null;
$multVinc = (mysql_num_rows($rsClt) > 1) ? true : false;
while($cltVal = mysql_fetch_assoc($rsClt)){
    $clt['projetos'] .= $pros[$cltVal['id_projeto']].", ";
    $clt['nome'] = $cltVal['nome'];
    $clt['cpf'] = $cltVal['cpf'];
    $clt['funcao'] .= $cltVal['funcao'].", ";
    $clt['dt'] .= ($cltVal['fim']!="00/00/0000") ? $cltVal['ini']." - ".$cltVal['fim'].", " : $cltVal['ini'].", ";
    $clt['salario'] .= number_format($cltVal['salario'],2,",",".").", ";
    $clt['id_regiao'] .= $cltVal['id_regiao'];
}

$projetosClt = substr($clt['projetos'] , 0, -2);
$clt['funcao'] = substr($clt['funcao'] , 0, -2);
$clt['dt'] = substr($clt['dt'] , 0, -2);
$clt['salario'] = substr($clt['salario'] , 0, -2);

$dirf->getDadosFolhas($idClt);
$dirf->getDadosDecimoTerceiro($idClt);
$dirf->getDadosFerias($idClt);

$dirf->getDadosExtra($idClt);
$dirf->getDadosPensaoAlimenticia($idClt);

$dirf->getDadosRescisao2015($idClt);

?>
<div class="page-header box-rh-header" style="margin-top: -12px;">
    <h4><?php echo $idClt." - ".$clt['nome']." - ".$clt['cpf'] ; ?></h4>
</div>

<table class="table table-striped table-hover table-condensed table-bordered">    
    <tbody>      
        <tr>
            <td class="text-bold">Projeto(s)</td>
            <td><?php echo $projetosClt; ?></td>
        </tr>
        <tr>
            <td class="text-bold">Função</td>
            <td><?php echo $clt['funcao']; ?></td>
        </tr>
    </tbody>
</table>

<?php if ($exibeFeriasResci){ echo '<div class="col-lg-6 col-md-12">'; } ?>
    <table class="table table-striped table-hover table-condensed table-bordered">    
        <tbody>      
            <tr>
                <td class="text-bold">Rendimento Tributavel</td>
                <td>R$ <?php echo number_format($dirf->salario,2,",","."); ?></td>
            </tr>
            <tr>
                <td class="text-bold">INSS</td>
                <td>R$ <?php echo number_format($dirf->inss,2,",","."); ?></td>
            </tr>
            <tr>
                <td class="text-bold">IRRF</td>
                <td>R$ <?php echo number_format($dirf->ir,2,",","."); ?></td>
            </tr>
            <tr>
                <td class="text-bold">13º Salário DIRF</td>
                <td>R$ <?php echo number_format($dirf->salario13,2,",","."); ?></td>
            </tr>
        </tbody>
    </table>
<?php if ($exibeFeriasResci){ echo '</div>'; }?>

<?php if ($exibeFeriasResci){ ?>
<div class="col-lg-6 col-md-12">
    <table class="table table-striped table-hover table-condensed table-bordered">    
        <tbody>      
            <tr>
                <td class="text-bold">Admissao - Demissão</td>
                <td><?php echo $clt['dt']; ?></td>
            </tr>
            <tr>
                <td class="text-bold">Salário Atual</td>
                <td>R$ <?php echo $clt['salario']; ?></td>
            </tr>
            <tr>
                <td class="text-bold">INSS de Férias</td>
                <td>R$ <?php echo number_format($dirf->inssFerias,2,",","."); ?></td>
            </tr>
            <tr>
                <td class="text-bold">13º Salário Informe</td>
                <td>R$ <?php echo number_format($dirf->salario13Informe,2,",","."); ?></td>
            </tr>
        </tbody>
    </table>
</div>
<?php } ?>

<div class="clear"></div>
<div class="alert alert-success">
    <strong>Atenção!</strong> Lembrando que Janeiro, na verdade corresponde a folha de DEZEMBRO do ano anterior e assim consequentemente Fevereiro corresponde a folha de Janeiro em diante.
</div>
<table class="table table-striped table-hover table-condensed table-bordered">
    <thead>
        <tr class="bg-primary valign-middle">
            <td>#</td>
            <?php for($i=1; $i<=12; $i++){ ?>
            <td><?php echo mesesArray($i); ?></td>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Ren.Trib.</td>
            <?php for($i=0; $i<=11; $i++){ $rtTotal += $dirf->valorMes[$i]['salario']; ?>
            <td><?php echo number_format($dirf->valorMes[$i]['salario'],2,",",".")?></td>
            <?php } ?>
        </tr>
        <tr>
            <td>INSS</td>
            <?php for($i=0; $i<=11; $i++){ //$rtTotal += $dirf->valorMes[$i]['inss']; ?>
            <td><?php echo number_format($dirf->valorMes[$i]['inss'],2,",",".")?></td>
            <?php } ?>
        </tr>
        <?php if($exibeFeriasResci){ ?>
        <tr>
            <td>Férias</td>
            <?php for($i=1; $i<=12; $i++){ $rtTotal += $dirf->valorMesFerias[$i]; ?>
            <td><?php echo number_format($dirf->valorMesFerias[$i],2,",",".")?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td>Rescisão</td>
            <?php for($i=1; $i<=12; $i++){ $rtTotal += $dirf->valorRescisaoMes[$i]; ?>
            <td><?php echo number_format($dirf->valorRescisaoMes[$i],2,",",".")?></td>
            <?php } ?>
        </tr>
        <?php } ?>
    </tbody>
</table>

<?php if ($exibeFeriasResci){ ?>
    <?php if(!$multVinc) { ?>
        <p>Lembrando que Mes na DIRF não é o Mes de Competencia da folha, os contra cheques são mostrados abaixo MES DIRF / COMP Folha</p>
        <table class="table table-striped table-hover table-condensed table-bordered">
            <thead>
                <tr class="bg-primary valign-middle">
                    <?php for($i=0; $i<=11; $i++){ ?>
                    <td><?php if($i==0){echo "Jan/Dez ".($_REQUEST['ano']-1); }else{ echo mesesArray($i+1)."/ ".mesesArray($i); } ?></td>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php for($i=0; $i<=11; $i++){ ?>
                    <td class="text-center">
                        <?php if($dirf->valorMes[$i]['id_folha'] > 0){ ?>
                        <a href="../../contracheque/contra_cheque_oo.php?enc=<?php echo str_replace("+", "--", encrypt("{$clt['id_regiao']}&{$dirf->valorMes[$i]['id_clt']}&{$dirf->valorMes[$i]['id_folha']}")) ?>" target="_blank" title="Gerar PDF" class="gera-contra btn btn-default btn-xs">                                            
                            <i class="text-danger fa fa-file-pdf-o" alt="Gerar PDF" title="Gerar PDF"></i>
                        </a>
                        <?php } ?>
                    </td>
                    <?php } ?>
                </tr>
            </tbody>
        </table>

    <?php }else{ ?>
        <div class="alert alert-warning">
            Pelo fato do funcionário ter multiplos vinculos, não é possivel exibir os contra cheques
        </div>
    <?php } ?>
<?php } ?>
<?php 
if($_COOKIE['logado'] == 158){
    echo "<div><ul><li>ValObjct: {$dirf->salario}</li><li>ValSoma: {$rtTotal}</li><ul></div>";
}

$dirf->limpaVariaveis(); ?>