$(document).ready(function () {

    $("input").keyup(function () {
        var status = checkField($(this));
        renderHtml($(this), status, false);
    });

    $("input").change(function () {
        var status = checkField($(this));
        renderHtml($(this), status, false);
    });

    $("form").submit(function () {
        var notCheckValueParamData = "not-check";
        if (typeof $(this).data(notCheckValueParamData) !== "undefined" && Boolean($(this).data(notCheckValueParamData)) === true) {
            return true;
        }
        var $canSubmitForm = true;
        $(this).find("input").each(function () {
            var status = checkField($(this));
            renderHtml($(this), status, true);
            if (!status) {
                $canSubmitForm = false;
                return false;
            }
        });
        return $canSubmitForm;
    });
});

function renderHtml($element, status, showAlert) {
    if ($element.hasClass("datepicker")) {
        $element = $element.next();
    }
    var $errorRenderer = $element.parent().find(".error-data-entry");
    var required = typeof $element.data("required") !== "undefined";
    var classFormControlDanger = "form-control-danger";
    var classFormControlSuccess = "form-control-success";
    $element.removeClass(classFormControlDanger);
    $element.removeClass(classFormControlSuccess);
    if (!status) {
        $element.addClass(classFormControlDanger);
        if (showAlert) {
            //swal("Erreur", $errorRenderer.html(), "error");
        }
        $element.focus();

    } else {
        if (required) {
            $element.addClass(classFormControlSuccess);
        }
    }
}

function checkField($element) {
    var required=false;
    if(typeof $element.data("required") !== "undefined" && $element.data("required")===true){
        required=true;
    }
    var pattern = getRules($element);
    if (pattern !== null) {
        if(required || $element.val()!==""){
            return checkValue(pattern, $element.val(), required);
        }
    } else {
        if(required) {
            return $element.val() !== "";
        }
    }
    return true;
}

function getRules($element) {
    var rules = {
        "email": /[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}/,
        "code-postal": /[0-9]{5}/,
        "telephone": /(0|\+33|0033)[1-9][0-9]{8}/
    };
    var $type = $element.data("type");
    if (typeof rules[$type] !== "undefined") {
        return rules[$type];
    } else {
        var pattern = $element.data("pattern");
        if (typeof pattern !== "undefined") {
            var regex= new RegExp(pattern);
            return regex;
        }
    }
    return null;
}

function checkValue(reggex, value, required) {
    if (required) {
        return reggex.test(value);
    } else {
        if (value.length > 0) {
            return reggex.test(value);
        }
    }
    return true;
}