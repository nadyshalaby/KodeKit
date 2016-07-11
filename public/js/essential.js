$(function () {
    var user_role = $('.user-role');
    user_role.on('click', function () {
        $('#role').val($(this).data('role'));
    });
    // --------------------------Trigger Error Messages Section ---------------------------
    var errorBox = $(".error-box");
    if ($.trim(errorBox.text()).length > 0) {
        errorBox.slideDown(700);
        $(document).on('click', function () {
            errorBox.slideUp(700);
        });
    }

// -------------------------- Form Validation Section ---------------------------
    var name = $('input[name=name]');
    var email = $('input[name=email]');
    var pass = $('input[id=pass]');
    var repass = $('input[name=repass]');
    var tel = $('input[name=tel]');
    var mobile = $('input[name=mobile]');
    name.on('keyup focus', function () {
        var txt = $(this).val().trim();
        var status = "";
        if (txt == '') {
            status = "اسم الشخص مطلوب";
        } else if (testRegExp(/[^a-zA-Z \u0600-\u06ff\u0750-\u077f\ufb50-\ufc3f\ufe70-\ufefc]/, txt) && txt.length >= 2) {
            status = "اسم الشخص لا يمكن ان يحتوي علي ارقام او اي رموز خاصه"
        } else {
            status = "الاسم صحيح";
        }
        $('.status').show().text(status);
    }).on('blur', function () {
        $('.status').hide();
    });
    email.on('keyup focus', function () {
        var txt = $(this).val().trim();
        var status = "";
        if (txt == '') {
            status = "الايميل مطلوب";
        } else if (!testRegExp(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/, txt)) {
            status = "صيغه الايميل غير صحيحة"
        } else {
            status = "الايميل صحيح";
        }
        $('.status').show().text(status);
    }).on('blur', function () {
        $('.status').hide();
    });
    pass.on('keyup focus', function () {
        var txt = $(this).val().trim();
        var status = "";
        if (txt == '') {
            status = "الباسورد مطلوب";
        } else if (!testRegExp(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/, txt) || txt.length < 8) {
            status = "صيغه الباسورد لابد ان يحتوي علي حرف كبير و حرف صغير و رقم و علي الاقل 8 احرف"
        } else {
            status = "الباسورد صحيح";
        }
        $('.status').show().text(status);
    }).on('blur', function () {
        $('.status').hide();
    });
    tel.on('keyup', function () {
        var txt = $(this).val().trim();
        var status = "";
        if (!testRegExp(/^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\/]?){0,})(?:[\-\.\ \\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\/]?(\d+))?$/i, txt) || txt.length < 10) {
            status = "الهاتف غير صحيح لاحظ انه لا يسمح بغير الارقام او - او+ او () او المسافات و لا يقل عن 10 رقم";
        } else {
            status = "الهاتف صحيح";
        }
        $('.status').show().text(status);
    }).on('blur', function () {
        $('.status').hide();
    });
    mobile.on('keyup focus', function () {
        var txt = $(this).val().trim();
        var status = "";
        if (txt == '') {
            status = "الموبايل مطلوب";
        } else if (!testRegExp(/^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\/]?){0,})(?:[\-\.\ \\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\/]?(\d+))?$/i, txt) || txt.length < 10) {
            status = "الموبايل غير صحيح لاحظ انه لا يسمح بغير الارقام او - او+ او () او المسافات و لا يقل عن 10 رقم"
        } else {
            status = "الموبايل صحيح";
        }
        $('.status').show().text(status);
    }).on('blur', function () {
        $('.status').hide();
    });
    repass.on('keyup focus', function () {
        var txt = $(this).val().trim();
        var status = "";
        if (txt == '') {
            status = "اعادة الباسورد مطلوبه";
        } else if (pass.val() != txt) {
            status = "اعادة الباسورد غير متطابقه"
        } else {
            status = "اعادة الباسورد متطابقه";
        }
        $('.status').show().text(status);
    }).on('blur', function () {
        $('.status').hide();
    });

    /**
     * Checks the passed pattern against the passed string
     * @param regex pattern
     * @param string str
     * @returns bool
     */
    function testRegExp(pattern, str) {
        var patt = new RegExp(pattern);
        return patt.test(str);
    }

// Read more ajax request...

    var readMore = $(".read-more");
    readMore.on('click', function (e) {
        e.preventDefault();
        var $this = $(this);
        var complainId = $this.data('complainId');
        req('http://localhost/clinic/complain/desc/' + complainId, null, function (data) {
            $('#modalLabel').text(data.diagnostic);
            var body = '<h6> <b><u>Date</u>: </b></h6><p>' + data.created_at + '</p>' + '<h6> <b><u>Status</u>: </b></h6><p>' + data.status + '</p>' + '<p><h6> <b><u>Description</u>: </b></h6></p><p>' + data.description + '</p>';
            $('#modalBody').html(body);
            $('#btn-delete').attr('data-id', data.id);
            $('#btn-delete').attr('data-role', 'complain');
            $('#myModal').modal('toggle');
        }, function () {
            alert('Error');
        });
    });
    var patient = $(".patient");
    patient.on('click', function (e) {
        e.preventDefault();
        var $this = $(this);
        var patientId = $this.data('patientId');
        req('http://localhost/clinic/patient/desc/' + patientId, null, function (data) {
            $('#modalLabel').text(data.name);
            var body = '<h6> <b><u>Email</u>: </b></h6><p>' + data.email + '</p>' +
                    '<h6> <b><u>Mobile</u>: </b></h6><p>' + data.mobile + '</p>' +
                    '<h6> <b><u>Telephone</u>: </b></h6><p>' + data.tel + '</p>' +
                    '<h6> <b><u>Address</u>: </b></h6><p>' + data.address + '</p>' +
                    '<h6> <b><u>Gender</u>: </b></h6><p>' + data.gender + '</p>' +
                    '<h6> <b><u>Profile url</u>: </b></h6><p>http://localhost/clinic/user/' + data.slug + '</p>' +
                    '<h6> <b><u>Joined since</u>: </b></h6><p>' + data.created_at + '</p>';
            $('#modalBody').html(body);
            $('#btn-delete').attr('data-id', data.id);
            $('#btn-delete').attr('data-role', 'patient');
            $('#myModal').modal('toggle');
        }, function () {
            alert('Error');
        });
    });

    var btnDelete = $("#btn-delete");
    btnDelete.on('click', function () {
        var $this = $(this);
        var role = $this.data('role');
        var id = $this.data('id');
        switch (role) {
            case 'patient':
                req('http://localhost/clinic/admin/user/delete/' + id, new FormData($('#account-form')[0]), function (data) {
                    if (data.status) {
                        location = 'http://localhost/clinic/';
                    } else {
                        alert('Error: Couldn\'t delete your self from here');
                    }
                }, function () {
                    alert('Error');
                });
                break;
            case 'complain':
                req('http://localhost/clinic/complain/delete/' + id, new FormData($('#account-form')[0]), function (data) {
                    location.reload(true);
                }, function () {
                    alert('Error');
                });
                break;
        }
    });
    var btnAdminComplainDelete = $("#admin-complain-delete");
    btnAdminComplainDelete.on('click', function () {
        req('http://localhost/clinic/admin/complain/delete', new FormData($('#admin-form')[0]), function (data) {
            alert(data);
            location.reload(true);
        }, function () {
            alert('Error');
        });
    });

    var msgDelete = $("#msg-delete");
    msgDelete.on('click', function () {
        var $this = $(this);
        var id = $this.data('id');
        req('http://localhost/clinic/message/delete/' + id, new FormData($('#account-form')[0]), function (data) {
            location.reload(true);
        }, function () {
            alert('Error');
        });

    });
    var msgSeen = $("#msg-seen");
    msgSeen.on('click', function () {
        var $this = $(this);
        var id = $this.data('id');
        req('http://localhost/clinic/message/seen/' + id, new FormData($('#account-form')[0]), function (data) {
            if (data) {
                $this.removeClass('btn-default').addClass('btn-success').attr('disabled', true);
            } else {
                alert('Error');
            }
        }, function () {
            alert('Error');
        });

    });
    var accDelete = $("#account-delete");
    accDelete.on('click', function () {
        req('http://localhost/clinic/user/delete', new FormData($('#account-form')[0]), function (data) {
            if (data.status) {
                location = 'http://localhost/clinic/';
            } else {
                alert('Error: Couldn\'t delete your account, try later');
            }
        }, function () {
            alert('Error');
        });
    });


    /**
     * Custom logging function
     * @param mixed data
     * @returns void
     */
    function _(data) {
        console.log(data);
    }

    /** 
     * Custom Ajax request function
     * @param string url
     * @param mixed|FormData data
     * @param callable(data) completeHandler
     * @param callable errorHandler
     * @param callable progressHandler
     * @returns void
     */
    function req(url, data, completeHandler, errorHandler, progressHandler) {
        $.ajax({
            url: url, //server script to process data
            type: 'POST',
            xhr: function () {  // custom xhr
                myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) { // if upload property exists
                    myXhr.upload.addEventListener('progress', progressHandler, false); // progressbar
                }
                return myXhr;
            },
            // Ajax events
            success: completeHandler,
            error: errorHandler,
            // Form data
            data: data,
            // Options to tell jQuery not to process data or worry about the content-type
            cache: false,
            contentType: false,
            processData: false
        }, 'json');

    }

    $('input[type=checkbox]').removeAttr('checked');

});