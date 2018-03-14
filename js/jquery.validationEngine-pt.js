

(function(jQuery) {
    jQuery.fn.validationEngineLanguage = function() {};
    jQuery.validationEngineLanguage = {
        newLang: function() {
            jQuery.validationEngineLanguage.allRules = 	{
                "required":{    			// Add your regex rules here, you can take telephone as an example
                    "regex":"none",
                    "alertText":"* Este campo é obrigatório",
                    "alertTextCheckboxMultiple":"Por favor selecione uma opção",
                    "alertTextCheckboxe":"* Esta opção é obrigatória"
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
                    "alertText2":" opções"
                },
                "confirm":{
                    "regex":"none",
                    "alertText":"* Your field is not matching"
                },
                "telephone":{
                    "regex":"/^[0-9\-\(\)\ ]+$/",
                    "alertText":"Número de telefone inválido"
                },
                "email":{
                    "regex":"/^[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4}$/",
                    "alertText":"E-mail inválido"
                },
                "date":{
                    "regex":"/^[0-9]{4}\-\[0-9]{1,2}\-\[0-9]{1,2}$/",
                    "alertText":"* Invalid date, must be in YYYY-MM-DD format"
                },
                "dateBr":{
                    "regex":"/^(0[1-9]|[1,2][0-9]|3[0,1])\\/(0[1-9]|1[0,1,2])\\/([1-5]{1})([0-9]{3})$/",
                    "alertText":"* Data inválida"
                },
                "onlyNumber":{
                    "regex":"/^[0-9\ ]+$/",
                    "alertText":"* Apenas números"
                },
                "noSpecialCaracters":{
                    "regex":"/^[0-9a-zA-Z]+$/",
                    "alertText":"Caracteres especiais não são permitidos"
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
                    "alertText":"Selecione uma opção válida"
                },
                "auxDistance": {
                    "alertText": "O valor deve ser 25% do salário base do funcionário"
                },
                "min": {
                "regex": "none",
                "alertText": "* Valor mínimo é "
                },
                "max": {
                    "regex": "none",
                    "alertText": "* Valor máximo é "
                }
                
                /*,
                "timeFormat":{
                    "regex": /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/,
                    "alertText":"* Hora Inválida"}                           
                }*/
            }
            
        }
    }
})(jQuery);

jQuery(document).ready(function() {	
    jQuery.validationEngineLanguage.newLang()
});