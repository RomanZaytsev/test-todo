function setEventHandler() {
    var elements = document.querySelectorAll("*[data-event]");
    for (var i = elements.length - 1; i >= 0; i--) {
        try {
            var res = eval("(function() { return {" + elements[i].dataset.event + "}; })()");
            for (var j in res) {
                elements[i].addEventListener(j, res[j]);
            }
            elements[i].removeAttribute("data-event");
        } catch (e) {
            console.error(e);
        }
    }
}

function ajaxFormOpenLink(e) {
    e.preventDefault();
    var form = e.target;
    var url = form.dataset.ajaxaction ? window.location.protocol + "//" + window.location.host + form.dataset.ajaxaction : form.action;
    url += "?";
    var data = MyTools.serialize(form);
    for (var i in data) {
        url += i + "=" + data[i] + "&";
    }
    url = MyTools.trim(url, "&");
    openLink(url, true);
}

function ajaxFormSubmit(e) {
    e.preventDefault();
    if (this.inprocess) return false;
    var form = e.target;
    var formdata = new FormData(form);
    var self = this;
    this.inprocess = true;
    setTimeout(function () {
        self.inprocess = false;
    }, 1000);
    var url = form.dataset.ajaxaction ? form.dataset.ajaxaction : (form.action.length > 0 ? form.action : window.location.href);
    var set = {
        url: url,
        data: formdata,
        processData: false,
        contentType: false,
        method: form.getAttribute('method'),
        dataType: 'json',
        success: function (data, textStatus, XHR) {
            $(form).find("input").parent().removeClass("has-error")
            $(form).find(".form-error-text").text('');
            for (i in data.validate) {
                if (data.validate[i] === true) {
                    $(form).find("*[name='" + i + "']").parent().removeClass("has-error").addClass("has-success");
                    $(form).find("*[name='" + i + "']").parent().find(".form-inpit-error-text").text('');
                } else {
                    $(form).find("*[name='" + i + "']").parent().removeClass("has-success").addClass("has-error");
                    $(form).find("*[name='" + i + "']").parent().find(".form-inpit-error-text").text(data.validate[i]);
                }
            }
            if (data.redirect == 'history.back') {
                window.history.back();
            } else {
                if (data.redirect != undefined) window.location.href = data.redirect;

                if (data.status == "OK") {
                    MyTools.event_personal.fireEvent(form, "success", data);
                } else {
                    this.error(XHR, data, null);
                }
            }
        },
        error: function (XHR, data, errorThrown) {
            MyTools.event_personal.fireEvent(form, "error", data);
        },
        complete: function () {
            MyTools.event_personal.fireEvent(form, "complete");
        }
    };
    $.ajax(set);
}

document.addEventListener("DOMContentLoaded", function () {
    setEventHandler();
});

function showinfopopup(e) {
    if (this.classList.contains('show')) {
        this.classList.remove('show');
    } else {
        this.classList.add('show');
    }
}

function showdetails(e, details) {
    var details = $(e.target.dataset.details)[0];
    if (details.classList.contains('show')) {
        details.classList.remove('show');
        e.target.classList.remove('show');
        if ($('.result-details-container.show').length == 1) {
            $('.email-container')[0].classList.remove('show');
        }
    } else {
        details.classList.add('show');
        e.target.classList.add('show');
        $('.email-container')[0].classList.add('show');
    }
}

function cloneRow(rowId) {
    var row = document.getElementById(rowId); // find row to copy
    var clone = row.cloneNode(true); // copy children too
    clone.id = ""; // change id or other attributes/contents
    row.parentElement.appendChild(clone); // add new row to end of table
}

function isIOS() {
    return (/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream) || (!!navigator.platform && /iPad|iPhone|iPod/.test(navigator.platform));
}
