$(document).ready(function () {
    var $size = $('.lines>tr.lineQuotation').length;
    var $nbLines = $size;
    var $counterLines=$size+1;
    var $counter = getNbLinesAcomptes() + 1;
    var $tbody = $(".lines");
    var $lines = $("#template");
    var $payments = $("#templateAcompte");
    var $btnPayments = $(".addPayments");

    checkDateDisabled();

    $("body").on("change", "#validity_deadline", function () {
        verifierDates();
        checkDateDisabled()
    });

    $("body").on("change", "#deadline", function () {
        verifierDates();
        checkDateDisabled();
    });
    var $deadLineSelect = $("#deadline").parent().find(".datepicker").val(null);
    $("body").on("click", $deadLineSelect, function () {
        checkDateDisabled()
    });

    if (typeof $(".unmodifiable") !== "undefined") {
        afficherBoutonAcompte($btnPayments);
        actualiserRemises();
        calculerPrixDeslignes();

        $("body").on("change", "input", function () {
            actualiserRemises();
            calculerPrixDeslignes();
            calculerMontantAcomptes();
            $nbLines = nbLines();
        });
        $("body").on("change", "select", function () {
            actualiserRemises();
            calculerPrixDeslignes();
            calculerMontantAcomptes();
            $nbLines = nbLines();
        });

        $(".btn-reload").click(function () {
            if (confirm("Voulez vous vraiment annuler les modifications ?")) {
                location.reload(true)
            }
        });

        $("body").on("click", ".deleteLineAcompte", function () {
            $(this).parent().parent().remove();
            setAcompteRank();
        });

        $("body").on("click", ".deleteLine", function () {
            $(this).parent().parent().remove();
            $nbLines--;
            actualiserRemises();
            calculerPrixDeslignes();
            calculerMontantAcomptes();
            afficherBoutonAcompte($btnPayments);
            $nbLines = nbLines();

        });

        $("body").on("change","input", function () {
            //checkForm($nbLines, false);
        });
        $("body").on("change","textarea", function () {
            //checkForm($nbLines, false);
        });

        $(".creationDevis").submit(function () {
            return checkForm($nbLines, true);
        });

        $btnPayments.click(function () {
            if (getTotalAcomptes() < 90) {
                var $html = $payments.html();
                $html = $html.toString().replace(/:id/g, $counter.toString());
                $tbody.append($html);
                setAcompteRank();
                $counter++;
            } else {
                swal("Erreur", "La somme totale des acomptes est déjà égale à 90% du devis, vous ne pouvez donc pas en ajouter d'avantages", "error");
            }
            $(".amountField").change(function () {
                calculerMontantAcomptes();
                if (getTotalAcomptes() > 90) {
                    $(this).val(0);
                    swal("Erreur", "Le total du montant des acompte dépasse les 90%", "error");
                }
            });

        });

        //Nouvelle ligne
        $(".addLine").click(function () {
            var $html = $lines.html();
            $html = $html.toString().replace(/:id/g, $counterLines.toString());
            $(".linesDevisNotPayments").before($html);
            $counter++;
            $nbLines++;
            $counterLines++;
            afficherBoutonAcompte($btnPayments);
        });
    }

});

function checkForm($nbLines, lines)
{
    var status = true;
    $(".field").each(function () {
        status = checkField($(this));
        renderHtml($(this), status, true);
        if (!status) {
            return false;
        }
    });
    if (!status) {
        return false;
    }
    var retour = false;
    if ($nbLines > 0) {
        retour = true;
    } else {
        if(lines){
            swal("Erreur", "Le devis doit contenir au moins une ligne...", "error");
        }
    }
    return retour;
}

function checkDateDisabled() {
    var $deadLineSelect = $("#deadline").parent().find(".datepicker").val(null);
    var $parent = $deadLineSelect.parent();
    if ($("#validity_deadline").val() !== "") {
        $deadLineSelect.attr("disabled", false);
        $parent.show("fast");
    } else {
        $deadLineSelect.attr("disabled", true);
        $deadLineSelect.val(null);
        $parent.hide("fast");
    }
}

function verifierDates() {
    var date = new Date().getTime();
    var $dateFin = $("#validity_deadline");
    var dateField = new Date($dateFin.val()).getTime();
    if (dateField > date) {
        $dateFin.parent().find(".datepicker").css("borderColor", "green");
    } else {
        $dateFin.val("");
        $dateFin.parent().find(".datepicker").val(null);
        $dateFin.parent().find(".datepicker").css("borderColor", "red");
        swal("Erreur", "La date de fin de validité ne peut être antérieure ou égale à aujourd'hui", "error");
        return;
    }
    var $deadLine = $("#deadline");
    dateField = new Date($deadLine.val()).getTime();
    var dateFieldOffre = new Date($dateFin.val()).getTime();
    if ($deadLine.val() !== "") {
        if (!isNaN(dateFieldOffre) && dateField > dateFieldOffre) {
            $deadLine.parent().find(".datepicker").css("borderColor", "green");
        } else {
            $deadLine.val("");
            $deadLine.parent().find(".datepicker").val(null);
            swal("Erreur", "La date de fin de projet doit être supérieure à la date de début de projet", "error");
        }
    } else {
        $deadLine.parent().find(".datepicker").css("borderColor", "grey");
    }

}

function getNbLinesAcomptes() {
    return $(".paymentsLines").length;
}

function getTotalAcomptes() {
    var total = 0;
    $('.paymentsLines').each(function () {
        var valeur = $(this).find(".amount>input").val();
        if (!isNaN(valeur)) {
            total += Number(valeur);
        }
    });
    return total;
}

function setAcompteRank() {
    var nb = 1;
    $('.paymentsLines').each(function () {
        var value = 'Acompte N°' + nb + ' (en %)';
        value = value.toString().replace(/:rank/g, nb.toString());
        $(this).find(".libelle").html(value);
        $(this).find(".inputHidden>input").val(nb);
        nb++;
    });
}

function calculerMontantAcomptes() {
    var $total = parseFloat(getTotalWithRemise());
    $('.paymentsLines').each(function () {
        var $amount = parseInt($(this).find(".amount>input").val());
        if (!isNaN($amount)) {
            var $amountPrice = ($amount * $total) / 100;
            $(this).find(".totalPaymentsLine").html($amountPrice.toFixed(2) + "€");
        } else {
            $(this).find(".totalPaymentsLine").html("0€");
        }
    });
}

function afficherBoutonAcompte($element) {
    var nb = 0;
    $(".lineQuotation").each(function () {
        nb++;
    });
    if (nb > 0) {
        $element.show();
    } else {
        $element.hide();
        $(".paymentsLines").remove();
    }
}

function actualiserRemises() {
    $(".unityField").each(function () {
        if ($(this).find("option:selected").text().toString().trim() === "Remise en pourcent") {
            $(this).parent().parent().find(".qteLine>input").addClass("invisible");
            $(this).parent().parent().find(".qteLine>input").val(0);
            $(this).parent().parent().find("td.priceLine>input").attr("placeholder", "Montant");
            $(this).parent().parent().find("td.priceLine>input").addClass("reduction");
        } else {
            $(this).parent().parent().find(".qteLine>input").removeClass("invisible");
            //$(this).parent().parent().find(".qteLine>input").val(null);
            $(this).parent().parent().find("td.priceLine>input").attr("placeholder", "Prix");
            $(this).parent().parent().find("td.priceLine>input").removeClass("reduction");
        }
        $(".reduction").change(function () {
            var amountReduction = 0;
            $(".reduction").each(function () {
                amountReduction += parseInt($(this).val());
            });
            if (amountReduction > 100) {
                swal("Erreur", "Le cummul des réduction dépasse les 100%", "error");
                $(this).val(0);
            }
        });
    });
}

function getTotalWithoutRemises() {
    var $total = 0;
    var i = 0;
    $('.lines>tr.lineQuotation').each(function () {
        i++;
        var $qte = $(this).find("td.qteLine>input").val();
        var $price = $(this).find("td.priceLine>input").val();
        var $unity = parseInt($(this).find(".unityField").val());
        var $subTotal = 0;
        if ($unity !== 4) {
            if (!isNaN($qte) && !isNaN($price)) {
                $subTotal = $qte * $price;
            }
            $(this).find(".totalPriceLine").html($subTotal + " €");
            $total += $subTotal;
        }
    });
    return $total;

}


function calculerPrixDeslignes() {

    $(".total").html(getTotalWithRemise() + "€");
}

function nbLines() {
    var i = 0;
    $('.lineQuotation').each(function () {
        i++;
    });
    return i;
}

function getTotalWithRemise() {
    var $total = 0;
    var $remise = 0;
    var i = 0;
    var $qte = "";
    var $price = "";
    var $unity = "";
    var $subTotal = 0;
    var priceTotal = parseFloat(getTotalWithoutRemises());
    $('.lineQuotation').each(function () {
        i++;
        $qte = parseInt($(this).find("td.qteLine>input").val());
        $price = parseFloat($(this).find("td.priceLine>input").val());
        $unity = parseInt($(this).find(".unityField").val());
        $subTotal = 0;
        if ($unity === 4) {
            if (!isNaN($price)) {
                if (getTotalWithoutRemises() > 0) {
                    if ($price === "") {
                        $price = 0;
                    }
                    $remise += parseFloat($price);
                    var $remisageLigne = parseFloat(($price * priceTotal) / parseFloat(100));
                    $(this).find(".totalPriceLine").html(" <p class='text-danger'>-" + $price + "% (-" + $remisageLigne + "€)</p>");
                } else {
                    swal("Erreur", "Vous ne pouvez appliquer une réduction sur un montant déjà égal à 0", "error");
                    $(this).remove();
                }
            }
        } else {
            if (!isNaN($qte) && !isNaN($price)) {
                $subTotal = parseFloat(parseFloat($qte) * parseFloat($price));
            }
            $(this).find(".totalPriceLine").html($subTotal + " €");
            $total += $subTotal;
        }
    });
    var $remisage = ($remise * $total) / 100;
    $total -= $remisage;
    return parseFloat($total);
}

function getRemiseTotal() {
    var $remise = 0;
    var $price = "";
    var $unity = "";
    $('.lineQuotation').each(function () {
        $price = $(this).find("td.priceLine>input").val();
        $unity = parseInt($(this).find(".unityField").val());
        if ($unity === 4) {
            if (!isNaN($price)) {
                if ($price === "") {
                    $price = 0;
                }
                $remise += parseInt($price);
            }
        }
    });
    return $remise;
}

