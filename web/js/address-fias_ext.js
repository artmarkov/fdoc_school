$(function () {
    $(document).on("click", ".address-fias-ext", function (e) {
        e.preventDefault();
        let target = $(this).data('target');
        let val = document.getElementById(target + "[fias_id]").value;
        if (val) {
            document.getElementById("spinner_" + target).classList.add("fa-spinner", "fa-pulse");
            $.ajax({
                type: "post",
                url: $(this).data('url'),
                data: {"value": " fias_id=" + val},
                success: function ($ret) {
                    document.getElementById("help_" + target).innerHTML = $ret.data ? '<span style="color: green">Результат проверки: </span>' + $ret.data : '<span style="color: red">Результаты проверки:</span> Данные не найдены...';
                    document.getElementById("spinner_" + target).classList.remove("fa-spinner", "fa-pulse");
                }
            });
        }
    });
});
