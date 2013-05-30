<?php
/**
 * Plugin Déclinaisons Produit
 * (c) 2012 Rainer Müller
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function shop_livraison_declarer_tables_principales($tables_principales){

        $tables_principales['spip_commandes_details']['field']['livraison']= "tinyint(1) NOT NULL";

        return $tables_principales;

}

?>