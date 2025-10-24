jQuery(function (_0x22bcx1) {
    ("use strict");
    _0x22bcx1("form#wrapped").attr("action", "./registration/");
    _0x22bcx1("#wizard_container").wizard({
        stepsWrapper: "#wrapped", submit: ".submit", beforeSelect: function (_0x22bcx4, _0x22bcx5) {
            if (_0x22bcx1("input#website").val().length != 0) {
                return false;
            }
            ;
            if (!_0x22bcx5.isMovingForward) {
                return true;
            }
            ;
            var _0x22bcx6 = _0x22bcx1(this).wizard("state").step.find(":input");
            return !_0x22bcx6.length || !!_0x22bcx6.valid();
        }
    }).validate({
        errorPlacement: function (_0x22bcx2, _0x22bcx3) {
            if (_0x22bcx3.is(":radio") || _0x22bcx3.is(":checkbox")) {
                _0x22bcx2.insertBefore(_0x22bcx3.next());
            } else {
                _0x22bcx2.insertAfter(_0x22bcx3);
            }
        },
        messages: {
            name: {
                required: "Por favor, insira seu nome."
            },
            email: {
                required: "Por favor, insira um endereço de email.",
                email: "Por favor, insira um endereço de email válido."
            },
            whatsapp: {
                required: "Por favor, insira um número de whatsapp válido."
            }
        }
    });
    _0x22bcx1("#progressbar").progressbar();
    _0x22bcx1("#wizard_container").wizard({
        afterSelect: function (_0x22bcx4, _0x22bcx5) {
            _0x22bcx1("#progressbar").progressbar("value", _0x22bcx5.percentComplete);
            _0x22bcx1("#location").text("(" + _0x22bcx5.stepsComplete + "/" + _0x22bcx5.stepsPossible + ")");
        }
    });
    _0x22bcx1("#wrapped").validate({
        ignore: [], rules: { select: { required: true }, password1: { required: true, minlength: 5 }, password2: { required: true, minlength: 5, equalTo: "#password1" } }, errorPlacement: function (_0x22bcx2, _0x22bcx3) {
            if (_0x22bcx3.is("select:hidden")) {
                _0x22bcx2.insertAfter(_0x22bcx3.next(".nice-select"));
            } else {
                _0x22bcx2.insertAfter(_0x22bcx3);
            }
        }
    });

    _0x22bcx1("form#wrapped").on('submit', function (event) {
        event.preventDefault();

        var form = _0x22bcx1(this);
        var url = form.attr('action');
        var alert = _0x22bcx1('.alert.alert-warning');
        var success = _0x22bcx1('.alert.alert-success');

        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                response = JSON.parse(response);

                let loader = $("#loader_form")

                if (response.success == false) {
                    alert.find('span').text(response.message);
                    alert.removeClass('d-none')
                    loader.fadeOut();
                } else {
                    success.find('span').text(response.message);
                    success.removeClass('d-none')
                    $("#congratulations").removeClass('d-none')
                    $("#wizard_container").hide()
                    loader.fadeOut();
                }
            },
            error: function (xhr, status, error) {
                alert.find('span').text('Ocorreu um erro: ' + error)
                alert.removeClass('d-none')
            }
        });
    });

});
function getVals(_0x22bcx8, _0x22bcx9) {
    switch (_0x22bcx9) {
        case "fullname":
            var _0x22bcxa = $(_0x22bcx8).val();
            $("#fullname").text(_0x22bcxa);
            break;
        case "whatsapp":
            var _0x22bcxa = $(_0x22bcx8).val();
            $("#whatsapp").text(_0x22bcxa);
            break;
        case "email":
            var _0x22bcxa = $(_0x22bcx8).val();
            $("#email").text(_0x22bcxa);
            break;
        case "country":
            var _0x22bcxa = $(_0x22bcx8).val();
            $("#country").text(_0x22bcxa);
            break;
        case "user_name":
            var _0x22bcxa = $(_0x22bcx8).val();
            $("#user_name").text(_0x22bcxa);
            break;
        case "password":
            var _0x22bcxa = $(_0x22bcx8).val();
            $("#password").text(_0x22bcxa);
            break;
    }
}
