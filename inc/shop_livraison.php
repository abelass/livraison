<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function zones2array($type){
    $tableau    = array();
    $lignes     = explode("\n",$type);
    foreach ($lignes as $l){
        $donnees= explode(',',$l);
        if ($donnees[1])
            $tableau[trim($donnees[0])] = trim ($donnees[1]);
        else
            $tableau[trim($donnees[0])] = '';
    }

    return $tableau;
}