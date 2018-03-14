<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include("../../classes/PlanoSaudeClass.php");
include('../../classes_permissoes/acoes.class.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$hoje = new DateTime();

$id_plano = $_REQUEST['id_plano'];

$objAcoes = new Acoes();
$objPlanoSaude = new PlanoSaudeClass();

$qtdPorPlanoSaude = $objPlanoSaude->getCltPlanoSaude($usuario['id_regiao']);
?>

<div class="page-header box-rh-header" style="margin-top: -12px;">
    <h4><?php echo $res['descicao']; ?></h4>
</div>

<?php
$objPlanoSaude->getPlanoSaude();
if($objPlanoSaude->getNumRowPlanoSaude() > 0) { 
?>
<table class="table table-bordered table-condensed table-hover valign-middle">
    <thead>
        <tr>
            <th colspan="6" class="bg-primary text-center"><h4>Lista de Planos de Saúde Cadastrados</h4></th>
        </tr>
    </thead>
    <tbody>
        <?php while($objPlanoSaude->getRowPlanoSaude()) { ?>
            <tr>
                <td width="45%"><?= $objPlanoSaude->getRazao() ?></td>
                <td width="45%"><?= $objPlanoSaude->getCnpj() ?></td>
                <td class="text-center">
                    <?php if($qtdPorPlanoSaude[$objPlanoSaude->getIdPlanoSaude()] == 0){ ?><button class="btn btn-xs btn-success pointer detalhe_saude" title="Visualizar Participantes" data-toggle="tooltip" data-placement="top" data-acao="Visualizar" data-key="<?= $objPlanoSaude->getIdPlanoSaude() ?>"><i class="fa fa-search"></i></button><?php } ?>
                </td>
                <td class="text-center">
                    <?php if($objAcoes->verifica_permissoes(107)){ ?><button class="btn btn-xs btn-warning pointer action_plano" title="Editar Plano" data-toggle="tooltip" data-placement="top" data-acao="Editar" data-key="<?= $objPlanoSaude->getIdPlanoSaude() ?>"><i class="fa fa-pencil"></i></button><?php } ?>
                </td>
                <td class="text-center">
                    <?php if($qtdPorPlanoSaude[$objPlanoSaude->getIdPlanoSaude()] == 0 && $objAcoes->verifica_permissoes(106)) { ?><button class="btn btn-xs btn-danger pointer deletar" title="Excluir Plano" data-placement="top" data-toggle="tooltip" data-key="<?= $objPlanoSaude->getIdPlanoSaude() ?>" data-nome="<?= $objPlanoSaude->getRazao() ?>"><i class="fa fa-trash-o"></i></button><?php } ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php } else {
    echo '<div class="alert alert-warning">Nenhum Participante Cadastrado!</div>';
} ?>      