$(document).ready(function () {
  
    $('body').on('click', '.des_travar', function(){
        var $this   = $(this);
//        var titulo = 'MODULO CONTABIL - DESTRAVAR LANCAMENTOS';
        var id_trava = $this.data('id_trava');
        console.log(id_trava);
        bootConfirm("Tem certeza que deseja destravar periodo ?", 'Destravando...', function (confirm) {
            if (confirm) {
                $.post('methods_trava.php', {method: 'destravar', id_trava: id_trava}, function (data) {
                    var status = (data.status) ? "sucess" : "danger";
                    if(data.status){
                        
                    }
                    bootAlert(data.msg, 'Destravando...', null, status);
                }, 'json');
            }
        }, "danger");        
    });

    $('body').on('click', '.travar', function(){
        var $this   = $(this);
        var travar_periodo = $this.data('travar_periodo');
        var travar_projeto = $this.data('travar_projeto');
        bootConfirm("Tem certeza que deseja travar periodo ?", 'Travando...', function (confirm) {
            if (confirm) {
                $.post('methods_trava.php', {method: 'travar', travar_periodo: travar_periodo, travar_projeto: travar_projeto}, function (data) {
                    var status = (data.status) ? "sucess" : "danger";
                    if(data.status){
                        
                    }
                    bootAlert(data.msg, 'Trava efetuada...', null, status);
                }, 'json');
            }
        }, "info");        
    });
        
    
});