<div class="container content-container">
    <div class="top-of-page">
        <div class="content-heading">
            <h1 class="content-title"><?php echo $title; ?></h1>
            <div class="content-desc">Заполните эту форму, чтобы зарегистрироваться.</div>
            <div class="required-asterisk">Звёздочкой отмечены обязательные поля!</div>
        </div>
    </div>
    <div id="content-body" class="content">
        <form action="?controller=user&action=regform" method="POST" id="form" onsubmit="">
            <ol style="padding-left: 0">
                <div class="form-question">
                    <div class="item">
                        <div class="form-entry">
                            <label aria-hidden="true" class="q-item-label">
                                <div class="q-title">Ваше имя
                                    <label aria-label="Обязательное поле"></label>
                                    <span class="required-asterisk">*</span></div>
                                <div class="q-help"></div>
                            </label>
                            <input id="regformname" type="text" name="mynameis" value="" class="q-short"
                                   aria-label="Ваше имя" aria-required="true" title="">
                            <div class="error-message"></div>
                        </div>
                    </div>
                </div>
                <div class="form-question">
                    <div class="item">
                        <div class="form-entry">
                            <label aria-hidden="true" class="q-item-label">
                                <div class="q-title">e-mail
                                    <label aria-label="Обязательное поле"></label>
                                    <span class="required-asterisk">*</span></div>
                                <div class="q-help"></div>
                            </label>
                            <input id="regformemail" type="text" name="email" value="" class="q-short"
                                   aria-label="e-mail" aria-required="true" title="">
                            <div class="error-message"></div>
                        </div>
                    </div>
                </div>
                <div class="form-question">
                    <div class="item">
                        <div class="form-entry">
                            <label aria-hidden="true" class="q-item-label">
                                <div class="q-title">пароль
                                    <label aria-label="Обязательное поле"></label>
                                    <span class="required-asterisk">*</span></div>
                                <div class="q-help"></div>
                            </label>
                            <input id="regformpass" type="text" name="passwd" value="" class="q-short"
                                   aria-label="пароль" aria-required="true" title="">
                            <div class="error-message"></div>
                        </div>
                    </div>
                </div>
                <div class="item navigate">
                    <p>
                        <input type="button" name="submit" value="Готово" id="submit">
                    </p>
                </div>
            </ol>
        </form>
    </div>
</div>
<script type="text/javascript">
    var submit = function () {
        var name = document.getElementById("regformname").value;
        var email = document.getElementById("regformemail").value;
        var pass = document.getElementById("regformpass").value;
        // Передаем пароль в открытом виде для серверного хэширования
        user.account.create({
            data: {
                name: name,
                email: email,
                password: pass
            },
            success: function (data) {
                user.authorization.login({
                    page_return: '?controller=site&action=index',
                    data: {
                        login: name,
                        password: pass
                    },
                    success: function (data) {
                        //alert("Вы авторизованы!");
                    },
                    error: function (XHR, textStatus, errorThrown) {
                        alert(textStatus);
                    }
                });
            },
            error: function (XHR, textStatus, errorThrown) {
                alert(textStatus);
            }
        });
    };
    document.getElementById("submit").onclick = submit;
    document.getElementById('regformpass').onkeydown = function () {
        if (event.keyCode == 13) {
            submit();
            return false;
        }
    };
</script>
