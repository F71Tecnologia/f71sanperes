$(document).ready(function() {
        $('.imprime').click(function() {
            print();
        }); 
        
        /*validacao dos campos se estão vazios*/
        
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
                required: "Campo Obrigatório",
            },
            dataAlt:{
                required: "Campo Obrigatório",
            },
            qt_dias:{
               required: "Campo Obrigatório",
            },
            status:{
               required: "Campo Obrigatório",
            }
      }
    }); 
    });