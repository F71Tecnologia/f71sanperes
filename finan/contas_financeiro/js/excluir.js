$(document).ready(function () {
    $('.btn_excluir').click(function () {
        var $this = $(this);
        var id = $this.data('id');
        bootConfirm('Tem certeza que deseja excluir este patrim�nio?', 'Excluir', function (resultado) {
            if (resultado) {
                $.post('#', {method: 'excluir', id: id}, function (data) {
                    if (data.status) {
                        bootAlert('Exclus�o realizada com sucesso!', 'Excluir', null, 'success');
                        $this.closest('tr').remove();
                    } else {
                        bootAlert('Erro ao excluir!', 'Excluir', null, 'danger');
                    }
                }, 'json');
            }
        }, 'danger');
    });
});