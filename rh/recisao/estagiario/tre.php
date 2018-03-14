<?php
include('../../../conn.php');
include('../../../funcoes.php');

function printHelper($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}

function getClt($id_clt) {
    $sql = "SELECT D.*,A.pis, A.nome AS nome_funcionario, CONCAT(A.endereco,A.numero,A.complemento) AS endereco_funcionario, A.bairro AS bairro_funcionario, 
            A.cidade AS cidade_funcionario, A.uf AS uf_funcionario, A.cep AS cep_funcionario, A.campo1 AS numero_ctps, 
            A.serie_ctps, A.uf_ctps, A.cpf, DATE_FORMAT(A.data_nasci, '%d/%m/%Y') AS data_nascimento, A.mae, 
            C.logradouro AS logradouro_empresa, C.complemento AS complemento_empresa, C.bairro AS bairro_empresa,
            C.cidade AS cidade_empresa, C.uf AS uf_empresa, C.numero AS numero_empresa, C.cnpj, C.razao, C.endereco AS endereco_empresa, 
            C.cep AS cep_empresa, C.cnae AS cnae_empresa, DATE_FORMAT(D.data_fim,'%d/%m/%Y') AS data_fim_br,
            DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS data_entrada 
            FROM estagiario AS A 
            LEFT JOIN rhempresa AS C ON(A.id_projeto= C.id_projeto) 
            LEFT JOIN rh_rescisao_estagiario AS D ON A.id_estagiario = D.id_estagiario
            LEFT JOIN rh_rescisao_estagiario_motivo AS E ON D.id_motivo = E.id_motivo
            WHERE A.id_estagiario=$id_clt";
    $result = mysql_query($sql);
    $row_estagiario = mysql_fetch_array($result);
    return $row_estagiario;
}

$row_estagiario = getClt($_REQUEST['id_estagiario']);

$id_rescisao = isset($_REQUEST['id_rescisao']) ? $_REQUEST['id_rescisao'] : NULL;
$id_clt = isset($_REQUEST['id_clt']) ? $_REQUEST['id_clt'] : NULL;

$cnpj_empresa = $row_estagiario['cnpj'];
$razao_empresa = $row_estagiario['razao'];
$cep_empresa = $row_estagiario['cep'];
$cnae = $row_estagiario['cnae_empresa'];
$endereco_empresa = $row_estagiario['logradouro_empresa'];
$cnpj = $row_estagiario['cnpj'];
$municipio_empresa = $row_estagiario['cidade_empresa'];
$uf_empresa = $row_estagiario['uf_empresa'];
$bairro_empresa = $row_estagiario['bairro_empresa'];
$pis = $row_estagiario['pis'];


?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Rescis&atilde;o de Estágio de <?php echo $row_estagiario['id_estagiario'] . ' - ' . $row_estagiario['nome_funcionario']; ?></title>
        <link href="../rescisao_1.css" rel="stylesheet" type="text/css" />

        <link href="../../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />

        <script src="../../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../../js/global.js" type="text/javascript"></script>
        <script src="../../../js/jquery.price_format.2.0.min.js" type="text/javascript"></script>
        <script src="../../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
        <style type="text/css" media="print">
            table.rescisao td.secao {
                background-color:#C0C0C0;
                text-align:center;
                font-size:14px;
                height:20px;
            }
        </style>
        <style type="text/css">
            .font13{
                font-size: 12px;
                font-style: normal;
                font-weight: 100;
            }
        </style>
    </head>
    <body>        

        <br>
        <br>
        <table class="rescisao" cellpadding="0" cellspacing="1" style="background: #FFF;">
            <tr>
                <td colspan="6" class="secao"><h1>TERMO DE RESCISÃO DE ESTÁGIO</h1></td>
            </tr>
            <tr>
                <td colspan="6" class="secao">IDENTIFICA&Ccedil;&Atilde;O DA EMPRESA</td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="campo"><span class="numero">01</span> CNPJ/CEI</div>
                    <div class="valor"><?php echo $row_estagiario['cnpj']; ?></div>
                </td>
                <td colspan="4">
                    <div class="campo"><span class="numero">02</span> Raz&atilde;o Social/Nome</div>
                    <div class="valor"><?php echo $row_estagiario['razao']; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <div class="campo"><span class="numero">03</span> Endere&ccedil;o (logradouro, n&ordm;, andar, apartamento)</div>
                    <div class="valor"><?php echo $row_estagiario['logradouro_empresa']; ?></div>
                </td>
                <td colspan="2">
                    <div class="campo"><span class="numero">04</span> Bairro</div>
                    <div class="valor"><?php echo $row_estagiario['bairro_empresa']; ?></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="campo"><span class="numero">05</span> Munic&iacute;pio</div>
                    <div class="valor"><?php echo $row_estagiario['cidade_empresa']; ?></div>
                </td>
                <td>
                    <div class="campo"><span class="numero">06</span> UF</div>
                    <div class="valor"><?php echo $row_estagiario['uf_empresa']; ?></div>
                </td>
                <td>
                    <div class="campo"><span class="numero">07</span> CEP</div>
                    <div class="valor"><?php echo $row_estagiario['cep_empresa']; ?></div>
                </td>
                <td>
                    <div class="campo"><span class="numero">08</span> CNAE</div>
                    <div class="valor"><?php echo $row_estagiario['cnae_empresa']; ?></div>
                </td>
                <td colspan="2">
                    <div class="campo"><span class="numero">09</span> CNPJ/CEI Tomador/Obra</div>
                    <div class="valor"><?php echo $row_estagiario['cnpj']; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="6" class="secao">IDENTIFICA&Ccedil;&Atilde;O DO ESTAGIÁRIO</td>
            </tr>
            <tr>
                <td colspan="6">
                    <div class="campo"><span class="numero">10</span> Nome</div>
                    <div class="valor"><?php echo $row_estagiario['nome_funcionario']; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <div class="campo"><span class="numero">11</span> Endere&ccedil;o (logradouro, n&ordm;, andar, apartamento)</div>
                    <div class="valor"><?php echo $row_estagiario['endereco_funcionario']; ?></div>
                </td>
                <td colspan="2">
                    <div class="campo"><span class="numero">12</span> Bairro</div>
                    <div class="valor"><?php echo $row_estagiario['bairro_funcionario']; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="campo"><span class="numero">13</span> Munic&iacute;pio</div>
                    <div class="valor"><?php echo $row_estagiario['cidade_funcionario']; ?></div>
                </td>
                <td colspan="2">
                    <div class="campo"><span class="numero">14</span> UF</div>
                    <div class="valor"><?php echo $row_estagiario['uf_funcionario']; ?></div>
                </td>
                <td colspan="2">
                    <div class="campo"><span class="numero">15</span> CEP</div>
                    <div class="valor"><?php echo $row_estagiario['cep_funcionario']; ?></div>
                </td>

            </tr>
            <tr>
                <td colspan="2">
                    <div class="campo"><span class="numero">16</span> CPF</div>
                    <div class="valor"><?php echo $row_estagiario['cpf']; ?></div>
                </td>
                <td>
                    <div class="campo"><span class="numero">17</span> Data de nascimento</div>
                    <div class="valor"><?php echo $row_estagiario['data_nascimento']; ?></div>
                </td>
                <td colspan="3">
                    <div class="campo"><span class="numero">18</span> Nome da m&atilde;e</div>
                    <div class="valor"><?php echo $row_estagiario['mae']; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="6" class="secao">DADOS DO CONTRATO</td>
            </tr>
            <tr>
                <td colspan="4">
                    <div class="campo"><span class="numero">19</span> Causa do Afastamento</div>
                    <div class="valor"><?php echo $row_estagiario['descricao_motivo'].' - '.$row_estagiario['obs_motivo']; ?></div>
                </td>
                <td>
                    <div class="campo"><span class="numero">20</span> Data de admiss&atilde;o</div>
                    <div class="valor"><?php echo $row_estagiario['data_entrada']; ?></div>
                </td>
                <td>
                    <div class="campo"><span class="numero">21</span> Data de afastamento</div>
                    <div class="valor"><?php echo $row_estagiario['data_fim_br']; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="6" class="secao">DISCRIMINA&Ccedil;&Atilde;O DAS VERBAS RESCIS&Oacute;RIAS</td>
            </tr>
            <tr>
                <td colspan="6" class="secao">VERBAS RESCIS&Oacute;RIAS</td>
            </tr>
            <tr id="titulo_campos_1">
                <td width="17%" class="secao_filho">Rubrica</td>
                <td width="16%" class="secao_filho">Valor</td>
                <td width="17%" class="secao_filho">Rubrica</td>
                <td width="16%" class="secao_filho">Valor</td>
                <td width="17%" class="secao_filho">Rubrica</td>
                <td width="16%" class="secao_filho">Valor</td>
            </tr>
            <tr>
                <td class="font13"><span class="numero">22</span>&nbsp;Saldo de Salário</td>
                <td class="font13">R$ <?= number_format($row_estagiario['valor_bolsa'],2,',','.') ?></td>
                <td class="font13"><span class="numero">23</span>&nbsp;Saldo do Recesso</td>
                <td class="font13">R$ <?= number_format($row_estagiario['valor_recesso'],2,',','.') ?></td>
                <td class="font13"></td>
                <td class="font13"></td>
            </tr>
            <tr>
                <td class="font13 secao" colspan="5">TOTAL VERBAS RESCISÓRIAS</td>
                <td class="font13 secao">R$ <?= number_format($row_estagiario['total_liquido'],2,',','.') ?></td>
            </tr>

            <tr>
                <td colspan="6" class="secao">DEDU&Ccedil;&Otilde;ES</td>
            </tr>

            <tr id="titulo_campos_2">
                <td class="secao_filho">Desconto</td>
                <td class="secao_filho">Valor</td>
                <td class="secao_filho">Desconto</td>
                <td class="secao_filho">Valor</td>
                <td class="secao_filho">Desconto</td>
                <td class="secao_filho">Valor</td>
            </tr>
            <tr>
                <td class="font13"></td>
                <td class="font13"></td>
                <td class="font13"></td>
                <td class="font13"></td>
                <td class="font13"></td>
                <td class="font13"></td>
            </tr>
            
            <tr>
                <td class="font13 secao" colspan="5">TOTAL DESCONTOS</td>
                <td class="font13 secao">R$ 0,00</td>
            </tr>
            <tr>
                <td class="font13 secao" colspan="5">VALOR RESCISÓRIO LÍQUIDO</td>
                <td class="font13 secao">R$ <?= number_format($row_estagiario['total_liquido'],2,',','.') ?></td>
            </tr>
            
        </table>
    </body>
</html>