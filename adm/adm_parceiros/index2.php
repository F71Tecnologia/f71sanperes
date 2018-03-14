<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/EventoClass.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
//CARREGANDO MENU DE ACORDO COM AS PERMISSOES DA PESSOA
$botoes = new BotoesClass("../img_menu_principal/");
$icon = $botoes->iconsModulos;
$master = $usuario['id_master'];
$acesso_excluir = array(9, 5, 158);
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Administrativo</title>

        <link rel="shortcut icon" href="../../favicon.png">

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-admin-header"><h2><?php echo $icon[2] ?> - ADMINISTRATIVO</h2></div>
                    <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                        <input type="hidden" name="id" id="id" value="" />
                        
                        <h3>Gestão de Parceiros</h3>
                        <div class="form-group">
                            <div class="pull-right">
                                <a class="btn btn-success" href="javascript:;"><i class="fa fa-plus"></i> Novo Parceiro</a>
                                <!-- i class="fa fa-plus"></i><input type="submit" class="button btn btn-success" value="Novo Parceiro" name="novo" id="novoParceiro" /-->
                            </div>
                        </div>

                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th>Última Edição</th>
                                    <th>Editar</th>
                                    <?php if (in_array($_COOKIE['logado'], $acesso_excluir)) { ?>
                                        <th>Excluir</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Listando os Parceiros
                                $qr_parceiros = mysql_query("SELECT * FROM parceiros INNER JOIN regioes ON parceiros.id_regiao =  regioes.id_regiao WHERE parceiros.parceiro_status = '1' AND  regioes.id_master = '$master' ORDER BY parceiro_id ");
                                while ($row_parceiro = mysql_fetch_assoc($qr_parceiros)) {
                                    //if ($row_parceiro['id_regiao'] == '15' or $row_parceiro['id_regiao'] == '36' or $row_parceiro['id_regiao'] == '37')
                                    //    continue;   // condição para não mostrar as regiões 15,36,37
                                    ?>
                                    <tr>
                                        <td><img src="<?php echo 'logo/' . $row_parceiro['parceiro_logo']; ?>" width="25" height="25"/></td>
                                        <td><?php echo $row_parceiro['parceiro_nome']; ?></td>
                                        <td>
                                            <?php
                                            $qr_funcionario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_parceiro[parceiro_id_atualizacao]'");
                                            $row_funcionario = mysql_fetch_assoc($qr_funcionario);
                                            $data = date('d/m/Y', strtotime($row_parceiro['parceiro_atualizacao']));
                                            $nome = explode(' ', $row_funcionario['nome']);

                                            if ($row_parceiro['parceiro_atualizacao'] != '0000-00-00 00:00:00') {
                                                echo 'Editado por: ' . $nome[0] . ' em ' . $data;
                                            } else {

                                                $qr_funcionario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_parceiro[parceiro_autor]'");
                                                $row_funcionario = mysql_fetch_assoc($qr_funcionario);
                                                $nome = explode(' ', $row_funcionario['nome']);

                                                echo 'Cadastrado por: ' . $nome[0] . ' em ' . date('d/m/Y', strtotime($row_parceiro['parceiro_data']));
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a class="btn btn-default btn-edita" href="javascript:;" data-key="<?php echo $row_parceiro['parceiro_id']; ?>"> <i class="fa fa-pencil"></i> </a></td>
                                        <?php if (in_array($_COOKIE['logado'], $acesso_excluir)) { ?>
                                        <td><a class="btn btn-danger btn-exclui" href="javascript:;" data-key="<?php echo $row_parceiro['parceiro_id']; ?>"> <i class="fa fa-trash-o"></i> </a></td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <footer>
                <div class="row">
                    <div class="page-header"></div>
                    <div class="pull-right"><a href="#top">Voltar ao topo</a></div>
                    <div class="col-lg-12">
                        <p>Pay All Fast 3.0</p>
                        <p>Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.</p>
                    </div>
                </div>
            </footer>
        </div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $(".btn-edita").click(function(){
                    var id = $(this).data('key');
                    $("#form1").attr('action','form_parceiro.php');
                    $("#id").val(id);
                    $("#form1").submit();
                });
                
                $(".btn-exclui").click(function(){
                    var id = $(this).data('key');
                    var parceiro = $(this).parent().prev().prev().prev().html();
                    thickBoxConfirm("Exclusão","<p>Deseja realmente excluir o parceiro:</p> <p><strong>"+parceiro+"</strong>?</p>",450,250, function(data){
                        if(data===true){
                            $.post("exclusao.php",{exclui:"exclui",id:id},function(value){
                                if(value.status==1){
                                    document.location.reload();
                                }
                            },'json');
                        }
                    },"");
                });
                
            });
        </script>
    </body>
</html>