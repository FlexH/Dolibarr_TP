<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../main.inc.php';
require_once($path."../../master.inc.php");
require_once ("../../contact/class/contact.class.php");

$searchMode=GETPOST('searchMode','alpha');
$searchType=GETPOST('searchType','san-alpha');
$searchContent=GETPOST('searchContent','san-alpha');

/**
 * Get all Thirdparties from DB and return and array of ID's
 *
 * @return array containing ID of thirdparties
 */
function getAllThirdParties(){
    global $db;

    $ret = array();

    $sql = 'SELECT s.rowid FROM '.MAIN_DB_PREFIX.'societe as s';
    $resql = $db->query($sql);
    if ($resql)
    {
        $num = $db->num_rows($resql);
        $i = 0;
        if ($num)
        {
            while ($i < $num)
            {
                $obj = $db->fetch_object($resql);
                if ($obj)
                {
                    $ret[$i]['id'] = $obj->rowid;
                }
                $i++;
            }
        }
    }
    return $ret;
}

function getAllContacts(){
    global $db;

    $ret = array();

    $sql = 'SELECT sp.rowid FROM '.MAIN_DB_PREFIX.'socpeople as sp';
    $resql = $db->query($sql);
    if ($resql)
    {
        $num = $db->num_rows($resql);
        $i = 0;
        if ($num)
        {
            while ($i < $num)
            {
                $obj = $db->fetch_object($resql);
                if ($obj)
                {
                    $ret[$i]['id'] = $obj->rowid;
                }
                $i++;
            }
        }
    }
    return $ret;
}

/**
 * Return a JSON array with thirdparties information for ajax Datatables.
 *
 * @param null $arrayList An array containing ID of thirdparties to parse. By default, parse all thirdparties
 *
 */
function getTiersInformations($arrayList=null){

    global $db;
    $langs = $GLOBALS['langs'];
    $langs->load("companies");

    $ret['data'] = array();


    if($arrayList == null){
        $tiersList = getAllThirdParties();
    }else{
        $tiersList = $arrayList;
    }
    $line = array();

    // Parsing each line of the array
    foreach ($tiersList as $item) {
        $thirdParty = new Societe($db);
        $thirdParty->fetch($item['id']);

        $line['id'] =  $thirdParty->id;
        $line['name'] =  '<a href="/societe/soc.php?socid=' . $thirdParty->id . '">' . $thirdParty->name . '</a>';
        $line['code_client'] =  $thirdParty->code_client;
        $line['town'] =  $thirdParty->town;
        $line['zip'] =  $thirdParty->zip;
        $line['type'] =  $langs->trans($thirdParty->typent_code);
        $line['phone'] =  '<a href="tel:' . $thirdParty->phone . '">' . $thirdParty->phone . '</a>';
        $line['libCustProspStatut'] =  $thirdParty->getLibCustProspStatut();
        $line['libStatut'] =  $thirdParty->getLibStatut(2);

        // Adding line to the returned array
        array_push($ret['data'], $line);

    }

    // Return array
    echo(json_encode($ret));

}

/**
 * @param $content String containing content to search
 * @return array Array of the thirdparties ID mathching the search
 */
function searchTiers($content){

    global $db;

    $ret = array();
    $sql = "SELECT s.rowid FROM ".MAIN_DB_PREFIX."societe as s WHERE concat(s.nom, s.name_alias, s.code_client) LIKE '%$content%'";
    $resql = $db->query($sql);
    if ($resql)
    {
        $num = $db->num_rows($resql);
        $i = 0;
        if ($num)
        {
            while ($i < $num)
            {
                $obj = $db->fetch_object($resql);
                if ($obj)
                {
                    $ret[$i]['id'] = $obj->rowid;
                }
                $i++;
            }
        }
    }
    return $ret;

}
/**
 * @param $content String containing content to search
 * @return array Array of the contacts ID mathching the search
 */
function searchPeople($content){

    global $db;

    $ret = array();
    $sql = "SELECT sp.rowid FROM ".MAIN_DB_PREFIX."socpeople as sp WHERE concat(sp.firstname, sp.lastname) LIKE '%$content%'";
    $resql = $db->query($sql);
    if ($resql)
    {
        $num = $db->num_rows($resql);
        $i = 0;
        if ($num)
        {
            while ($i < $num)
            {
                $obj = $db->fetch_object($resql);
                if ($obj)
                {
                    $ret[$i]['id'] = $obj->rowid;
                }
                $i++;
            }
        }
    }
    return $ret;

}

/**
 * Return a JSON array with contacts information for ajax Datatables.
 *
 * @param null $arrayList An array containing ID of contact to parse. By default, parse all contacts
 *
 */
function getPeopleInformations($arrayList=null){

    global $db;
    $langs = $GLOBALS['langs'];
    $langs->load("companies");

    $ret['data'] = array();


    if($arrayList == null){
        $tiersList = getAllContacts();
    }else{
        $tiersList = $arrayList;
    }
    $line = array();

    // Parsing each line of the array
    foreach ($tiersList as $item) {
        $contact = new Contact($db);
        $contact->fetch($item['id']);

        $line['id'] =  $contact->id;
        $line['firstname'] =  $contact->firstname;
        $line['lastname'] =  $contact->lastname;
        $line['poste'] =  $contact->poste;
        $line['phone'] =  '<a href="tel:' . $contact->phone . '">' . $contact->phone . '</a>';
        $line['phone_mobile'] =  '<a href="tel:' . $contact->phone_mobile . '">' . $contact->phone_mobile . '</a>';
        $line['fax'] =  $contact->fax;
        $line['email'] =  '<a href="mailto:' . $contact->email . '">' . $contact->email . '</a>';

        $contactSoc = new Societe($db);
        $contactSoc->fetch($contact->socid);
        $line['tiers'] =  '<a href="/societe/soc.php?socid=' . $contactSoc->id . '">' . $contactSoc->name . '</a>'; ;

        $line['visibility'] =  $contact->LibPubPriv($contact->priv);
        $line['state'] =  $contact->getLibStatut(2);

        // Adding line to the returned array
        array_push($ret['data'], $line);

    }

    // Return array
    echo(json_encode($ret));

}



// We are in search mode : search an display results
if($searchMode == "search"){

    switch ($searchType){
        case "tiers":
            $tiersFound = searchTiers($searchContent);

            // Check if search give results
            if (empty($tiersFound)){
                $ret = array();
                $ret['data'] = 0;
                echo json_encode($ret);
            }else{
                getTiersInformations(searchTiers($searchContent));
            }
            break;
        case "contact":
            $contactFound = searchPeople($searchContent);

            // Check if search give results
            if (empty($contactFound)){
                $ret = array();
                $ret['data'] = 0;
                echo json_encode($ret);
            }else{
                getPeopleInformations(searchPeople($searchContent));
            }
    }





}else{
    // We are not in search mode, display all the thirdparties
    getTiersInformations();
}
