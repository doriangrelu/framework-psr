Form={
    multiSelect:function(idForm, firstList, lastList){
        $(idForm).submit(function () {

        });
    },

    validForm:function(idForm, inputs){
        var $formulaire=$(idForm);
        $formulaire.submit(function (e) {
            e.preventDefault();

            return false;
        });
    }

};