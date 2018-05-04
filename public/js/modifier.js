$(document).ready(function(){
    var $disabled=true;
    $(".modifier").click(function(){
        if($disabled){
            $(this).text("Vérouiller");
            $("fieldset").attr("disabled", false);
            $disabled=false;
        } else {
            $(this).text("Dévérouiller");
            $("fieldset").attr("disabled", true);
            $disabled=true;
        }
    });
});