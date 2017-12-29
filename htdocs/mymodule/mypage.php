<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) <year>  <name of author>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file		mypage.php
 *	\ingroup	mymodule
 *	\brief		This file is an example php page
 *				Put some comments here
 */

//if (! defined('NOREQUIREUSER'))	define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))		define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))	define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))	define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))		define('NOCSRFCHECK','1');
//if (! defined('NOTOKENRENEWAL'))	define('NOTOKENRENEWAL','1');
// If there is no menu to show
//if (! defined('NOREQUIREMENU'))	define('NOREQUIREMENU','1');
// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREHTML'))	define('NOREQUIREHTML','1');
//if (! defined('NOREQUIREAJAX'))	define('NOREQUIREAJAX','1');
// If this page is public (can be called outside logged session)
//if (! defined("NOLOGIN"))			define("NOLOGIN",'1');
// Choose the following lines to use the correct relative path
// (../, ../../, etc)
$res = 0;
if (! $res && file_exists("../main.inc.php")) {
    $res = @include("../main.inc.php");
}
if (! $res && file_exists("../../main.inc.php")) {
    $res = @include("../../main.inc.php");
}
if (! $res && file_exists("../../../main.inc.php")) {
    $res = @include("../../../main.inc.php");
}
// The following should only be used in development environments
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) {
    $res = @include("../../../dolibarr/htdocs/main.inc.php");
}
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) {
    $res = @include("../../../../dolibarr/htdocs/main.inc.php");
}
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) {
    $res = @include("../../../../../dolibarr/htdocs/main.inc.php");
}
if (! $res) {
    die("Main include failed");
}

require_once($path."../../htdocs/master.inc.php");
// Change this following line to use the correct relative path from htdocs
// (do not remove DOL_DOCUMENT_ROOT)
require_once DOL_DOCUMENT_ROOT . "/mymodule/class/myclass.class.php";

// Load translation files required by the page
$langs->load("mymodule@mymodule");

// Access control
if ($user->societe_id > 0) {
    // External user
    accessforbidden();
}


$contextpage='thirdpartylist';
$hookmanager->initHooks(array($contextpage));

/*
 * VIEW
 *
 * Put here all code to build page
 */
require_once ("../societe/class/societe.class.php");
llxHeader('', 'TiersPlus', '');
echo '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.css"/>
 
<script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.js"></script>';



echo load_fiche_titre('Espace tiers et contacts','','title_companies.png');

echo '<div class="container-fluid">';

echo '<div class="row">';

// Display Search form
echo '<div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Rechercher</h3>
            </div>
            <div class="panel-body">
                <form id="searchForm">
                    <div class="form-group">
                        <label for="inputTiers">Tiers</label>
                        <input type="text" class="form-control" id="inputTiers" placeholder="Client / Proscpect ...">
                    </div>
                    <div class="form-group">
                        <label for="inputContact">Contact</label>
                        <input type="text" class="form-control" id="inputContact" placeholder="Contact...">
                    </div>
                    <button type="submit" class="btn btn-default">Rechercher</button>
                  </form>
            </div>
        </div>
      </div>';

// Display Tiers count graph
echo '<div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Statistiques</h3>
            </div>
            <div class="panel-body">
                <div id="placeholder_tiersCount_png" style="width:300px;height:180px"></div>
            </div>
        </div>
      </div>';



echo '<div id="tiersTableContainer">
        <table id="tiersTable" class="table table-hover table-striped" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Société</th>
                <th>Code client</th>
                <th>Ville</th>
                <th>Code postal</th>
                <th>Type</th>
                <th>Téléphone</th>
                <th>Nature</th>
                <th>Etat</th>
            </tr>
        </thead>
    </table>
    </div>';

echo '<div id="contactTableContainer">
        <table id="contactTable" class="table table-hover table-striped" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Prénom</th>
                <th>Nom</th>
                <th>Poste</th>
                <th>Téléphone</th>
                <th>Mobile</th>
                <th>Fax</th>
                <th>Email</th>
                <th>Société</th>
                <th>Visibilité</th>
                <th>Etat</th>
            </tr>
        </thead>
    </table>
    </div></div>';

echo '<div class="row">
<div class="panel panel-default">
  <div class="panel-body">
    <ul class="list-inline">
    <li><a href="/societe/soc.php?action=create&leftmenu=" class="btn btn-default">Ajouter un tiers</a></li>
    <li><a href="/contact/card.php?leftmenu=contacts&action=create" class="btn btn-default">Ajouter un contact</a></li>
</ul>
  </div>
</div>';


echo '<script type="text/javascript" language="javascript">

jQuery(document).ready(function() {
    
var tiersTable = $("#tiersTable").DataTable( {
        "searching": false,
        "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/French.json"
            },
        "ajax": "/mymodule/ajax/search.php",
        "columns": [
            { "data": "name" },
            { "data": "code_client" },
            { "data": "town" },
            { "data": "zip" },
            { "data": "type" },
            { "data": "phone" },
            { "data": "libCustProspStatut" },
            { "data": "libStatut" }
        ]
    } );

var contactTable = $("#contactTable").DataTable( {
        "searching": false,
        "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/French.json"
            },
        
        "columns": [
            { "data": "firstname" },
            { "data": "lastname" },
            { "data": "poste" },
            { "data": "phone" },
            { "data": "phone_mobile" },
            { "data": "fax" },
            { "data": "email" },
            { "data": "tiers" },
            { "data": "visibility" },
            { "data": "state" }
        ]
    } );

jQuery("#contactTableContainer").hide();

$("#searchForm").submit(function(e){
        e.preventDefault();
        
        var searchContent;       
        
        inputSearchContentTiers = jQuery("#inputTiers").val();
        inputSearchContentContact = jQuery("#inputContact").val();
        
        if(!inputSearchContentTiers && !inputSearchContentContact){
            alert("Vous devez indiquer un tiers ou un contact à rechercher.");
        }else if(inputSearchContentTiers && !inputSearchContentContact){
            searchContent = inputSearchContentTiers;
            jQuery("#tiersTableContainer").show();
            jQuery("#contactTableContainer").hide();
            tiersTable.clear();
            tiersTable.ajax.url("/mymodule/ajax/search.php?searchMode=search&searchType=tiers&searchContent="+searchContent).load();
            
        }else if (!inputSearchContentTiers && inputSearchContentContact){
            searchContent = inputSearchContentContact;
            jQuery("#tiersTableContainer").hide();
            jQuery("#contactTableContainer").show();
            jQuery("#contactTable").show();
            contactTable.clear();
            contactTable.ajax.url("/mymodule/ajax/search.php?searchMode=search&searchType=contact&searchContent="+searchContent).load();            
            
        }else if (inputSearchContentTiers && inputSearchContentContact){
            searchContent = inputSearchContentTiers;
            jQuery("#tiersTableContainer").show();
            jQuery("#contactTableContainer").hide();
            tiersTable.clear();
            tiersTable.ajax.url("/mymodule/ajax/search.php?searchMode=search&searchType=tiers&searchContent="+searchContent).load();            
            
        }
 
        
    });

});
</script>';

generateGraph();
// End of page
echo '</div>';



llxFooter();
$db->close();


/**
 *  Get Tiers type count from DB and return an HTML graph using Dolgraph class.
 *
 * @return void
 */
function generateGraph(){

    global $db;
    require_once ("../core/class/dolgraph.class.php");

    // 3 = client/prospect
    // 2 = prospect
    // 1 = client
    // 0 = ni client ni prospect

    // Initialize graph data
    $ret = array();
    $ret[0][0] = 'Autre';
    $ret[0][1] = 0;

    $ret[1][0] = 'Client';
    $ret[1][1] = 0;

    $ret[2][0] = 'Prospect';
    $ret[2][1] = 0;


    $ret[3][0] = 'Client et Prospect';
    $ret[3][1] = 0;

    // Get info from DB
    $sql = 'SELECT s.client FROM '.MAIN_DB_PREFIX.'societe as s';
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
                    // You can use here results

                    if($obj->client == 0){
                        $ret[0][1]++;
                    }
                    elseif($obj->client == 1){
                        $ret[1][1]++;
                    }
                    elseif($obj->client == 2){
                        $ret[2][1]++;
                    }
                    elseif($obj->client == 3){
                        $ret[3][1]++;
                    }
                }
                $i++;
            }
        }
    }

    // Generate and display graph using DolGraph class
    $px1 = new DolGraph;
    $px1->SetWidth(300);
    $px1->SetHeight(180);
    $px1->SetData($ret);
    $px1->SetLegend(0);
    $px1->SetType(array('pie'));
    $px1->setShowPercent(0);
    $px1->draw('../mymodule/tiersCount.png', 'http://dolibarr.test/mymodule/tiersCount.png');
    echo($px1->show());

}