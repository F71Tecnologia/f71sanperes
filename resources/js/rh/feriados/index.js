$(function () {
    $(".bt-image").on("click", function () {
        var action = $(this).data("type");
        var key = $(this).data("key");
        var emp = $(this).parents("tr").find("td:first").next().html();

        if (action === "editar") {
            $("#feriado").val(key);
            $("#form1").attr('action', 'form_feriado_novo.php');
            $("#form1").submit();

        } else if (action === "excluir") {
            bootConfirm("Voc� deseja realmente excluir este feriado?", "Exclus�o de Feriado", function (confirm) {
                if (confirm) {
                    if (confirm == true) {
                        $("#" + key).remove();
                        $.post("del_feriado_novo.php", {id: key}, function (data) {
                            console.log(data.return);
                            var msg = ((data.return === true) ? "Feriado excluido com sucesso!" : "Erro ao excluir feriado");
                            var type = ((data.return === true) ? "success" : "danger");
                            bootAlert(msg, "Exclus�o de Feriado", null, type);
                        }, "json");
                    }
                }
            }, 'danger');
        }
    });

    $("#novoFeriado").click(function () {
        $("#form1").attr('action', 'form_feriado_novo.php');
        $("#form1").submit();
    });
});