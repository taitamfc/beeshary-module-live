<?php
/**
* 2010-2017 Webkul.
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MarketplaceUpdateOrderTrackingNumberModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $idOrder = Tools::getValue('id_order');
        $trackingNumber = Tools::getValue('tracking_number');
        $idOrderCarrier = Tools::getValue('id_order_carrier');

        if (isset($idOrder) && $idOrder != '' && isset($trackingNumber) && $trackingNumber != '' && isset($idOrderCarrier) && $idOrderCarrier != '') {
            $order = new Order($idOrder);
            $objOrderCarrier = new OrderCarrier($idOrderCarrier);

            // Update shipping number
            $order->shipping_number = Tools::getValue('tracking_number');
            $orderUpdated = $order->update();

            // Update order_carrier
            $objOrderCarrier->trackingNumber = pSQL(Tools::getValue('tracking_number'));
            $carrierUpdated = $objOrderCarrier->update();
            if ($orderUpdated && $carrierUpdated) {
                die(true);
            }
            die(false);
        }
    }
}
