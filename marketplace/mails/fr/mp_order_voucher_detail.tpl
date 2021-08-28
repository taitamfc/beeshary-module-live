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

{if isset($list) && $list}
	<table class="table table-recap" bgcolor="#ffffff" style="float: right;width:50%;border-collapse:collapse">
		<!-- Title -->
		<thead>
			<tr>
				<th
					colspan="6"
					style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px;">
					Voucher Details
				</th>
			</tr>
			<tr>
				<th
					style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px;">
					Discount Name
				</th>
				<th
					style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px;">
					Value
				</th>
			</tr>
		</thead>
		<tbody>
			{foreach $list.mp_voucher_info as $mp_voucher}
				<tr>
					<td style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px; text-align: center;">{$mp_voucher['voucher_name']}</td>
					<td style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px; text-align: center;">{$mp_voucher['voucher_value']}</td>
				</tr>
			{/foreach}
			<tr>
				<td style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px; text-align: center;"><strong>{l s='Total' mod='marketplace'}</strong></td>
				<td style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px; text-align: center;">{$list.total_voucher}</td>
			</tr>
		</tbody>
	</table>
{/if}