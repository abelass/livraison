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
	
    
function shop_livraisons_post_insertion($flux){
    // Après insertion d'une commande "encours" et s'il y a un panier en cours
    if (
        $flux['args']['table'] == 'spip_commandes'
        and ($id_commande = intval($flux['args']['id_objet'])) > 0
        and $flux['data']['statut'] == 'encours'
        and include_spip('inc/filtres')
    ){
    $pays=_request('pays');
    $data=sql_fetsel('id_livraison_zone,unite','spip_pays LEFT JOIN spip_livraison_zones USING(id_livraison_zone)','code='.sql_quote($pays));
    
    $montant=sql_fetsel('montant,id_livraison_montant','spip_livraison_montants','id_livraison_zone='.$data['id_livraison_zone']);
    
    
    $sql=sql_select('quantite','spip_commandes_details','id_commande='.$id_commande);
    
    $quantite=array();
    while($data=sql_fetch($sql)){
        $quantite[]=$data['quantite'];
        }
    $quantite=array_sum($quantite);
    
    if(!$prix_unitaire_ht=$montant['montant']){
        include_spip('inc/config');
        $prix_unitaire_ht=lire_config('shop_livraison/montant_defaut');
    }

      sql_insertq(
            'spip_commandes_details',
            array(
                'id_commande' => $id_commande,
                'objet' => 'livraison_montant',
                'id_objet' => $montant['id_livraison_montant'],
                'descriptif' => _T('livraison_montant:titre_livraison_montant'),
                'quantite' => 1,
                'prix_unitaire_ht' => $prix_unitaire_ht*$quantite,
                'livraison'=>1,
                'taxe'=>0,
            )
        );
    }

    return $flux;
}


?>