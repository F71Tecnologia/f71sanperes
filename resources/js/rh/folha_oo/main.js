$(function() {
    $('#buscarFinalizadas').click(function() {
        var ano = $("#anoFinalizada").val();
        var projeto = $("#projetoFinal").val();
        $.post("", {method: "getFolhasFinalizadas", ano: ano, projeto: projeto}, function(data) {
            $("#conteudo-finalizado").html(data);
            
        }, "html");
    });
    
    /**
     * CRIAR FOLHA
     * @returns {undefined}
     */
    $("body").on("click","#gerarFolha",function(){
        
        var projeto = $("#projetoNovo").val();
        var mes = $("#mesNovo").val();
        var ano = $("#anoNovo").val();
        var dataInicio = $("#inicioNovo").val();
        var decimoTerceiro = $("#terceiro:checked").val();
        var tipoDecimo = 0;
        
        if(decimoTerceiro == 1){
            tipoDecimo = $("#tipoDecimo").val();
        }
        
        $.blockUI({
            message: "Gerando...",
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .5,
                color: '#fff'
            }
        });
        
        $.post("", {method:"gerarFolha",projeto:projeto,mes:mes,ano:ano,dataInicio:dataInicio,terceiro:decimoTerceiro,tipoTerceiro:tipoDecimo}, function(data){
            
            if(data == "sucesso"){
               location.href="index.php";
            }else{
                $.unblockUI();
                alert("Problemas na geração da folha, entre em contato com o administrador: "+data);
            }
            
        },"html");
    });
    
    /**
     * VISUALIZAR FOLHA ABERTA
     * @returns {undefined}
     */
    $("body").on("click",".visualizar",function(){
        
        var folha = $(this).data("key");
         
        $("input[name='folha']").val(folha);
        $("form[id='formVisualizarFolha']").attr({action:"folha.php"}).submit();
        
    }); 
    
    
    /**
     * EXCLUIR FOLHA ABERTA
     */
    $("body").on("click",".excluir",function(){
        var folha = $(this).data("key");
        var projeto = $(this).data("projeto");
        bootConfirm("Deseja realmente Excluir permanentemente essa folha?", "Excluir", function(data){
            if(data === true){
                $.post("", {method: "excluirFolha", folha: folha, projeto:projeto}, function (data) {
                    if (data.status) {
                        $("tr[data-key='" + folha + "']").hide();
                    }
                }, "json");
            }
        },'danger');        
    });
    
    /**
     * VER FOLHA FINALIZADA E DESPROCESSAR FOLHA FINALIZADA
     */
    $("body").on("click",".btAct",function(){
        var $this = $(this);
        var acao = $this.data('type');
        var idFolha = $this.data('key');
        
        if(acao === 'ver'){
            
            $("input[name='folha']").val(idFolha);
            $("form[id='formVisualizarFolha']").attr({action:"folha.php"}).submit();
            
        }else if(acao === 'des'){
            
            bootConfirm("Deseja realmente Desprocessar essa folha?", "Desprocessar", function(data){
                if(data == true){
                    $this.addClass("hidden");
                    $('#loading'+idFolha).removeClass("hidden");
                    $.post("",{method:"desprocessarFolha",folha:idFolha},function(re){
                        if(re.status===1){
                            window.location = '';
                        }else{
                            alert('Ocorreu um problema no desprocessamento da folha');
                        }
                    },'json');
                        
                }
            },'danger');
            
        }
    });
    
    /**
     * FILTRO DA FOLHA NOVA
     */
    $('.totais').not('.totais-mostrar_todos').hide();
    $('body').on('click', '.legenda', function(){
        $this = $(this);
        $('.destaque, .totais').hide();
        if($this.data('key') == 'mostrar_todos'){
            $('.destaque, .totais-mostrar_todos').show();
        } else if($('.legenda-clt-'+$this.data('key')).length){
            $('.legenda-clt-' + $this.data('key')).parent().parent().parent().show();
            $('.totais-' + $this.data('key')).show();
        } 
//                    else {
//                        alert('É só jogar essa classe .legenda-clt-'+$this.data('key')+' na div do status');
//                    }
    });
});
