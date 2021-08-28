<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * object module available
 */
function upgrade_module_1_8_3($object)
{
    $upgrade_version = '1.8.3';

    $object->upgrade_detail[$upgrade_version] = array();

    if (!Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'mr_method` ADD `is_deleted` INT NOT NULL')) {
        $object->upgrade_detail[$upgrade_version][] = $object->l('Can\'t add new field in methodtable');
    }

    Configuration::updateValue('MONDIAL_RELAY', $upgrade_version);

    return (bool)count($object->upgrade_detail[$upgrade_version]);
}
