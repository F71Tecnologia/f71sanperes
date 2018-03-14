<?php
if(empty($_COOKIE['logado'])) {
	print "<script>location.href = '../login.php?entre=true';</script>";
	exit;
}

include('../../conn.php');
include('../../funcoes.php');


if(isset($_GET['regiao'])){

$id_regiao = mysql_real_escape_string($_GET['regiao']);
$projeto   = mysql_real_escape_string($_GET['projeto']);

if($projeto == 'todos') {
	$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE status IN(10,200) AND id_regiao = '$id_regiao'  ORDER BY nome ASC");	
	
}else {
	$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE status IN(10,200) AND id_regiao = '$id_regiao' AND id_projeto = '$projeto' ORDER BY nome ASC");
}


if(mysql_num_rows($qr_clt) != 0 ){
?>	
	<table border="0" class="relacao2">
    <tr>
    <td colspan="5" align="left">
        <a href="#" class="marcar_todos">MARCAR TODOS</a> / 
        <a href="#" class="desmarcar_todos">DESMARCAR TODOS</a> 
    </td>

    </tr>
    <tr class="secao_nova">
    	<td>        	
        </td>
 		<td>COD</td>
    	<td>NOME</td>
        <td>ATIVIDADE/CARGO</td>
        <td>SALÁRIO</td>
    </tr>
    
    
<?php	
}
while($row_clt = mysql_fetch_assoc($qr_clt)):

$qr_curso = mysql_query("SELECT nome,salario FROM curso WHERE  id_curso = '$row_clt[id_curso]'");
$row_curso = mysql_fetch_assoc($qr_curso);

$cargo = $row_curso['nome'];
$salario = $row_curso['salario'];

$class=(($i++ %2) == 0)? 'class="linha_um"' : 'class="linha_dois"'; 
?>

<tr <?php echo $class;?>>
    <td><input type="checkbox" name="id_clt[]" value="<?php echo $row_clt['id_clt'];?>" class="clt"/></td>
    <td><?php echo $row_clt['id_clt'];?></td>
    <td><?php echo htmlentities($row_clt['nome']);?></td>
    <td><?php echo htmlentities( $row_curso['nome']);?></td>
    <td>R$ <?php echo number_format($salario,2,',','.');  ?></td>
</tr>

<?php
endwhile;
}
?>
<tr>
	<td colspan="5" align="center"><input type="submit" name="enviar" value="PROCESSAR" /></td>
</tr>
</table>