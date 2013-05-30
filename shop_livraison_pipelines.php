<?php
/**
 * Utilisations de pipelines par Shop Livraisons
 *
 * @plugin     Shop Livraisons
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Shop_livraison\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	


function shop_livraison_formulaire_charger($flux){
    $form=$flux['args']['form'];
    if($form == 'configurer_shop_livraison'){
 //Intervention sur la config 
    include_spip('inc/config');
    if(!$cfg['zones_livraison']=_request('zones_livraison')){
        $zones_livraison=lire_config('shop_livraison/zones_livraison',array());
            $flux['data']['zones_livraison']='';
            foreach ($zones_livraison as $key => $value) {
                if($key)$flux['data']['zones_livraison'].="$key,$value\n";
            }
        }
    }
    return $flux;    
}

function shop_livraison_formulaire_traiter($flux){
    // intervenir sur la configuration du plugin
    $form=$flux['args']['form'];
    if($form == 'configurer_shop_livraison'){
        include_spip('inc/config');
        include_spip('inc/shop_livraison');
        $cfg=lire_config('shop_livraison');
        $cfg['zones_livraison']  = zones2array(_request('type_liens'));
        

    ecrire_config('shop_livraison',$cfg);
    }
    return $flux;
}

?>