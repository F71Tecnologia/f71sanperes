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
            bootConfirm("Você deseja realmente excluir este feriado?", "Exclusão de Feriado", function (confirm) {
                if (confirm) {
                    if (confirm == true) {
                        $("#" + key).remove();
                        $.post("del_feriado_novo.php", {id: key}, function (data) {
                            console.log(data.return);
                            var msg = ((data.return === true) ? "Feriado excluido com sucesso!" : "Erro ao excluir feriado");
                            var type = ((data.return === true) ? "success" : "danger");
                            bootAlert(msg, "Exclusão de Feriado", null, type);
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