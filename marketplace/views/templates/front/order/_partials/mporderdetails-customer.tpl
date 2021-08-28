{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="box-account box-recent">
	<div class="box-head">
		<h2><i class="icon-user"></i> {l s='Customer Details' mod='marketplace'}</h2>
		<div class="wk_border_line"></div>
	</div>
	<div class="box-content">
		<div class="tabs">
			<ul class="nav nav-tabs">
				<li class="nav-item">
					<a class="nav-link active" href="#ship_addr" data-toggle="tab">
						<i class="icon-truck"></i>
						{l s='Shipping Address' mod='marketplace'}
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#invoice_addr" data-toggle="tab">
						<i class="icon-file-text"></i>
						{l s='Invoice Address' mod='marketplace'}
					</a>
				</li>
				{hook h="displayMpOrderCustomerDetailsTab"}
			</ul>
			<div class="tab-content" id="tab-content">
				<div class="tab-pane fade in active" id="ship_addr">
					<div class="well">
						<div class="row">
							<div class="col-sm-12">
								{$addresses.deliveryFormat nofilter}
								{if $addresses.delivery->other}
									<hr />{$addresses.delivery->other}<br />
								{/if}
							</div>
						</div>
					</div>
					{hook h="displayMpOrderDeliveryAddressBottom"}
				</div>
				<div class="tab-pane fade in" id="invoice_addr">
					<div class="well">
						<div class="row">
							<div class="col-sm-6">
								{$addresses.invoiceFormat nofilter}
								{if $addresses.invoice->other}
									<hr />{$addresses.invoice->other}<br />
								{/if}
							</div>
						</div>
					</div>
					{hook h="displayMpOrderInvoiceAddressBottom"}
				</div>
				{hook h="displayMpOrderCustomerDetailsTabContent"}
			</div>
		</div>
	</div>
</div>