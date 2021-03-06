{** * 2017-2018 PHPIST. * * @author Yassine belkaid
<yassine.belkaid87@gmail.com>
  * @copyright 2017-2018 PHPIST * @license https://store.webkul.com/license.html *}
	
  <div id="sellerProfileForm" style="display:block;">
    <div class="alert alert-danger pp_display_errors_profile" style="display: none;"></div>
    {if isset($smarty.get.derr) && $smarty.get.derr}
    <ul class="alert alert-danger sys_errors">
      {if $smarty.get.derr == 1}
      <li>{l s='Customer could not be created, please contact the adminisatrator.' mod='mpsellerwiselogin'}</li>
      {/if} {if $smarty.get.derr == 2}
      <li>{l s='Seller could not be created, please contact the adminisatrator.' mod='mpsellerwiselogin'}</li>
      {/if}
    </ul>
    {/if} {if isset($errors) && $errors|@count}
    <ul class="alert alert-danger sys_errors">
      {foreach from=$errors item=error}
      <li>{$error|escape:'htmlall':'utf-8'}</li>
      {/foreach}
    </ul>
    {/if}
    <div class="form-group">
      <i class="material-icons pp_seller_profile">person</i>
      <div class="pp_seller_profile_title">{l s='My profile' mod='mpsellerwiselogin'}</div>
      <div class="pp_seller_profile_subtitle">{l s='Introduce yourself and meet many curious people.' mod='mpsellerwiselogin'}</div>
    </div>
    <div class="form-group fullname">
      <div class="input-group input_left">
        <div class="input-group-addon">{l s='First name' mod='mpsellerwiselogin'}</div>
        <input type="text" class="form-control" name="firstname" id="pp_firstname" placeholder="{l s='First name' mod='mpsellerwiselogin'}*" required readonly {if isset($obj_customer.firstname)}value="{$obj_customer.firstname|escape:'htmlall':'utf-8'}" {/if} />
      </div>
      <div class="input-group input_right">
        <div class="input-group-addon">{l s='Last name' mod='mpsellerwiselogin'}</div>
        <input type="text" class="form-control" name="lastname" id="pp_lastname" placeholder="{l s='Last name' mod='mpsellerwiselogin'}*" required readonly {if isset($obj_customer.lastname)}value="{$obj_customer.lastname|escape:'htmlall':'utf-8'}" {/if} />
      </div>
      <div class="clearfix"></div>
    </div>
    {if isset($extrafields) && isset($extrafields.profession)}
    <div class="form-group">
      <input type="text" class="form-control input_left" name="{$extrafields.profession.attribute_name|escape:'htmlall':'UTF-8'}" id="{$extrafields.profession.attribute_name|escape:'htmlall':'UTF-8'}" placeholder="{l s='Profession' mod='mpsellerwiselogin'}*"
        maxlength="{$extrafields.profession.char_limit|escape:'htmlall':'UTF-8'}" required {if isset($smarty.post.profession)}value="{$smarty.post.profession|escape:'htmlall':'utf-8'}" {/if} />

      <div class="input_right">


		<select class="form-control" style="overflow: hidden;" id="seller_lang" name="spoken_langs[]" multiple data-placeholder="{l s='Spoken languages' mod='mpsellerwiselogin'}*" required>
               <option value="108">Fran??ais</option>
               <option value="109">Anglais</option>
               <option value="110">Allemand</option>
               <option value="111">Espagnol</option>
               <option value="112">Portuguais</option>
               <option value="113">Italien</option>
               <option value="114">Chinois</option>
               <option value="115">Arabe</option>
               <option value="116">Autre</option>
            </select>
      </div>
      <div class="clearfix"></div>
    </div>
    {/if} 
	
	
    <div class="form-group">
		<label>S??lectionnez votre savoir-faire</label>
		<select id="pp_theme" name="pp_theme" class="form-control">
			<option value="1">Agriculture, production et ??levage</option>
			<option value="2">Apiculture et produits de la ruche</option>
			<option value="3">Art & Peinture</option>
			<option value="4">Artisanat Ecologique & recyclage</option>
			<option value="5">Boulangerie, Patisserie et Biscuiterie</option>
			<option value="6">Chocolat & Confiserie</option>
			<option value="7">Confitures & Gourmandises sucr??es</option>
			<option value="8">Cosm??tiques & produits de bien-??tre</option>
			<option value="9">Cr??ation de bijoux</option>
			<option value="10">Epicerie sal??e et conserverie</option>
			<option value="11">Gastronomie et M??tiers de bouche</option>
			<option value="12">Insolite</option>
			<option value="13">Mobilier & d??coration</option>
			<option value="14">Nature & botanique</option>
			<option value="15">Papier & calligraphie</option>
			<option value="16">Pierre & Marbre</option>
			<option value="17">Poterie & C??ramique</option>
			<option value="18">Savonnerie & parfumerie</option>
			<option value="19">Textile, Mode et accessoires</option>
			<option value="20">Travail du Bois</option>
			<option value="21">Travail du Cuir</option>
			<option value="22">Travail du M??tal</option>
			<option value="23">Travail du Verre</option>
			<option value="24">Vignoble, Brasserie & distillerie</option>
		</select>
    </div>
    
	
	
	
	{if isset($extrafields) && isset($extrafields.quisuisje)}
    <div class="form-group">
      <textarea class="form-control" name="{$extrafields.quisuisje.attribute_name|escape:'htmlall':'UTF-8'}" id="{$extrafields.quisuisje.attribute_name|escape:'htmlall':'UTF-8'}" placeholder="{l s='Who am I? I present myself/my work in 3 lines.' mod='mpsellerwiselogin'}"
        maxlength="{$extrafields.quisuisje.char_limit|escape:'htmlall':'UTF-8'}">{if isset($smarty.post.quisuisje)}{$smarty.post.quisuisje|escape:'htmlall':'utf-8'}{/if}</textarea>
    </div>
    {/if} 
	
	{if isset($extrafields) && isset($extrafields.mapassion)}
    <div class="form-group">
      <textarea class="form-control" name="{$extrafields.mapassion.attribute_name|escape:'htmlall':'UTF-8'}" id="{$extrafields.mapassion.attribute_name|escape:'htmlall':'UTF-8'}" placeholder="{l s='My passion. In a few words, what animates/motivates me in my job?' mod='mpsellerwiselogin'}"
        maxlength="{$extrafields.mapassion.char_limit|escape:'htmlall':'UTF-8'}">{if isset($smarty.post.mapassion)}{$smarty.post.mapassion|escape:'htmlall':'utf-8'}{/if}</textarea>
    </div>
    {/if} 
	
	{if isset($extrafields) && isset($extrafields.unproverbe)}
    <div class="form-group">
      {**
      <!--input type="text" class="form-control" name="{$extrafields.unproverbe.attribute_name|escape:'htmlall':'UTF-8'}" id="{$extrafields.unproverbe.attribute_name|escape:'htmlall':'UTF-8'}"
			placeholder="{l s='A proverb, a diction, a phrase that suits me' mod='mpsellerwiselogin'}" maxlength="{$extrafields.unproverbe.char_limit|escape:'htmlall':'UTF-8'}" {if isset($smarty.post.unproverbe)}value="{$smarty.post.unproverbe|escape:'htmlall':'utf-8'}"{/if} /-->**}
      <select class="form-control" name="{$extrafields.unproverbe.attribute_name|escape:'htmlall':'UTF-8'}" id="{$extrafields.unproverbe.attribute_name|escape:'htmlall':'UTF-8'}">
				<option value="">Un proverbe un dicton, une phrase qui me correspond</option>
				<option value="?? bon vin point d'enseigne">A bon vin point d'enseigne</option>
				<option value="?? chaque jour suffit sa peine">A chaque jour suffit sa peine</option>
				<option value="?? chaque probl??me, une solution">A chaque probl??me, une solution</option>
				<option value="?? l'??uvre on conna??t l'artisan">A l'??uvre on conna??t l'artisan</option>
				<option value="bien faire, et laisser dire">Bien faire, et laisser dire</option>
				<option value="c'est dans les vieux pots qu'on fait la meilleure soupe">C'est dans les vieux pots qu'on fait la meilleure soupe</option>
				<option value="c'est en forgeant qu'on devient forgeron">C'est en forgeant qu'on devient forgeron</option>
				<option value="ce que femme veut, Dieu le veut">Ce que femme veut, Dieu le veut</option>
				<option value="ce qui ne tue pas rend plus fort">Ce qui ne tue pas rend plus fort</option>
				<option value="il n'est point de sot m??tier">Il n'est point de sot m??tier</option>
				<option value="il n'y a que le premier pas qui co??te">Il n'y a que le premier pas qui co??te</option>
				<option value="l'argent est un bon serviteur et un mauvais ma??tre">L'argent est un bon serviteur et un mauvais ma??tre</option>
				<option value="l'erreur est humaine">L'erreur est humaine</option>
				<option value="l'habit ne fait pas le moine">L'habit ne fait pas le moine</option>
				<option value="la critique est ais??e mais l'art est difficile">La critique est ais??e mais l'art est difficile</option>
				<option value="la fortune sourit aux audacieux">La fortune sourit aux audacieux</option>
				<option value="la parole est d'argent et le silence est d'or">La parole est d'argent et le silence est d'or</option>
				<option value="Le bon vivant n'est pas celui qui mange beaucoup, mais celui qui go??te avec bonheur ?? toutes les formes de la vie">Le bon vivant n'est pas celui qui mange beaucoup, mais celui qui go??te avec bonheur ?? toutes les formes de la vie</option>
				<option value="Le bonheur n'est vrai que quand il est partag??">Le bonheur n'est vrai que quand il est partag??</option>
				<option value="les petits ruisseaux font les grandes rivi??res">Les petits ruisseaux font les grandes rivi??res</option>
				<option value="Ne remets pas ?? demain ce que tu peux faire aujourd'hui">Ne remets pas ?? demain ce que tu peux faire aujourd'hui</option>
				<option value="Plaisir non partag?? n'est plaisir qu'?? moiti??">Plaisir non partag?? n'est plaisir qu'?? moiti??</option>
				<option value="Savoir partager son temps, c'est savoir jouir de la vie">Savoir partager son temps, c'est savoir jouir de la vie</option>
				<option value="Un brin de folie ??gaye la vie">Un brin de folie ??gaye la vie</option>
	        </select>

        <p>* Champs obligatoires</p>

    </div>
    {/if}
    <div class="form-group">
      <a id="submitProfile" href="#" class="next_btn">{l s='Next' mod='mpsellerwiselogin'}</a>
    </div>
  </div>
