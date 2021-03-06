<?php
/**
 * 2017-2018 PHPIST.
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
 *  @author    Yassine Belkaid <yassine.belkaid87@gmail.com>
 *  @copyright 2017-2018 PHPIST
 *  @license   https://store.webkul.com/license.html
 */

use PrestaShop\PrestaShop\Core\Crypto\Hashing as Crypto;


require_once(_PS_MODULE_DIR_.'mpbadgesystem/classes/MpSellerBadges.php');
require_once(_PS_MODULE_DIR_.'mpbadgesystem/classes/MpBadge.php');
require_once(_PS_MODULE_DIR_.'marketplace/classes/WkMpSellerDelivery.php');
class MpSellerWiseLoginSellerCreationModuleFrontController extends ModuleFrontController
{
    const MP_SELLER_CREATION_PROFILE = 0;
    const MP_SELLER_CREATION_STORE = 1;
    const MP_SELLER_CREATION_IMAGES = 2;
    const MP_SELLER_CREATION_DELIVERY_METHOD = 2;
    const MP_SELLER_CREATION_TERMS = 3;

    public function initContent()
    {
        parent::initContent();

        if ($this->context->cookie->logged) {
            $seller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if($seller){
                Tools::redirect($this->context->link->getModulelink('marketplace', 'dashboard'));
            }

            // Tools::redirect($this->context->link->getModulelink('marketplace', 'dashboard'));
            // $customer = new Customer($this->context->cookie->id_customer);
            // $customer->logout();
        }

        if (Tools::isSubmit('signupForm') && (int)Tools::getValue('ajax')) {
            $this->signupProcess();
        } elseif (Tools::isSubmit('submitSellerCreationForm')) {
            $this->sellerRegistrationProcess();
        } else {
            $this->renderForm();
        }
    }

    /**
     * @todo refactor. this method must not be into this module. must override.
     * @param $fields
     * @param $action
     * @param string $method
     * @return array|mixed|object
     */
    public function subscription($fields,$action,$method='POST'){
        $url = 'https://api.stripe.com/v1/'.$action;

        if(Configuration::get('STRIPE_MODE') == 0){
            $secret_key = Configuration::get('STRIPE_PRIVATE_KEY_TEST');
        }else{
            $secret_key = Configuration::get('STRIPE_PRIVATE_KEY_LIVE');
        }
        $headers = array('Authorization: Bearer '. $secret_key);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        if($method == 'POST'){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true); // return php array with api response
    }
    public function renderForm()
    {



        /**** create customer
        $customerRequest = array (

        'email' => 'abcddsdfsf@xyz.com', //customer email

        );
        $customerCurlRequest = $this->subscription('sk_test_BlvS2tRLOGMayfekcpG9QKGN',$customerRequest,'customers');
        echo "<pre>";print_r($customerCurlRequest);die;
         */


        /**** create card token

        $cardTokenRequest = array (
        "card" => [
        'number' => '4242424242424242',
        'exp_month' => '08',
        'exp_year' => '2022',
        'cvc' => '123',
        ]

        );
        $cardTokenCurlRequest = $this->subscription('sk_test_BlvS2tRLOGMayfekcpG9QKGN',$cardTokenRequest,'tokens');
        echo "<pre>";print_r($cardTokenCurlRequest);die;

         */

        /**** add card to customer
        $cardRequest = array (

        'source' => 'tok_1E5ooGLMmP4OBSVPP707j5EO', //customer email

        );
        $curlRequest = $this->subscription('sk_test_BlvS2tRLOGMayfekcpG9QKGN',$cardRequest,'customers/cus_EYw8verfGInae6/sources');
        echo "<pre>";print_r($curlRequest);die;*/
        /*** create subscription

        $subscriptionsRequest = array (
        "customer" => "cus_EYw8verfGInae6",
        "items" => [
        [
        "plan" => "fix_plan",
        ],
        ]

        );
        $subscriptionCurlRequest = $this->subscription('sk_test_BlvS2tRLOGMayfekcpG9QKGN',$subscriptionsRequest,'subscriptions');
        echo "<pre>";print_r($subscriptionCurlRequest);die;*/
        /**** get all plans*/
        $planRequest = array ();
        $plansCurlRequest = $this->subscription($planRequest,'plans?limit=3','GET');
        //echo "<pre>";print_r($plansCurlRequest);die;
        $this->smartyVars();
        $this->defineJSVars();

        $requestSeller = false;
        $obj_customer = false;
        if ($this->context->cookie->logged) {
            $seller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if( !$seller ){
                $requestSeller 	= true;
                $customer_id 	= $this->context->customer->id;
                $obj_customer 	= new Customer($customer_id);
                $obj_customer	= (array)$obj_customer;
            }
        }

        $this->context->smarty->assign([
            'requestSeller'	=>	$requestSeller,
            'obj_customer'	=>	$obj_customer,
            'stripeplans'	=>	$plansCurlRequest['data'],
            'stripelogo'	=>	Configuration::get('STRIPE_POPUP_LOGO'),
            'pkkey'			=>	(Configuration::get('STRIPE_MODE')==0)?Configuration::get('STRIPE_PUBLIC_KEY_TEST'):Configuration::get('STRIPE_PUBLIC_KEY_LIVE')
        ]);


        $this->setTemplate('module:'.$this->module->name.'/views/templates/front/sellercreation.tpl');

    }

    public function signupProcess()
    {
        Hook::exec('actionBeforeSubmitAccount');

        $firstname = Tools::getValue('firstname');
        $lastname = Tools::getValue('lastname');
        $email = Tools::getValue('email');
        $password = Tools::getValue('password');
        $password_conf = Tools::getValue('password_conf');
        $_errors = array();
        $responseKey = Tools::getValue('g-recaptcha-response');

        $verify_url = "https://www.google.com/recaptcha/api/siteverify?secret=6LdTvFoUAAAAAOht-DLTAxbJqojWW7ZHWwQYwQ1d&response=$responseKey";

        $response = file_get_contents($verify_url);
        $response = Tools::jsonDecode($response);

        if ($response->success == false) {
            $_errors[] = $this->module->l("Merci de remplir le captcha");
        } elseif (empty($firstname) || strlen($firstname) > 255) {
            $_errors[] = $this->module->l('Merci de renseigner un pr??nom correct');
        } elseif (empty($lastname) || strlen($lastname) > 255) {
            $_errors[] = $this->module->l('Merci de renseigner un nom correct');
        } elseif (!Validate::isEmail($email) || empty($email)) {
            $_errors[] = $this->module->l('Merci de renseigner une adresse email correcte');
        } elseif (Customer::customerExists($email)) {
            $_errors[] = $this->module->l('Cette adresse email est d??j?? utilis??');
        } elseif (strlen($email) > 128) {
            $_errors[] = $this->module->l('Le champs %1$s est trop long (%2$d caract??res maximum).',
                array('email', 128), 'Shop.Notifications.Error');
        } elseif (!Validate::isPasswd($password) || (empty($password))) {
            $_errors[] = $this->module->l('Merci de renseigner le mot de passe');
        } elseif (empty($password_conf) || !Validate::isPasswd($password_conf) || $password != $password_conf) {
            $_errors[] = $this->module->l("La confirmation du mot de passe n'est pas valide");
        }

        if (count($_errors)) {
            die(json_encode(['status' => false, '_errors' => $_errors]));
        }

        $customer = new Customer();
        $crypto   = new Crypto();

        if ($this->context->cookie->logged) {
            $customer_id 	= $this->context->customer->id;
            $customerObj 	= new Customer($customer_id);
        }else{
            $customerObj = new Customer();
            $customer->firstname = $firstname;
            $customer->lastname  = $lastname;
            $customer->email     = $email;
        }
        $customer->passwd    = $crypto->hash($password, _COOKIE_KEY_);

        $customer->newsletter = false;
        if ((int)Tools::getValue('subscribed')) {
            $customer->newsletter = true;
        }

        $ok = $customer->save();

        if ($ok) {
            $this->context->updateCustomer($customer);
            $this->context->cart->update();
            $this->sendConfirmationMail($customer, $password);
            Hook::exec('actionCustomerAccountAdd', array(
                'newCustomer' => $customer,
            ));
        }

        die(json_encode(['status' => true, 'redirect_url' => $this->context->link->getPageLink('index')]));
    }

    public function sellerRegistrationProcess()
    {
        Hook::exec('actionBeforeSubmitAccount');
        //$tokenRequest = array ();
        //$tokenCurlRequest = $this->subscription($tokenRequest,'tokens/'.POST['stripeToken'],'GET');
        //echo "<pre>";print_r($tokenCurlRequest);

        //echo "<pre>";print_r($_POST);die;
        $partnerId = Tools::getValue('partner_id');
        $seller_firstname = Tools::getValue('firstname');
        $seller_lastname = Tools::getValue('lastname');
        $seller_email = Tools::getValue('email_pro');
        $store_name = Tools::getValue('store_name');
        $store_name_unique = Tools::getValue('store_name_unique');
        $store_address = Tools::getValue('store_address');
        $post_code = Tools::getValue('post_code');
        $city = Tools::strtolower(Tools::getValue('city'));
        $tel_pro = Tools::getValue('tel_pro');
        $passwd = Tools::getValue('passwd');
        $longitude = Tools::getValue('longitude','');
        $latitude = Tools::getValue('latitude','');



        $_errors = array();

        // input validation
        if (!Validate::isName($seller_firstname) || empty($seller_firstname)) {
            $_errors['firstname'] = $this->module->l('Merci de renseigner votre pr??nom !');
        }
        if (!Validate::isName($seller_lastname) || empty($seller_lastname)) {
            $_errors['lastname'] = $this->module->l('Merci de renseigner votre nom !');
        }
        if (!Validate::isEmail($seller_email) || empty($seller_email)) {
            $_errors['seller_email'] = $this->module->l('Merci de renseigner votre adresse email !');
        } else if (Customer::customerExists($seller_email) || WkMpSeller::isSellerEmailExist($seller_email)) {
            $_errors['seller_email'] = $this->module->l("L'adresse email est d??j?? utilis??e");
        }

        if ($this->context->cookie->logged) {
            unset( $_errors['firstname'] );
            unset( $_errors['lastname'] );
            unset( $_errors['seller_email'] );
        }


        if (!Validate::isCatalogName($store_name) || empty($store_name)) {
            $_errors[] = $this->module->l('Merci de renseigner votre nom de boutique !');
        }
        if (!Validate::isCatalogName($store_name_unique) || empty($store_name_unique)) {
            $_errors[] = $this->module->l('Merci de renseigner votre nom de boutique unique !');
        }
        if (WkMpSeller::isShopNameExist($store_name_unique)) {
            $_errors[] = $this->module->l('Nom de boutique unique d??j?? utilis?? !');
        }
        if (!Validate::isAddress($store_address) || empty($store_address)) {
            $_errors[] = $this->module->l("Merci de renseigner l'adresse de la boutique");
        }
        if (!Validate::isPostCode($post_code) || empty($post_code)) {
            $_errors[] = $this->module->l('Merci de renseigner un code postal');
        }
        if (!Validate::isCityName($city) || empty($city)) {
            $_errors[] = $this->module->l('Merci de renseigner une ville');
        }
        if (!Validate::isPhoneNumber($tel_pro) || empty($tel_pro)) {
            $_errors[] = $this->module->l('Merci de renseigner un num??ro de t??l??phone');
        }
        if (!Validate::isGenericName(Tools::getValue('profession')) || empty(Tools::getValue('profession'))) {
            $_errors[] = $this->module->l('Merci de renseigner votre m??tier');
        }
        if (!Validate::isSiret(Tools::getValue('siret')) || empty(Tools::getValue('siret'))){
            $_errors[] = 'Veuillez saisir un numero SIRET valide (14 chiffres)';
        }


        if (count($_errors)) {
            $this->smartyVars();
            $this->context->smarty->assign([
                'errors' => $_errors,
            ]);
            $this->defineJSVars();
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/sellercreation.tpl');
        } else {

            $is_partner = true;
            if( isset ( $_POST['stripeEmail'] ) ){
                $is_partner = false;
            }
            if( !$is_partner ){
                $subscription_id = '';
                /**** create customer*/
                $customerRequest = array (
                    'email' =>$_POST['stripeEmail'], //customer email
                );
                $customerCurlRequest = $this->subscription($customerRequest,'customers');
                //echo "<pre>";print_r($customerCurlRequest);die;
                $customer_id = $customerCurlRequest['id'];
                if($customer_id){
                    /**** add card to customer*/
                    $cardRequest = array (

                        'source' => $_POST['stripeToken'], //customer email

                    );
                    $curlRequest = $this->subscription($cardRequest,'customers/'.$customer_id.'/sources');
                    //echo "<pre>";print_r($curlRequest);die;
                    /*** create subscription*/

                    $subscriptionsRequest = array (
                        "customer" => $customer_id,
                        "items" => [
                            [
                                "plan" => $_POST['plan_id'],
                            ],
                        ]

                    );
                    $subscriptionCurlRequest = $this->subscription($subscriptionsRequest,'subscriptions');
                    $subscription_id = $subscriptionCurlRequest['id'];
                    //echo "<pre>";print_r($subscriptionCurlRequest);die;
                }
            }



            $tags = Tools::getValue('tags');
            // bank infos
            $bank_type = Tools::getValue('bank_type');
            $bank_beneficiary = Tools::getValue('bank_beneficiary');
            $bank_establishment = Tools::getValue('bank_establishment');
            $bank_iban_code = Tools::getValue('bank_iban_code');
            $bank_code_bic = Tools::getValue('bank_code_bic');
            // shipping infos
            $delivery_method = Tools::getValue('delivery_method');
            $delivery_delay = Tools::getValue('delivery_delay');
            $shipping_days = Tools::getValue('shipping_days');
            $option_free_delivery = Tools::getValue('option_free_delivery');
            // extra fields
            $profession = Tools::getValue('profession');
            $quisuisje = Tools::getValue('quisuisje');
            $mapassion = Tools::getValue('mapassion');
            $unproverbe = Tools::getValue('unproverbe');
            $labels = $badgeIds = Tools::getValue('labels');
            $labels = implode(',',$labels);
            $siret = Tools::getValue('siret');
            $pp_theme = Tools::getValue('pp_theme');
            $syndicat_pro = Tools::getValue('syndicat_pro');
            $spoken_langs = Tools::getValue('spoken_langs');
            $spoken_langs = implode(',',$spoken_langs);
            $pp_theme = implode(',',$pp_theme);

            if ($this->context->cookie->logged) {
                $customer_id 	= $this->context->customer->id;
                $customerObj 	= new Customer($customer_id);

                $seller_firstname = $customerObj->firstname;
                $seller_lastname = $customerObj->lastname;
            }else{
                $customerObj = new Customer();
                $customerObj->firstname = $seller_firstname;
                $customerObj->lastname  = Tools::ucwords($seller_lastname);
                $customerObj->email     = $seller_email;
            }

            if( $is_partner ){
                $customerObj->optin = 1;
            }

            // create customer

            $crypto = new Crypto();
            $setPasswd = '';
            if (!empty($passwd) && strlen($passwd) >= 5) {
                $customerObj->passwd = $crypto->hash(pSQL($passwd), _COOKIE_KEY_);
                $setPasswd = $passwd;
            } else {
                $genPasswd = Tools::passwdGen(8);
                $customerObj->passwd = $crypto->hash($genPasswd, _COOKIE_KEY_);
                $setPasswd = $genPasswd;
            }
            $customerObj->newsletter = false;
            $customerObj->is_guest = 0;
            $customerObj->active = 1;

            $customer_id = $customer_id;
            $subscription_id = $subscription_id;
            $customerObj->stripe_customer_id = $customer_id;
            $customerObj->stripe_subscription_id = $subscription_id;
            $customerObj->groupBox = [3, 4];

            if (!$customerObj->save()) {
                Tools::redirectLink($this->context->link->getModulelink('mpsellerwiselogin', 'sellercreation', ['derr' => 1]));
            }

            $id_customer = (int)$customerObj->id;
            $this->context->updateCustomer($customerObj);
            $this->context->cart->update();
            $this->sendConfirmationMail($customerObj, $setPasswd);
            Hook::exec('actionCustomerAccountAdd', array(
                '_POST' => $_POST,
                'newCustomer' => $customerObj,
            ));

            // create seller
            $default_lang = (int)$this->context->language->id;
            $wk_mp_multilang_admin_approve = (int)Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE');
            if ($wk_mp_multilang_admin_approve) {
                $default_lang = 1;
            } elseif ((int)Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == 1) {
                $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
            }

            $country = 8; // France
            $state   = 0;
            $tel_pro = str_replace(['.'," ", '+'], "", $tel_pro);
            Hook::exec('actionBeforeAddSeller');

            $sellerObj = new WkMpSeller();
            $sellerObj->shop_name_unique = $store_name_unique;
            $sellerObj->business_email = $seller_email;
            $sellerObj->seller_firstname = Tools::strtolower($seller_firstname);
            $sellerObj->seller_lastname = Tools::strtolower($seller_lastname);
            $sellerObj->link_rewrite = Tools::link_rewrite($store_name_unique);
            $sellerObj->phone = (substr($tel_pro, 0, 2) == '33' ? '+' : '+33') . $tel_pro;
            $sellerObj->address = $store_address;
            $sellerObj->postcode = $post_code;
            $sellerObj->tags = $tags;
            $sellerObj->default_lang = $default_lang;
            $sellerObj->langs = implode(', ', Tools::getValue('seller_default_lang'));
            $sellerObj->seller_customer_id = $id_customer;
            $sellerObj->syndicat_pro = $syndicat_pro;
            $sellerObj->city = Tools::strtolower($city);
            $sellerObj->id_country = $country;
            $sellerObj->id_state = $state;
            $sellerObj->latitude = $latitude;
            $sellerObj->longitude = $longitude;

            $active = 0;
            if ((int)Configuration::get('WK_MP_SELLER_ADMIN_APPROVE') == 0) {
                $active = 1;
            }

            $sellerObj->active = $active;
            $sellerObj->shop_approved = 0;


            // Wether to display all seller details for new seller
            if ((int)Configuration::get('WK_MP_SHOW_SELLER_DETAILS')) {
                $sellerObj->seller_details_access = Configuration::get('WK_MP_SELLER_DETAILS_ACCESS');
            }

            // $sellerObj->shop_name = $store_name_unique;
            // $sellerObj->shop_name_1 = $store_name_unique;
            // $sellerObj->about_shop = Tools::getValue('store_description');
            // $sellerObj->about_shop_1 = Tools::getValue('store_description');

            $langs = Language::getLanguages(true);
            foreach ($langs as $lang) {
                /*$shop_lang_id = $lang['id_lang'];
                if ($wk_mp_multilang_admin_approve) {
                    //if shop name in other language is not available then fill with seller language same for others
                    // if (!Tools::getValue('mp_shop_name_'.$lang['id_lang'])) {
                        $shop_lang_id = $default_lang;
                    // }
                } else {
                    //if multilang is OFF then all fields will be filled as default lang content
                    $shop_lang_id = $default_lang;
                }*/
                $sellerObj->shop_name[$lang['id_lang']] = $store_name_unique;
                $sellerObj->about_shop[$lang['id_lang']] = Tools::getValue('store_description');
                // $sellerObj->shop_name[$lang['id_lang']] = Tools::getValue('store_name_'.$shop_lang_id);
                // $sellerObj->about_shop[$lang['id_lang']] = Tools::getValue('store_description_'.$shop_lang_id);

            }



            if (!$sellerObj->save()) {
                Tools::redirectLink($this->context->link->getModulelink('mpsellerwiselogin', 'sellercreation', ['derr' => 2]));
            }

            $id_seller = (int)$sellerObj->id;

            $objSellerBadge = new MpSellerBadges();
            $objSellerBadge->deletePrevSellerBadges($id_seller);
            $error = [];
            foreach ($badgeIds as $badge) {
                $objSellerBadge->badge_id = $badge;
                $objSellerBadge->mp_seller_id = $id_seller;
                $objSellerBadge->add();
            }

            //If seller default active approval is ON then mail seller about account activation
            if ($id_seller && (int)$sellerObj->active) {
                WkMpSeller::sendMail($id_seller, 1, 1);
            }

            //Mail to Admin on seller request
            $sellerName = $seller_firstname.' '.$seller_lastname;
            $sellerObj->mailToAdminWhenSellerRequest($sellerName, $store_name, $seller_email, $tel_pro);

            if (!empty($partnerId) && $partnerId) {
                Hook::exec('actionAddBadgeAfterAddSeller', array('partnerId' => $partnerId, 'sellerId' => $id_seller));
            }
            Hook::exec('actionAfterAddSeller', array('id_seller' => $id_seller));

            // create seller/shop images if any
            $this->createLogosBanners($id_seller);
            // add seller bank account info
            if ($bank_type && $bank_establishment && $bank_iban_code && $bank_code_bic) {
                $sellerBankObj = new WkMpSellerBank();
                $sellerBankObj->id_seller = $id_seller;
                $sellerBankObj->bank_type = $bank_type;
                $sellerBankObj->establishment = $bank_establishment;
                $sellerBankObj->beneficiary = $bank_beneficiary;
                $sellerBankObj->code_bic = $bank_code_bic;
                $sellerBankObj->iban_code = 'FR'. $bank_iban_code;
                $sellerBankObj->save();
            }

            if ($bank_iban_code) {
                $mpPayment = new WkMpCustomerPayment();
                $mpPayment->seller_customer_id = $id_customer;
                $mpPayment->payment_mode_id = 1;
                $mpPayment->payment_detail = 'FR'. $bank_iban_code;
                $mpPayment->save();
            }

            if( $id_seller ){
                $idSeller = $id_seller;

                $objStore = new MarketplaceStoreLocator();
                $objStore->name = $sellerObj->shop_name_unique;
                $objStore->id_seller = $idSeller;
                $objStore->country_id = $sellerObj->id_country;
                $objStore->state_id = $sellerObj->id_state;
                $objStore->city_name = $sellerObj->city;
                $objStore->address1 = $sellerObj->address;
                $objStore->latitude = ( $sellerObj->latitude ) ? $sellerObj->latitude : 0;
                $objStore->longitude = ($sellerObj->longitude) ? $sellerObj->longitude : 0;
                $objStore->zip_code = $sellerObj->postcode;
                $objStore->email = $sellerObj->business_email;
                if (Configuration::get('MP_STORE_LOCATION_ACTIVATION')) {
                    $objStore->active = 1;
                } else {
                    $objStore->active = 0;
                }
                $objStore->active = $sellerObj->active;
                $objStore->save();

            }

            // add seller extra fields info
            if ($profession) {
                $this->persistExtraField(1, $profession, $id_seller, $id_seller);
            }
            if ($quisuisje) {
                $this->persistExtraField(2, $quisuisje, $id_seller, $id_seller);
            }
            if ($mapassion) {
                $this->persistExtraField(3, $mapassion, $id_seller, $id_seller);
            }
            if ($unproverbe) {
                $this->persistExtraField(4, $unproverbe, $id_seller, $id_seller);
            }
            /*
            * edit 15-04-21 by claire
            */
            if ($labels) {
            	if( count( $badgeIds ) ){
					$objSellerBadge = new MpSellerBadges();
					// $objSellerBadge->deletePrevSellerBadges($id_seller);					
					foreach ($badgeIds as $badge) {
						$objSellerBadge->badge_id = $badge;
						$objSellerBadge->mp_seller_id = $id_seller;
						$objSellerBadge->add();
					}
				}
                $this->persistExtraField(5, $labels, $id_seller, $id_seller);
            }
            if ($siret) {
                $this->persistExtraField(6, $siret, $id_seller, $id_seller);
            }
            if ($pp_theme) {
                $this->persistExtraField(8, $pp_theme, $id_seller, $id_seller);
            }
            if ($spoken_langs) {
                $this->persistExtraField(10, $spoken_langs, $id_seller, $id_seller);
            }
            if ($syndicat_pro) {
                $this->persistExtraField(11, $syndicat_pro, $id_seller, $id_seller);
            }
            // remove weird empty persisted values
            $getExtraFieldsEmptyValues = Db::getInstance()->executeS('SELECT id FROM '. _DB_PREFIX_ .'marketplace_extrafield_value WHERE `field_value` = "" AND `mp_id_seller` = '. $id_seller);
            if ($getExtraFieldsEmptyValues) {
                foreach ($getExtraFieldsEmptyValues as $_extra) {
                    Db::getInstance()->execute('DELETE FROM '. _DB_PREFIX_ .'marketplace_extrafield_value WHERE id = '. (int)$_extra['id']);
                    Db::getInstance()->execute('DELETE FROM '. _DB_PREFIX_ .'marketplace_extrafield_value_lang WHERE id = '. (int)$_extra['id']);
                }
            }



            // add seller shipping info
            if ($delivery_method) {
                $wkmpSellerDeliveryObj = new WkMpSellerDelivery();
                $wkmpSellerDeliveryObj->id_seller = $id_seller;
                $wkmpSellerDeliveryObj->delivery_method = implode(' et ', $delivery_method);
                $wkmpSellerDeliveryObj->delivery_delay = $delivery_delay;
                $wkmpSellerDeliveryObj->shipping_days = implode(', ', $shipping_days);
                $wkmpSellerDeliveryObj->option_free_delivery = $option_free_delivery;
                $wkmpSellerDeliveryObj->save();
            }

            Tools::redirect($this->context->link->getModuleLink('marketplace', 'dashboard'));
        }
    }

    private function sendConfirmationMail(Customer $customer, $password = null)
    {
        if (!Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
            return true;
        }

        $id_lang = $this->context->language->id;
        Mail::Send(
            (int) $id_lang,
            'account',
            Mail::l('Welcome!', (int) $id_lang),
            array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
                '{passwd}' => $password
            ),
            $customer->email,
            $customer->firstname.' '.$customer->lastname,
            null,
            null,
            null,
            null,
            _PS_MAIL_DIR_,
            false,
            null,
            null
        );
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryUi('ui.widget');
        $this->addJqueryPlugin('tagify');

        $this->registerStylesheet('imgareaselect', 'js/jquery/plugins/imgareaselect/jquery.imgareaselect.css');
        $this->registerJavascript('imgareaselect', 'js/jquery/plugins/imgareaselect/jquery.imgareaselect.js', ['priority' => 95, 'position' => 'bottom']);
        $this->registerStylesheet('mpcreation-seller', 'modules/'.$this->module->name.'/views/css/seller_creation.css');
        $this->registerJavascript('chosen-jquery', 'js/jquery/plugins/jquery.chosen.js', ['priority' => 100, 'position' => 'bottom']);
        $this->registerStylesheet('chosen-jquery', 'themes/beeshary/assets/css/jquery.chosen.css');
        $this->registerJavascript('ps-validate-js', 'js/validate.js');
        $this->registerJavascript(
            'seller-wise-login-js',
            'modules/'.$this->module->name.'/views/js/mpsellerwiselogin.js',
            [
                'media' => 'all',
                'priority' => 900,
                'position' => 'bottom',
            ]
        );
    }

    public function defineJSVars()
    {
        $jsVars = [
            'emailIdError' => $this->module->l('Please Change your Email-Id to continue from here.', 'sellercreation'),
            'allFieldMandatoryError' => $this->module->l('All Fields Are Mandatory.', 'sellercreation'),
            'firstNameError' => $this->module->l('First name is not valid.', 'sellercreation'),
            'lastNameError' => $this->module->l('Last name is not valid.', 'sellercreation'),
            'invalidEmailIdError' => $this->module->l('Please Enter Valid Email-Id.', 'sellercreation'),
            'passwordLengthError' => $this->module->l('Password Length Must Be More Than 4 digit.', 'sellercreation'),
            'invalidPasswordError' => $this->module->l('Please enter valid Password', 'sellercreation'),
            'invalidUniqueShopNameError' => $this->module->l('Invalid unique shop name.', 'sellercreation'),
            'shopNameRequiredLang' => $this->module->l('Shop name is required in Default Language', 'sellercreation'),
            'shopNameRequired' => $this->module->l('Shop name is required.', 'sellercreation'),
            'invalidShopNameError' => $this->module->l('Invalid shop name', 'sellercreation'),
            'phoneNumberError' => $this->module->l('Phone number is not valid.', 'sellercreation'),
            'emailAlreadyExist' => $this->module->l('This email is already registered as Seller, Please Login.', 'sellercreation'),
            'shopNameAlreadyExist' => $this->module->l('Unique Shop name already taken. Try another.', 'sellercreation'),
            'shopNameError' => $this->module->l('Shop name can not contain any special character except underscrore. Try another.', 'sellercreation'),
            'sellerProfessionRequired' => $this->module->l('Please select your profession.', 'sellercreation'),
            'sellerLangRequired' => $this->module->l('Please select your language.', 'sellercreation'),
            'sellerAddressRequired' => $this->module->l('Please enter your store address.', 'sellercreation'),
            'sellerStoreDescRequired' => $this->module->l('Please enter your store description.', 'sellercreation'),
            'sellerStorePostCodeRequired' => $this->module->l('Please enter your store post code.', 'sellercreation'),
            'sellerStoreCityRequired' => $this->module->l('Please enter your store city.', 'sellercreation'),
            'checkCustomerAjaxUrl' => $this->context->link->getModulelink('mpsellerwiselogin', 'checkcustomerajax'),
            'validateUniquenessAjaxUrl' => $this->context->link->getModulelink('marketplace', 'validateuniqueshop'),
            'modImgDir' => _MODULE_DIR_ . $this->module->name.'/views/img/',
            'chaterAdherence' => $this->module->l('You have to adhere to the Beeshary Charter. Please check the box to create your store.', 'sellercreation'),
        ];

        Media::addJsDef($jsVars);
    }

    private function getSteps()
    {
        $steps = [
            $this->module->l('Profile', 'sellercreation'),
            $this->module->l('Store', 'sellercreation'),
            //$this->module->l('Images', 'sellercreation'),
            //$this->module->l('Delivery method', 'sellercreation'),
            $this->module->l('Terms of use', 'sellercreation'),
            //$this->module->l('Abonnement', 'sellercreation'),
        ];
        $partnerId = Tools::getValue('partner');
        if ($partnerId && $partnerId != '') {
            return $steps;
        }
        $steps[] = $this->module->l('Abonnement', 'sellercreation');

        return $steps;
    }

    public function smartyVars()
    {
        $mpextrafield = new MpExtraField();
        $extrafielddetailarray = $mpextrafield->displayExtraFieldOnAddPage(2);
        $filterExtraFields = array();

        foreach ($extrafielddetailarray as $extrafield) {
            $filterExtraFields[$extrafield['attribute_name']] = $extrafield;
        }

        $labels = Tools::getValue('labels');
        $partner = Tools::getValue('partner',0);

        $MpBadge = new MpBadge();
        $badges = $MpBadge->getAllBadges();

        $requestSeller = false;
        if ($this->context->cookie->logged) {
            $seller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if( !$seller ){
                $requestSeller 	= true;
            }
        }

        $params = [
            'extrafields' => $filterExtraFields,
            'requestSeller' => $requestSeller,
            'badges' => $badges,
            'partner' => $partner,
            //'languages' => Language::getLanguages(true),
            'steps' => $this->getSteps(),
            'MP_GEOLOCATION_API_KEY' => Configuration::get('MP_GEOLOCATION_API_KEY'),
        ];

        $this->context->smarty->assign($params);
    }

    private function createLogosBanners($id_seller)
    {
        // profile image
        if (isset($_FILES['profile_image']['name']) && !empty($_FILES['profile_image']['tmp_name'])) {
            $imgInfo = getimagesize($_FILES['profile_image']['tmp_name']);
            if (isset($imgInfo) && ($imgInfo[0] >= 300 && $imgInfo[1] >= 300)) {
                $this->uploadSellerImage($_FILES['profile_image'], $id_seller, 'seller_img/', 'sellerprofileimage');
            }
        }
        // shop image
        if (isset($_FILES['shop_logo']['name']) && !empty($_FILES['shop_logo']['tmp_name'])) {
            $imgInfo = getimagesize($_FILES['shop_logo']['tmp_name']);
            if (isset($imgInfo) && ($imgInfo[0] >= 300 && $imgInfo[1] >= 300)) {
                $this->uploadSellerImage($_FILES['shop_logo'], $id_seller, 'shop_img/', 'shopimage');
            }
        }
        // profile banner
        if (isset($_FILES['profile_banner']['name']) && !empty($_FILES['profile_banner']['tmp_name'])) {
            $imgInfo = getimagesize($_FILES['profile_banner']['tmp_name']);
            if (isset($imgInfo) && ($imgInfo[0] >= 1538 && $imgInfo[1] >= 380)) {
                $this->uploadSellerImage($_FILES['profile_banner'], $id_seller, 'seller_banner/', 'profilebannerimage');
            }
        }
        // shop banner
        if (isset($_FILES['shop_banner']['name']) && !empty($_FILES['shop_banner']['tmp_name'])) {
            $imgInfo = getimagesize($_FILES['shop_banner']['tmp_name']);
            if (isset($imgInfo) && ($imgInfo[0] >= 750 && $imgInfo[1] >= 750)) {
                $this->uploadSellerImage($_FILES['shop_banner'], $id_seller, 'shop_banner/', 'shopbannerimage');
            }
        }
    }

    private function uploadSellerImage($files, $id_seller, $img_dir, $action)
    {
        $uploadDirPath = 'modules/marketplace/views/img/'. $img_dir;
        $uploader = new WkMpImageUploader();
        $data = $uploader->upload($files, array(
            'actionType' => $action, //Maximum Limit of files. {null, Number}
            'limit' => 10, //Maximum Limit of files. {null, Number}
            'maxSize' => 10, //Maximum Size of files {null, Number(in MB's)}
            'extensions' => array('jpg', 'png', 'gif', 'jpeg'), //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
            'required' => false, //Minimum one file is required for upload {Boolean}
            'uploadDir' => $uploadDirPath, //Upload directory {String}
            'title' => array('name'), //New file name {null, String, Array} *please read documentation in README.md
        ));

        if (isset($data['data']['metas'][0]['name']) && !empty($action)) {
            $imageNewName = $data['data']['metas'][0]['name'];
            $objMpSeller  = new WkMpSeller($id_seller);

            switch ($action) {
                case 'sellerprofileimage':
                    $objMpSeller->profile_image = $imageNewName;
                    break;
                case 'shopimage':
                    $objMpSeller->shop_image = $imageNewName;
                    break;
                case 'profilebannerimage':
                    $objMpSeller->profile_banner = $imageNewName;
                    break;
                case 'shopbannerimage':
                    $objMpSeller->shop_banner = $imageNewName;
                    break;
            }
            $objMpSeller->save();
        }
    }

    private function persistExtraField($extrafield_id, $field_value, $id_shop, $id_seller)
    {
        $extrafieldValueObj = new MarketplaceExtrafieldValue();
        // $saved = $extrafieldValueObj->insertExtraFieldValue($extrafield_id, $field_value, $id_shop, $id_seller, 0, 1);
        $extrafieldValueObj->extrafield_id = (int)$extrafield_id;
        $extrafieldValueObj->marketplace_product_id = 0;
        $extrafieldValueObj->mp_id_shop = (int)$id_shop;
        $extrafieldValueObj->mp_id_seller = (int)$id_seller;
        $extrafieldValueObj->field_value = pSQL($field_value);
        $extrafieldValueObj->field_value = $field_value;
        $extrafieldValueObj->is_for_shop = 1;

        $langs = Language::getLanguages(true);
        foreach ($langs as $lang) {
            $extrafieldValueObj->field_val[$lang['id_lang']] = pSQL($field_value);
        }

        return $extrafieldValueObj->save();
    }
}
