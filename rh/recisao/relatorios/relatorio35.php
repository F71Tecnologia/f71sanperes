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


$nome_projeto = mysql_result(mysql_query("SELECT nome FROM projeto WHERE id_projeto = $projeto"),0);



function format_cpf($cpf)   { 
   $cpf1 = substr($cpf,0,3);
   $cpf2 = substr($cpf,3,3);
   $cpf3 = substr($cpf,6,3);
   $cpf4 = substr($cpf,9,2);

   return $cpf1.'.'.$cpf2.'.'.$cpf3.'-'.$cpf4;
}


//ANO
$optAnos = array();
for ($i = 2009; $i <= date('Y'); $i++) {
    $optAnos[$i] = $i;
}
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');



$qr_mes = mysql_query("SELECT * FROM ano_meses");
while($row_mes = mysql_fetch_assoc($qr_mes)){    
    
      $opt_mov_cred[$row_mes['num_mes']] = $row_mes['nome_mes'];  
}


$mesSel = (isset($_REQUEST['mes']))? $_REQUEST['mes']: '';



if(isset($_POST['gerar'])){ 
    
 
    
$qr_folha = mysql_query("select GROUP_CONCAT(id_clt) as ids_clt from rh_folha as A
INNER JOIN rh_folha_proc as B
ON A.id_folha = B.id_folha
WHERE A.mes = '$_POST[mes]' AND A.ano = $_POST[ano] AND A.projeto = $projeto AND B.status = 3;");

$row_folha = mysql_fetch_assoc($qr_folha);


//SELECIONANDO OS DADOS DO RELATÓRIO
$qr = "SELECT '-' as matricula, '-' as id_clt,'CLT' AS vinculo,'OSS' AS empreg,A.nome,A.sexo,
      REPLACE(REPLACE(A.cpf,'.',''),'-','') as cpf,
    B.nome as cargo,
    IF(B.especialidade = 0, 'SEM ESPECIALIDADE', B.especialidade ) as especialidade,B.area,C.nome as nivel,

DATE_FORMAT(A.data_entrada, '%d/%m/%Y') as admissao,A.locacao,'INSTITUTO DATA RIO - IDR' as lotgeral, 
IF(B.hora_semana = 12, '0,5',
						IF(B.hora_semana = 24, '1',
							IF(B.hora_semana = 36, '1,5', '2')))
 as situacao,
 B.hora_semana
 FROM rh_clt AS A

LEFT JOIN curso AS B ON (A.id_curso=B.id_curso)

LEFT JOIN escolaridade AS C ON (A.escolaridade=C.id)

WHERE A.id_clt IN($row_folha[ids_clt]) ORDER BY A.nome";


$result = mysql_query($qr);
echo "<!-- \r\n $qr \r\n-->";
$total = mysql_num_rows($result);
$count = 0;

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Relatório de Força de Trabalho</title>
        <link href="../net1.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>

        <style>
            .grid thead tr th {font-size: 12px!important;}
            .bt-edit{cursor: pointer;}  
           

        </style>
        <style media="print">
            form{ visibility:  hidden;}
       
            
            
        </style>
        
    </head>

    <body class="novaintra">
        <div id="content" style="width: 90%;">
            <div id="head">
                <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                <div class="fleft">
                    <h2>Todos os Projetos</h2>
                    <h3>Relatório de Força de Trabalho</h3>
                    <p><strong><?php echo $nome_projeto;?></strong></p>
                </div>
                <div class="fright"> <?php include('../reportar_erro.php'); ?></div> 
            </div>
            <br class="clear">
            <br/>
            
            <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <legend>Dados</legend>
                    <div class="fleft">
                        <p><label class="first">Ano:</label> <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano')); ?></p>
                        <p><label class="first">Mês:</label>  <?php echo montaSelect($opt_mov_cred, $mesSel, array('name' => "mes", 'id' => 'mes')); ?></p>
                     
                    </div>
  
                    <br class="clear"/>                
                    <p class="controls" style="margin-top: 10px;">
                                <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
        </form>
        
            <div class="clear"></div>
            
            
            <?php if(isset($_POST['gerar'])) { ?>
            <br/>
            <table width="96%" align="center" cellpadding="0" cellspacing="0" border="0" class="grid">
                <thead>
                    <tr>
                        <th>Matrícula</th>
                        <th>ID Funcional</th>
                        <th>Vínculo</th>
                        <th>Vínculo Empregatício</th>
                        <th>Nome</th>
                        <th>Sexo</th>
                        <th>CPF</th>
                        <th>Cargo</th>
                        <th>Especialidade</th>
                        <th>Área</th>
                        <th>Nível</th>
                        <th>Data de Admissão</th>
                        <th>Lotação</th>
                        <th>Lotação Geral</th>
                        <th>Situação</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysql_fetch_assoc($result)) { 
                      
                    ?>
                        <tr class="<?php echo $count++ % 2 ? "even":"odd"?> ">
                            <td align="center"><?php echo $row['matricula'] ?></td>
                            <td align="center"><?php echo $row['id_clt'] ?></td>
                            <td align="center"><?php echo $row['vinculo'] ?></td>
                            <td align="center"><?php echo $row['empreg'] ?></td>
                            <td><?php echo $row['nome'] ?></td>
                            <td align="center"><?php echo $row['sexo'] ?></td>
                            <td width="130" align="center"><?php echo format_cpf($row['cpf']) ?></td>
                            <td align="center"><?php echo $row['cargo'] ?></td>
                            <td><?php echo $row['especialidade'] ?></td>
                            <td align="center"><?php echo $row['area'] ?></td>
                            <td><?php echo $row['nivel'] ?></td>
                            <td align="center"><?php echo $row['admissao'] ?></td>
                            <td><?php echo $row['locacao'] ?></td>
                            <td><?php echo $row['lotgeral'] ?></td>
                            <td align="center"><?php echo $row['situacao'] ?></td>
                           
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="14" class="txright"><strong>Total de funcionários:</strong></td>
                        <td><?php echo $total ?></td>
                    </tr>
                </tfoot>
            </table>
            
            <?php }?>

        </div>
    </body>
</html>