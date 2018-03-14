<?php
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
<!--
body {
	background-color: #006633;
}
.style1 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 12px;
}
.style3 {font-size: 12px}
.style6 {color: #003300}
.style7 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; }
.style37 {font-family: Arial, Helvetica, sans-serif}
.style39 {font-family: Arial, Helvetica, sans-serif; color: #003300;}
.style40 {font-weight: bold; font-family: Arial, Helvetica, sans-serif;}
.style41 {
	color: #FFFFFF;
	font-size: 16px;
}
.style42 {font-weight: bold; color: #003300; font-family: Arial, Helvetica, sans-serif;}
.style43 {font-family: Arial, Helvetica, sans-serif; color: #FFFFFF; font-size: 14px; }
.style44 {font-family: Arial, Helvetica, sans-serif; color: #003300; font-size: 14px; }
.style45 {font-size: 14px}
.style46 {font-family: Arial, Helvetica, sans-serif; font-size: 14px; }
.style47 {
	font-size: 16px;
	color: #FF0000;
}
.style48 {
	font-size: 8px;
	color: #FF0000;
}
.style49 {font-size: 9px}
-->
</style></head>

<body>
<img width='440' height='20' /><br />
<br />
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
  <tr>
    <td colspan="2" bgcolor="#003300" class="style1"><div align="center" class="style43">DADOS DO PROJETO</div></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style6">Código:</span></div></td>
    <td bgcolor="#FFFFFF" class="style1">&nbsp;&nbsp;
    <input name='codigo' type='text' class='campotexto' id='codigo' size='10' /></td>
  </tr>
  <tr>
    <td width="23%" bgcolor="#CCFFCC" class="style1"><div align="right" class="style39"><span class="style37">Tipo de Contratação:</span></div></td>
    <td width="77%" bgcolor="#FFFFFF" class="style1"><span class="style6 style37"> &nbsp;&nbsp;
        <select name='tipo_bol' id='tipo_bol' class='campotexto'>
          <option value="1">Bolsista</option>
          <option value="2">CLT</option>
          <option value="3">Cooperado</option>
        </select>
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39"><span class="style37">Projeto</span></div></td>
    <td bgcolor="#FFFFFF" class="style1"> <span class="style6 style37">&nbsp; &nbsp;$row[nome] &nbsp;&nbsp;/ <span class="style37">&nbsp;&nbsp;Região: $row[regiao]
            <input type='hidden' name='regiao' value='$row[5]' />
    </span></span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39"><span class="style37">Curso:</span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style39">&nbsp;&nbsp;
        <select name='idcurso' id='idcurso' class='campotexto'>
          ";

$result_grupo = mysql_query("SELECT * FROM curso where id_regiao = $row[id_regiao] and campo3 = '$projeto' ORDER BY campo2", $conn);
while ($row_grupo = mysql_fetch_array($result_grupo)){
print "
          <option value='$row_grupo[id_curso]'>$row_grupo[campo2] / Valor: $row_grupo[valor] - $row_grupo[campo1]</option>
          ";
}

print "
          </select>
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39"><span class="style37">Unidade:</span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style39">&nbsp;&nbsp;
        <select name='locacao' id='locacao' class='campotexto'>
          ";

$result_unidade = mysql_query("SELECT * FROM unidade where id_regiao = $row[id_regiao] and campo1 = '$projeto' ORDER BY unidade", $conn);
while ($row_unidade = mysql_fetch_array($result_unidade)){
print "
          <option>$row_unidade[unidade]</option>
          ";
}


print "
          </select>
    </span></td>
  </tr>
</table>
<span class="style1"><br />
</span>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
  <tr>
    <td colspan="8" bgcolor="#003300" class="style1"><div align="center" class="style6 style3 style40 style42">
      <div align="center" class="style41">DADOS CADASTRAIS</div>
    </div></td>
  </tr>
  
  <tr>
    <td width="13%" bgcolor="#CCFFCC" class="style1"><div align="right" class="style6 style3 style40 style42">
      <div align="right"><span class="style37">Nome:</span></div>
    </div></td>
    <td width="87%" colspan="7" bgcolor="#FFFFFF" class="style1"><div align="left" class="style6 style3 style40 style42">
      <div align="left"><span class="style37">&nbsp;&nbsp;
        <input name='nome' type='text' class='campotexto' id='nome' size='75'
        onFocus='document.all.nome.style.background='#CCFFCC''
        onBlur=\"document.all.nome.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
      </span></div>
    </div></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style6 style3 style40 style42">
      <div align="right"><span class='style37'>Endereco:</span></div>
    </div></td>
    <td colspan="7" bgcolor="#FFFFFF" class="style1"><div align="left" class="style6 style3 style40 style42">
      <div align="left"><span class="style37">&nbsp;&nbsp;
          <input name='endereco' type='text' class='campotexto' id='endereco' size='75' 
        onFocus=\"document.all.endereco.style.background='#CCFFCC'\" 
        onBlur=\"document.all.endereco.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
      </span></div>
    </div></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style6 style3 style40 style42">
      <div align="right"><span class="style37">Bairro:</span></div>
    </div></td>
    <td bgcolor="#FFFFFF" class="style1"><div align="left" class="style6 style3 style40 style42">
      <div align="left"><span class="style37">&nbsp;&nbsp;
        <input name='bairro' type='text' class='campotexto' id='bairro' size='15' 
        onFocus=\"document.all.bairro.style.background='#CCFFCC'\" 
        onBlur=\"document.all.bairro.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
        &nbsp;&nbsp;</span></div>
    </div></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style6 style3 style40 style42">
      <div align="right"><span class="style37"> Cidade:</span></div>
    </div></td>
    <td bgcolor="#FFFFFF" class="style1"><div align="left" class="style6 style3 style40 style42">
      <div align="left"><span class="style37">
        <input name='cidade' type='text' class='campotexto' id='cidade' size='12' 
        onFocus=\"document.all.bairro.style.background='#CCFFCC'\" 
        onBlur=\"document.all.bairro.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
      </span></div>
    </div></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style6 style3 style40 style42">
      <div align="right"><span class="style37">UF:</span></div>
    </div></td>
    <td bgcolor="#FFFFFF" class="style1"><div align="left" class="style6 style3 style40 style42">
      <div align="left"><span class="style37">&nbsp;&nbsp;
        <input name='uf' type='text' class='campotexto' id='uf' size='2' maxlength='2' 
        onFocus=\"document.all.bairro.style.background='#CCFFCC'\" 
        onBlur=\"document.all.bairro.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"
        onkeyup=\"pula(2,this.id,cep.id)\" />
      </span></div>
    </div></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style6 style3 style40 style42">
      <div align="right"><span class="style37">CEP:&nbsp;</span></div>
    </div></td>
    <td bgcolor="#FFFFFF" class="style1"><div align="left" class="style6 style3 style40 style42">
      <div align="left"><span class="style37">&nbsp;&nbsp;
        <input name='cep' type='text' class='campotexto' id='cep' size='10' maxlength='9' 
        style="background:#FFFFFF; text-transform:uppercase;"
        onFocus="document.all.cep.style.background='#CCFFCC'" 
        onBlur="document.all.cep.style.background='#FFFFFF'"
        OnKeyPress="formatar('#####-###', this)" 
        onKeyUp="pula(9,this.id,tel_fixo.id)" />
      </span></div>
    </div></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style6 style3 style40 style42">
      <div align="right"><span class="style37">Telefones:</span></div>
    </div></td>
    <td colspan="2" bgcolor="#FFFFFF" class="style1"><div align="right" class="style6 style3 style40 style42">
      <div align="center"><span class="style37">Fixo:&nbsp;</span></div>
    </div></td>
    <td bgcolor="#FFFFFF" class="style1"><div align="center" class="style6 style40">
      <div align="left"><span class="style37">
        <input name='tel_fixo' type='text' class='campotexto' id='tel_fixo' size='12' onkeypress="\&quot;return(TelefoneFormat(this,event))\&quot;" onkeyup="\&quot;pula(13,this.id,tel_cel.id)\&quot;" />
        </span></div>
    </div></td>
    <td bgcolor="#CCFFCC" class="style1"> <div align="center" class="style6 style37">
      <div align="right"><span class="style37">Cel:</span></div>
    </div></td>
    <td bgcolor="#FFFFFF" class="style1"><div align="center" class="style6 style40">
      <div align="left"><span class="style37">&nbsp;&nbsp;
          <input name='tel_cel' type='text' class='campotexto' id='tel_cel' size='12' onkeypress="\&quot;return(TelefoneFormat(this,event))\&quot;" onkeyup="\&quot;pula(13,this.id,tel_rec.id)\&quot;" />
        &nbsp;</span></div>
    </div></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="center" class="style6 style37">
      <div align="right"><span class="style37">Recado:</span></div>
    </div></td>
    <td bgcolor="#FFFFFF" class="style1"><div align="center" class="style6 style40">
      <div align="left"><span class="style37">&nbsp;&nbsp;
          <input name='tel_rec' type='text' class='campotexto' id='tel_rec' size='12' onkeypress="\&quot;return(TelefoneFormat(this,event))\&quot;" onkeyup="\&quot;pula(13,this.id,nasc_dia.id)\&quot;" />
        </span></div>
    </div></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39"><span class="style37">Data de Nascimento:
    </span></div></td>
    <td colspan="2" bgcolor="#FFFFFF" class="style1"><span class='style6 style37'> &nbsp;&nbsp;
      <input name='nasc_dia' id='nasc_dia' type='text' class='campotexto' size='2' maxlength="2" onkeyup="\&quot;pula(2,this.id,nasc_mes.id)\&quot;" />
/
<input name='nasc_mes' id='nasc_mes' type='text' class='campotexto' size='2' maxlength="2" onkeyup="\&quot;pula(2,this.id,nasc_ano.id)\&quot;" />
/
<input name='nasc_ano' id='nasc_ano' type='text' class='campotexto' size='4' maxlength="4" onkeyup="\&quot;pula(4,this.id,trabalho.id)\&quot;" />
    </span> <span class='style6 style37'>&nbsp;</span></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39"><span class="style37">Naturalidade:</span></div></td>
    <td colspan="2" bgcolor="#FFFFFF" class="style1"><span class="style6 style37">&nbsp;&nbsp;
        <input name='naturalidade' type='text' class='campotexto' id='naturalidade' size='10' />
    </span></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style6 style37"> Nacionalidade:</span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><div align="left" class="style39"><span class="style37">&nbsp;&nbsp;
        <input name='nacionalidade' type='text' class='campotexto' id='nacionalidade' size='8' />
    </span></div></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39"><span class="style37">Estado Civil:</span></div></td>
    <td colspan="5" bgcolor="#FFFFFF" class="style1"><span class="style6 style37"> &nbsp;&nbsp;
        <select name='civil' class='campotexto' id='civil'>
          <option>Solteiro</option>
          <option>Casado</option>
          <option>Viúvo</option>
          <option>Sep. Judicialmente</option>
          <option>Divorciado</option>
        </select>
    </span></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style6 style37">Sexo:</span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><table align="left">
      <tr>
        <td class="style39"><span class='style37'>&nbsp;&nbsp;
              <input type='radio' name='sexo' value='M' checked="checked" />
          Masculino</span></td>
        <td class="style39"><span class='style37'>&nbsp;&nbsp;
              <input type='radio' name='sexo' value='F' />
          Feminino</span></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="8" bgcolor="#CCFF99" class="style1"><div align="center" class="style44">DADOS DA FAMÍLIA E EDUCACIONAIS</div></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39"><span class="style37">Filiação - Pai:</span></div></td>
    <td colspan="7" bgcolor="#FFFFFF" class="style1"><span class="style6 style37">&nbsp;&nbsp;
        <input name='pai' type='text' class='campotexto' id='pai' size='75' />
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39"><span class="style37">Filiação - Mãe</span></div></td>
    <td colspan="7" bgcolor="#FFFFFF" class="style1"><span class="style6 style37">&nbsp;&nbsp;
        <input name='mae' type='text' class='campotexto' id='mae' size='75' />
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39"><span class="style37">Estuda Atualmente?</span></div></td>
    <td colspan="5" bgcolor="#FFFFFF" class="style1"><table align="left">
        <tr>
          <td class="style39"><span class='style37'>&nbsp;&nbsp;
                <input type='radio' name='sexo' value='M' checked="checked" />
            SIM</span></td>
          <td class="style39"><span class='style37'>&nbsp;&nbsp;
                <input type='radio' name='sexo' value='F' />
            NÃO</span></td>
        </tr>
      </table></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39"><span class="style37">Término em:</span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style6 style37">&nbsp;&nbsp;
      <input name='escola_dia' type='text' class='campotexto' value='30' size='2' maxlength="2" />
/
<input name='escola_mes' type='text' class='campotexto' size='2' maxlength="2" value='11' />
/
<input name='escola_ano' type='text' class='campotexto' size='4' maxlength="4" />
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39"><span class="style37">Escolaridade</span>:</div></td>
    <td colspan="2" bgcolor="#FFFFFF" class="style1"><span class="style6 style37">&nbsp;&nbsp;&nbsp;
        <input name='escolaridade' type='text' class='campotexto' id='escolaridade' size='15' />
    </span></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39"><span class="style37">Instituíção:</span></div></td>
    <td colspan="2" bgcolor="#FFFFFF" class="style1"><span class="style6 style37">&nbsp;
        <input name='instituicao' type='text' class='campotexto' id='titulo' size='20' />
    </span></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39"><span class="style37">Curso:&nbsp;</span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style6 style37">&nbsp;&nbsp;
        <input name='curso' type='text' class='campotexto' id='zona' size='10' />
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39">Número de Filhos</div></td>
    <td colspan="7" bgcolor="#FFFFFF" class="style1">&nbsp;&nbsp;
    <input name='filhos' type='text' class='campotexto  style37' id='filhos' size='2' />
    <div align="right"></div>    <div align="right"></div></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39">Nome:</div></td>
    <td colspan="5" bgcolor="#FFFFFF" class="style1"><span class="style6 style37"> &nbsp;&nbsp;&nbsp;
      <input name='filho_1' type='text' class='campotexto' id='filho_1' size='50' />
    </span>      <div align="right" class="style39"></div></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39">nascimento:</div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style39">
      &nbsp;&nbsp;
      <input onkeyup="\&quot;mascara_data(this)\&quot;" name='data_filho_1' type='text' class='campotexto' size='12' maxlength='10' />
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39">Nome:</div></td>
    <td colspan="5" bgcolor="#FFFFFF" class="style1"><span class="style6 style37"> &nbsp;&nbsp;&nbsp;
      <input name='filho_2' type='text' class='campotexto' id='filho_2' size='50' />
    </span>      <div align="right" class="style39"></div></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39">nascimento:</div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style39">
    &nbsp;&nbsp;
    <input onkeyup="\&quot;mascara_data(this)\&quot;" name='data_filho_2' type='text' class='campotexto' size='12' maxlength='10' />
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39">Nome:</div></td>
    <td colspan="5" bgcolor="#FFFFFF" class="style1"><span class="style6 style37"> &nbsp;&nbsp;&nbsp;
      <input name='filho_3' type='text' class='campotexto' id='filho_3' size='50' />
    &nbsp;</span>      <div align="right" class="style39"></div></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39">nascimento:</div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style39">
    &nbsp;&nbsp;
    <input onkeyup="\&quot;mascara_data(this)\&quot;" name='data_filho_3' type='text' class='campotexto' size='12' maxlength='10' />
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39">Nome:</div></td>
    <td colspan="5" bgcolor="#FFFFFF" class="style1"><span class="style6 style37"> &nbsp;&nbsp;&nbsp;
      <input name='filho_4' type='text' class='campotexto' id='filho_4' size='50' />
    </span>      <div align="right" class="style39"></div></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39">nascimento:</div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style39">
    &nbsp;&nbsp;
    <input onkeyup="\&quot;mascara_data(this)\&quot;" name='data_filho_4' type='text' class='campotexto' size='12' maxlength='10' />
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39">Nome:</div></td>
    <td colspan="5" bgcolor="#FFFFFF" class="style1"><span class="style39">&nbsp;&nbsp;&nbsp;
      <input name='filho_5' type='text' class='campotexto' id='filho_5' size='50' />
    </span>      <div align="right" class="style39"></div></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right" class="style39">nascimento:</div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style39">
    &nbsp;&nbsp;
    <input onkeyup="\&quot;mascara_data(this)\&quot;" name='data_filho_5' type='text' class='campotexto' size='12' maxlength='10' />
    </span></td>
  </tr>
  
  <tr>
    <td colspan="8" bgcolor="#CCFF99" class="style1"><div align="center" class="style44">APARÊNCIA</div></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" class="style1"><span class="style6">Cabelos:</span></td>
    <td colspan="3" bgcolor="#FFFFFF" class="style1"><span class="style6"><span class="style37">&nbsp;&nbsp;
          <select name='cabelos' id='cabelos'>
            <option>Loiro</option>
            <option>Castanho Claro</option>
            <option>Castanho Escuro</option>
            <option>Ruivo</option>
            <option>Pretos</option>
          </select>
    </span></span></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style6">Olhos:</span></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style6"><span class="style37">&nbsp;&nbsp;
          <select name='olhos' id='olhos'>
            <option>Castanho Claro</option>
            <option>Castanho Escuro</option>
            <option>Verde</option>
            <option>Azul</option>
            <option>Mel</option>
            <option>Preto</option>
          </select>
    </span></span></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style6"><span class="style37">Peso: </span></span></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style6"><span class="style37">
      &nbsp;&nbsp;
      <input name='peso' type='text' class='campotexto' id='peso' size='5' />
    </span></span></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" class="style1"><span class="style37">Altura:</span></td>
    <td colspan="3" bgcolor="#FFFFFF" class="style1"><span class="style6"><span class="style37">
      &nbsp;&nbsp;
      <input name='altura' type='text' class='campotexto' id='altura' size='5' />
&nbsp;&nbsp; </span></span></td>
    <td colspan="3" bgcolor="#FFFFFF" class="style1"><span class="style6"><span class="style37">&nbsp;Marcas ou Cicatriz aparente</span>?</span></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style6">&nbsp;&nbsp;<span class="style37">
      <input name='defeito' type='text' class='campotexto' id='defeito' size='18' />
    </span></span></td>
  </tr>
  <tr>
    <td colspan="8" bgcolor="#FFFFFF" class="style1">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="8" bgcolor="#FFFFFF" class="style1"><div align="center" class="style39"><span class="style37">Enviar Foto:</span><input name='foto' type='checkbox' id='foto' onClick="document.all.tablearquivo.style.display = (document.all.tablearquivo.style.display == 'none') ? '' : 'none' ;" value='1'/>
    </div></td>
  </tr>
</table>
<br />
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
  <tr>
    <td colspan="6" bgcolor="#003300" class="style1"><div align="center" class="style43">DOCUMENTAÇÃO</div></td>
  </tr>
  <tr>
    <td width="16%" bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37">Nº do RG:</span></div></td>
    <td width="12%" bgcolor="#FFFFFF" class="style1"><span class="style6 style37"> &nbsp;&nbsp;
      <input name='rg' type='text' class='campotexto' id='rg' size='12' maxlength="13" onkeypress="\&quot;formatar('##.###.###-#'," this)\="this)\">
    </span></td>
    <td width="15%" bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37">Orgão Expedidor:</span></div></td>
    <td width="21%" bgcolor="#FFFFFF" class="style1"><span class="style37">&nbsp;&nbsp;
        <input name='orgao' type='text' class='campotexto' id='orgao' size='8' />
    </span> </td>
    <td width="18%" bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37"> Data Expedição:</span> </div></td>
    <td width="18%" bgcolor="#FFFFFF" class="style1"><span class="style37">&nbsp;
        &nbsp;
        <input name='rg_dia' type='text' class='campotexto' id='rg_dia' size='2' maxlength="2" onkeyup="\&quot;pula(2,this.id,rg_mes.id)\&quot;" />
/
<input name='rg_mes' type='text' class='campotexto' id='rg_mes' size='2' maxlength="2" onkeyup="\&quot;pula(2,this.id,rg_ano.id)\&quot;" />
/
<input name='rg_ano' type='text' class='campotexto' id='rg_ano' size='4' maxlength="4" onkeyup="\&quot;pula(4,this.id,cpf.id)\&quot;" />
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37">CPF:</span></div></td>
    <td colspan="3" bgcolor="#FFFFFF" class="style1"><span class="style37">&nbsp;&nbsp;
        <input name='cpf' type='text' class='campotexto' id='cpf' size='10' maxlength="14" 
onkeypress="\&quot;formatar('###.###.###-##'," this)\="this)\"" onkeyup=\&quot;pula(14,this.id,titulo.id)\&quot; />
    </span></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37">Certificado de&nbsp;Reservista:</span></div></td>
    <td bgcolor="#FFFFFF" class="style1">&nbsp;&nbsp;<span class="style37">
      <input name='reservista' type='text' class='campotexto' id='reservista' size='18' />
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37">Carteira de Trabalho:&nbsp;</span></div></td>
    <td colspan="3" bgcolor="#FFFFFF" class="style1"><span class='style37'>&nbsp;&nbsp;&nbsp;
        <input name='trabalho' type='text' class='campotexto' id='trabalho' size='15' />
    </span></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37">data:</span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style37">
    &nbsp;&nbsp;
    <input name='trabalho_dia' type='text' class='campotexto' id='trabalho_dia' size='2' maxlength="2" onkeyup="\&quot;pula(2,this.id,trabalho_mes.id)\&quot;" />
/
<input name='trabalho_mes' type='text' class='campotexto' id='trabalho_mes' size='2' maxlength="2" onkeyup="\&quot;pula(2,this.id,trabalho_ano.id)\&quot;" />
/
<input name='trabalho_ano' type='text' class='campotexto' id='trabalho_ano' size='4' maxlength="4" onkeyup="\&quot;pula(4,this.id,naturalidade.id)\&quot;" />
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37">Nº Título de Eleitor:</span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style37">&nbsp;&nbsp;
        <input name='titulo' type='text' class='campotexto' id='titulo2' size='10' />
    </span></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37"> Zona:&nbsp;</span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style37">&nbsp;
        <input name='zona' type='text' class='campotexto' id='zona2' size='3' />
    </span></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37">Seção:&nbsp;</span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style37">&nbsp;&nbsp;
        <input name='secao' type='text' class='campotexto' id='secao' size='3' />
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style28"><span class='style37'>PIS:</span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style37">&nbsp;&nbsp;
      <input name='pis' type='text' class='campotexto' id='pis' size='12' />
    </span></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37">Data Pis: </span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style37">
    <input name='pis_dia' type='text' class='campotexto' id='pis_dia' size='2' maxlength="2" />
/
<input name='pis_mes' type='text' class='campotexto' id='pis_mes' size='2' maxlength="2" />
/
<input name='pis_ano' type='text' class='campotexto' id='pis_ano' size='4' maxlength="4" />
    </span></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37">FGTS:</span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style37">&nbsp;&nbsp;
        <input name='fgts' type='text' class='campotexto' id='fgts' size='10' />
    </span></td>
  </tr>
</table>
<span class="style1"><br />
</span>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
  <tr>
    <td colspan="6" bgcolor="#003300" class="style1"><div align="center" class="style43">BENEFÍCIOS</div></td>
  </tr>
  <tr>
    <td width="16%" bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37">Assistência</span> <span class="style37">Médica:</span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><table>
        <tr>
          <td width='114'><span class='style37'>&nbsp;&nbsp;
                <input type='radio' name='medica' value='1' />
            Sim</span></td>
          <td width='94'><span class='style37'>&nbsp;&nbsp;
                <input type='radio' name='medica' value='0' checked="checked" />
            Não</span></td>
        </tr>
      </table></td>
    <td width="18%" bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37"> <span class="style37">Tipo de Plano:    </span>:</span> </div></td>
    <td width="18%" colspan="3" bgcolor="#FFFFFF" class="style1"><span class="style37"><span class="style37">
      &nbsp;&nbsp;
      <select name='plano_medico' class='campotexto' id='plano_medico'>
        <option value="1">Familiar</option>
        <option value="2">Individual</option>
      </select>
    </span></span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37">Seguro, Apólice:</span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style37">&nbsp;&nbsp;
        <select name='apolice' class='campotexto' id='apolice'>
          <option value='0'>Não Possui</option>
          ";
	
$sql_apo = "SELECT * FROM apolice where id_regiao = $row[id_regiao]";
$result_apo = mysql_query($sql_apo, $conn);
while ($row_apo = mysql_fetch_array($result_apo)){
print "
          <option value="$row_apo[id_apolice]">$row_apo[banco]</option>
          ";
}
print "
        </select>
    </span></td>
    <td bgcolor="#CCFFCC" class="style1"><span class="style37">Dependente</span></td>
    <td colspan="3" bgcolor="#FFFFFF" class="style1"><span class="style37">&nbsp;&nbsp;
        <input name='dependente' type='text' class='campotexto' id='dependente' size='65' />
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><span class="style37">Insalubridade:</span></td>
    <td bgcolor="#FFFFFF" class="style1">&nbsp;&nbsp;&amp;nbsp
    <input name='insalubridade' type='checkbox' id='insalubridade2' value='1' $chek1/></td>
    <td bgcolor="#CCFFCC" class="style1"><span class="style37">Vale Transporte:&nbsp;</span></td>
    <td colspan="3" bgcolor="#FFFFFF" class="style1"><span class='style37'>
    &nbsp;
    <input name='transporte' type='checkbox' id='transporte2' value='1' $chek2/>
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><span class="style37">Tipo de Vale    </span></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style37">
      &nbsp;&nbsp;
      <select name='tipo_vale'>
        <option value="3">Ambos</option>
        <option value='2'>Papel</option>
        <option value="1">Cartão</option>
                              </select>
    </span></td>
    <td bgcolor="#CCFFCC" class="style1"><span class="style37">Cart&atilde;o</span>:</td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style37">&nbsp;&nbsp;
        <input name='num_cartao' type='text' class='campotexto' id='num_cartao' size='12' />
    </span></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37">Valor Total:
      
    </span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style37">
      &nbsp;&nbsp;&nbsp;
      <input name='valor_cartao' type='text' class='campotexto' id='valor_cartao' size='12' />
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1">Papel: &nbsp;&nbsp;Quantidade 1: </td>
    <td bgcolor="#FFFFFF" class="style1">&nbsp;
    <input name='vale_qnt_1' type='text' class='campotexto' id='vale_qnt_1' size='3' /></td>
    <td bgcolor="#CCFFCC" class="style1"> &nbsp;Valor 1:</td>
    <td colspan="3" bgcolor="#FFFFFF" class="style1">&nbsp;
      <input name='vale_valor_1' type='text' class='campotexto' id='vale_valor_1' size='12' /></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1">Quantidade 2:</td>
    <td bgcolor="#FFFFFF" class="style1">&nbsp;
      <input name='vale_qnt_2' type='text' class='campotexto' id='vale_qnt_2' size='3' /></td>
    <td bgcolor="#CCFFCC" class="style1">&nbsp;Valor 2:</td>
    <td colspan="3" bgcolor="#FFFFFF" class="style1">&nbsp;
    <input name='vale_valor_2' type='text' class='campotexto' id='vale_valor_2' size='12' /></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1">Quantidade 3:</td>
    <td bgcolor="#FFFFFF" class="style1">&nbsp;
      <input name='vale_qnt_3' type='text' class='campotexto' id='vale_qnt_3' size='3' /></td>
    <td bgcolor="#CCFFCC" class="style1">&nbsp;Valor 3:</td>
    <td colspan="3" bgcolor="#FFFFFF" class="style1">&nbsp;
    <input name='vale_valor_3' type='text' class='campotexto' id='vale_valor_3' size='12' /></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1">Quantidade 4:</td>
    <td bgcolor="#FFFFFF" class="style1">&nbsp;
    <input name='vale_qnt_4' type='text' class='campotexto' id='vale_qnt_4' size='3' /></td>
    <td bgcolor="#CCFFCC" class="style1">&nbsp;Valor 4:</td>
    <td colspan="3" bgcolor="#FFFFFF" class="style1">&nbsp;
    <input name='vale_valor_4' type='text' class='campotexto' id='vale_valor_4' size='12' /></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1">Adicional <span class="style37">&nbsp;Noturno :</span></td>
    <td bgcolor="#FFFFFF" class="style1"><table width="189">
      <tr>
        <td width='59'><span class='style37'>
          <input type='radio' name='ad_noturno' value='1' />
          Sim</span></td>
        <td width='193'><span class='style37'>&nbsp;&nbsp;
              <input type='radio' name='ad_noturno' value='0' checked="checked" />
          Não</span></td>
      </tr>
    </table></td>
    <td bgcolor="#CCFFCC" class="style1"><span class="style37">Integrante do</span> <span class="style37">CIPA:</span></td>
    <td colspan="3" bgcolor="#FFFFFF" class="style1"><table width="154">
      <tr>
        <td width='66'><span class='style37'> &nbsp;
            <input type='radio' name='cipa' value='1' />
          Sim</span></td>
        <td width='76'><span class='style37'>&nbsp;&nbsp;
            <input type='radio' name='cipa' value='0' checked="checked" />
          Não</span></td>
      </tr>
    </table></td>
  </tr>
</table>
<span class="style1"><br />
</span>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
  <tr>
    <td colspan="4" bgcolor="#003300" class="style1"><div align="center" class="style43">DADOS BANCÁRIOS</div></td>
  </tr>
  <tr>
    <td width="17%" bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37">Banco:</span></div></td>
    <td width="31%" bgcolor="#FFFFFF" class="style1"><span class="style37">&nbsp;
      <select name='tipopg2' class='campotexto' id='tipopg2'>
        <option value="$rbancos">$bancos</option>
        ";

$result_pg = mysql_query("SELECT * FROM tipopg where id_projeto = '$projeto'", $conn);
while ($row_pg = mysql_fetch_array($result_pg)){
  if($row_pg['tipopg'] == $row2[tipo_pagamento]){
   print "        ";   
  }else{
  print "        ";
  }
}

print "
            </select>
    </span></td>
    <td width="17%" bgcolor="#CCFFCC" class="style1"><div align="right">Agência: </div></td>
    <td width="35%" bgcolor="#FFFFFF" class="style1"><span class='style37'>&nbsp;&nbsp;
      <input name='agencia' type='text' class='campotexto' id='agencia' size='12' />
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right">Conta: </div></td>
    <td bgcolor="#FFFFFF" class="style1">&nbsp;<span class="style37">
      <input name='conta' type='text' class='campotexto' id='conta' size='12' />
    </span></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right">Nome do Banco<span class="style48"><br /> 
          <span class="style49">(caso não esteja na lista acima)</span></span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style37">
      &nbsp;&nbsp;
      <input name='nomebanco' type='text' class='campotexto' id='nomebanco' size='50' />
    </span></td>
  </tr>
</table>
<span class="style1"><br />
</span>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
  <tr>
    <td colspan="4" bgcolor="#003300" class="style1"><div align="center" class="style43">DADOS FINANCEIROS E DE CONTRATO</div></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37">Data de Entrada:</span></div></td>
    <td bgcolor="#FFFFFF" class="style1"><span class="style37">&nbsp;&nbsp;
        <input name='ca_dia' type='text' class='campotexto' id='ca_dia' size='2' maxlength="2" onkeyup="\&quot;pula(2,this.id,ca_mes.id)\&quot;" />
/
<input name='ca_mes' type='text' class='campotexto' id='ca_mes' size='2' maxlength="2" onkeyup="\&quot;pula(2,this.id,ca_ano.id)\&quot;" />
/
<input name='ca_ano' type='text' class='campotexto' id='ca_ano' size='4' maxlength="4" onkeyup="\&quot;pula(4,this.id,observacoes.id)\&quot;" />
    </span></td>
    <td bgcolor="#CCFFCC" class="style1"><div align="right">Data do Exame Admissional: </div></td>
    <td bgcolor="#FFFFFF" class="style1">&nbsp;<span class='style37'>
    <input name='exame_dia' type='text' class='campotexto' id='exame_dia' size='2' maxlength="2" />
/
<input name='exame_mes' type='text' class='campotexto' id='exame_mes' size='2' maxlength="2" />
/
<input name='exame_ano' type='text' class='campotexto' id='exame_ano' size='4' maxlength="4" />
&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
  </tr>
  <tr>
    <td width="23%" bgcolor="#CCFFCC" class="style1"><div align="right"><span class="style37">Local de Pagamento:</span></div></td>
    <td width="77%" colspan="3" bgcolor="#FFFFFF" class="style1">&nbsp;&nbsp;<span class="style37">
      <input name='localpagamento' type='text' class='campotexto' id='localpagamento' size='25' />
    </span></td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><span class='style7'>
    <div align="right">Tipo de Pagamento:</div></td>
    <td colspan="3" bgcolor="#FFFFFF" class="style1">&nbsp;&nbsp;
      <select name='tipopg' class='campotexto' id='tipopg'>
        ";

$result_pg = mysql_query("SELECT * FROM tipopg where id_projeto = '$projeto'", $conn);
while ($row_pg = mysql_fetch_array($result_pg)){
  if($row_pg['tipopg'] == $row2[tipo_pagamento]){
   print "
        <option value='$row_pg[id_tipopg]' selected="selected">$row_pg[tipopg]</option>
        ";   
  }else{
  print "
        <option value='$row_pg[id_tipopg]'>$row_pg[tipopg]</option>
        ";
  }
}

print "
      </select>
&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#CCFFCC" class="style1"><span class="style37">Observações:</span></td>
    <td colspan="3" bgcolor="#FFFFFF" class="style1"><textarea name='observacoes' id='observacoes' class='campotexto' cols='55' rows='4'></textarea></td>
  </tr>
</table>
<br />
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
  <tr>
    <td width="254%" colspan="4" bgcolor="#003300" class="style1"><div align="center" class="style43">FINALIZAÇÃO DO CADASTRAMENTO</div></td>
  </tr>
  
  
  
  
  <tr>
    <td colspan="4" bgcolor="#FFFFCC" class="style1"><div align="center">
      <p><span class="style46"><br />
        Todos os</span><span class="style45"> documentos foram ASSINADOS?</span> <span class="style37">
          <input name='impressos2' type='checkbox' id='impressos2' value='1' />
        </span></p>
      <p><span class="style47">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</span><br />
          </p>
      <table width='200' border='0' align="center" cellpadding='0' cellspacing='0'>
        <tr>
          <td align='center' class='style7'><input type='reset' name='Submit2' value='Limpar' class='campotexto' /></td>
          <td align='center' valign='middle' class='style7'><input type='submit' name='Submit' value='CADASTRAR' class='campotexto' />
              <br /></td>
        </tr>
      </table>
      <br />
      <div align="center"><span class="style7">
        <input type='hidden' name='id_cadastro' value='4' />
        <input type='hidden' name='id_projeto' value='$projeto' />
        <input type='hidden' name='user' value='$id_user' />
      </span><br />
      </div>
      </div></td>
  </tr>
</table>
<p>
  <input name='codigo2' type='text' class='campotexto' id='codigo2' size='10' />
  <br />
  <br />
</p>
</body>
</html>
