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
    $row=sql_fetsel('id_livraison_zone,unite','spip_pays LEFT JOIN spip_livraison_zones USING(id_livraison_zone)','code='.sql_quote($pays));
    
    $sql=sql_select('quantite,objet,id_objet','spip_commandes_details','id_commande='.$id_commande);
    include_spip('inc/pipelines_ecrire');
    $quantite=array();
    $mesures=array(); 
    $prix=array() ;
    $id_objet=array() ;
    while($data=sql_fetch($sql)){
        $prix_unitaire_ht='';
        $montant='';
        $quantite[]=$data['quantite'];
        //On regarde si on une unité s'applique
        if(isset($row['unite'])){
            $objet_prix=sql_fetsel('objet,id_objet','spip_prix_objets','id_prix_objet='.$data['id_objet']);
            $e = trouver_objet_exec($objet_prix['objet']);
            $table=table_objet_sql($objet_prix['objet']);
            $id_table_objet=$e['id_table_objet'];            
            $mesure=sql_getfetsel('mesure',$table,$id_table_objet.'='.$objet_prix['id_objet']);
            $montant=sql_fetsel('montant,id_livraison_montant','spip_livraison_montants','id_livraison_zone='.$row['id_livraison_zone'].' AND mesure_min <='.$mesure.' AND mesure_max >='.$mesure);
            //Si pas de montant trouvé, on applique le montqnt par défaut
             if(!$prix_unitaire_ht=$montant['montant']){
                include_spip('inc/config');
                 $prix_unitaire_ht=lire_config('shop_livraison/montant_defaut');
                }
            $prix[]=$data['quantite']*$prix_unitaire_ht;
            $id_objet[]=$montant['id_livraison_montant'];
            
        }
    }
    $id_objet=serialize($id_objet);
    $quantite=array_sum($quantite);
    
    
    //On regarde si on une unité s'applique
    if(count($prix)==0){
        $montant=sql_fetsel('montant,id_livraison_montant','spip_livraison_montants','id_livraison_zone='.$row['id_livraison_zone']);  
        //Si pas de montant trouvé, on applique le montqnt par défaut
        if(!$prix_unitaire_ht=$montant['montant']){
            include_spip('inc/config');
            $prix_unitaire_ht=lire_config('shop_livraison/montant_defaut');
        }
        $id_objet=$montant['id_livraison_montant'];
        $total=$prix_unitaire_ht*$quantite;
        }
    else $total=array_sum($prix);
    
      sql_insertq(
            'spip_commandes_details',
            array(
                'id_commande' => $id_commande,
                'objet' => 'livraison_montant',
                'id_objet' => $montant['id_livraison_montant'],
                'descriptif' => _T('livraison_montant:titre_livraison_montant'),
                'quantite' => 1,
                'prix_unitaire_ht' => $total,
                'taxe'=>0,
            )
        );
    }

    return $flux;
}

function shop_livraisons_formulaire_traiter($flux){
    
    // Installer des champs extras après la configuration prix
    if ($flux['args']['form'] == 'configurer_shop_prix') {

    /*Installation de champs via le plugin champs extras*/
    include_spip('inc/cextras');
    include_spip('base/shop_livraisons');
    $maj_item = array();
    foreach(shop_livraisons_declarer_champs_extras() as $table=>$champs) {
        champs_extras_creer($table, $champs);
        } 
    }
 
    return $flux;
}

?>