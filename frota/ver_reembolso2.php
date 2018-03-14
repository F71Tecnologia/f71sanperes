<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
exit;
}

include ("../conn.php");
include ("../wfunction.php");
include ("../classes/LogClass.php");

$log = new Log();

$usuario = carregaUsuario();
$id = $_REQUEST['id'];
$id_regiao = $usuario['id_regiao'];

$reembolso = $_REQUEST['reembolso'];

$master = $usuario['id_master'];

$RE_ree = mysql_query("SELECT *,date_format(data, '%d/%m/%Y') as data FROM fr_reembolso WHERE id_reembolso = '$reembolso'");
$RowRee = mysql_fetch_array($RE_ree);

$codigo = sprintf("%05d",$RowRee['0']);


$dataCAD = date('Y-m-d');

switch($id){
    case 1:

        if($RowRee['funcionario'] == "1"){
            $result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$RowRee[id_user]'");
            $row_user = mysql_fetch_array($result_user);
            $NOME = $row_user['nome1'];  
        }else{
            $NOME = $RowRee['nome']; 
        }

        $obs = "Banco: ".$RowRee['banco']." AG: ".$RowRee['agencia']." CC: ".$RowRee['conta']." Favorecido: ".$RowRee['favorecido']." cpf: ".$RowRee['cpf'];

        $valor = number_format($RowRee['valor'],2,",","."); ?>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <table class="table table-condensed table-bordered text-sm">
            <tr>
                <td>Nome:</td>
                <td colspan="3"><?=$NOME?></td>
            </tr>
            <tr>
                <td>Valor:</td>
                <td><?=$valor?></td>
                <td>Data:</td>
                <td><?=$RowRee['data']?></td>
            </tr>
            <tr>
                <td>Descri&ccedil;&atilde;o</td>
                <td colspan="3"><?=$RowRee['descricao']?></td>
            </tr>
            <tr>
                <td colspan="4">Dados para o Dep&oacute;sito</td>
            </tr>
            <tr>
                <td>Banco:</td>
                <td colspan="3"><?=$RowRee['banco']?></td>
            </tr>
            <tr>
                <td>Agencia:</td>
                <td><?=$RowRee['agencia']?></td>
                <td>Conta:</td>
                <td><?=$RowRee['conta']?></td>
            </tr>
            <tr>
                <td>Favorecido:</td>
                <td><?=$RowRee['favorecido']?></td>
                <td>CPF:</td>
                <td><?=$RowRee['cpf']?></td>
            </tr>
            <tr>
                <td colspan="2" class="text-center">
                    <input class="btn btn-success btn-xs liberarReembolso" name="liberar" type="submit" value="Liberar" data-key="<?=$reembolso?>">
                    <input type="hidden" name="nomeE" value="<?=$NOME?>">
                    <input type="hidden" name="obs" value="<?=$obs?>">
                    <input type="hidden" name="id" id="id" value="2">
                    <input type="hidden" name="reembolso" id="reembolso" value="<?=$reembolso?>">
                    <input type="hidden" name="regiao" value="<?php echo $id_regiao?>"/>
                </td>
                <td colspan="2" class="text-center">
                    <input class="btn btn-danger btn-xs recusarReembolso" name="liberar2" type="submit" value="Recusar" data-key="<?=$reembolso?>">
                    <input type="hidden" name="id" id="id" value="3">
                    <input type="hidden" name="reembolso" id="reembolso" value="<?=$reembolso?>">
                </td>
            </tr>
        </table>
        <!--formulario de leiberacao de reembolso-->
        <form action="" method="post" class="formLiberarReembolso" name='form1' id="formReembolso" onSubmit="return validaForm()" style="display: none;">
            <table class="table table-bordered table-condensed text-center text-sm valign-middle">
                <tr class="bg-primary">
                    <td colspan="4">DIGITE OS DADOS RELATIVOS A SA&Iacute;DA</td>
                </tr>
                <tr>
                    <td width="110px;">Nome da Sa&iacute;da:</td>
                    <td colspan="3">
                        <input name="nome" type="text" id="nome" value="REEMBOLSO <?=$codigo." - ".$nomeE?>"  class="form-control"/>
                    </td>
                </tr>
                <tr>
                    <td>Especifica&ccedil;&atilde;o:</td>
                    <td colspan="3">
                        <input name="especifica" type="text" id="especifica" value="<?=$obs." Descricao: ".$RowRee['descricao']?>" class="form-control"/>
                    </td>
                </tr>
                <tr>
                    <td>Tipo:</td>
                    <td colspan="3">
                        <?php $result_tipo = mysql_query("SELECT * FROM entradaesaida WHERE grupo = 30 AND id_entradasaida IN(218, 219,221,222,223,226,227)"); ?>
                        <select name='tipo' class="form-control">
                            <?php while($row_tipo = mysql_fetch_array($result_tipo)){ ?>
                                <option value='<?=$row_tipo[0]?>' title='<?=$row_tipo[descricao]?>'><?="$row_tipo[0] - $row_tipo[nome]"?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Projeto:</td>
                    <td colspan="3">
                        <select name="projeto" id="projeto" class="form-control">
                            <option value="">Selecione o projeto...</option>            
                            <?php $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$usuario[id_regiao]'");
                            while($row_projeto = mysql_fetch_assoc($qr_projeto)){ ?>
                            <option value="<?=$row_projeto['id_projeto']?>"><?="$row_projeto[id_projeto] - $row_projeto[nome]"?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Banco:</td>
                    <td colspan="3">
                        <select name="banco" id="banco" class="form-control">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Custo Adicional:</td>
                    <td colspan="3">
                        <input name="adicional" type="text" id="adicional" class="valor form-control"/>
                    </td>
                </tr>
                <tr>
                    <td>Valor:</td>
                    <td>
                        <input name="valor" type="text" id="valor" value="<?=$valor?>" style="display:none"/> <?=$valor?>
                    </td>
                    <td>Data para Cr&eacute;dito:</td>
                    <td>
                        <input name="data_credito" type="text" id="data_credito" maxlength="10" class="data form-control">
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <input name='reembolso' type='hidden' id='reembolso' value='<?=$reembolso?>'>
                        <input name='action' type='hidden' value='cadastrar_reembolso_saida'>
                        <input name='regiao' type='hidden' id='regiao' value='<?php echo $id_regiao; ?>'>
                        <input type="button" name="Submit" id="gravarReembolsoSaida" class="btn btn-success" value="GRAVAR SA&Iacute;DA" />
                    </td>
                </tr>
            </table>
        </form>
        <script>
        $(function(){
            $(".valor").maskMoney({prefix:'', allowNegative: true, thousands:'.', decimal:','});
            
            $('#data_credito').datepicker();
            
            $(".recusarReembolso").on('click', function(){
                var idReembolso = $(this).data("key");
                $.post("../frota/ver_reembolso2.php", {bugger:Math.random(), id:3, reembolso:idReembolso}, function(resultado){
                    bootDialog(
                        resultado, 
                        "RECUSA DE Reembolso!", 
                        [{
                            label: 'Fechar',
                            action: function(){
                                $('.modal').modal('hide');
                                //$('.reembolso'+idReembolso).remove();
                                window.location.reload();
                            }
                        }],
                        'danger'
                    );
                });
            });
            
            $(".liberarReembolso").on('click', function(){
                $(this).parent().parent().parent().parent().toggle();
                $(".formLiberarReembolso").toggle();
            });
            
            $('#projeto').on("change",function(){
                var projeto = $(this).val();
                $.ajax({
                    url : '../frota/action.bancos.php?projeto='+projeto,
                    success: function(resposta){
                        $('#banco').html(resposta);
                    }
                });
            });
        });
        </script>
    <?php 
    break;
    case 3:
	$reembolso = $_REQUEST['reembolso'];
	$RE_ree = mysql_query("UPDATE fr_reembolso SET status = '0' WHERE id_reembolso = '$reembolso'");
        if($RE_ree){ 
            $log->gravaLog('Recusar Reembolso', 'Reembolso '.$reembolso.' recusado');
            echo "Reembolso recusado com sucesso!";
        } else {
            echo "Falha na recusa do reembolso!";
        } ?>
    <?php
    break;
} ?>