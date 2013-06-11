<?php
/**
 * Plugin Déclinaisons Produit
 * (c) 2012 Rainer Müller
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function shop_livraisons_declarer_tables_interfaces($interfaces) {

    $interfaces['table_des_tables']['livraison_montants'] = 'livraison_montants';
    $interfaces['table_des_tables']['livraison_zones'] = 'livraison_zones';

    return $interfaces;
}

/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function shop_livraisons_declarer_tables_objets_sql($tables) {

    $tables['spip_livraison_montants'] = array(
        'type' => 'livraison_montant',
        'principale' => "oui", 
        'table_objet_surnoms' => array('livraisonmontant'), // table_objet('livraison_montant') => 'livraison_montants' 
        'field'=> array(
            "id_livraison_montant" => "bigint(21) NOT NULL",
            "id_livraison_zone"  => "bigint(21) NOT NULL DEFAULT 0",
            "montant"            => "float NOT NULL DEFAULT 0",
            "mesure_min"         => "float NOT NULL DEFAULT 0",
            "mesure_max"         => "float NOT NULL DEFAULT 0",
            "maj"                => "timestamp",
        ),
        'key' => array(
            "PRIMARY KEY"        => "id_livraison_montant",
            "KEY"                => "id_livraison_zone",            
        ),
        'titre' => "montant AS titre, '' AS lang",
         #'date' => "",
        'champs_editables'  => array('id_livraison_zone','montant', 'mesure_min', 'mesure_max'),
        'champs_versionnes' => array('montant', 'mesure_min', 'mesure_max'),
        'rechercher_champs' => array("montant" => 4),
        'tables_jointures'  => array('id_livraison_zone'),
        

    );

    $tables['spip_livraison_zones'] = array(
        'type' => 'livraison_zone',
        'principale' => "oui", 
        'table_objet_surnoms' => array('livraisonzone'), // table_objet('livraison_zone') => 'livraison_zones' 
        'field'=> array(
            "id_livraison_zone"  => "bigint(21) NOT NULL",
            "nom"                => "text NOT NULL DEFAULT ''",
            "descriptif"         => "text NOT NULL DEFAULT ''",
            "unite"              => "varchar(25) NOT NULL DEFAULT ''",
            "maj"                => "timestamp",
        ),
        'key' => array(
            "PRIMARY KEY"        => "id_livraison_zone",
        ),
        'titre' => "nom AS titre, '' AS lang",
         #'date' => "",
        'champs_editables'  => array('nom', 'descriptif', 'unite'),
        'champs_versionnes' => array('nom', 'descriptif', 'unite'),
        'rechercher_champs' => array("nom" => 8, "descriptif" => 4),
        'tables_jointures'  => array(),
        

    );

    return $tables;
}

function shop_livraisons_declarer_tables_principales($tables_principales){

        $tables_principales['spip_pays']=array(
            'field'=>array('id_livraison_zone'=>"BIGINT NOT NULL"),
            'key'=>array('KEY id_livraison_zone'=>"id_livraison_zone"),        
            'join'=>array('id_livraison_zone'=>"id_livraison_zone"),      
             );

        return $tables_principales;

}

function shop_livraisons_declarer_champs_extras($champs = array()) {

    //Les objets choisis dans shop_prix    
    include_spip('inc/config');
    include_spip('shop_livraisons_fonctions');    

    $objets_prix=lire_config('shop_prix/objets_prix',array());
    $unite_defaut=lire_config('shop_livraison/unite_defaut',array()); 
    $mesure_defaut=mesure_defaut(); 
    include_spip('shop_prix_fonctions');
    

    /*Pour chaque objet prix on active le champ mesure*/
    foreach($objets_prix AS $objet){
        $restriction=array();
        if($objet=='article')$restriction=rubrique_prix();

       $champs[table_objet_sql($objet)]['mesure'] = array(
          'saisie' => 'input',//Type du champ (voir plugin Saisies)
          'options' => array(
                'nom' => 'mesure', 
                'label' => _T('livraison:label_mesure_'.$mesure_defaut), 
                'explication'=>_T('livraison:explication__mesure',array('unite'=>$unite_defaut)), 
                'sql' => "text NOT NULL DEFAULT ''",
                'versionner' => true,                                 
                'restrictions'=>array(
                    'rubrique' => implode(':',$restriction), 
                    'voir' => array('auteur' => ''),//Tout le monde peut voir
                    'modifier' => array('auteur' => 'admin'),//Seuls les administrateurs peuvent modifier
                    )
                )  
            );
        }
    return $champs;
    }
?>