<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script>
            $(function() {
                
                
                
                $("#salario_novo").change(function(){                                                            
                    var antigo = $('#salario_antigo').val();
                    var novo = $('#salario_novo').val();
                    var total = novo - antigo;                                                            
                    //console.log(total);
                    $(".bt-image").on("click", function() {
                        $('#diferenca').html(total);
                    });
                });
                
                
                
//                function id(form1){
//                    return document.getElementById(form1);
//                }                
//                window.onload = function(){
//                    id('salario_novo').onkeyup = function(){
//                        console.log("oi");
//                        id('diferenca').value = total( this.value , id('salario_antigo').value );
//                    }
//                    id('salario_antigo').onkeyup = function(){
//                        id('diferenca').value = total( id('salario_novo').value , this.value );
//                    }
//                }
            });
        </script>
    </head>
    
    <body>
        <form action="" method="post" id="form1">
            Salário Antigo: <input type="hidden" name="salario_antigo" id="salario_antigo" value="2000" /> R$ 2.000,00 <br />
            Salário Atual: <input type="text" name="salario_novo" id="salario_novo" /> <img src="../../imagens/icones/icon-calculator.gif" title="Calcular Diferença" id="calculo_diferenca" class="edita_valor bt-image" /><br />
            Diferença: R$ <strong id="diferenca"></strong>
            <!--<input type="text" name="diferenca" id="diferenca" readonly="readonly" style="border: 0;" />-->
        </form>
    </body>
</html>