<?php
/**
 * Fonctions utiles au plugin Shop Livraisons
 *
 * @plugin     Shop Livraisons
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Shop_livraison\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function unite_mesure($id_livraison_zone,$mesure=''){
    include_spip('inc/config');
    $config=lire_config('shop_livraison',array());
    
    $unite=sql_getfetsel('unite','spip_livraison_zones','id_livraison_zone='.$id_livraison_zone);
    
    if($mesure) $unite=$mesure.' .'._T('livraison:label_unite_'.$unite);
    else $unite=_T('livraison:label_unite_'.$unite);
    return $unite;
}

?>