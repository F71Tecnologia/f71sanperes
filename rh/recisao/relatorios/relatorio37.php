<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}

require("../conn.php");
require("../wfunction.php");


$usuario = carregaUsuario();

$projeto = $_REQUEST['pro'];
$regiao  = $_REQUEST['reg'];


function preenche_zero($num, $tamanho, $zero = 0){
    
    return sprintf("%".$zero.$tamanho."s",$num);
    
}


$nome_projeto = mysql_result(mysql_query("SELECT nome FROM projeto WHERE id_projeto = $projeto"),0);


$n_linhas_pg = 50;

if(isset($_GET['pg'])){
    $pg_atual = $_GET['pg'];
    $inicio  = ($n_linhas_pg * $pg_atual) - $n_linhas_pg ;
  
    
}else {
     $pg_atual = 1;
     $inicio  = 0;    
}





$qr_clts = mysql_query("SELECT A.matricula, 
REPLACE(REPLACE(A.cpf,'.',''),'-','') as cpf,
'0000',
SUBSTR(A.cpf,-2) as digito_cpf,
IF((DATEDIFF(NOW(),A.data_nasci)/365) <18, 2,1) as capacidade_civil,
IF((DATEDIFF(NOW(),A.data_nasci)/365) <18, 5,1) as tipo_movimento,
A.nome, A.endereco,A.numero,A.complemento, A.bairro,
SUBSTR(A.cep,1,5)as cep,
SUBSTR(A.cep,-3) as complemento_cep,
SUBSTR(A.tel_fixo,2,2) AS ddd,
REPLACE(SUBSTR(A.tel_fixo,5,8),'-','') AS telefone,
'0000',
'0000',
DATE_FORMAT(A.data_nasci,'%d/%m/%Y') as data_nasci,
A.naturalidade,
A.uf_nasc,
IF(A.sexo = 'M',1,IF(A.sexo = 'F',2,''))as sexo,
SUBSTR(A.pai,1,40) as pai,
SUBSTR(A.mae,1,40) as mae,

If(A.nacionalidade like '%BRASIL%',1,2) as tipo_nacionalidade,
If(A.nacionalidade like '%BRASIL%','BRASILEIRA', A.nacionalidade) as nacionalidade,
IF(A.civil = 'Solteiro', 1, IF(A.civil = 'Casado', 2, 3)) as civil,
'RG' as tipo_doc,
REPLACE(REPLACE(A.rg,'.',''),'-','')as rg,
DATE_FORMAT(A.data_rg,'%d/%m/%Y') as data_rg,
A.orgao,
B.nome as nome_curso, 
REPLACE(B.salario,'.','') as salario,
A.nome_conjuge
FROM rh_clt as A 
left JOIN curso as B
ON A.id_curso = B.id_curso
WHERE A.id_regiao = $regiao AND A.id_projeto = $projeto AND A.status = 10
ORDER BY A.id_clt ASC
LIMIT $inicio, $n_linhas_pg
");


$qr_empresa = mysql_query("SELECT razao, endereco,
                           SUBSTR(cep,1,5)as cep,
                            SUBSTR(cep,-3) as complemento_cep
                            FROM rhempresa WHERE id_projeto = $projeto AND id_regiao = $regiao");
$row_empresa = mysql_fetch_assoc($qr_empresa);




$total_registros = mysql_num_rows(mysql_query("SELECT * FROM rh_clt WHERE id_regiao = $regiao AND id_projeto = $projeto AND status = 10"));
$total_paginas   = ceil($total_registros/$n_linhas_pg);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Relatório de Abertura de Conta</title>
        <link href="../net1.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>

        <style>
            .grid thead tr th {font-size: 12px!important;}
            .bt-edit{cursor: pointer;}  
            .tamanho{ width: 100%;}
            .pag{ text-align: center;}
            .pag a{ text-decoration: none;
                    font-weight: bold;
                    padding: 5px;
                    font-size: 16px;
                    border: 1px solid #FFF;
                    color: #0099FF;
            }
            .pag a:hover{
                border: 1px solid  #f1f0f0;
                background-color:   #f0ebeb;
            }
            .atual{ font-weight: bold;
                    padding: 5px;
                    font-size: 16px;
                     border: 1px solid #FFF;
                     color: #000;
            }
           

        </style>
        <style media="print">
            form{ visibility:  hidden;}
            
        </style>
        
    </head>

    <body class="novaintra" style="background-color:#FFF; width: 100%">
        <div id="content" style="width: 90%;" >
            <div id="head">
                <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                <div class="fleft">
                
                    <h3>Relatório de Abertura de Conta</h3>
                    <p><strong><?php echo $nome_projeto;?></strong></p>
                </div>
                <div class="fright"> <?php include('../reportar_erro.php'); ?></div>
                
                
                
            </div>
            <br class="clear">
          
         
                
                    <div class="pag">
                        <p><h4>PÁGINAS</h4> 
                            <?php
                            for($i=1; $i<=$total_paginas;$i++){
                                
                               if($i != $pg_atual){
                                    ?>      
                                    <a href="relatorio37.php?reg=<?php echo $regiao;?>&pro=<?php echo $projeto; ?>&pg=<?php echo $i;?>" <?php echo $class;?>>
                                        <?php echo $i;?>
                                    </a>
                            <?php        } else {    
                                
                                echo '<span class="atual">'.$i.'</span>';
                            }
                            
                            }?>
                        </p>
                       
                    </div>
  
                    <br class="clear"/>                
      
           
        
            <div class="clear"></div>          
            
         
            <table  align="center" cellpadding="0" cellspacing="0" border="0" class="grid" >
              
                    <tr class="titulo">
                        <td width="100">Tipo de Registro</td>
                        <td>Matrícula</td>
                        <td>CPF</td>
                        <td>FILIAL</td>                     
                        <td>CONTROLE</td>                     
                        <td>CAPACIDADE CIVIL</td>                     
                        <td>TIPO DE MOVIMENTO</td>                     
                        <td>NOME DO FUNCIONÁRIO</td>                     
                        <td>ENDEREÇO RESIDENCIAL</td>                     
                        <td>NÚMERO</td>                     
                        <td>COMPLEMENTO</td>                     
                        <td>BAIRRO</td>                     
                        <td>CEP</td>                     
                        <td>SUFIXO CEP</td> 
                        <td>DDD</td>
                        <td>TELEFONE</td>
                        <td>DDDD FAX</td>
                        <td>FAX</td> 
                        <td>ENDEREÇO P/ CORRESPONDÊNCIA</td>
                        <td>Nº DO ENDEREÇO</td>
                        <td>BAIRRO DO ENDEREÇO</td>
                        <td>COMPLEMENTO</td>
                        <td>CEP</td>
                        <td>SUFIXO CEP</td>
                        <td>CÓDIGO DE OCUPAÇÃO</td>
                        <td>DATA DE NASCIMENTO</td>
                        <td>NATURALIDADE</td>
                        <td>UF DE NASCIMENTO</td>
                        <td>SEXO</td>
                        <td>NOME DO PAI</td>
                        <td>NOME DA MÃE</td>
                        <td>BRASILEIRO/ESTRANGEIRO</td>
                        <td>NACIONALIDADE</td>
                        <td>ESTADO CIVIL</td>
                        <td>TIPO DE DOCUMENTO</td>
                        <td>NÚMERO DO DOCUMENTO</td>
                        <td>DATA DE EMISSÃO</td>
                        <td>ORGÃO EMISSOR</td>
                        <td>NOME DA EMPRESA</td>
                        <td>CARGO</td>
                        <td>RENDA</td>
                        <td>TEMPO DE SERVIÇO</td>
                        <td>ENDEREÇO DA EMPRESA</td>
                        <td>NUMERO CEP</td>
                        <td>SUFIXO CEP</td>
                        <td>NOME DO CONJUGE</td>
                        <td>DESTINO DO BANCO</td>
                        <td>DESTINO DA AGÊNCIA</td>
                        <td>DÍGITO</td>
                        <td>DESTINO DO RAZÃO</td>
                        <td>DESTINO CONTA</td>
                        <td>DESTINO CONTA</td>
                        <td>DESTINO DÍGITO</td>
                        <td>DESTINO TIPO DA CONTA</td>
                      
                    </tr>
              
                <tbody>
                    <?php while ($row = mysql_fetch_assoc($qr_clts)) { 
                      
                    ?>
                        <tr class="<?php echo $count++ % 2 ? "even":"odd"?> ">
                            <td align="center" >1</td>
                            <td align="center"><?php echo preenche_zero($row['matricula'],8); ?></td>
                            <td align="center"><?php echo $row['cpf']?></td>
                            <td align="center">0000</td>
                            <td align="center"><?php echo $row['digito_cpf']?></td>
                            <td align="center"><?php echo $row['capacidade_civil']?></td>
                            <td  align="center"><?php echo $row['tipo_movimento']?></td>
                            <td  align="left"><?php echo $row['nome']?></td>
                            <td align="left"><?php echo $row['endereco']?></td>
                            <td align="center"><?php echo $row['numero']?></td>
                            <td align="center"><?php echo $row['complemento']?></td>
                            <td align="center"><?php echo $row['bairro']?></td>
                            <td align="center"><?php echo $row['cep']?></td>
                            <td align="center"><?php echo $row['complemento_cep']?></td>
                            <td align="center"><?php echo $row['ddd']?></td>
                            <td align="center"><?php echo $row['telefone']?></td>
                            <td align="center"></td>
                            <td align="center"></td>
                            <td align="center"><?php echo $row['endereco']?></td>
                            <td align="center"><?php echo $row['numero']?></td>                          
                            <td align="center"><?php echo $row['bairro']?></td>
                            <td align="center"><?php echo $row['complemento']?></td>
                            <td align="center"><?php echo $row['cep']?></td>
                            <td align="center"><?php echo $row['complemento_cep']?></td>
                            <td align="center">119</td>
                            <td align="center"><?php echo $row['data_nasci']?></td>
                            <td align="center"><?php echo $row['naturalidade']?></td>
                            <td align="center"><?php echo $row['uf_nasc']?></td>
                            <td align="center"><?php echo $row['sexo']?></td>
                            <td align="center"><?php echo $row['pai']?></td>
                            <td align="center"><?php echo $row['mae']?></td>
                            <td align="center"><?php echo $row['tipo_nacionalidade']?></td>
                            <td align="center"><?php echo $row['nacionalidade']?></td>
                            <td align="center"><?php echo $row['civil']?></td>
                            
                            <td align="center"><?php echo $row['tipo_doc']?></td>
                            <td align="center"><?php echo $row['rg']?></td>
                            <td align="center"><?php echo $row['data_rg']?></td>
                            <td align="center"><?php echo $row['orgao']?></td>
                            <td align="center"><?php echo $row_empresa['razao'];?></td>
                            <td align="center"><?php echo $row['nome_curso'];?></td>
                            <td align="center"><?php echo $row['salario'];?></td>
                            <td align="center"><?php echo $row['tempo_servico'];?></td>
                            <td align="center"><?php echo $row_empresa['endereco'];?></td>
                            <td align="center"><?php echo $row_empresa['cep'];?></td>
                            <td align="center"><?php echo $row_empresa['complemento_cep'];?></td>
                            <td align="center"><?php echo $row['nome_conjuge'];?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                           
                        </tr>
                    <?php } ?>
                </tbody>
               
            </table>
            
          

        </div>
    </body>
</html>