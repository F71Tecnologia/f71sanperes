<?php 
if(empty($_COOKIE['logado'])){
print "<script>location.href = '../../login.php?entre=true';</script>";
} else {
include "../../../conn.php";
include "../../../classes/funcionario.php";
include '../../../classes_permissoes/regioes.class.php';

$Fun = new funcionario();
$Fun -> MostraUser(0);
$Master = $Fun -> id_master;
$REGIOES = new Regioes();

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);

$id_master = $row_user['id_master'];



}
?>
<html>
<head>
<title>Gerar IRRF</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<style>
body{
	background-color:#EAEAEA;
	margin:0 auto;
	text-align:center;
	font-family:Tahoma, Geneva, sans-serif
}

#corpo {
	width:775px;
	margin:0 auto;
	text-align:center;
}	
#conteudo {
	background-color: #FFF;

}


</style>
<body>
	<div id="corpo">
    	<div id="conteudo">
        	 <img src="../imagens/logo_ir.jpg" width="500" height="150">
             <h4>Relatório DIRF</h4>
             
            <form name="form" method="post" action="rel_anual_dirf.php">
            <table align="center">
            <tr>
            	<td></td>
            </tr>
            <tr>
            	<td></td>
            </tr>
                <tr>
                    <td>Ano:</td>
                    <td>
                        <select name="ano" >
                        <option value="">Selecione o ano..</option>
                        <?php
                        for($i =2009; $i<=date('Y');$i++){
                            
                            echo '<option value="'.$i.'">'.$i.'</option>"';
                        }
                        ?>
                        </select>
                     </td>
                       
                </tr>
                
            <tr>
            	<td>Região:</td>
                <td>
                <select name="regioes">
                <option value="todos">TODAS</option>
                
                <?php 
				$REGIOES->Preenhe_select_por_master($id_master);
				
				?>
                </select>
                </td>
            </tr>    
             <tr>
             	 <td colspan="2" align="center"> <input type="submit" value="GERAR"  name="enviar" /></td>
             </tr>   
                
                
            </table>
            </form>
         </div>
	</div>
</body>
</html>