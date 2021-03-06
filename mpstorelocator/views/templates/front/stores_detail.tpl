{*
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}
<!-- /modules/mpstorelocator/views/templates/front/stores_detail.tpl -->
<div id="wk_store_content" class="aa">
    {* <div class='wkstores-heading'>{l s='Stores' mod='mpstorelocator'}</div> *}
    <div class="wk_store_details">
       
        {foreach $store_locations as $key => $store}
		<div class="wk_store col-md-3{if $store_locations|@count-1 == $key || ($store_locations|@count % 2 == 0 && $store_locations|@count-2 == $key)} wk-border-none{/if} stores_list1" id="{$store.id}" lat="{$store.latitude|addslashes}" lng="{$store.longitude|addslashes}"  align="center">
            <div class="wk_store_details_container"  align="center">
                <div class="wk_store_details_up"  align="center" {if $store.seller.profile_banner} style="background-image:url({$smarty.const._MODULE_DIR_}marketplace/views/img/seller_banner/{$store.seller.profile_banner})" {else}  style="background-image:url({$smarty.const._MODULE_DIR_}marketplace/views/img/seller_banner/banner_default.png)" {/if}>
                {*$store.seller.profile_image|@var_dump*}
                    <div class="favoris_container"><i class="nova-heart"></i></div>
					{if $store.seller.is_partner}
					<div class="watermark-partner badge_banner store-page">
						<img src="{$store.seller.watermarks.badge_banner}">
					</div>
                    {/if}
                     {if $store.seller.is_partner} 
                    <div class="img_store_center is_partner_img">{if $store.seller.profile_image != "defaultshopimage.jpg" }<img class="img-fluid" src="{$smarty.const._MODULE_DIR_}marketplace/views/img/seller_img/{$store.seller.profile_image}"> {else} <img class="img-fluid" src="{$smarty.const._MODULE_DIR_}marketplace/views/img/seller_img/defaultimagecma.jpg">{/if}</div>
                    {else}
                     <div class="img_store_center">{if $store.seller.profile_image}<img class="img-fluid" src="{$smarty.const._MODULE_DIR_}marketplace/views/img/seller_img/{$store.seller.profile_image}"> {else} <img class="img-fluid" src="{$smarty.const._MODULE_DIR_}marketplace/views/img/seller_img/default-artisan-pic.png">{/if}</div>
                    {/if}
                </div>
                <div class="wk_store_details_down"  align="center">
                    <div class="wk_store_name1" align="center"><a {if isset($displayStorePage) && $displayStorePage == 1}href="/profile/{$store.seller.link_rewrite}"{/if} class="wkstore-name"><a class="" href="/profile/{$store.seller.link_rewrite}"> {$store.seller.seller_firstname}</a></div>
                    <div class="profession_container"  align="center"><span class="prof_span">{$store.custom_fields.profession}</div>
                    <div class="meta_infos">
                        <div class="dis_inline loc_container"><span class="fa fa-map-marker"> {$store.city_name}</span></div> | 
							<div class="dis_inline stars_container">
							{for $x = 1 to $store.average_ratings step 1}
							<span class="fa fa-star"></span>
							{/for}
							{for $x = 1 to $store.left_ratings step 1}
							<span class="fa fa-star-o"></span>
							{/for}
							{$store.total_review}
							</div>
                    </div>
                </div>
                    
            </div>


           <div style="display:none;">
                <div class="padding-top-15" align="center"><u><a {if isset($displayStorePage) && $displayStorePage == 1}href="{$link->getModuleLink('mpstorelocator', 'storedetails', ['id_store' => $store.id])}"{/if} class="wkstore-name">{$store.name}</a></u></div>
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="wkstore-details-heading">{l s='Address ' mod='mpstorelocator'}</td>
                        <td>: </td>
                        <td>
                            <div>{$store.address1} {$store.address2}</div>
                            <div>{$store.city_name}, {$store.state_name} {$store.zip_code}</div>
                            <div>{$store.country_name}</div>
                        </td>
                    </tr>
                    <tr {if !isset($store.distance)}style="display: none"{/if}>
                        <td class="wkstore-details-heading">{l s='Distance ' mod='mpstorelocator'}</td>
                        <td>: </td>
                        <td><span id="store_distance_{$store.id}">{if isset($store.distance)}{$store.distance}{/if}</span></td>
                    </tr>
                    {if isset($displayStoreTiming) && $displayStoreTiming}
                    <tr>
                        <td class="wkstore-details-heading">{l s='Store Timing ' mod='mpstorelocator'}</td>
                        <td>: </td>
                        <td><span>{l s='Today ' mod='mpstorelocator'} : 
                        {if empty($store.current_hours)}
                            {l s='Closed' mod='mpstorelocator'}
                        {else}
                            {$store.current_hours}
                        {/if}
                         </span>
                         <i class="material-icons wkshow_hours">expand_more</i>
                            <div class="wkstore_hours">
                            <table>
                            {foreach from=$store.hours key=key item=hour}
                                <tr>
                                    {if $key == $store.current_day}
                                        <td><b>{$key}</b></td>
                                        <td> : </td>
                                        <td>
                                        <b>
                                            {if empty($hour)}
                                                {l s='Closed' mod='mpstorelocator'}
                                            {else}
                                                {$hour}
                                            {/if}
                                        </b>
                                        </td>
                                    {else}
                                        <td>{$key}</td>
                                        <td> : </td>
                                        <td>
                                            {if empty($hour)}
                                                {l s='Closed' mod='mpstorelocator'}
                                            {else}
                                                {$hour}
                                            {/if}
                                        </td>
                                    {/if}
                                </tr>
                            {/foreach}
                                </table>
                            </div>
                        </td>
                    </tr>
                    {/if}
                    {if !empty($store.email) && isset($displayEmail) && $displayEmail}
                    <tr>
                        <td class="wkstore-details-heading">{l s='Email ' mod='mpstorelocator'}</td>
                        <td>: </td>
                        <td>{$store.email}</td>
                    </tr>
                    {/if}
                    {if !empty($store.phone) && isset($displayContactDetails) && $displayContactDetails}
                    <tr>
                        <td class="wkstore-details-heading">{l s='Contact Details ' mod='mpstorelocator'}</td>
                        <td>: </td>
                        <td>{$store.phone}</td>
                    </tr>
                    {/if}
                    {if !empty($store.fax) && isset($displayFax) && $displayFax}
                    <tr>
                        <td class="wkstore-details-heading">{l s='Fax No. ' mod='mpstorelocator'}</td>
                        <td>: </td>
                        <td>{$store.fax}</td>
                    </tr>
                    {/if}
                </table> 
                <a class="btn wkstore-btn" href="https://maps.google.com/maps?saddr={if isset($currentLocation)}({$currentLocation.lat}, {$currentLocation.lng}){/if}&daddr=({$store.latitude},{$store.longitude})" id="store_direction_{$store.id}" id="store_direction_{$store.id}" target="_blank">
                    <span class="">{l s='Get Directions' mod='mpstorelocator'}</span>
                </a>
            </div> 
        </div>
        {/foreach}
    
    </div>
</div>
