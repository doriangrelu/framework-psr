$(document).ready(function(){
    digitRuleListenner();
    $("input").change(function () {
        var $rule=$(this).data("rule");
        if(typeof $rule !== "undefined"){
            rule($(this), $rule);
        }
    });

    function digitRuleListenner()
    {
        $("input").keypress(function (e) {
            var $rule=$(this).data("rule");
            if(typeof $rule !== "undefined"){
                if($rule==="digit"){
                    return(!isNaN(String.fromCharCode(e.which)))
                }

            }
            return true;
        })
    }

    function rule($element, $rule){
        var regex;
        switch ($rule){
            case "digit":
                regex=/[0-9]+/g;
                var matches = $($element).val().toString().match(regex);
                var value="";
                for(var match in matches) {
                    value+=matches[match];
                }
                $element.val(value);
                break;
        }
        return false;
    }

});