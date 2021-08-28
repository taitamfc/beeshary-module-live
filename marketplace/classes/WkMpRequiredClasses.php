<?php
/**
* 2010-2020 Webkul.
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
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

include_once 'WkMpInstall.php';
include_once 'WkMpAdminShipping.php';
include_once 'WkMpCommission.php';
include_once 'WkMpCustomerPayment.php';
include_once 'WkMpHelper.php';
include_once 'WkMpImageUploader.php';
include_once 'WkMpOrderVoucher.php';
include_once 'WkMpSeller.php';
include_once 'WkMpSellerHelpDesk.php';
include_once 'WkMpSellerOrder.php';
include_once 'WkMpSellerOrderDetail.php';
include_once 'WkMpSellerPaymentMode.php';
include_once 'WkMpSellerPaymentSplit.php';
include_once 'WkMpSellerProduct.php';
include_once 'WkMpSellerProductCategory.php';
include_once 'WkMpSellerProductImage.php';
include_once 'WkMpSellerReview.php';

/*--- Combination ---*/
include_once 'WkMpProductAttribute.php';
include_once 'WkMpProductAttributeShop.php';
include_once 'WkMpProductAttributeImage.php';
include_once 'WkMpProductAttributeCombination.php';
include_once 'WkMpStockAvailable.php';
include_once 'WkMpAttributeImpact.php';

/*--- Combination Activate/Deactivate module ---*/
if (Module::isInstalled('wkcombinationcustomize')) {
    include_once dirname(__FILE__).'/../../wkcombinationcustomize/classes/WkCombinationStatus.php';
}

/*--- Product Feature ---*/
include_once 'WkMpProductFeature.php';

/*--- Order Status ---*/
include_once 'WkMpSellerOrderStatus.php';

/*--- Seller Payment Transaction ---*/
include_once 'WkMpSellerTransactionHistory.php';
