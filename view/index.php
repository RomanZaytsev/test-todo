<div class="container">
    <div class="">
        <h1>Список задач</h1>
        <div>
            <div class="well clearfix">
                <div class="pull-right">
                    <button type="button" class="btn btn-xs btn-primary" id="command-add" data-row-id="0"><span
                                class="glyphicon glyphicon-plus"></span> Создать новую задачу
                    </button>
                </div>
            </div>
            <table id="Task_grid" class="table table-condensed table-hover table-striped" width="60%" cellspacing="0"
                   data-toggle="bootgrid">
                <thead>
                <tr>
                    <th data-column-id="id" data-order="desc" data-type="numeric" data-identifier="true">id</th>
                    <th data-column-id="username">имя пользователя</th>
                    <th data-column-id="email">email</th>
                    <th data-column-id="text">текст задачи</th>
                    <th data-column-id="done" data-formatter="status">статус</th>
                    <th data-column-id="commands" data-formatter="commands" data-sortable="false">Команды</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div id="add_model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="frm_add" method="post" data-ajaxaction="?controller=task&action=post"
                  data-event="submit:ajaxFormSubmit,error:formError,success:formSuccess">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Создать новую задачу</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="username" class="control-label">имя пользователя:</label>
                        <input type="text" class="form-control" id="username" name="username"/>
                        <div class="form-inpit-error-text text-danger"></div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="control-label">email:</label>
                        <input type="text" class="form-control" id="email" name="email"/>
                        <div class="form-inpit-error-text text-danger"></div>
                    </div>
                    <div class="form-group">
                        <label for="text" class="control-label">текст задачи:</label>
                        <textarea class="form-control" id="text" name="text"></textarea>
                        <div class="form-inpit-error-text text-danger"></div>
                    </div>
                    <div class="form-group">
                        <p class="form-status error text-danger">
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    <button id="btn_add" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="edit_model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать задачу</h4>
            </div>
            <form id="frm_edit" method="post" data-ajaxaction="?controller=task&action=update"
                  data-event="submit:ajaxFormSubmit,error:formError,success:formSuccess">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id" value="0">
                    <div class="form-group">
                        <label for="username" class="control-label">имя пользователя:</label>
                        <input <?php echo(@$user->name != "admin" ? "disabled" : ""); ?> type="text"
                                                                                         class="form-control"
                                                                                         id="edit_username"
                                                                                         name="username"/>
                        <div class="form-inpit-error-text text-danger"></div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="control-label">email:</label>
                        <input <?php echo(@$user->name != "admin" ? "disabled" : ""); ?> type="text"
                                                                                         class="form-control"
                                                                                         id="edit_email" name="email"/>
                        <div class="form-inpit-error-text text-danger"></div>
                    </div>
                    <div class="form-group">
                        <label for="text" class="control-label">текст задачи:</label>
                        <textarea <?php echo(@$user->name != "admin" ? "disabled" : ""); ?> class="form-control"
                                                                                            id="edit_text"
                                                                                            name="text"></textarea>
                        <div class="form-inpit-error-text text-danger"></div>
                    </div>
                    <?php if (@$user->name == "admin"): ?>
                        <div class="form-group">
                            <label for="status" class="control-label">Выполнена:</label>
                            <input type="checkbox" class="form-control" name="done" value="0" style="display:none;"
                                   checked="checked"/>
                            <input type="checkbox" class="form-control" id="edit_done" name="done" value="1"/>
                            <div class="form-inpit-error-text text-danger"></div>
                        </div>
                        <div class="form-group">
                            <p class="form-status error text-danger">
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    <?php if (@$user->name == "admin"): ?>
                        <button id="btn_edit" class="btn btn-primary">Сохранить</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="success_model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content alert alert-success">
            <div>Задача сохранена
                <div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            var griddata = [];
            var grid = null;
            $(document).ready(function () {
                grid = $("#Task_grid").bootgrid({
                    ajax: true,
                    rowSelect: true,
                    rowCount: 3,
                    responseHandler: function(data) {
                        data.current = data.current * 1;
                        data.rowCount = data.rowCount * 1;
                        data.total = data.total * 1;
                        var updatedUrl = MyTools.updateURLParameter(window.location.href, 'page', data.current);
                        history.pushState({'title': document.title, 'page': data.current}, null, updatedUrl);
                        return data; //must return a response object so your grid has records
                    },
                    post: function () {
                        return {
                            id: "bootgrid"
                        };
                    },
                    url: "?controller=task&action=list",
                    formatters: {
                        "commands": function (column, row) {
                            var buttons = "<button type=\"button\" class=\"btn btn-xs btn-default command-edit\" data-row-id=\"" + row.id + "\"><span class=\"glyphicon glyphicon-edit\"></span></button>";
                            <?php if (@$user->name == "admin"): ?>
                            buttons +="<button type=\"button\" class=\"btn btn-xs btn-default command-delete\" data-row-id=\"" + row.id + "\"><span class=\"glyphicon glyphicon-trash\"></span></button>";
                            <?php endif; ?>
                            return buttons;
                        },
                        "status": function (column, row) {
                            griddata[row.id] = row;
                            var result = [];
                            if (row.done == 1) result.push("Выполнено");
                            if (row.checked == 1) result.push("Отредактировано<br/>администратором");
                            return result.join(',<br/>');
                        }
                    }
                })
                .on('loaded.rs.jquery.bootgrid', function () {
                    grid.find(".command-edit").on("click", function (e) {
                        var ele = $(this).parent();
                        var g_id = $(this).parent().siblings(':first').html();
                        var g_name = $(this).parent().siblings(':nth-of-type(2)').html();

                        $('#edit_model').find('.has-error').removeClass("has-error");
                        $('#edit_model').find('.has-success').removeClass("has-success");
                        $('#edit_model').find('.form-inpit-error-text').text('');
                        $('#edit_model').modal('show');
                        if ($(this).data("row-id") > 0) {
                            $('#edit_id').val(ele.siblings(':first').html()); // in case we're changing the key
                            $('#edit_username').val(ele.siblings(':nth-of-type(2)').html());
                            $('#edit_email').val(ele.siblings(':nth-of-type(3)').html());
                            $('#edit_text').val(ele.siblings(':nth-of-type(4)').html());
                            console.log(griddata[g_id].done);
                            if (griddata[g_id].done == 1) $('#edit_done').prop('checked', 'checked');
                            else $('#edit_done').removeAttr('checked');
                        } else {
                            alert('Now row selected! First select row, then click edit button');
                        }
                    }).end().find(".command-delete").on("click", function (e) {
                        var conf = confirm('Delete ' + $(this).data("row-id") + ' items?');
                        if (conf) {
                            tasks.delete({ data: { id: $(this).data("row-id") }});
                        }
                    });
                });

                $("#command-add").click(function () {
                    $('#add_model').find('input, textarea, select').val('');
                    $('.form-inpit-error-text').text('');
                    $('#add_model').find('.form-group').removeClass('has-error');
                    $('#add_model').find('.form-group').removeClass('has-success');
                    $('#add_model').modal('show');
                });
            });

            window.onpopstate = function(e) {
                console.log([e.state.page, $('#Task_grid').bootgrid('getCurrentPage')]);
                if(e.state.page != $('#Task_grid').bootgrid('getCurrentPage')) {
                    $('#Task_grid').bootgrid('reload');
                }
            }

            function formError(e) {
                $(e.target).find(".form-status").text(e.status);
            }

            function formSuccess(e) {
                $('#add_model').modal('hide');
                $('#edit_model').modal('hide');
                $('#success_model').modal('show');
                $('#Task_grid').bootgrid('reload');
                setTimeout(function () {
                    $('#success_model').modal('hide');
                }, 1000);
            }
        </script>
    </div>
</div>