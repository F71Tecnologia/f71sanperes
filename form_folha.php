
<!-- DESENVOLVIDO POR JEAN PIERRE JOCHEN - E-mail: jeanjochen@gmail.com -->


<center>
<h2>
		<font face="Verdana, Arial, Helvetica, sans-serif"> Folha de Pagamento
		</font></h2> 
<hr align="center" color="#0099CC"><p>

</center>
<h7>
<form action="form_folha.php" method="POST">
  <font face="Verdana, Arial, Helvetica, sans-serif"> <font size="2">Valor Hora:
  <input name="ValorHora" type="text" size="5" />
  <font color="#FF0000">(Use ponto ao invés da vírgula) </font><br>
  </font></font> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">
  <br>
  Mes de competência <br>
  <select name="Mes">
    <option value="Janeiro">Janeiro </option>
    <option value="Fevereiro">Fevereiro </option>
    <option value="Mar&ccedil;o">Mar&ccedil;o </option>
    <option value="Abril">Abril </option>
    <option value="Maio">Maio </option>
    <option value="Junho">Junho </option>
    <option value="Julho">Julho </option>
    <option value="Agosto">Agosto </option>
    <option value="Setembro">Setembro </option>
    <option value="Outubro">Outubro </option>
    <option value="Novembro">Novembro </option>
    <option value="Dezembro">Dezembro </option>
  </select>
  <br>
  <br>
  Nº de dias úteis <br>
  <input type="text" name="DiasUteis" size="3"/>
  <br>
  <br>
  Nº de domingos + Feriados <br>
  <input type="text" name="Domingos" size="3" />
  <br>
  <br>
  Nº de dias trabalhados<br>
  <input type="text" name="DiasTrab" />
  </font><font face="Verdana, Arial, Helvetica, sans-serif">
  <p>
  <hr align="center" size="1" noshade>
 <br>
  <hr align="center" size="1" noshade>
  <input type="submit" value="Calcular">
  </font> <font face="Arial, Helvetica, sans-serif"> </font>
</form>

<div align="center"><strong><font face="Verdana, Arial, Helvetica, sans-serif" size="4">Demonstrativo de pagamento</font></strong> <font size="1" face="Verdana, Arial, Helvetica, sans-serif"></p>
  <br>
  </font> </div>
<div align="center">
<p align="left"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">

<?php
$V1 = 7.33; // Este valor corresponde a 7hs e 20min de trabalho diário
$ValorHora = $_POST["ValorHora"];
$Mes = $_POST["Mes"];
$DiasUteis = $_POST["DiasUteis"];
$Domingos = $_POST["Domingos"];
$DiasTrab = $_POST["DiasTrab"];
$Tipo = $_POST["Tipo"];

//// CÁLCULO HORISTA /////

$HorasTrab = $DiasTrab * $V1;
$VlrHr = $HorasTrab * $ValorHora;
$DSR = $Domingos * $V1;
$VlrDSR = $DSR * $ValorHora;

     $HorasTrab = $DiasTrab * $V1;
		 		$VlrHr = $HorasTrab * $ValorHora;
		 		$DSR = $Domingos * $V1;
		 		$VlrDSR = $DSR * $ValorHora;
		 		$SalarioH = $VlrDSR + $VlrHr;

						 echo "O CALCULO COM BASE EM HORISTA FICA ASSIM<BR>";
						 echo "Mês selecionado para o cálculo = $Mes<br>";
						 echo "Cálculo das horas trabalhadas = $HorasTrab<br>";
						 echo "Valor Hora = $VlrHr<br>";
						 echo "Descanso Semanal Remunerado = $DSR<br>";
						 echo "Valor do DSR = $VlrDSR<br>";
			    echo "Valor do Salário = $SalarioH <br><p>";

//// FIM DO CÁLCULO DO SALÁRIO HORISTA /////



//// CÁLCULO MENSALISTA /////

		 $Salario = 220 * $ValorHora;
		 echo "O CALCULO COM BASE EM MENSALISTA FICA ASSIM<br>";
		 echo "Cálculo das horas trabalhadas = $Salario<br>";

//// FIM DO CÁLCULO DO SALÁRIO MENSALISTA /////



?>
    </font> </p>
</div>
<p>&nbsp; </p>
