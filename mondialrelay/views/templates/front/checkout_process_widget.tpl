{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<noscript>
    <h3 style="color: red;">{l s='JavaScript is disabled on your browser, please enable it in order to select a Point Relais®.' mod='mondialrelay'}</h3>
</noscript>

<script type="text/javascript">
	// Global JS Value 
	var PS_MRData = {$MR_Data|escape nofilter};
	var id_address = '{$address->id|intval}';
	var ssl = {$ssl|escape:'javascript':'UTF-8'};
    var weight = {$weight|floatval};
	if( weight == 0 ) weight = 100;
	//var weight = 999999; // en Kg
	var iso_code = '{$country->iso_code|escape:'htmlall':'UTF-8'}';
	var enseigne = '{$account_shop.MR_ENSEIGNE_WEBSERVICE|escape:'htmlall':'UTF-8'}';
	var selected_point = false;
	var button = false;
	var button_validate = "{l s='Validate' mod='mondialrelay'}";
	var relay_point_selected_box_label = "{l s='Selected pickup location:' mod='mondialrelay'}";
    var MR_ajax_url = "{$MR_ajax_url|urldecode nofilter}";
	
	function loadMR_Map(zone_widget, dlv_mode) {
		
		$('#MRW-Map').html("");
	
		// Charge le widget dans la DIV d'id "Zone_Widget" avec les paramètres de base    
		// renverra la selection de l'utilisateur dans le champs d'ID "Retour_Widget"  
		 $(zone_widget).MR_ParcelShopPicker({
				Weight: weight,
				ColLivMod: dlv_mode,
                Target: "#Retour_Widget",  // selecteur jquery ou renvoyer l'ID du relais selectionné    
                Brand: enseigne,  // votre code client
				PostCode: "{$address->postcode|escape:'htmlall':'UTF-8'}",
                Country: iso_code,  /* pays*/  
				UseSSL: ssl,
				OnParcelShopSelected: function PS_MRAddSelectedRelayPointInDB_Widget(data) {
					var str = '';
					str += data.Nom+"\n";
					if(data.Adresse1)
						str += data.Adresse1+"\n";
					if(data.Adresse2)
						str += data.Adresse2+"\n";						
					str += data.CP+"\n";
					//str += data.ID+"\n";
					str += data.Ville+"\n";
					str += data.Pays+"\n";
					
					str = str.split("\n").join("<br />");
					
					var newdata = {};
					newdata.Num = data.ID;
					newdata.LgAdr1 = data.Nom;
					newdata.LgAdr2 = '';
					newdata.LgAdr3 = data.Adresse1;
					newdata.LgAdr4 = data.Adresse2;
					newdata.CP = data.CP;
					newdata.Ville = data.Ville;
					newdata.Pays = data.Pays;
					newdata.permaLinkDetail = '';
					
					var id_carrier = (typeof(PS_MRSelectedRelayPoint['carrier_id']) != undefined) ? PS_MRSelectedRelayPoint['carrier_id'] : 4;
					
					$.ajax({
						type: 'POST',
						url: MR_ajax_url,
						data: {
							'method' : 'addSelectedCarrierToDB',
							'relayPointInfo' : newdata,
							'id_carrier' : id_carrier,
							'id_mr_method' : PS_MRCarrierMethodList[id_carrier],
							'mrtoken' : mrtoken
						},
						success: function(json)
						{
							/*
							if (PS_MROPC && PS_MRData.PS_VERSION < '1.5')
								updateCarrierSelectionAndGift();
							*/							
							PS_MRSelectedRelayPoint['relayPointNum'] = data.ID;
							
							
							displayPickupPlace(str);
						},
						error: function(xhr, ajaxOptions, thrownError)
						{
						}
					});				
					
				}
		});
	}
	

</script>

<div class="widget_box" style="display:block;width:0px;height:1px;overflow:hidden;">
	<a id="link_zone_widget" href="#Zone_Widget">&nbsp;</a>
	<div id="Zone_Widget"></div>
	<div id="selected_point_box">
		<div style="padding: 10px; overflow: auto">
			<div style="background: #edffb2; border: solid 1px #a5f913; padding: 5px;font-family:verdana;font-size:10px">
				{l s='Selected pickup location:' mod='mondialrelay'}
				<input type="text" id="Retour_Widget" /></br>
			</div>
		</div>
	</div>
</div>
