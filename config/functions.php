<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 22/11/17
 * Time: 17:30
 */

/**
 * Débug différentes valeurs sans arrêter l'execution
 * @param array ...$debug
 */
function d(...$debug)
{
    $i = 0;
    foreach ($debug as $value) {

        $i++;
        dump($value[0] ?? $value);
        echo "\n";
        echo "--------------------------------------    #$i    --------------------------------------";
        echo "\n";
    }
}

/**
 * Débug différente valeur et arrête l'execution
 * @param array ...$debug
 */
function dd(...$debug)
{
    d($debug);
    die();
}

/**
 * Vérifie si au moins une des valeurs needle est dans le tableau array
 * @param array $needles
 * @param $array
 * @return bool
 */
function array_in_array(array $needles, $array): bool
{

    foreach ($needles as $needle) {
        if (in_array($needle, $array)) {
            return true;
        }
    }
    return false;
}

/**
 * @param array $numlist
 * @return array
 */
function mergeSort(array $numlist): array
{
    if (count($numlist) == 1) {
        return $numlist;
    }
    $mid = floor((int)count($numlist) / (int)2);
    $left = array_slice($numlist, 0, $mid);
    $right = array_slice($numlist, $mid);

    $left = mergeSort($left);
    $right = mergeSort($right);

    return merge($left, $right);
}

/**
 * Fonction utilisée par l'algoruthme merge_sort
 * @param $left
 * @param $right
 * @return array
 */
function merge($left, $right)
{
    $result = array();
    $leftIndex = 0;
    $rightIndex = 0;
    while ($leftIndex < count($left) && $rightIndex < count($right)) {
        if ($left[$leftIndex] > $right[$rightIndex]) { //Condition here
            $result[] = $right[$rightIndex];
            $rightIndex++;
        } else {
            $result[] = $left[$leftIndex];
            $leftIndex++;
        }
    }
    while ($leftIndex < count($left)) {
        $result[] = $left[$leftIndex];
        $leftIndex++;
    }
    while ($rightIndex < count($right)) {
        $result[] = $right[$rightIndex];
        $rightIndex++;
    }
    return $result;
}

function testing($dernierMois){
    $arParMois = array();
    $date_courant = date("Y-m-d");

    for($i = 0; $i < $dernierMois; $i++){
        if($i === 0){
            $arParMois[$i] = array(
                'month' => date("m")
            );
        }else{
            //- 1 mois à la date du jour
            $mois = date("m", strtotime("-1 month", strtotime($date_courant)));
            $arParMois[$i] = array(
                'month' => $mois
            );
            $date_courant = date("Y-".$mois."-d");
        }
    }

    return $arParMois;
}

/**
 * @param int $dernierMois
 * @return array
 */
function getLatestMonth(int $dernierMois=6):array
{
    $arParMois = array();
    $date_courant = date("Y-m-d");
    $arParMois[] = [
        "mois"=>date("m"),
        "annee"=>date("Y"),
        "moisComplet"=>date("F")
    ];
    for ($i = 0; $i < $dernierMois; $i++) {
        $mois = date("m", strtotime("-1 month", strtotime($date_courant)));
        $year = date("Y", strtotime("-1 month", strtotime($date_courant)));
        $moisComplet = date("F", strtotime("-1 month", strtotime($date_courant)));
        $arParMois[] = [
            "mois"=>$mois,
            "annee"=>$year,
            "moisComplet"=>$moisComplet
        ];
        $date_courant = date("$year-" . $mois . "-d");
    }
    return $arParMois;
}