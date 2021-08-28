{*
* 2010-2016 Webkul
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author Webkul IN <support@webkul.com>
*  @copyright  2010-2016 Webkul IN
*}
<table id="payment-tab" width="100%">
	<tr>
		<td class="payment center small grey bold" width="44%">{l s='Payment Method' mod='mpsellerinvoice'}</td>
		<td class="payment left white" width="56%">
			<table width="100%" border="0">
				{foreach from=$order->getOrderPaymentCollection() item=payment}
					<tr>
						<td class="right small">{$payment->payment_method}</td>
						{* <!-- <td class="right small">{displayPrice currency=$payment->id_currency price=$payment->amount}</td> --> *}
					</tr>
				{/foreach}
			</table>
		</td>
	</tr>
</table>
