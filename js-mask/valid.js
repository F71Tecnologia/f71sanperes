$(document).ready(function() {
        $('.imprime').click(function() {
            print();
        }); 
        
        /*validacao dos campos se est�o vazios*/
        
          $("#formEdit").validate({
        rules:{

            dataRet:{
                required: true
            },

            dataAlt:{
               required: true
            },
            qt_dias:{
                required: true
            },
            status:{
                required: true
            }
        },
        // Define as mensagens de erro para cada regra
        messages:{
            dataRet:{
                required: "Campo Obrigat�rio",
            },
            dataAlt:{
                required: "Campo Obrigat�rio",
            },
            qt_dias:{
               required: "Campo Obrigat�rio",
            },
            status:{
               required: "Campo Obrigat�rio",
            }
      }
    }); 
    });