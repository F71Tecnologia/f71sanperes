

(function(jQuery) {
    jQuery.fn.validationEngineLanguage = function() {};
    jQuery.validationEngineLanguage = {
        newLang: function() {
            jQuery.validationEngineLanguage.allRules = 	{
                "required":{    			// Add your regex rules here, you can take telephone as an example
                    "regex":"none",
                    "alertText":"* Este campo � obrigat�rio",
                    "alertTextCheckboxMultiple":"Por favor selecione uma op��o",
                    "alertTextCheckboxe":"* Esta op��o � obrigat�ria"
                },
                "length":{
                    "regex":"none",
                    "alertText":"*Between ",
                    "alertText2":" e ",
                    "alertText3": " characters allowed"
                },
                "maxCheckbox":{
                    "regex":"none",
                    "alertText":"* Checks allowed Exceeded"
                },
                "minCheckbox":{
                    "regex":"none",
                    "alertText":"* Por favor selecione ",
                    "alertText2":" op��es"
                },
                "confirm":{
                    "regex":"none",
                    "alertText":"* Your field is not matching"
                },
                "telephone":{
                    "regex":"/^[0-9\-\(\)\ ]+$/",
                    "alertText":"N�mero de telefone inv�lido"
                },
                "email":{
                    "regex":"/^[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4}$/",
                    "alertText":"E-mail inv�lido"
                },
                "date":{
                    "regex":"/^[0-9]{4}\-\[0-9]{1,2}\-\[0-9]{1,2}$/",
                    "alertText":"* Invalid date, must be in YYYY-MM-DD format"
                },
                "dateBr":{
                    "regex":"/^(0[1-9]|[1,2][0-9]|3[0,1])\\/(0[1-9]|1[0,1,2])\\/([1-5]{1})([0-9]{3})$/",
                    "alertText":"* Data inv�lida"
                },
                "onlyNumber":{
                    "regex":"/^[0-9\ ]+$/",
                    "alertText":"* Apenas n�meros"
                },
                "noSpecialCaracters":{
                    "regex":"/^[0-9a-zA-Z]+$/",
                    "alertText":"Caracteres especiais n�o s�o permitidos"
                },
                "ajaxUser":{
                    "file":"validateUser.php",
                    "extraData":"name=eric",
                    "alertTextOk":"* This user is available",
                    "alertTextLoad":"* Loading, please wait",
                    "alertText":"* This user is already taken"
                },
                "ajaxName":{
                    "file":"validateUser.php",
                    "alertText":"* This name is already taken",
                    "alertTextOk":"* This name is available",
                    "alertTextLoad":"* Loading, please wait"
                },
                "onlyLetter":{
                    "regex":"/^[a-zA-Z\ \']+$/",
                    "alertText":"Apenas letras"
                },
                "select":{
                    "regex":"/^[^-1]|^1/",
                    "alertText":"Selecione uma op��o v�lida"
                },
                "auxDistance": {
                    "alertText": "O valor deve ser 25% do sal�rio base do funcion�rio"
                },
                "min": {
                "regex": "none",
                "alertText": "* Valor m�nimo � "
                },
                "max": {
                    "regex": "none",
                    "alertText": "* Valor m�ximo � "
                }
                
                /*,
                "timeFormat":{
                    "regex": /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/,
                    "alertText":"* Hora Inv�lida"}                           
                }*/
            }
            
        }
    }
})(jQuery);

jQuery(document).ready(function() {	
    jQuery.validationEngineLanguage.newLang()
});