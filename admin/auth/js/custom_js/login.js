function onSubmit(token) { document.getElementById("authentication").submit(); }
"use strict";
$(document).ready(function () {
    //=================Preloader===========//
    $(window).on('load', function () {
        $('.preloader img').fadeOut();
        $('.preloader').fadeOut();
    });
    //=================end of Preloader===========//
    $('input').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue',
        increaseArea: '20%' // optional
    });
    $("#authentication").bootstrapValidator({
        fields: {
            student_id: {
                validators: {
                    notEmpty: {
                        message: 'Student ID is required'
                    }
                }
            }, username: {
                validators: {
                    notEmpty: {
                        message: 'The username is required'
                    }
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: 'Password is required'
                    }

                }
            }

        }
    }).on('success.form.bv', function (e) {
        //e.preventDefault();
        //grecaptcha.execute();
    });
});