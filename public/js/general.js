$(document).ready(function () {
    $("a[href='http://www.amcharts.com']").remove();
    
    $(".collapsed.active").click(function () {
        return false;
    });

    $(".danger-action").click(function () {
        return confirm($(this).data("message").toString());
    });

    flatpickr('.datepicker', {
        enableTime: false,
        altInput: true,
        altFormat: 'j F Y',
        dateFormat: 'Y-m-d H:i:S',
        "locale": "fr"
    })
    flatpickr('.datetimepicker', {
        enableTime: true,
        altInput: true,
        altFormat: 'j F Y, H:i',
        dateFormat: 'Y-m-d H:i:S',
        "locale": "fr"
    })
});

function getCsrfSecurity()
{
    return getCookie("csrf");
}

function setCookie(sName, sValue) {
    var today = new Date(), expires = new Date();
    expires.setTime(today.getTime() + (365 * 24 * 60 * 60 * 1000));
    document.cookie = sName + "=" + encodeURIComponent(sValue) + ";expires=" + expires.toGMTString();
}

function deleteCookie(sName) {
    var today = new Date(), expires = new Date();
    expires.setTime(today.getTime() - (365 * 24 * 60 * 60 * 1000));
    var sValue=null;
    document.cookie = sName + "=" + encodeURIComponent(sValue) + ";expires=" + expires.toGMTString();
}

function getCookie(sName) {
    var oRegex = new RegExp("(?:; )?" + sName + "=([^;]*);?");
    if (oRegex.test(document.cookie)) {
        return decodeURIComponent(RegExp["$1"]);
    } else {
        return null;
    }
}


