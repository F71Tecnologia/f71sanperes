<?php
include('include/restricoes.php');
include('../funcoes.php');
include('include/criptografia.php');
include('../classes/formato_data.php');
include('../wfunction.php');

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);

//$regiao = mysql_real_escape_string($_GET['regiao']);
//$link_master = $_GET['master'];
$usuario = carregaUsuario();
$prestador = $_GET['prestador'];
$regiao = $usuario['id_regiao'];
$compra = $_GET['compra'];

$qr_prestador = mysql_query("SELECT *, DATE_FORMAT(data_nasc_socio1, '%d/%m/%Y') as data_nasc_socio1_f,
                            DATE_FORMAT(data_nasc_socio2, '%d/%m/%Y') as  data_nasc_socio2_f
                            FROM prestadorservico WHERE id_prestador = '$prestador'");
$row = mysql_fetch_assoc($qr_prestador);

$rs_medidas = montaQuery("prestador_medida","*",null,"medida ASC");

//EXCLUIR DEPENDENTES (AJAX)
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];

//mysql_query("UPDATE prestador_dependente SET prestador_dep_status = '0' WHERE prestador_dep_id = '$id' LIMIT 1") or die(mysql_error());
    unset($id);
}
//FIM EXCLUIR DEPENDENTES
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-5589-1" />
        <title>Dados do Prestador</title>
        <link href="../adm/css/estrutura.css" rel="stylesheet" type="text/css" />
        <script src="../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
            <script src="../jquery/priceFormat.js" type="text/javascript"></script>
        <style type="text/css">
            p{
                color: #000;
                text-transform: capitalize;
                font-weight:normal;
                text-align: left;
            }
        </style>
        <script type="text/javascript">
            $(function() {
            $('#valor').priceFormat({
                        prefix: '',
                        centsSeparator: ',',
                        thousandsSeparator: '.'
                    });
                    });
    </script>
    </head>
    <body>
        <div id="corpo">
            <div id="conteudo">
                <img src="../imagens/logomaster<?php echo $row_user['id_master']; ?>.gif" />
                <h3>VISUALIZAÇÃO DE DADOS</h3>
                <br/>
                    <table id="cadastro"  class="relacao" style="margin-top:40px;" >
                        <tr class="titulo_tabela1">
                            <td height="21" colspan="6" >DADOS DO PROJETO </td>
                        </tr>
                        <tr>
                            <td width="19%" height="30" class="secao">Projeto:</td>
                            <td width="51%" height="30" colspan="6" align="left">
                                <?php
                                $result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao'  AND (status_reg = '1' OR status_reg = '0')");
                                
                                while ($row_projeto = mysql_fetch_array($result_projeto)) {
                                    $selected = "";
                                    if ($row['id_projeto'] == $row_projeto['id_projeto'])
                                        $selected = "selected='selected'";
                                    print "<p value='$row_projeto[0]' $selected>" . htmlentities($row_projeto['nome']) . "</p>";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="19%" height="30" class="secao">Data Início:</td>
                            <td width="51%" height="30" align="left">
                                <p><?= date("d/m/Y", strtotime($row['contratado_em'])); ?></p>
                            </td>
                            <td>Data Término:</td>
                            <td colspan="3">
                                <p><?= date("d/m/Y", strtotime($row['encerrado_em'])); ?></p>
                            </td>
                        </tr>

                        <tr>
                            <td height="31" colspan="6"  class="titulo_tabela1">DADOS DO CONTRATANTE</td>
                        </tr>
                        <tr>
                            <td class="secao"> Contratante:</td>
                            <td align="left" colspan="5">
                                <p><?= $row['razao'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35" class="secao">Endere&ccedil;o:</td>
                            <td height="35" colspan="5" align="left">
                                <p><?= $row['endereco'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35" class="secao">CNPJ:</td>
                            <td height="35" colspan="5" align="left">
                                <p><?= $row['cnpj'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35"   class="secao">Responsavel:</td>
                            <td align="left">
                                <p><?= $row['responsavel'] ?></p>
                            </td>
                            <td class="secao">Estado civil:</td>
                            <td colspan="3" align="left"><p><?= $row['civil'] ?></p>  </td>
                        </tr>
                        <tr>
                            <td height="35"  class="secao">Nacionalidade:</td>
                            <td align="left">
                                <p><?= $row['nacionalidade'] ?></p>
                            </td>
                            <td>
                                Forma&ccedil;&atilde;o: 
                            </td>
                            <td colspan="3 " align="left">
                                <p><?= $row['formacao'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35"  class="secao">RG:</td>
                            <td align="left">  <p><?= $row['rg'] ?></p>  </td>
                            <td>CPF:</td>
                            <td colspan="3" align="left"> <p><?= $row['cpf'] ?> </p></td>
                        </tr>
                        <tr>
                            <td height="31" colspan="6" class="titulo_tabela1">DADOS DA EMPRESA CONTRATADA</td>
                        </tr>
                        <tr>  	

                            <td class="secao">Tipo: </td>
                            <td colspan="6" align="left" >
                                <?php 
                                                                switch ($row['prestador_tipo']) {
                                                                    case 1:echo "Pessoa Jurídica";break;
                                                                    case 2:echo "Pessoa Jurídica - Cooperativa";break;
                                                                    case 3:echo "Pessoa Física";break;
                                                                    case 4:echo "Pessoa Jurídica - Prestador de Serviço";break;
                                                                    case 5:echo "Pessoa Jurídica - Administradora";break;
                                                                    case 6:echo "Pessoa Jurídica - Publicidade";break;
                                                                    case 7:echo "Pessoa Jurídica Sem Retenção";break;
                                                                    default:break;
                                                                }
                                
                                ?>

                            </td>

                        </tr>

                        <tr id="dependente" >  
                            <td valign="top">

                                <span class="secao" style="display:block; text-align:center;"> <strong>Dados do(s) Dependente(s): </strong> </span>   
                            </td>	                       
                            <td  colspan="6" id="tabela_dependente">        
                                <div id="tabela_dependente" style="background-color: #DEF;padding-top:10px;" >
                                    <?php
                                    $qr_dependente = mysql_query("SELECT * FROM prestador_dependente WHERE prestador_id = '$row[id_prestador]' AND prestador_dep_status = '1'") or die(mysql_error());
                                    $verifica = mysql_num_rows($qr_dependente);

                                    if ($verifica != 0) {

                                        while ($row_dependente = mysql_fetch_assoc($qr_dependente)):
                                            ?>

                                            <table style="background-color:  #EFEFEF;width:100%;" class="relacao"  >
                                                <tr height='35'>
                                                    <td  class="secao">Nome:</td>
                                                    <td align="left"><p> <?php echo $row_dependente['prestador_dep_nome']; ?></p></td>
                                                </tr>

                                                <tr height='35'>
                                                    <td class="secao">Grau de Parentesco: </td>
                                                    <td align="left"><p><?php echo $row_dependente['prestador_dep_parentesco']; ?> </p></td>
                                                </tr>

                                                <tr height='35'>
                                                    <td class="secao">Data de Nascimento:</td>
                                                    <td align="left"><p> <?php echo implode('/', array_reverse(explode('-', $row_dependente['prestador_dep_data_nasc']))); ?></p> </td>
                                                </tr> 

                                                <tr height='35'><td colspan='2'><a href="#"  onclick="$(this).parent().parent().parent().remove();
                            return false;" >Excluir</a></td></tr>

                                            </table>


                                            <?php
                                        endwhile;
                                    } else {
                                        echo 'Não há dependentes';
                                         } ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td  class="secao">Existe Contrato:</td>
                            <td colspan="5" align="left">
                                <p>
                                    <?php 
                                    if($row['prestacao_contas'] == 1){
                                        echo 'Sim';
                                        } else {
                                            echo 'Não';
                                        } ?> 
                                </p><br/>
                                
                            </td>
                        </tr>
                        <tr>
                            <td height="35" class="secao">Nome Fantasia:</td>
                            <td colspan="5" align="left">
                                <p><?php echo $row['c_fantasia'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35" class="secao">Raz&atilde;o Social:</td>
                            <td colspan="5" align="left">
                                <p><?php echo $row['c_razao'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35" class="secao">Endere&ccedil;o:</td>
                            <td colspan="5" align="left">
                                <p><?php echo $row['c_endereco'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td class="secao">CNPJ:</td>  
                            <td align="left">
                                <p><?php echo $row['c_cnpj'] ?></p>
                            </td>
                            <td class="secao">IE:</td>
                            <td align="left"><p><?php echo $row['c_ie'] ?></p></td>
                            <td class="secao">CCM:</td>
                            <td align="left"><p><?php echo $row['c_im'] ?></p></td>
                        </tr>
                        <tr>
                            <td class="secao">Telefone:</td>
                            <td  align="left">
                                <p><?php echo $row['c_tel'] ?></p> 
                            </td>
                            <td class="secao">Fax:</td>
                            <td align="left">
                                <p><?php echo $row['c_fax'] ?></p>
                            </td>
                            <td class="secao"> E-mail: </td>
                            <td align="left">
                                <p><?php echo $row['c_email'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35" class="secao">Responsavel:</td>
                            <td align="left">
                                <p><?php echo $row['c_responsavel'] ?></p> 
                            </td>
                            <td class="secao">Estado civil:</td>
                            <td colspan="3" align="left">
                                <p><?php echo $row['c_civil'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35" class="secao">Nacionalidade:</td>
                            <td align="left">
                                <p><?php echo $row['c_nacionalidade'] ?></p>
                            </td>
                            <td class="secao">Forma&ccedil;&atilde;o: </td>
                            <td  colspan="3" align="left">
                                <p><?php echo $row['c_formacao'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35" class="secao">RG:</td>
                            <td align="left">
                                <p><?php echo $row['c_rg'] ?></p>
                            </td>
                            <td class="secao">CPF:</td>
                            <td  colspan="3" align="left">
                                <p><?php echo $row['c_cpf'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td class="secao">E-mail: </td>
                            <td align="left"> 
                                <p><?php echo $row['c_email'] ?></p>
                            </td>
                            <td class="secao">Site: </td>
                            <td  colspan="4" align="left">
                                <p><?php echo $row['c_site'] ?></p>
                            </td>
                        </tr>
                        <tr>	
                            <td class="secao">Valor limite:</td>
                            <td colspan="5" align="left">
                                <p><?php echo number_format($row['valor_limite'], 2, ',', '.') ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="25" class="titulo_tabela1"  colspan="6">DADOS DA PESSOA DE  CONTATO NA CONTRATADA</td>
                        </tr>
                        <tr>
                            <td height="35" class="secao">Nome Completo:</td>
                            <td  colspan="5">
                                <p><?php echo $row['co_responsavel'] ?></p> 
                            </td>
                        </tr>
                        <tr>
                            <td class="secao">Telefone:</td>
                            <td align="left">
                                <p><?php echo $row['co_tel'] ?></p>
                            </td>
                            <td class="secao"> Fax:</td>
                            <td  colspan="3" align="left">
                                <p><?php echo $row['co_fax'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35" class="secao"> Email: </td>
                            <td  colspan="5">
                                <p><?php echo $row['co_email'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35"  class="secao">Estado civil:</td>
                            <td align="left">
                                <p><?php echo $row['co_civil'] ?></p>
                            </td>
                            <td class="secao"> Nacionalidade:</td>
                            <td  colspan="4" align="left">
                                <p><?php echo $row['co_nacionalidade'] ?></p> 
                            </td>
                        </tr>
                        <tr>
                            <td height="35"  class="secao"> Data de Nascimento:</td>
                            <td	colspan="5" align="left">
                                <p><?php echo implode('/', array_reverse(explode('-', $row['co_data_nasc']))) ?></p> 
                            </td>
                        </tr>
                        <tr>
                            <td height="25" colspan="6" bgcolor="#C9C9C9">Sócio 1</td>
                        </tr>
                        <tr>
                            <td height="35"  class="secao">Nome Completo:</td>
                            <td align="left">
                                <p><?php echo $row['co_responsavel_socio1'] ?></p>
                            </td>
                            <td  class="secao">Telefone:</td>
                            <td align="left">
                                <p><?php echo $row['co_tel_socio1'] ?></p>
                            </td>
                            <td  class="secao">Fax:</td>
                            <td align="left"> 
                                <p><?php echo $row['co_fax_socio1']; ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35"  class="secao">Email: </td>
                            <td colspan="5" align="left">
                                <p><?php echo $row['co_email_socio1'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35"  class="secao">Estado civil:</td>
                            <td  align="left">
                                <p><?php echo $row['co_civil_socio1'] ?></p>
                            </td>
                            <td  class="secao"> Nacionalidade:</td>
                            <td colspan="3" align="left"> 
                                <p><?php echo $row['co_nacionalidade_socio1'] ?></p> 
                            </td>
                        </tr>
                        <tr>
                            <td height="35"  class="secao"> Data de Nascimento:</td>
                            <td colspan="5" align="left">
                                <p><?php echo $row['data_nasc_socio1_f']; ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35"  class="secao">Município: </td>
                            <td colspan="5" align="left">
                                <p><?php echo $row['co_municipio_socio1'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="25" colspan="6" bgcolor="#C9C9C9">Sócio 2</td>
                        </tr>
                        <tr>
                            <td height="35"  class="secao">Nome Completo:</td>
                            <td align="left">
                                <p><?php echo $row['co_responsavel_socio2'] ?></p>
                            </td>
                            <td  class="secao">Telefone:</td>
                            <td   align="left">
                                <p><?php echo $row['co_tel_socio2'] ?></p>
                            </td>
                            <td class="secao">Fax:</td>
                            <td align="left">
                                <p><?php echo $row['co_fax_socio2'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35" class="secao" >Estado civil:</td>
                            <td align="left">
                                <p><?php echo $row['co_civil_socio2'] ?></p>
                            </td>
                            <td class="secao">Nacionalidade:</td>
                            <td colspan="5" align="left">
                                <p><?php echo $row['co_nacionalidade_socio2'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35" class="secao" > Data de Nascimento:</td>
                            <td colspan="5" align="left">
                                <p><?php echo $row['data_nasc_socio2_f'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35"  class="secao">Email: </td>
                            <td colspan="5" align="left">
                                <p><?php echo $row['co_email_socio2'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35" class="secao" >Município:</td>
                            <td colspan="5" align="left"> 
                                <p><?php echo $row['co_municipio_socio2'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="29" colspan="6"  class="titulo_tabela1">DADOS BANCÁRIOS</td>
                        </tr>

                        <tr>
                            <td height="44"  class="secao">Nome do banco:</td>
                            <td colspan="5" align="left">
                                <p><?php echo $row['nome_banco'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="35"  class="secao">Agência:</td>
                            <td align="left">
                                <p><?php echo $row['agencia'] ?></p>
                            </td>
                            <td  class="secao">Conta:</td>

                            <td colspan="3" align="left">
                                <p><?php echo $row['conta'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="29" colspan="6" class="titulo_tabela1">OBJETO DO CONTRATO</td>
                        </tr>
                        <tr>
                            <td height="44" class="secao">Munic&iacute;pio onde ser&aacute; executado o servi&ccedil;o:</td>
                            <td align="left">
                                <p><?php echo $row['co_municipio'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="44"  class="secao">Assunto:</td>
                            <td align="left">
                                <p><?php echo $row['assunto'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td  class="secao">Data do Processo:</td>
                            <td align="left">
                                <p><?php echo formato_brasileiro($row['data']) ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td  class="secao">Descrição</td>
                            <td>
                              <p><?php echo $row['objeto'] ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td height="27" colspan="6" class="titulo_tabela1">ESPECIFICA&Ccedil;&Atilde;O DO TIPO DE SERVI&Ccedil;O A SER PRESTADO</td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                <p><?php echo $row['especificacao'] ?></p>
                            </td>
                        </tr>
                        <tr style="display:">
                            <td height="46"  >ANEXO I &ndash;  VALOR R$</td>
                            <td>
                                <p id="valor" onkeydown="FormataValor(this, event, 20, 2)" ><?php echo $row['valor'] ?></p>
                            </td>
                            <td  class="secao">DATA:</td>
                            <td colspan="4" align="left">
                                <p><?php echo formato_brasileiro($row['data']) ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td>Unidade de Medida:</td>
                            <td>
                                <?php $qr_medida = "select * from prestador_medida where id_medida ={$row['id_medida']}";
                                      $rsmedida = mysql_query($qr_medida);
                                      $medida = mysql_fetch_array($rsmedida);
                                      echo $medida['medida'];
                                ?>
                            </td>
                            <td></td>
                            <td colspan="4" align="left">
                            </td>
                        </tr>
                        <tr>
                            <td height="46" colspan="6" align="center" valign="middle" >

                                <input type="hidden" name="regiao" value="<?= $regiao ?>">
                                    <input type="hidden" name="prestador_id" value="<?php echo $row['id_prestador'] ?>"/>
                                    <input type="hidden" name="compra" value="<?php echo $compra ?>"/>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </body>
</html>