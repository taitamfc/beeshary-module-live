{**
 * 2017-2018 PHPIST.
 *
 *  @author    Yassine belkaid <yassine.belkaid87@gmail.com>
 *  @copyright 2017-2018 PHPIST
 *  @license   https://store.webkul.com/license.html
 *}

<!-- /sites/dev.beeshary.com/modules/mpsellerwiselogin/views/templates/front/_partials -->
<div id="sellerStoreForm" style="display: none;">
    <div class="alert alert-danger pp_display_errors_store" style="display: none;"></div>
    <div class="prv_section">
        <a class="prv_profile" href="javascript:void(0);">
            <img src="{$smarty.const._THEME_IMG_DIR_}bee-fleche.svg" />
        </a>
    </div>
    <div class="form-group">
        <img class="center-block store_top_pic" src="{$urls.base_url}themes/beeshary_child/assets/img/picto-boutique.jpg" />
        <div class="pp_seller_profile_title">{l s='My store' mod='mpsellerwiselogin'}</div>
        <div class="pp_seller_profile_subtitle">{l s='Create your online store' mod='mpsellerwiselogin'}</div>
        <input type="hidden" name="latitude" id="latitude" />
        <input type="hidden" name="longitude" id="longitude" />
    </div>
    <div class="form-group">
        <input type="text" class="form-control" name="store_name" id="store_name" placeholder="{l s='Store name' mod='mpsellerwiselogin'}*" required{if isset($smarty.post.store_name)} value="{$smarty.post.store_name|escape:'htmlall':'utf-8'}"{/if} />
    </div>
    <div class="form-group" style="margin-bottom: 1px">
        <input type="text" class="form-control" name="store_name_unique" id="store_name_unique" placeholder="{l s="Nom de la boutique visible dans l'url" mod='mpsellerwiselogin'}*" required{if isset($smarty.post.store_name_unique)} value="{$smarty.post.store_name_unique|escape:'htmlall':'utf-8'}"{/if} />
    </div>
    <p style="text-align:center;">Mettre "Nom visible dans l'url. Ex: https://beeshary.com/boutique/nom-boutique</p>
    <div class="form-group">
        <textarea class="form-control" name="store_description" maxlength ="1000" id="store_description" placeholder="{l s='Store description' mod='mpsellerwiselogin'}">{if isset($smarty.post.store_description)}{$smarty.post.store_description|escape:'htmlall':'utf-8'}{/if}</textarea>
    </div>
    {if isset($extrafields)}
        <div class="form-group">
            {if $extrafields.labels.attribute_name == 'labels' }
                <!-- PAUL : Add Label before Select Box -->
                <label class="address_pro">{l s='Choisissez vos labels parmi la liste d??roulante:' mod='mpsellerwiselogin'}</label>
                <!-- PAUL -->
                <!--input type="text" class="form-control input_left" name="{$extrafields.labels.attribute_name|escape:'htmlall':'UTF-8'}" id="{$extrafields.labels.attribute_name|escape:'htmlall':'UTF-8'}" placeholder="{l s='My labels and certificates' mod='mpsellerwiselogin'}" maxlength="{$extrafields.labels.char_limit|escape:'htmlall':'UTF-8'}"{if isset($smarty.post.labels)} value="{$smarty.post.labels|escape:'htmlall':'utf-8'}"{/if} /-->
                <select class="form-control has-chosen" style="overflow: hidden;" id="labels" name="labels[]" multiple data-placeholder="{l s='My labels and certificates' mod='mpsellerwiselogin'}*" >
				<!-- PAUL : show default Label ARTIBOUTIK -->
                    {foreach from=$badges item=label}
					{if $partner}
						{if $label.id == 1}
							<option value="{$label.id|intval}" selected="">{$label.badge_name|escape:'htmlall':'UTF-8'}</option>
						{else}
							<option value="{$label.id|intval}">{$label.badge_name|escape:'htmlall':'UTF-8'}</option>
						{/if}
					{else}
						{if $label.badge_name == 'ARTIBOUTIK'}{continue}{/if}
                        <option value="{$label.id|intval}">{$label.badge_name|escape:'htmlall':'UTF-8'}</option>
					{/if}
                    {/foreach}
				<!-- PAUL -->
                </select>
            {/if}
            <div class="clearfix"></div>
        </div>
    {/if}

    <div class="form-group">
        <input type="text" class="form-control" name="syndicat_pro" id="syndicat_pro" placeholder="Autre label ou distinction"{if isset($smarty.post.syndicat_pro)} value="{$smarty.post.syndicat_pro|escape:'htmlall':'utf-8'}"{/if} />
    </div>
    {if isset($extrafields) && isset($extrafields.siret)}
        <div class="form-group">
            <input type="text" class="form-control " name="{$extrafields.siret.attribute_name|escape:'htmlall':'UTF-8'}" id="{$extrafields.siret.attribute_name|escape:'htmlall':'UTF-8'}" placeholder="{l s='Siret' mod='mpsellerwiselogin'}*" maxlength="14"{if isset($smarty.post.siret)} value="{$smarty.post.siret|escape:'htmlall':'utf-8'}"{/if} />
        </div>
    {/if}
    <!-- Comment : 28/09/2018
	<div class="form-group">
		<input type="text" class="form-control tagify" name="tags" id="tags" placeholder="{l s='Tags' mod='mpsellerwiselogin'}"{if isset($smarty.post.tags)} value="{$smarty.post.tags|escape:'htmlall':'utf-8'}"{/if} />
		<small class="small_col">Saisissez un mot ou expression et validez par une virgule.</small>
	</div>
	-->
    <div class="form-group">
        <label class="address_pro">{l s='Professional address:' mod='mpsellerwiselogin'}</label>
        <div class="input-group">
            <input type="text" class="form-control" name="store_address" id="store_address" placeholder="{l s='Store address' mod='mpsellerwiselogin'}*" required{if isset($smarty.post.store_address)} value="{$smarty.post.store_address|escape:'htmlall':'utf-8'}"{/if} />
            <div class="input-group-addon">
                <img src="{$urls.base_url}themes/beeshary_child/assets/img/bee_icon_mapmarker.png" width="16px" />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="input-group input_left">
            <!-- DAVID : postcode to post_code" -->
            <input type="text" class="form-control" name="post_code" id="post_code" placeholder="{l s='Post code' mod='mpsellerwiselogin'}*" maxlength="5" required{if isset($smarty.post.postcode)}  value="{$smarty.post.postcode|escape:'htmlall':'utf-8'}"{/if} />
            <div class="input-group-addon">
                <img src="{$urls.base_url}themes/beeshary_child/assets/img/bee_icon_mapmarker.png" width="16px" />
            </div>
        </div>

        <div class="input-group input_right">
            <input type="text" class="form-control" name="city" id="city" placeholder="{l s='City' mod='mpsellerwiselogin'}*" required{if isset($smarty.post.city)} value="{$smarty.post.city|escape:'htmlall':'utf-8'}"{/if} />
            <div class="input-group-addon">
                <img src="{$urls.base_url}themes/beeshary_child/assets/img/bee_icon_mapmarker.png" width="16px" />
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="form-group">
        <div class="input-group input_left">
            <input type="text" class="form-control" name="tel_pro" id="tel_pro" placeholder="{l s='Tel pro' mod='mpsellerwiselogin'}*" maxlength="12" required{if isset($smarty.post.tel_pro)} value="{$smarty.post.tel_pro|escape:'htmlall':'utf-8'}"{/if} />
            <div class="input-group-addon">
                <img src="{$urls.base_url}themes/beeshary_child/assets/img/bee_icon_user.png" />
            </div>
        </div>
        {if $requestSeller }
            <div class="input-group input_right">
                <input type="text" class="form-control" name="email_pro" id="email_pro" placeholder="{l s='Email pro' mod='mpsellerwiselogin'}*" required readonly{if isset($obj_customer.email)} value="{$obj_customer.email|escape:'htmlall':'utf-8'}"{/if} />
                <div class="input-group-addon">
                    <img src="{$urls.base_url}themes/beeshary_child/assets/img/bee_icon_at.png" />
                </div>
            </div>
        {else}
            <div class="input-group input_right">
                <input type="text" class="form-control" name="email_pro" id="email_pro" placeholder="{l s='Email pro' mod='mpsellerwiselogin'}*" required{if isset($smarty.post.email_pro)} value="{$smarty.post.email_pro|escape:'htmlall':'utf-8'}"{/if} />
                <div class="input-group-addon">
                    <img src="{$urls.base_url}themes/beeshary_child/assets/img/bee_icon_at.png" />
                </div>
            </div>
        {/if}
        <div class="clearfix"></div>
    </div>
    <div class="form-group">
        <div class="input-group">
            <input type="password" class="form-control" name="passwd" id="passwd" placeholder="Mot de passe* (min 5 caract??res)" required />
            <div class="input-group-addon">
                <img src="{$urls.base_url}themes/beeshary_child/assets/img/bee_icon_passwd.png" />
            </div>
        </div>
        <p style="text-align: center;">* Champs obligatoires</p>
    </div>
    <div class="form-group">
        <a id="submitStore" href="#" class="next_btn">{l s='Next' mod='mpsellerwiselogin'}</a>
    </div>
    {if isset($MP_GEOLOCATION_API_KEY) && $MP_GEOLOCATION_API_KEY}
        <script src="https://maps.googleapis.com/maps/api/js?key={$MP_GEOLOCATION_API_KEY}&libraries=places&language=fr&region=FR"></script>
    {/if}

<!-- PAUL : show default Label ARTIBOUTIK -->
{if $partner}
	<style type="text/css">
	#labels_chosen ul.chosen-choices li:first-child .search-choice-close{
		display: none;
	}
	</style>
{/if}
<!-- PAUL -->

</div>
