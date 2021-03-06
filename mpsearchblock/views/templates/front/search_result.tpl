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

{extends file=$layout}
{block name='content'}
<h1 class="page-heading  product-listing">
	{l s='Search' mod='mpsearchblock'}&nbsp;
	{if isset($key)}
		<span class="lighter">"{$key}"</span>
	{/if}
	<span class="heading-counter">{$count_result|intval}&nbsp;{l s=' results have been found' mod='mpsearchblock'}</span>
</h1>
{if isset($error)}
	{* <div class="alert {if $error == 1}alert-warning{elseif $error == 2}alert-danger{/if} alert-dismissible" role="alert">
		{if isset($error)}
			<strong>{l s='Error!' mod='mpsearchblock'}</strong>
		{/if}
		<span>
			{if isset($error)}
				{if $error == 1}
					{l s='Please enter a search keyword.' mod='mpsearchblock'}
				{elseif $error == 2}
					{l s='No results were found for your search' mod='mpsearchblock'}&nbsp;"{$key}"
				{/if}
			{/if}
		</span>
	</div> *}
{else}
	<div class="content_sortPagiBar">
		<div class="sortPagiBar clearfix">
			<div class="row">
				<div class="col-sm-5">
					<form class="productsSortForm" id="productsSortForm">
						<div class="select selector1">
							<label for="selectProductSort">{l s='Sort by' mod='mpsearchblock'}</label>
							<select class="selectProductSort form-control" id="selectProductSort" name="search_type">
								<option selected="selected">--</option>
								{if isset($ps_product)}
									<option value="{$link->getModuleLink('mpsearchblock', 'formsearch', ['top_search_box' => $key, 'search_type' => $category, 'sort_by' => '1'])}" {if isset($sortBy)}{if $sortBy == 1}selected="selected"{/if}{/if}>{l s='Product Price: Lowest first' mod='mpsearchblock'}</option>
									<option value="{$link->getModuleLink('mpsearchblock', 'formsearch', ['top_search_box' => $key, 'search_type' => $category, 'sort_by' => '2'])}" {if isset($sortBy)}{if $sortBy == 2}selected="selected"{/if}{/if}>{l s='Product Price: Highest first' mod='mpsearchblock'}</option>
								{/if}
								<option value="{$link->getModuleLink('mpsearchblock', 'formsearch', ['top_search_box' => $key, 'search_type' => $category, 'sort_by' => '3'])}" {if isset($sortBy)}{if $sortBy == 3}selected="selected"{/if}{/if}>{l s='Name: A to Z' mod='mpsearchblock'}</option>
								<option value="{$link->getModuleLink('mpsearchblock', 'formsearch', ['top_search_box' => $key, 'search_type' => $category, 'sort_by' => '4'])}" {if isset($sortBy)}{if $sortBy == 4}selected="selected"{/if}{/if}>{l s='Name: Z to A' mod='mpsearchblock'}</option>
							</select>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="row no_margin searchResultWrapper">
		{if isset($ps_product)}
			<div class="col-sm-12 margin-top-15 searchCatCont">
				<div class="row no_padding">
					<div class="col-sm-12 category_header clearfix" style="background-color:{$title_bg_color};">
						<span class="text-uppercase pull-left cat_header_font" style="color:{$title_text_color};">{l s='PRODUCTS' mod='mpsearchblock'}</span>
						<span class="pull-right cat_toggle_btn" data-related-id="mp_prod_container">
							<i class="material-icons">&#xE8D6;</i>
						</span>
					</div>
				</div>
				<div class="row no_padding" id="mp_prod_container">
					<div class="create_block cat_container">
						{foreach from=$ps_product key=product_k item=product_v}
							<div class="result_block {if $product_k > 9}wk_hide_result{/if}">
								<div class="col-sm-3">
									<img src="{$product_v['mp_product_img']}" class="img-thumbnail result_img">
								</div>
								<div class="col-sm-9">
									<p class="result_name">{$product_v['name']}</p>
									<div class="create_block result_desc">
										{$product_v['description_short'] nofilter}
									</div>
									<p class="result_price" itemprop="price">
										{$product_v.price}
									</p>
									<a href="{$product_v['link']}" class="btn btn-primary">{l s='View Product' mod='mpsearchblock'}</a>
								</div>
							</div>
						{/foreach}
						{if ($ps_product|@count) > 10}
							<button type="button" class="btn btn-success wk_view_all_js">{l s='View All' mod='mpsearchblock'}</button>
						{/if}
					</div>
				</div>
			</div>
		{/if}

		{if isset($mp_shop)}
			<div class="col-sm-12 margin-top-15 searchCatCont">
				<div class="row no_padding">
					<div class="col-sm-12 category_header clearfix" style="background-color:{$title_bg_color};">
						<span class="text-uppercase pull-left cat_header_font" style="color:{$title_text_color};">{l s='SHOPS' mod='mpsearchblock'}</span>
						<span class="pull-right cat_toggle_btn" data-related-id="mp_shop_container">
							<i class="material-icons">&#xE8D6;</i>
						</span>
					</div>
				</div>
				<div class="row no_padding" id="mp_shop_container">
					<div class="create_block cat_container">
						{foreach from=$mp_shop key=shop_k item=shop_v}
							<div class="result_block {if $shop_k > 9}wk_hide_result{/if}">
								<div class="col-sm-3">
									<img src="{$shop_v['mp_shop_img']}" class="img-thumbnail result_img">
								</div>
								<div class="col-sm-9">
									<p class="result_hvsub_name">{$shop_v['mp_shop_name']}</p>
									<p class="result_sub_name">{$shop_v['mp_seller_name']}</p>
									<div class="create_block result_desc">
										{$shop_v['mp_shop_desc'] nofilter}
									</div>
									{* <a href="{$link->getModuleLink('marketplace', 'shopstore', ['mp_shop_name' => $shop_v['mp_shop_rewrite']])}" class="btn btn-primary">{l s='View Shop' mod='mpsearchblock'}</a> *}
									<a href="{$link->getModuleLink('marketplace', 'sellerprofile', ['mp_shop_name' => $shop_v['mp_shop_rewrite']])}" class="btn btn-primary">{l s='View Seller Profile' mod='mpsearchblock'}</a>
								</div>
							</div>
						{/foreach}
						{if ($mp_shop|@count) > 10}
							<button type="button" class="btn btn-success wk_view_all_js">{l s='View All' mod='mpsearchblock'}</button>
						{/if}
					</div>
				</div>
			</div>
		{/if}

		{if isset($seller)}
			<div class="col-sm-12 margin-top-15 searchCatCont">
				<div class="row no_padding">
					<div class="col-sm-12 category_header clearfix" style="background-color:{$title_bg_color};">
						<span class="text-uppercase pull-left cat_header_font" style="color:{$title_text_color};">{l s='SELLERS' mod='mpsearchblock'}</span>
						<span class="pull-right cat_toggle_btn" data-related-id="mp_seller_container">
							<i class="material-icons">&#xE8D6;</i>
						</span>
					</div>
				</div>
				<div class="row no_padding" id="mp_seller_container">
					<div class="create_block cat_container">
						{foreach from=$seller key=seller_k item=seller_v}
							<div class="result_block {if $seller_k > 9}wk_hide_result{/if}">
								<div class="col-sm-3">
									<img src="{$seller_v['mp_seller_img']}" class="img-thumbnail result_img">
								</div>
								<div class="col-sm-9">
									<p class="result_hvsub_name">{$seller_v['mp_seller_name']}</p>
									<p class="result_sub_name">{$seller_v['mp_shop_name']}</p>
									<div class="create_block result_desc">
										{$seller_v['mp_shop_desc'] nofilter}
									</div>
									<a href="{$link->getModuleLink('marketplace', 'sellerprofile', ['mp_shop_name' => $seller_v['mp_shop_rewrite']])}" class="btn btn-primary">{l s='View Seller Profile' mod='mpsearchblock'}</a>
									{* <a href="{$link->getModuleLink('marketplace', 'shopstore', ['mp_shop_name' => $seller_v['mp_shop_rewrite']])}" class="btn btn-primary">{l s='View Shop' mod='mpsearchblock'}</a> *}
								</div>
							</div>
						{/foreach}
						{if ($seller|@count) > 10}
							<button type="button" class="btn btn-success wk_view_all_js">{l s='View All' mod='mpsearchblock'}</button>
						{/if}
					</div>
				</div>
			</div>
		{/if}

		{if isset($shop_locat)}
			<div class="col-sm-12 margin-top-15 searchCatCont">
				<div class="row no_padding">
					<div class="col-sm-12 category_header clearfix" style="background-color:{$title_bg_color};">
						<span class="text-uppercase pull-left cat_header_font" style="color:{$title_text_color};">{l s='LOCATIONS' mod='mpsearchblock'}</span>
						<span class="pull-right cat_toggle_btn" data-related-id="mp_shop_locat_container">
							<i class="material-icons">&#xE8D6;</i>
						</span>
					</div>
				</div>
				<div class="row no_padding" id="mp_shop_locat_container">
					<div class="create_block cat_container">
						{foreach from=$shop_locat key=shop_locat_k item=shop_locat_v}
							<div class="result_block {if $shop_locat_k > 9}wk_hide_result{/if}">
								<div class="col-sm-3">
									<img src="{$shop_locat_v['mp_shop_img']}" class="img-thumbnail result_img">
								</div>
								<div class="col-sm-9">
									<p class="result_hvsub_name">{$shop_locat_v['mp_shop_name']}</p>
									<p class="result_sub_name">{$shop_locat_v['mp_seller_name']}</p>
									<div class="create_block result_desc">
										{$shop_locat_v['mp_shop_desc'] nofilter}
									</div>
									{* <a href="{$link->getModuleLink('marketplace', 'sellerprofile', ['mp_shop_name' => $seller_v['mp_shop_rewrite']])}" class="btn btn-primary">{l s='View Seller Profile' mod='mpsearchblock'}</a> *}
									 <a href="{$link->getModuleLink('marketplace', 'shopstore', ['mp_shop_name' => $shop_locat_v['mp_shop_rewrite']])}" class="btn btn-primary">{l s='View Seller Profile' mod='mpsearchblock'}</a> 
								</div>
							</div>
						{/foreach}
						{if ($shop_locat|@count) > 10}
							<button type="button" class="btn btn-success wk_view_all_js">{l s='View All' mod='mpsearchblock'}</button>
						{/if}
					</div>
				</div>
			</div>
		{/if}

		{if isset($category_detail)}
			<div class="col-sm-12 margin-top-15 searchCatCont">
				<div class="row no_padding">
					<div class="col-sm-12 category_header clearfix" style="background-color:{$title_bg_color};">
						<span class="text-uppercase pull-left cat_header_font" style="color:{$title_text_color};">{l s='CATEGORIES' mod='mpsearchblock'}</span>
						<span class="pull-right cat_toggle_btn" data-related-id="category_container">
							<i class="material-icons">&#xE8D6;</i>
						</span>
					</div>
				</div>
				<div class="row no_padding" id="category_container">
					<div class="create_block cat_container">
						{foreach from=$category_detail key=cat_k item=cat_v}
							<div class="result_block {if $cat_k > 9}wk_hide_result{/if}">
								<div class="col-sm-3">
									<img src="{$link->getCatImageLink($cat_v['link_rewrite'], $cat_v['id_image'])}" class="img-thumbnail result_img">
								</div>
								<div class="col-sm-9">
									<p class="result_hvsub_name">{$cat_v['name']}</p>
									<p class="result_sub_name"></p>
									<div class="create_block result_desc">
										{$cat_v['description'] nofilter}
									</div>
									<a href="{$cat_v['link']}" class="btn btn-primary">{l s='View Category' mod='mpsearchblock'}</a>
								</div>
							</div>
						{/foreach}
						{if ($category_detail|@count) > 10}
							<button type="button" class="btn btn-success wk_view_all_js">{l s='View All' mod='mpsearchblock'}</button>
						{/if}
					</div>
				</div>
			</div>
		{/if}
	</div>
{/if}
{/block}