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

class MpSellerVoucherSellerCartRuleModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Marketplace', array(), 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard')
        ];

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Manage Vouchers', array(), 'Breadcrumb'),
            'url' => $this->context->link->getModuleLink('mpsellervoucher', 'managevoucher')
        ];

        $pageBreadcrumbVar = ['url' => ''];
        if (Tools::getValue("id_mp_cart_rule")) {
            $pageBreadcrumbVar['title'] = $this->getTranslator()->trans('Edit Voucher', array(), 'Breadcrumb');
        } else {
            $pageBreadcrumbVar['title'] = $this->getTranslator()->trans('Add Voucher', array(), 'Breadcrumb');
        }
        $breadcrumb['links'][] = $pageBreadcrumbVar;

        return $breadcrumb;
    }

    public function initContent()
    {
        parent::initContent();

        $link = new Link();
        if (isset($this->context->customer->id)) {
            if (Tools::getValue('ajax'))
                $this->filterResults();

            $id_customer = $this->context->customer->id;
            $obj_mp_sellerinfo = new WkMpSeller();
            $obj_mp_cart_rule = new MpCartRule();

            $mp_seller_details = $obj_mp_sellerinfo->getSellerDetailByCustomerId($id_customer);
            if ($mp_seller_details && $mp_seller_details['active']) {
                $mp_seller_id = $mp_seller_details['id_seller'];
                $this->context->smarty->assign('seller_default_lang', $mp_seller_details['default_lang']);

                // Code For Edit Voucher
                if (Tools::getValue("id_mp_cart_rule")) {
                    $id_mp_cart_rule = Tools::getValue("id_mp_cart_rule");
                    $obj_mp_cart_rule = new MpCartRule((int)$id_mp_cart_rule);
                    if ($obj_mp_cart_rule->id_seller == $mp_seller_id) {
                        $voucher_detail = $obj_mp_cart_rule->getVoucherDetailById($id_mp_cart_rule, $mp_seller_details['default_lang']);
                        if ($voucher_detail) {
							$voucher_detail['date_from'] 	= date( 'd-m-Y H:i:s' , strtotime( $voucher_detail['date_from'] ) );
							$voucher_detail['date_to'] 		= date( 'd-m-Y H:i:s' , strtotime( $voucher_detail['date_to'] ) );
                            $this->context->smarty->assign('voucher_detail', $voucher_detail);
                        }
                    }
                }

                // Set default lang at every form according to configuration multi-language
                WkMpHelper::assignDefaultLang($mp_seller_id);

                $groups = Group::getGroups($this->context->language->id, true);
                $currencies = Currency::getCurrenciesByIdShop($this->context->shop->id);
                $current_currency = Currency::getCurrency($this->context->currency->id);

                // Generate countries list
                if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
                    $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
                } else {
                    $countries = Country::getCountries($this->context->language->id, true, false, false);
                }

                if (Configuration::get('MP_SELLER_CUSTOMER_VOUCHER_ALLOW')) {
                    if (Configuration::get('MP_VOUCHER_CUSTOMER_TYPE') == 1)
                        $customers = $obj_mp_cart_rule->getSellerProductOrderedCustomers($id_customer);
                    elseif (Configuration::get('MP_VOUCHER_CUSTOMER_TYPE') == 2)
                        $customers = Customer::getCustomers();

                    if ($customers)
                        $this->context->smarty->assign('customers', $customers);
                }


	            // NOTE : Only 'id_ps_product' > 0 :  seller products are listed because at the time of uploading data to ps table we need "id_ps_product" in "item" column of "ps_cart_rule_product_rule_value" table
                $sellerProducts = $obj_mp_cart_rule->getSellerProductByIdSeller($mp_seller_id, false, 1, $mp_seller_details['default_lang']);

                $this->context->smarty->assign(array(
                    'groups' => $groups,
                    'countries' => $countries,
                    'currencies' => $currencies,
                    'mp_seller_id' => $mp_seller_id,
                    'current_currency' => $current_currency,
                    'sellerProducts' => $sellerProducts,
                    'defaultDateFrom' => date('d-m-Y H:00:00'),
                    'defaultDateTo' => date('d-m-Y H:00:00', strtotime('+1 month')),
                    'logic' => 6,
                    'logged' => $this->context->customer->logged,
                ));
                $this->defineJSVars();
                $this->setTemplate('module:'.$this->module->name.'/views/templates/front/sellercartrule.tpl');
            } else {
                Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect('index.php?controller=authentication&back='.urlencode($link->getModuleLink('mpsellervoucher', 'sellercartrule')));
        }
    }

    public function filterResults()
    {
        $this->display_header = false;
        $this->display_footer = false;

        $obj_mp_cart_rule = new MpCartRule();
        $prod_word = Tools::getValue('word');
        $id_seller = Tools::getValue('id_seller');
        $obj_mp_sellerinfo = new WkMpSeller((int)$id_seller);
        if ($prod_word) {
            $mp_product = $obj_mp_cart_rule->getSellerProductByIdSeller($id_seller, $prod_word, 1, $obj_mp_sellerinfo->default_lang);
            die(Tools::jsonEncode($mp_product));
        }
        else
            die(false);
    }

    public function postProcess()
    {
        
		if (Tools::isSubmit('SubmitVoucher')) {
            if (isset($this->context->customer->id)) {
                $id_customer = $this->context->customer->id;
                $obj_mp_sellerinfo = new WkMpSeller();

                $mp_seller_details = $obj_mp_sellerinfo->getSellerDetailByCustomerId($id_customer);
                if ($mp_seller_details && $mp_seller_details['active']) {
					
					$_POST['date_from'] = ( isset($_POST['date_from']) ) ? date('Y-m-d H:i:s', strtotime($_POST['date_from']) ) : '';
					$_POST['date_to'] = ( isset($_POST['date_to']) ) ? date('Y-m-d H:i:s', strtotime($_POST['date_to']) ) : '';
					
                    $mp_seller_id = $mp_seller_details['id_seller'];
                    $seller_default_lang = $mp_seller_details['default_lang'];

                    // Get Default Lang
                    if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                        $default_lang = $seller_default_lang;
                    } else {
                        if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') { //Admin default lang
                            $default_lang = Configuration::get('PS_LANG_DEFAULT');
                        } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') { //Seller default lang
                            $default_lang = $seller_default_lang;
                        }
                    }

                    $languages = Language::getLanguages(false);
                    $className = 'MpCartRule';
                    $rules = call_user_func(array($className, 'getValidationRules'), $className);

                    /*==== Validations ====*/
                    if (!Tools::getValue('name_'.$default_lang)) {
                        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                            $seller_lang = Language::getLanguage((int) $default_lang);
                            $this->errors[] = Tools::displayError($this->module->l('Nom du bon de r??duction est requis ').$seller_lang['name']);
                        } else {
                            $this->errors[] = Tools::displayError($this->module->l('Nom du bon de r??duction est requis'));
                        }
                    }
                    else {
                        foreach ($languages as $language) {
                            $value = Tools::getValue('name_'.$language['id_lang']);
                            if ($value && Tools::strlen($value) > $rules['sizeLang']['name']) {
                                $this->errors[] = sprintf(Tools::displayError('Nom du bon de r??duction est trop long (%2$d car. max).'), $rules['sizeLang']['name']);
                            }
                            if (!Validate::isCleanHtml(Tools::getValue('name_'.$language['id_lang']))) {
                                $lang = Language::getLanguage((int)$language['id_lang']);
                                $this->errors[] = Tools::displayError($this->module->l("Le nom du bon de r??duction n'est pas valide ").$lang['name']);
                            }
                        }

                        if (Tools::getValue('for_customer')) {
                            if (!Validate::isUnsignedId(Tools::getValue('for_customer'))) {
                                $this->errors[] = Tools::displayError($this->module->l('Veuillez s??lectionner un client valide.'));
                            }
                        }

                        if (!Tools::getValue('date_from') || !Tools::getValue('date_to')) {
                            $this->errors[] = Tools::displayError($this->l('veuillez saisir une plage de dates valide.'));
                        } else {
                            if (strtotime(Tools::getValue('date_from')) > strtotime(Tools::getValue('date_to'))) {
                                $this->errors[] = Tools::displayError($this->l("Le bon ne peut pas se terminer avant qu'il ne commence."));
                            }
                        }

                        if (!Tools::getValue('reduction_type')) {
                            $this->errors[] = Tools::displayError($this->module->l('Veuillez s??lectionner le type de r??duction.'));
                        }
                        else {
                            if (!Validate::isInt(Tools::getValue('reduction_type'))) {
                                $this->errors[] = Tools::displayError($this->module->l("Une erreur s'est produite. Veuillez r??essayer."));
                            } else {
                                if (Tools::getValue('reduction_type') == 1) {
                                    if (!Validate::isPercentage(Tools::getValue('reduction_percent'))) {
                                        $this->errors[] = Tools::displayError($this->module->l("La valeur en pourcentage n'est pas valide."));
                                    }
                                } elseif (Tools::getValue('reduction_type') == 2) {
                                    if (!Validate::isFloat(Tools::getValue('reduction_amount'))) {
                                        $this->errors[] = Tools::displayError($this->module->l("Le montant n'est pas valide."));
                                    }
                                }
                            }
                        }

                        if (!Tools::getValue('reduction_for')) {
                            $this->errors[] = Tools::displayError($this->module->l('Veuillez s??lectionner une valeur dans laquelle la remise sera appliqu??e.'));
                        } else {
                            if (!Validate::isInt(Tools::getValue('reduction_for'))) {
                                $this->errors[] = Tools::displayError($this->module->l("Une erreur s'est produite. Veuillez r??essayer."));
                            } else {
                                if (Tools::getValue('reduction_for') == 1) {
                                    if (!Tools::getValue('mp_reduction_product')) {
                                        $this->errors[] = Tools::displayError($this->module->l('Veuillez s??lectionner un produit pour lequel vous cr??ez un bon.'));
                                    } else {
                                        if (!Validate::isInt(Tools::getValue('mp_reduction_product'))) {
                                            $this->errors[] = Tools::displayError($this->module->l('Veuillez s??lectionner un produit valide.'));
                                        }
                                        else {
                                            $mp_id_prod = Tools::getValue('mp_reduction_product');
                                            $obj_mp_product = new WkMpSellerProduct($mp_id_prod);
                                            $id_seller = $obj_mp_product->id_seller;

                                            if ($id_seller != $mp_seller_id) {
                                                $this->errors[] = Tools::displayError($this->module->l('Veuillez s??lectionner votre produit dans la liste d??roulante.'));
                                            }
                                        }
                                    }
                                } elseif (Tools::getValue('reduction_for') == 2) {
                                    if (Tools::getValue('reduction_type') == 2) {
                                        $this->errors[] = Tools::displayError($this->module->l("Une erreur s'est produite. Veuillez r??essayer."));
                                    } else {
                                        if (!Tools::getValue('multiple_reduction_product')) {
                                            $this->errors[] = Tools::displayError($this->module->l('S??lectionnez les produits sur lesquels le bon sera appliqu??.'));
                                        } else {
                                            $reductionProducts = Tools::getValue('multiple_reduction_product');
                                            foreach ($reductionProducts as $mpIdProduct) {
                                                if (!Validate::isInt($mpIdProduct)) {
                                                    $this->errors[] = Tools::displayError($this->module->l('Veuillez s??lectionner un produit valide.'));
                                                } else {
                                                    $obj_mp_product = new WkMpSellerProduct($mpIdProduct);
                                                    $id_seller = $obj_mp_product->id_seller;

                                                    if ($id_seller != $mp_seller_id) {
                                                        $this->errors[] = Tools::displayError($this->module->l("Une erreur s'est produite dans les produits s??lectionn??s, veuillez r??essayer plus tard."));
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    /*==== Validations ====*/

                    if (!count($this->errors)) {
                        if (Tools::getValue("id_mp_cart_rule")) {
                            $id_mp_cart_rule = Tools::getValue("id_mp_cart_rule");
                            $obj_mp_cart_rule = new MpCartRule((int)$id_mp_cart_rule);
                        }
                        else
                            $obj_mp_cart_rule = new MpCartRule();

                        $obj_mp_prod_rule = new MpCartRuleProductRule();
                        $obj_mp_prod_rule_grp = new MpCartRuleProductRuleGroup();

                        $_POST['product_restriction'] = 1;

                        $_POST['id_seller'] = $mp_seller_id;
                        foreach ($languages as $language) {
                            $voucher_lang_id = $language['id_lang'];
                            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                                if (!Tools::getValue('name_'.$voucher_lang_id)) {
                                    $voucher_lang_id = $default_lang;
                                }
                            } else {
                                $voucher_lang_id = $default_lang;
                            }

                            $obj_mp_cart_rule->name[$language['id_lang']] = Tools::getValue('name_'.$voucher_lang_id);
                        }

                        if (Tools::getValue("id_mp_cart_rule")) { // For update
                            if (!Configuration::get('MP_SELLER_VOUCHER_UPDATE_ADMIN_APPROVE')) {
                                $voucherStatus = ($obj_mp_cart_rule->admin_approval && $obj_mp_cart_rule->active) ? 1 : 0 ;
                                $_POST['active'] = $voucherStatus;
                                $_POST['admin_approval'] = $voucherStatus;
                            } else {
                                $_POST['active'] = 0;
                                $_POST['admin_approval'] = 0;
                            }
                        } else {
                            if (!Configuration::get('MP_SELLER_VOUCHER_ADMIN_APPROVE')) {
                                $_POST['active'] = 1;
                                $_POST['admin_approval'] = 1;
                            }
                        }
						
						$_POST['active'] = 1;
						$_POST['admin_approval'] = 1;

                        if (Tools::getValue("id_mp_cart_rule")) { // For Update
                            $prod_upd = true;
                            $old_id_mp_prod = $obj_mp_cart_rule->mp_reduction_product;
                            if (Tools::getValue('reduction_for') == 1) {
                                $new_id_mp_prod = Tools::getValue("mp_reduction_product");
                                if ($new_id_mp_prod == $old_id_mp_prod) {
                                    $prod_upd = false;
                                }
                            }
                        }

                        //data enter in object
                        $this->errors = $obj_mp_cart_rule->validateController();
                        $obj_mp_cart_rule->description = Tools::getValue('description');
                        $obj_mp_cart_rule->code = Tools::getValue('code');
                        $obj_mp_cart_rule->priority = Tools::getValue('priority');
                        $obj_mp_cart_rule->for_customer = trim(Tools::getValue('for_customer'));
                        $obj_mp_cart_rule->quantity = Tools::getValue('quantity');
                        $obj_mp_cart_rule->quantity_per_user = Tools::getValue('quantity_per_user');
                        $obj_mp_cart_rule->country_restriction = Tools::getValue('country_restriction');
                        $obj_mp_cart_rule->group_restriction = Tools::getValue('group_restriction');
                        $obj_mp_cart_rule->cart_rule_restriction = Tools::getValue('cart_rule_restriction');
                        $obj_mp_cart_rule->product_restriction = Tools::getValue('product_restriction');
                        $obj_mp_cart_rule->active = Tools::getValue('active');
                        $obj_mp_cart_rule->admin_approval = Tools::getValue('admin_approval');

                        if (Tools::getValue('reduction_type') == 1) {
                            $obj_mp_cart_rule->reduction_amount = 0;
                        } elseif (Tools::getValue('reduction_type') == 2) {
                            $obj_mp_cart_rule->reduction_percent = 0;
                        }

                        if (Tools::getValue('reduction_for') == 1) {
                            $obj_mp_cart_rule->mp_reduction_product = Tools::getValue('mp_reduction_product');
                        } elseif (Tools::getValue('reduction_for') == 2) {
                            $obj_mp_cart_rule->mp_reduction_product = -2;
                        }

                        if (!count($this->errors)) {
                            if ($obj_mp_cart_rule->save()) {
                                $id_mp_cart_rule = $obj_mp_cart_rule->id;

                                // Insert Into "mp_cart_rule_country" and "mp_cart_rule_group" tables
                                if ($obj_mp_cart_rule->country_restriction || $obj_mp_cart_rule->group_restriction) {
                                    $country_select = Tools::getValue('country_select');
                                    $group_select = Tools::getValue('group_select');

                                    $upd_data = Tools::getValue("id_mp_cart_rule") ? 1 : 0 ;
                                    $obj_mp_cart_rule->insertIntoMpCartRuleCountryGroupTables($country_select, $group_select, false, $upd_data);
                                }

                                $mpVoucherItems = false;
                                if (Tools::getValue('reduction_for') == 1) {
                                    $mpVoucherItems = $obj_mp_cart_rule->mp_reduction_product;
                                } elseif (Tools::getValue('reduction_for') == 2) {
                                    $mpVoucherItems = Tools::getValue('multiple_reduction_product');
                                }

                                // NOTE : @TODO if more options are added instead of only specific product then for differentiation use mp_reduction_product like it is done in prestashop with reduction_product variable
                                if (Tools::getValue("id_mp_cart_rule")) { // For update
                                    if ($prod_upd) {
                                        $obj_mp_prod_rule_grp->updateMpIdProdByIdMpCartRule($id_mp_cart_rule, $mpVoucherItems);
                                    }
                                } else {
                                    if ($obj_mp_cart_rule->product_restriction > 0) {  // for specific product
                                        $obj_mp_prod_rule_grp->id_mp_cart_rule = $id_mp_cart_rule;
                                        $obj_mp_prod_rule_grp->quantity = 1;
                                        $obj_mp_prod_rule_grp->save();
                                        $id_mp_product_rule_group = $obj_mp_prod_rule_grp->id;

                                        $obj_mp_prod_rule->id_mp_product_rule_group = $id_mp_product_rule_group;
                                        $obj_mp_prod_rule->type = 'products';
                                        $obj_mp_prod_rule->save();
                                        $id_mp_product_rule = $obj_mp_prod_rule->id;

                                        $obj_mp_prod_rule->insertIntoMpCartRuleProductRuleValue($id_mp_product_rule, $mpVoucherItems);
                                    }
                                }

                                if (Tools::getValue("id_mp_cart_rule")) { // For update
                                    if (!Configuration::get('MP_SELLER_VOUCHER_UPDATE_ADMIN_APPROVE')) {
                                        if ($obj_mp_cart_rule->id_ps_cart_rule) {
                                            $ps_id_cart_rule = $obj_mp_cart_rule->insertUpdateDataIntoPsTables(false, 1);
                                        } else {
                                            $ps_id_cart_rule = $obj_mp_cart_rule->insertUpdateDataIntoPsTables();
                                        }
                                    } else {
                                        if ($obj_mp_cart_rule->id_ps_cart_rule) {
                                            $cart_rule = new CartRule($obj_mp_cart_rule->id_ps_cart_rule);
                                            $cart_rule->active = 0;
                                            $cart_rule->save();
                                        }
                                    }
                                } else {
                                    if (!Configuration::get('MP_SELLER_VOUCHER_ADMIN_APPROVE')) {
                                        $ps_id_cart_rule = $obj_mp_cart_rule->insertUpdateDataIntoPsTables();
                                    }
                                }

                                // redirect
                                $params = array('id_mp_cart_rule' => $id_mp_cart_rule);
                                if (Tools::getValue("id_mp_cart_rule"))
                                    $params['edit_conf'] = 1;
                                else
                                    $params['add_conf'] = 1;
                                Tools::redirect($this->context->link->getModuleLink('mpsellervoucher', 'sellercartrule', $params));
                            }
                        }
                    }
                }
            }
        }
    }

    public function defineJSVars()
    {
        $jsVars = [
                'controller_link' => $this->context->link->getModulelink('mpsellervoucher', 'sellercartrule'),
                'currentText' => $this->trans('Now', [], 'Modules.MpSellerVoucher'),
                'closeText' => $this->trans('Done', [], 'Modules.MpSellerVoucher'),
                'timeOnlyTitle' => $this->trans('Choose Time', [], 'Modules.MpSellerVoucher'),
                'timeText' => $this->trans('Time', [], 'Modules.MpSellerVoucher'),
                'hourText' => $this->trans('Hour', [], 'Modules.MpSellerVoucher'),
                'minuteText' => $this->trans('Minute', [], 'Modules.MpSellerVoucher'),
            ];
        Media::addJsDef($jsVars);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');
        $this->registerStylesheet('seller-voucher-front-css', 'modules/'.$this->module->name.'/views/css/sellerVoucherFront.css');
        $this->registerJavascript('seller-voucher-front-js', 'modules/'.$this->module->name.'/views/js/sellerVoucherFront.js');

        $this->registerJavascript(
            'jquery-ui-timepicker-addon-js',
            'js/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js',
            [
                'media' => 'all',
                'priority' => 900,
                'position' => 'bottom',
            ]
        );
    }
}
