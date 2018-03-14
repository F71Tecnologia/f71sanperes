<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include("../../classes/ProjetoClass.php");
include("../../classes/ContrachequeClass.php");
include('../../funcoes.php');
include('../../wfunction.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$contraObj = new Contracheque();

$objProjeto = new ProjetoClass();

// lista de projetos (usado no menu esquerdo)
$projetosList = $objProjeto->getProjetos($id_regiao);

$id_projeto = $_REQUEST['id_projeto'];
$ano = $_REQUEST['ano'];

// isso aqui vai para uma classe depois


$listaFolha = $contraObj->listaFolhasFechadas($id_projeto, $ano);
$projetonome = $objProjeto->getProjeto($value['projeto']);
//print_r($listaFolha);

if ($listaFolha) {
    ?>
    <h3> Ano: <?= $ano ?></h3>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th style="width:5%">Cod</th>
                <th style="width:25%">Projeto</th>
                <th style="width:15%">M&ecirc;s</th>
                <th style="width:15%">Per&iacute;odo</th>
                <th style="width:10%" class="text-center">N&ordm; de Participantes</th>
                <th style="width:10%" class="text-center">individual</th>
                <th style="width:10%" class="text-center">Todos</th>
                <th style="width:10%" class="text-center">Arquivo CSV</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($listaFolha as $value) {
                if ($value['terceiro'] == 1) {
                    switch ($value['tipo_terceiro']) {
                        case 1:
                            $exibicao = "13º - 1ª parcela";
                            break;
                        case 2:
                            $exibicao = "13º - 2ª parcela";
                            break;
                        case 3:
                            $exibicao = "13º - Integral";
                            break;
                    }
                } else {
                    $exibicao = mesesArray($value['mes']);
                }

                $linkvario = encrypt("$id_regiao&todos&{$value['id_folha']}");
                $linkvario = str_replace("+", "--", $linkvario);
                ?>
                <tr>
                    <td><?= $value['id_folha'] ?></td>
                    <td><?= utf8_encode($value['projeto_nome']) ?></td>
                    <td><?= utf8_encode($exibicao) ?></td>
                    <td><?= utf8_encode("{$value['data_inicio']} à {$value['data_fim']}") ?></td>
                    <td class="text-center"><?= $value['clts'] ?></td>
                    <td class="text-center"><a href="#" class="btn btn-info btn-xs individual" data-folha="<?= $value['id_folha'] ?>"><i class="fa fa-user"></i></a></td>
                    <td class="text-center"><a href="#" class="btn btn-info btn-xs todos" data-folha="<?= $value['id_folha'] ?>"><i class="fa fa-users"></i></a></td>
                    <td class="text-center">
                        <a href="geracontra_txt.php?enc=$linkvario&id=3" class="btn btn-info btn-xs">
                            <i class="fa fa-file-excel-o"></i>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } else {
    ?>
    <div class="bs-callout bs-callout-danger text-danger">
        <h4>Aten&ccedil;&atilde;o!</h4>
        <p>N&atilde;o h&aacute; Folha para o <strong>Projeto</strong> ou <strong>Ano</strong> selecionado.</p>
    </div>
<?php }
?>






