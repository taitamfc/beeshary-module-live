<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class AuthController extends AuthControllerCore
{
    public function initContent()
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $should_redirect = false;

            if (Tools::isSubmit('submitCreate') || Tools::isSubmit('create_account')) {
                $register_form = $this
                    ->makeCustomerForm()
                    ->setGuestAllowed(false)
                    ->fillWith(Tools::getAllValues())
                ;

                if (Tools::isSubmit('submitCreate')) {
                    if (Module::isEnabled('invisiblegrecaptcha')) {
                        $grecaptcha = Module::getInstanceByName('invisiblegrecaptcha');
                        $grecaptcha_config = $grecaptcha->getConfigurations();
                        if ((bool)$grecaptcha_config['GRECAPTCHA_IN_REG_FORM']) {
                            if (!$grecaptcha->isEmailDomainAllowed(Tools::getValue('email'))) {
                                $this->context->controller->errors[] = $grecaptcha_config['EMAIL_DOMAIN_BLOCKED'];
                            } elseif ($grecaptcha->verifyCaptcha(Tools::getValue("g-recaptcha-response")) == false) {
                                $this->context->controller->errors[] = $grecaptcha_config['CAPTCHA_FAILED'];
                            }
                        }
                    }

                    if (count($this->context->controller->errors) == 0) {
                        $hookResult = array_reduce(
                            Hook::exec('actionSubmitAccountBefore', array(), null, true),
                            function ($carry, $item) {
                                return $carry && $item;
                            },
                            true
                        );

                        if ($hookResult && $register_form->submit()) {
                            $should_redirect = true;
                        }
                    }
                }

                $this->context->smarty->assign(array(
                    'register_form'  => $register_form->getProxy(),
                    'hook_create_account_top' => Hook::exec('displayCustomerAccountFormTop')
                ));
                $this->setTemplate('customer/registration');
            } else {
                $login_form = $this->makeLoginForm()->fillWith(
                    Tools::getAllValues()
                );

                if (Tools::isSubmit('submitLogin')) {
                    if ($login_form->submit()) {
                        $should_redirect = true;
                    }
                }

                $this->context->smarty->assign(array(
                    'login_form' => $login_form->getProxy()
                ));
                $this->setTemplate('customer/authentication');
            }

            FrontController::initContent();

            if ($should_redirect && !$this->ajax) {
                $back = urldecode(Tools::getValue('back'));

                if (Tools::urlBelongsToShop($back)) {
                    // Checks to see if "back" is a fully qualified
                    // URL that is on OUR domain, with the right protocol
                    return $this->redirectWithNotifications($back);
                }

                // Well we're not redirecting to a URL,
                // so...
                if ($this->authRedirection) {
                    // We may need to go there if defined
                    return $this->redirectWithNotifications($this->authRedirection);
                }

                // go home
                return $this->redirectWithNotifications(__PS_BASE_URI__);
            }
        } else {
            parent::initContent();
        }
    }

    protected function processSubmitAccount()
    {
        if (Module::isEnabled('invisiblegrecaptcha') && version_compare(_PS_VERSION_, '1.7', '<')) {
            $grecaptcha = Module::getInstanceByName('invisiblegrecaptcha');
            $grecaptcha_config = $grecaptcha->getConfigurations();
            if ((bool)$grecaptcha_config['GRECAPTCHA_IN_REG_FORM']) {
                if (!$grecaptcha->isEmailDomainAllowed(Tools::getValue('email'))) {
                    $this->errors[] = $grecaptcha_config['EMAIL_DOMAIN_BLOCKED'];
                } elseif ($grecaptcha_config['GRECAPTCHA_SECRET_KEY'] && $grecaptcha_config['GRECAPTCHA_SITE_KEY']) {
                    if ($grecaptcha->verifyCaptcha(Tools::getValue("g-recaptcha-response")) == false) {
                        $this->errors[] = $grecaptcha_config['CAPTCHA_FAILED'];
                    }
                }
            }
        }

        parent::processSubmitAccount();
    }
}
