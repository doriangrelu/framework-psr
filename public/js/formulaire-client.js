$(document).ready(function(){
    var $selectorTypeClient=$("#clientType");
    var $boutonNouveauClient=$("#bouton-nouveau-client");
    $boutonNouveauClient.attr("disabled", true);
    changeFormType($selectorTypeClient.val());
    $selectorTypeClient.change(function(){
        changeFormType($(this).val());
    });

});

function changeFormType(type)
{
    var $boutonNouveauClient=$("#bouton-nouveau-client");
    var $particuluier=$("#form-part");
    var $professionnel=$("#form-pro");
    var $target=$("#body-form-client");
    $target.empty();
    if(type==="pro"){
        $target.html($professionnel.html());
        $boutonNouveauClient.attr("disabled", false);
    } else {
        if(type==="part"){
            $target.html($particuluier.html());
            $boutonNouveauClient.attr("disabled", false);
        } else {
            $boutonNouveauClient.attr("disabled", true);
        }
    }
}