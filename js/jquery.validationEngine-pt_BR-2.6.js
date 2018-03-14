(function($){
    $.fn.validationEngineLanguage = function(){};
    $.validationEngineLanguage = {
        newLang: function(){
            $.validationEngineLanguage.allRules = {
                "required": {
                    "regex": "none",
                    "alertText": "* Este campo � obrigat�rio",
                    "alertTextCheckboxMultiple": "* Favor selecionar uma op��o",
                    "alertTextCheckboxe": "* Este checkbox � obrigat�rio",
                    "alertTextDateRange": "* Ambas as datas do intervalo s�o obrigat�rias"
                },
                "requiredInFunction": { 
                    "func": function(field, rules, i, options){
                        return (field.val() == "test") ? true : false;
                    },
                    "alertText": "* Field must equal test"
                },
                "dateRange": {
                    "regex": "none",
                    "alertText": "* Intervalo de datas inv�lido"
                },
                "dateTimeRange": {
                    "regex": "none",
                    "alertText": "* Intervalo de data e hora inv�lido"
                },
                "minSize": {
                    "regex": "none",
                    "alertText": "* Permitido o m�nimo de ",
                    "alertText2": " caractere(s)"
                },
                "maxSize": {
                    "regex": "none",
                    "alertText": "* Permitido o m�ximo de ",
                    "alertText2": " caractere(s)"
                },
                "groupRequired": {
                    "regex": "none",
                    "alertText": "* Voc� deve preencher um dos seguintes campos"
                },
                "min": {
                    "regex": "none",
                    "alertText": "* Valor m�nimo � "
                },
                "max": {
                    "regex": "none",
                    "alertText": "* Valor m�ximo � "
                },
                "past": {
                    "regex": "none",
                    "alertText": "* Data anterior a "
                },
                "future": {
                    "regex": "none",
                    "alertText": "* Data posterior a "
                },	
                "maxCheckbox": {
                    "regex": "none",
                    "alertText": "* M�ximo de ",
                    "alertText2": " op��es permitidas"
                },
                "minCheckbox": {
                    "regex": "none",
                    "alertText": "* Favor selecionar ",
                    "alertText2": " op��o(�es)"
                },
                "equals": {
                    "regex": "none",
                    "alertText": "* Os campos n�o correspondem"
                },
                "creditCard": {
                    "regex": "none",
                    "alertText": "* N�mero de cart�o de cr�dito inv�lido"
                },
                "phone": {
                    "regex": /^([\+][0-9]{1,3}([ \.\-])?)?([\(][0-9]{1,6}[\)])?([0-9 \.\-]{1,32})(([A-Za-z \:]{1,11})?[0-9]{1,4}?)$/,
                    "alertText": "* N�mero de telefone inv�lido"
                },
                "email": {
                    "regex": /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,
                    "alertText": "* Endere�o de email inv�lido"
                },
                "integer": {
                    "regex": /^[\-\+]?\d+$/,
                    "alertText": "* N�mero inteiro inv�lido"
                },
                "number": {
                    "regex": /^[\-\+]?((([0-9]{1,3})([,][0-9]{3})*)|([0-9]+))?([\.]([0-9]+))?$/,
                    "alertText": "* N�mero decimal inv�lido"
                },
                "date": {
                    "regex": /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/,
                    "alertText": "* Data inv�lida, deve ser no formato AAAA-MM-DD"
                },
                "dateBr":{
                     "regex": /^([0-9]|[0,1,2][0-9]|3[0,1])\/(0[1-9]|1[0,1,2])\/\d{4}$/,
                     "alertText": "* Data inv�lida"
                },
                
                "ipv4": {
                    "regex": /^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))$/,
                    "alertText": "* Endere�o IP inv�lido"
                },
                "url": {
                    "regex": /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i,
                    "alertText": "* URL inv�lida"
                },
                "ajaxUser":{
                    /*"file":"../methods.php",*/
                    "extraData":"method=validaUser",
                    "alertTextOk":"* Este usu�rio esta liberado",
                    "alertTextLoad":"* Carregando, aguarde",
                    "alertText":"* Este usu�rio j� est� sendo utilizado"
                },
                "ajaxName":{
                    "file":"validateUser.php",
                    "alertText":"* This name is already taken",
                    "alertTextOk":"* This name is available",
                    "alertTextLoad":"* Loading, please wait"
                },
                "onlyNumberSp": {
                    "regex": /^[0-9\ ]+$/,
                    "alertText": "* Apenas n�meros"
                },
                "onlyLetterSp": {
                    "regex": /^[a-zA-Z\ \']+$/,
                    "alertText": "* Apenas letras"
                },
                "Hour": {
                    "regex": /^([0-1][0-9]|2[0-3]):[0-5][0-9]$/gi,
                    "alertText": "* Hora Inv�lida"
                },
                "onlyLetterNumber": {
                    "regex": /^[0-9a-zA-Z]+$/,
                    "alertText": "* N�o s�o permitidos caracteres especiais"
                },
                "real": {
                	// Brazilian (Real - R$) money format
                	"regex": /^([1-9]{1}[\d]{0,2}(\.[\d]{3})*(\,[\d]{0,2})?|[1-9]{1}[\d]{0,}(\,[\d]{0,2})?|0(\,[\d]{0,2})?|(\,[\d]{1,2})?)$/,
                    "alertText": "* N�mero decimal inv�lido"
                },
                "select":{
                    "regex":/^[^-1]|^1/,
                    "alertText":"Selecione uma op��o v�lida"
                },
                "cpf": {
                    // CPF is the Brazilian ID
                    "func": function(field, rules, i, options){
                        cpf = field.val().replace(/[^0-9]+/g, '');
                        while(cpf.length < 11) cpf = "0"+ cpf;

                        var expReg = /^0+$|^1+$|^2+$|^3+$|^4+$|^5+$|^6+$|^7+$|^8+$|^9+$/;
                        var a = cpf.split('');
                        var b = new Number;
                        var c = 11;
                        b += (a[9] * --c);
                        if ((x = b % 11) < 2) { a[9] = 0 } else { a[9] = 11-x }
                        b = 0;
                        c = 11;
                        for (y=0; y<10; y++) b += (a[y] * c--);
                        if ((x = b % 11) < 2) { a[10] = 0; } else { a[10] = 11-x; }

                        var error = false;
                        if ((cpf.charAt(9) != a[9]) || (cpf.charAt(10) != a[10]) || cpf.match(expReg)) error = true;
                        return !error;
                    },
                    "alertText": "CPF inv�lido",
                    "alertTextOK": "CPF v�lido"
                },
                "pis": {
                    // CPF is the Brazilian ID
                    "func": function (field) {
                        var value = field.val();

                        value = value.replace('.', '');
                        value = value.replace('.', '');
                        var pis = value.replace('-', '');
//                        console.log(pis);
                        ftap="3298765432";
                        total=0;
                        resto=0;
                        numPIS=0;
                        strResto="";

                        numPIS=pis;

                        if (numPIS=="" || numPIS==null) 
                            return true;

                        for(i=0;i<=9;i++) {
                            resultado = (numPIS.slice(i,i+1))*(ftap.slice(i,i+1));
                            total=total+resultado;
                        }

                        resto = (total % 11);

                        if(resto < 2) 
                            resto = 0;

                        if (resto >= 2)
                            resto=11-resto;

                        if (resto!=(numPIS.slice(10,11)))
                            return false; 

                        return true;
                        
//                        if (ChecaPIS(pis) == false) {
//                            return false;
//                        }
                    },
                    "alertText": "PIS inv�lido",
                    "alertTextOK": "PIS v�lido"
                },
               "docsType": {
                   "regex": /png|jpg|doc|docx|pdf$/,
                   "alertText": "* Tipo do documento n�o suportado"
                },
                "auxDistancia": {
                    "alertText": "O valor deve ser 25% do sal�rio base do funcion�rio"
                }
            };
            
        }
    };

    $.validationEngineLanguage.newLang();
    
})(jQuery);