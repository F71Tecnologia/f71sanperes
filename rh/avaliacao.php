<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="login.php">Logar</a>';
	exit;
}
header('Content-Type: text/html; charset=utf-8');

include('../conn.php');

$autonomo = $_REQUEST["autonomo"];
?>

<!-- PERGUNTAS DO QUESTIONÁRIO

1 - Reside proximo a UPA? (sim / nao)
2 - Já trabalhou em outra UPA? (sim / nao)
3 - Qual UPA (digitar)
4 - Qual o Motivo do Afastamento? (digitar)
5 - Trabalha em outro local? (sim / nao)
6 - Onde? (digitar)
7 - Estuda Atualmente? (sim / nao)
8 - Quais outros cursos possui? (digitar)
9 - Qual o método de deslocamento até a UPA? (carro, moto, onibus, van, carona)
10 - Quanto tempo de experiencia possui na área? (digitar)
11 - Possui algum vínculo com Governo do Estado? (sim / nao)
12 - Matricula no Governo do Estado (digitar)

-->
<script>
function validaForm(){
           d = document.cadastro;
           if (!d.quest01[0].checked && !d.quest01[1].checked){
                     alert("Responda a Questão nº 1!");
					  d.quest03.focus();
                     return false;
           } 
           if (!d.quest02[0].checked && !d.quest02[1].checked){
                     alert("Responda a Questão nº 2!");
                     d.quest03.focus();
                     return false;
           }
           if (d.quest04.value == ""){
                     alert("Responda a Questão nº 4!");
                     d.quest04.focus();
                     return false;
           }
           if (!d.quest05[0].checked && !d.quest05[1].checked){
                     alert("Responda a Questão nº 5!");
                     d.quest06.focus();
                     return false;
           }
           if (!d.quest07[0].checked && !d.quest07[1].checked){
                     alert("Responda a Questão nº 7!");
                     d.quest08.focus();
                     return false;
           }
           if (d.quest08.value == ""){
                     alert("Responda a Questão nº 8!");
                     d.quest08.focus();
                     return false;
           }
           if (d.quest09.value == "0"){
                     alert("Responda a Questão nº 9!");
                     d.quest09.focus();
                     return false;
           }
		   if (d.quest10.value == ""){
                     alert("Responda a Questão nº 10!");
                     d.quest10.focus();
                     return false;
           }
           if (!d.quest11[0].checked && !d.quest11[1].checked){
                     alert("Responda a Questão nº 11!");
                     d.quest12.focus();
                     return false;
           }
           
           return true;
} 

</script>




<html>
<form name="cadastro" action="cadastroavaliacao.php" method="post" onSubmit="return validaForm()">
<table width="100%" cellpadding="15" cellspacing="0">
<tr>
<td colspan="2" align="center" bgcolor="#999999"><font color="ffffff"><b>FICHA CRITÉRIOS DE AVALIAÇÃO</b></font></td>
</tr>
<tr>
<td bgcolor="#EBEBEB">1 - RESIDE PRÓXIMO AO LOCAL DE TRABALHO?</td>
<td bgcolor="#EBEBEB"><input type="radio" name="quest01" value="SIM">SIM &nbsp; <input type="radio" name="quest01" value="NAO">NÃO  </td>
</tr>
<tr>
<td>2 - JÁ EXERCEU A MESMA FUNÇÃO?</td>
<td><input type="radio" name="quest02" value="SIM">SIM &nbsp; <input type="radio" name="quest02" value="NAO">NÃO  </td>
</tr>
<tr>
<td bgcolor="#EBEBEB">3 - QUAL LOCAL?</td><td bgcolor="#EBEBEB"><input type="text" name="quest03"></td>
</tr>
<tr>
<td>4 - QUAL MOTIVO DO AFASTAMENTO?</td><td><input type="text" name="quest04"></td>
</tr>
<tr>
<td bgcolor="#EBEBEB">5 - TRABALHA EM OUTRO LOCAL?</td>
<td bgcolor="#EBEBEB"><input type="radio" name="quest05" value="SIM">SIM &nbsp; <input type="radio" name="quest05" value="NAO">NÃO  </td>
</tr>
<tr>
<td>6 - ONDE?</td><td><input type="text" name="quest06"></td>
</tr>
<tr>
<td bgcolor="#EBEBEB">7 - ESTUDA ATUALMENTE?</td>
<td bgcolor="#EBEBEB"><input type="radio" name="quest07" value="SIM">SIM &nbsp; <input type="radio" name="quest07" value="NAO">NÃO  </td>
</tr>
<tr>
<td>8 - QUAIS OUTROS CURSOS POSSUI?</td><td><input type="text" name="quest08"></td>
</tr>
<tr>
<td bgcolor="#EBEBEB">9 - QUAL O MÉTODO DE DESLOCAMENTO ATÉ O LOCAL DE TRABALHO?</td>
<td bgcolor="#EBEBEB"><select name="quest09">
<option value="0">ESCOLHA UMA OPÇÃO</option>
<option value="CARRO">CARRO</option>
<option value="MOTO">MOTO</option>
<option value="ONIBUS">ONIBUS</option>
<option value="VAN">VAN</option>
<option value="CARONA">CARONA</option>
</select></td>
</tr>
<tr>
<td>10 - QUANTO TEMPO DE EXPERIÊNCIA POSSUI NA ÁREA?</td><td><input type="text" name="quest10"></td>
</tr>
<tr>
<td bgcolor="#EBEBEB">11 - POSSUI ALGUM VÍNCULO COM O GOVERNO?</td>
<td bgcolor="#EBEBEB"><input type="radio" name="quest11" value="SIM">SIM &nbsp; <input type="radio" name="quest11" value="NAO">NÃO  </td>
</tr>
<tr>
<td>12 - MATRÍCULA:</td><td><input type="text" name="quest12"></td>
</tr>
<input type="hidden" name="autonomo" value="<? echo $autonomo; ?>">
<input type="hidden" name="reg" value="<? echo $_GET["reg"]; ?>">
<input type="hidden" name="pro" value="<? echo $_GET["pro"]; ?>">
<tr>
<td colspan="2">&nbsp;</td>
</tr>
<tr>
<td colspan="2" align="center"><input type="submit" value="SALVAR"></td>
<tr>
</table>

</form>
</html>

