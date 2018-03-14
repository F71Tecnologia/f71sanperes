<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/EventoClass.php");
include("../../classes/ParceiroClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
//CARREGANDO MENU DE ACORDO COM AS PERMISSOES DA PESSOA
$botoes = new BotoesClass("../img_menu_principal/");
$icon = $botoes->iconsModulos;
$master = $usuario['id_master'];

$parceiro = $_REQUEST['id'];
$regioes = GlobalClass::carregaRegioes($master);

$objParceiro = new Parceiros();
$row_parceiro = $objParceiro->getItem($parceiro);

if ($_POST['pronto'] == 'edicao') {
    $objParceiro->edita($_POST);
}
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
                    <form action="" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="id_parceiro" value="<?php echo $row_parceiro['id_parceiro']?>" />

                        <h3>Edição de Parceiros Comercial</h3>
                        
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Regiao</label>
                                        <div class="col-lg-9">
                                            <?php echo montaSelect($regioes, $row_parceiro['id_regiao'], "name='regiao' id='regiao' class='form-control validate[required,custom[select]]'"); ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Nome</label>
                                        <div class="col-lg-9">
                                            <input type="text" id="nome" name="nome" class="form-control validate[required]" value="<?php echo $row_parceiro['parceiro_nome']; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group">
                                        <label for="regiao" class="col-lg-2 control-label">CNPJ</label>
                                        <div class="col-lg-4">
                                            <input type="text" id="cnpj" name="cnpj" class="form-control validate[required]" value="<?php echo $row_parceiro['parceiro_cnpj']; ?>">
                                        </div>

                                        <label for="projeto" class="col-lg-1 control-label">CCM</label>
                                        <div class="col-lg-4">
                                            <input type="text" id="ccm" name="ccm" class="form-control validate[required]" value="<?php echo $row_parceiro['parceiro_ccm']; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Endereço</label>
                                        <div class="col-lg-9">
                                            <input type="text" id="endereco" name="endereco" class="form-control validate[required]" value="<?php echo $row_parceiro['parceiro_endereco']; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Bairro</label>
                                        <div class="col-lg-9">
                                            <input type="text" id="bairro" name="bairro" class="form-control validate[required]" value="<?php echo $row_parceiro['parceiro_bairro']; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group">
                                        <label for="regiao" class="col-lg-2 control-label">Cidade</label>
                                        <div class="col-lg-4">
                                            <input type="text" id="cidade" name="cidade" class="form-control validate[required]" value="<?php echo $row_parceiro['parceiro_cidade']; ?>">
                                        </div>

                                        <label for="projeto" class="col-lg-1 control-label">Estado</label>
                                        <div class="col-lg-4">
                                            <select name="estado" id="estado"  class="form-control validate[required]"> 
                                                <option value="<?php echo $row_parceiro['parceiro_estado']; ?>"><?php echo $row_parceiro['parceiro_estado']; ?></option>   
                                                <option value=""></option>

                                                <option value="AC">AC</option>  
                                                <option value="AL">AL</option>  
                                                <option value="AM">AM</option>  
                                                <option value="AP">AP</option>  
                                                <option value="BA">BA</option>  
                                                <option value="CE">CE</option>  
                                                <option value="DF">DF</option>  
                                                <option value="ES">ES</option>  
                                                <option value="GO">GO</option>  
                                                <option value="MA">MA</option>  
                                                <option value="MG">MG</option>  
                                                <option value="MS">MS</option>  
                                                <option value="MT">MT</option>  
                                                <option value="PA">PA</option>  
                                                <option value="PB">PB</option>  
                                                <option value="PE">PE</option>  
                                                <option value="PI">PI</option>  
                                                <option value="PR">PR</option>  
                                                <option value="RJ">RJ</option>  
                                                <option value="RN">RN</option>  
                                                <option value="RO">RO</option>  
                                                <option value="RR">RR</option>  
                                                <option value="RS">RS</option>  
                                                <option value="SC">SC</option>  
                                                <option value="SE">SE</option>  
                                                <option value="SP">SP</option>  
                                                <option value="TO">TO</option>  
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr class="panel-wide">
                                
                                <div class="row">
                                    <div class="form-group">
                                        <label for="regiao" class="col-lg-2 control-label">Telefone</label>
                                        <div class="col-lg-4">
                                            <input type="text" id="telefone" name="telefone" class="form-control validate[required]" value="<?php echo $row_parceiro['parceiro_telefone']; ?>">
                                        </div>

                                        <label for="projeto" class="col-lg-1 control-label">Celular</label>
                                        <div class="col-lg-4">
                                            <input type="text" id="celular" name="celular" class="form-control validate[required]" value="<?php echo $row_parceiro['parceiro_celular']; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group">
                                        <label for="regiao" class="col-lg-2 control-label">Contato</label>
                                        <div class="col-lg-4">
                                            <input type="text" id="contato" name="contato" class="form-control validate[required]" value="<?php echo $row_parceiro['parceiro_contato']; ?>">
                                        </div>

                                        <label for="projeto" class="col-lg-1 control-label">CPF</label>
                                        <div class="col-lg-4">
                                            <input type="text" id="cpf" name="cpf" class="form-control validate[required]" value="<?php echo $row_parceiro['parceiro_cpf']; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Banco</label>
                                        <div class="col-lg-9">
                                            <input type="text" id="banco" name="banco" class="form-control validate[required]" value="<?php echo $row_parceiro['parceiro_banco']; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group">
                                        <label for="regiao" class="col-lg-2 control-label">Agência</label>
                                        <div class="col-lg-4">
                                            <input type="text" id="agencia" name="agencia" class="form-control validate[required]" value="<?php echo $row_parceiro['parceiro_agencia']; ?>">
                                        </div>

                                        <label for="projeto" class="col-lg-1 control-label">Conta</label>
                                        <div class="col-lg-4">
                                            <input type="text" id="conta" name="conta" class="form-control validate[required]" value="<?php echo $row_parceiro['parceiro_conta']; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Email</label>
                                        <div class="col-lg-9">
                                            <input type="text" id="email" name="email" class="form-control validate[required]" value="<?php echo $row_parceiro['parceiro_email']; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <hr class="panel-wide">
                                <h6 class="text-light-gray text-semibold text-xs">IMAGEM</h6>
                                
                                
                                <div class="row <?php echo ($row_parceiro['parceiro_id'] == "") ? "" : "hide"?> id="div-logo-file">
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Logo</label>
                                        <div class="col-lg-9">
                                            <input type="file" name="logo" id="logo"  class="form-control validate[required]"/>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row <?php echo ($row_parceiro['parceiro_id'] == "") ? "hide" : ""?>" id="div-logo-image">
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Logo</label>
                                        <div class="col-lg-9">
                                            <img src="<?php echo 'logo/' . $row_parceiro['parceiro_logo']; ?>" id="logomarca" class="img-thumbnail" />
                                            <a href="javascript:;" class="btn" id="remove_logo" data-key="<?php echo $row_parceiro['parceiro_id']; ?>"><i class="fa fa-trash-o"></i> Revomer Logo</a>
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                            <div class="panel-footer text-right">
                                <a href="javascript:;" class="btn btn-success" id="bt-salvar"><i class="fa fa-save"></i> Salvar</a>
                            </div>
                        </div>
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
                $('#remove_logo').click(function() {
                    thickBoxConfirm("Remover Logo", "Deseja realmente remover a logo?", 400, 200, function(confirm){
                        if(confirm === true){
                            $.ajax({
                                url: 'form_parceiro.php',
                                data: {
                                    caminho: $('#logomarca').attr('src'),
                                    id: $('#remove_logo').attr('rel')
                                },
                                success: function() {
                                    $('#visualiza_img').html('');
                                    $('#remove_logo').hide();
                                }
                            });
                        }
                    }, "");
                });

                $("#cnpj").mask('99.999.999/9999-99');
                $("#telefone").mask('(99) 9999-9999');
                $("#celular").mask('(99) 9999-9999');
                $("#cpf").mask('999.999.999-99');

                $("#cadastro").validationEngine();
            });
        </script>
    </body>
</html>


