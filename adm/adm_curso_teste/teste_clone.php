<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="shortcut icon" href="../../favicon.ico" />        
<link href="../../net1.css" rel="stylesheet" type="text/css" />
<link href="cursos.css" rel="stylesheet" type="text/css" />         
<link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
<link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
<link href="jquery.autocomplete.css" rel="stylesheet" type="text/css" />  

<script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
<script src="../../js/global.js" type="text/javascript"></script>
<script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
<script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
<script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
<script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
<script src="../../js/jquery.autocomplete.js" type="text/javascript"></script>

<fieldset class="horario">
    <div id="del_hor"><img src="../../imagens/icones/icon-delete.gif" title="Deletar horário" /></div>
    <legend>Dados do Horário</legend>
    <p>
        <label class='first'>Nome do Horário:</label>
        <input type="text" name="nome_horario[]" id="nome_horario" size="108" class="validate[required] limpa" />
    </p>
    <p>
        <label class='first'>Observações:</label>
        <input type="text" name="obs[]" id="obs" class="limpa" size="108" />
    </p>
    <p class="remove">
        <label class='first'>Preenchimento:</label>
        Entrada <input type="text" name="entrada[]" id="entrada" size="10" class="preenchimento validate[required] limpa" />
        Saída Almoço <input type="text" name="ida_almoco[]" id="ida_almoco" size="10" class="preenchimento validate[required] limpa" />
        Retorno Almoço <input type="text" name="volta_almoco[]" id="volta_almoco" size="10" class="preenchimento validate[required] limpa" />
        Saída <input type="text" name="saida[]" id="saida" size="10" class="preenchimento validate[required] limpa" />
    </p>

    <div id="esquerda">
        <p>
            <label class='first'>Horas Mês:</label>
            <input type="text" name="horas_mes[]" id="horas_mes" size="30" maxlength="4" class="validate[required,custom[onlyNumber]] limpa" />
        </p>
        <p>
            <label class='first'>Dias Mês:</label>
            <input type="text" name="dias_mes[]" id="dias_mes" size="30" maxlength="4" class="validate[required,custom[onlyNumber]] limpa" />
        </p>
    </div>

    <div id="direita">
        <p>
            <label class='first'>Dias Semana:</label>
            <input type="text" name="dias_semana[]" id="dias_semana" size="30" maxlength="4" class="validate[required,custom[onlyNumber]] limpa" />
        </p>
        <p>
            <label class='first'>Folgas:</label>
            <input class="check" type="checkbox" name="folga[][0]" value="1" /> Sábado
            <input class="check" type="checkbox" name="folga[][1]" value="2" /> Domingo
            <input class="check" type="checkbox" name="folga[][2]" value="5" /> Plantonista
        </p>
    </div>
</fieldset>
