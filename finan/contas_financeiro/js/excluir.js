$(document).ready(function () {
    $('.btn_excluir').click(function () {
        var $this = $(this);
        var id = $this.data('id');
        bootConfirm('Tem certeza que deseja excluir este patrimônio?', 'Excluir', function (resultado) {
            if (resultado) {
                $.post('#', {method: 'excluir', id: id}, function (data) {
                    if (data.status) {
                        bootAlert('Exclusão realizada com sucesso!', 'Excluir', null, 'success');
                        $this.closest('tr').remove();
                    } else {
                        bootAlert('Erro ao excluir!', 'Excluir', null, 'danger');
                    }
                }, 'json');
            }
        }, 'danger');
    });
});