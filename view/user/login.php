<div class="container content-container">
    <div class="top-of-page">
        <div class="content-heading">
            <h1 class="content-title"><?php echo $title; ?></h1>
            <div class="content-desc">Заполните эту форму, чтобы войти.</div>
        </div>
    </div>
    <div id="content-body" class="content">
        <form id="form" onsubmit="">
            <ol style="padding-left: 0">
                <div class="form-question">
                    <p class="form-error-message text-danger">
                    </p>
                </div>
                <div class="form-question">
                    <div class="item">
                        <div class="form-group">
                            <label for="loginusername" class="control-label">имя пользователя:</label>
                            <input id="loginusername" name="text" value="" class="form-control" aria-label="e-mail"
                                   aria-required="true" title="">
                            <div class="error-message"></div>
                        </div>
                    </div>
                </div>
                <div class="form-question">
                    <div class="item">
                        <div class="form-group">
                            <label for="loginpass" class="control-label">Пароль:</label>
                            <input id="loginpass" type="password" name="" value="" class="form-control"
                                   aria-label="пароль" aria-required="true" title="">
                            <div class="error-message"></div>
                        </div>
                    </div>
                </div>
                <div class="item navigate">
                    <p>
                        <input type="button" value="Войти" id="submit">
                    </p>
                </div>
            </ol>
        </form>
    </div>
</div>
<script type="text/javascript">
    var submit = function () {
        var loginusername = document.getElementById("loginusername").value;
        var password = document.getElementById("loginpass").value;
        user.authorization.login({
            page_return: '?controller=site&action=index',
            data: {
                login: loginusername,
                password: password
            },
            success: function (data) {
                //alert("Вы авторизованы!");
            },
            error: function (XHR, textStatus, errorThrown) {
                $('.form-error-message').text(textStatus);
            }
        });
    };
    document.getElementById('submit').onclick = submit;
    document.getElementById('loginpass').onkeydown = function () {
        if (event.keyCode == 13) {
            submit();
            return false;
        }
    };
</script>
