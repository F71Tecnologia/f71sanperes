<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}
include "../conn.php";
include "../wfunction.php";


//FAZENDO UM SELECT NA TABELA MASTAR PARA PEGAR AS INFORMAÇÕES DA EMPRESA
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$_SESSION[id_master]'");
$row_master = mysql_fetch_array($result_master);


$projeto = $_REQUEST['pro'];
$regiao = $_REQUEST['reg'];



////montando selects HTML
$QR_REGIAO = montaQuery('regioes', "id_regiao, regiao");
$optRegiao = array();
$optRegiao['todos'] = 'TODOS';
foreach ($QR_REGIAO as $valor) {  
 $optRegiao[$valor['id_regiao']] = $valor['regiao'];
}
$regiaoSel = (isset($_REQUEST['reg']))?$_REQUEST['reg'] :'';

$qr_projeto = montaQuery('projeto', 'id_projeto, nome', "id_regiao = '$regiao' ");   
 $optProjeto = array();
 foreach($qr_projeto as $valor){
     
     $optProjeto[$valor['id_projeto']]= $valor['id_projeto'].' - '.$valor['nome'];
 }
$projetoSel = (isset($_REQUEST['pro']))?$_REQUEST['pro'] :'';
/////////////////


$verifica_adm = mysql_result(mysql_query("SELECT sigla FROM regioes WHERE id_regiao = '$_SESSION[id_regiao]'"),0);

if($verifica_adm == 'AD'){
$sql = ($regiao == 'todos'  )? ' ORDER BY E.regiao,A.nome': "AND A.id_regiao = '$regiao' AND A.id_projeto = '$projeto'" ;    
}else {
 $sql = "AND A.id_regiao = '$regiao' AND A.id_projeto = '$projeto'" ;    
    
}


$qr_listagem = mysql_query("select A.id_clt, A.nome, A.cpf, IF(B.nome = '', 'Nenhum', B.nome) as nome_banco,
                            A.agencia, A.conta, C.salario, D.tipo_contratacao_nome, UPPER(A.tipo_conta) as tipo_conta,
                            E.regiao as nome_regiao
                            from rh_clt as A
                           LEFT JOIN bancos as B
                           ON A.banco = B.id_banco
                           LEFT JOIN curso as C
                           ON C.id_curso = A.id_curso
                           LEFT JOIN tipo_contratacao as D
                           ON D.tipo_contratacao_id = A.tipo_contratacao
                           LEFT JOIN regioes as E
                           ON E.id_regiao = A.id_regiao
                           WHERE A.`status` = 10 $sql;
                           ") or die(mysql_error());  
 $total_participantes = mysql_num_rows($qr_listagem);  




if(isset($_POST['busca_projeto'])){
    
 $regiao = $_POST['reg'];   
 $qr_projeto = montaQuery('projeto', 'id_projeto, nome', "id_regiao = '$regiao' ");   
 $optProjeto = array();
 foreach($qr_projeto as $valor){
     
     echo '<option value="'.$valor['id_projeto'].'">'.$valor['id_projeto'].' - '.htmlentities($valor['nome']).'</option>';
 }
  exit;  
}

?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net1.css" rel="stylesheet" type="text/css">
 <script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
 <script>
 $(function(){
     $('#reg').change(function(){
         
         var regiao = $(this).val();
         
         $.post( 'relatoriobanco2_1.php',{ reg: regiao, busca_projeto: 1},
         function(data){
             
         $('#pro').html(data);    
         } )
         
     })
     
     
 })
 </script>
</head>
 <body class="novaintra">
     
        <form action="" method="post" name="form1">
            <div id="content">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>RELATÓRIOS - Informações Bancárias dos Participantes</h2>                      
                    </div>
                    <div class="fright"> <?php include('../reportar_erro.php'); ?></div> 
                </div>
                <br class="clear"/>
                <br/>
                
                <?php if($verifica_adm == 'AD'){ ?>
                <fieldset>
                    <legend>Informações Bancárias dos Participantes</legend>
                    <div class="fleft">
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => 'reg', 'id' => 'reg')); ?></p>
                        <p><label class="first">Projeto:</label><?php echo montaSelect($optProjeto, $projetoSel, array('name' => 'pro', 'id' => 'pro')); ?></select></p>
                    </div>
                    <div class="fright" style="margin-right: 25px;">
                        <img src="imagens/status.jpg" >
                    </div>
                    <br class="clear"/>
                    <p class="controls" style="margin-top: 10px;"><input type="submit" name="filtrar" value="Filtrar" id="filtrar"/></p>
                </fieldset>  
                
                <?php
                } 
                    if(isset($_REQUEST['filtrar']) or $verifica_adm != 'AD'){  
                   
                     if($total_participantes != 0)  {
                     ?>  

                       <table width="100%" class="tabela_ramon" >
                       <tr  class="titulo">
                       <td>Cod</td>
                       <td>Nome</td>
                       <td>CPF</td>
                        <td>Banco</td>
                       <td>Tipo de Conta</td>
                       <td>Agência</td>  
                       <td>Conta</td>
                       <td>Salário</td>  
                       <td>Tipo Contratação</td>  
                       </tr>
                     <?php   
                     while($row = mysql_fetch_assoc($qr_listagem)){
                         
                       $class = ($i++ % 2 == 0)?'class="linha_um"': 'class="linha_dois"';  
                     
                       if($regiao == 'todos' and $row['nome_regiao'] != $regiaoAnt){
                           
                        $regiaoAnt = $row['nome_regiao'];   
                        echo '<tr><td colpspan="9">'.$row['nome_regiao'].'</td></tr>';
                       }
                      ?>   
                       <tr <?php echo $class; ?>> 
                           <td><?php echo $row['id_clt'];?></td>
                           <td><?php echo $row['nome'];?></td>
                           <td><?php echo $row['cpf'];?></td>
                           <td><?php echo $row['nome_banco'];?></td>
                           <td align="center"><?php echo $row['tipo_conta'];?></td>
                           <td><?php echo $row['agencia'];?></td>
                           <td><?php echo $row['conta'];?></td>
                           <td>R$ <?php echo number_format($row['salario'],2,',','.');?></td>
                           <td align="center"><?php echo $row['tipo_contratacao_nome'];?></td>                           
                       </tr>


            <?php   }
             } 
             
             } ?>
            </div>
        </body>
        </html>