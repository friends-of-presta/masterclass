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

use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;

class masterclass extends Module
{
    /**
     * In constructor we define our module's meta data.
     * It's better tot keep constructor (and main module class itself) as thin as possible
     * and do any processing in controller.
     */
    public function __construct()
    {
        $this->name = 'masterclass';
        $this->version = '1.0.0';
        $this->author = 'Mickaël Andrieu';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = 'Demonstration Module';
    }

    /**
     * Install module and register hooks to allow grid modification.
     *
     * @return bool
     */
    public function install()
    {
        return parent::install() &&
            $this->installTabs() &&
            $this->registerHook('actionLogsGridDefinitionModifier')
        ;
    }

    /**
     * Install module and register hooks to allow grid modification.
     *
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall() &&
            $this->uninstallTabs() &&
            $this->unregisterHook('actionLogsGridDefinitionModifier')
        ;
    }

    /**
     * Hooks allows to modify Logs grid definition.
     * This hook is a right place to add/remove columns or actions (bulk, grid).
     *
     * @param array $params
     */
    public function hookActionLogsGridDefinitionModifier(array $params)
    {
        /** @var GridDefinitionInterface $definition */
        $definition = $params['definition'];

        // Remove employee column from grid as we don't want to see that information.
        $definition->getColumns()->remove('employee');

        // Remove employee filter that is associated with "employee" column.
        // As filters are separately defined, you may or may not want to keep it after removing column.
        // If you decide to keep the filter for removed column
        // you should know that it will be rendered separately from grid table.
        $definition->getFilters()->remove('employee');
    }

    public function installTabs()
    {
        $mainTab = new Tab();
        $mainTab->active = 1;
        $mainTab->class_name = 'AdminMasterClass';
        $mainTab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $mainTab->name[$lang['id_lang']] = 'Master Class';
        }

        $mainTab->id_parent = (int) Tab::getIdFromClassName('AdminTools');

        $mainTab->module = $this->name;

        $mainTab->add();

        $indexTab = new Tab();
        $indexTab->active = 1;
        $indexTab->class_name = 'AdminMasterIndexClass';
        $indexTab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $indexTab->name[$lang['id_lang']] = 'Exemple';
        }

        $indexTab->id_parent = (int) Tab::getIdFromClassName('AdminMasterClass');

        $indexTab->module = $this->name;

        $indexTab->add();

        $listingTab = new Tab();
        $listingTab->active = 1;
        $listingTab->class_name = 'AdminMasterListingClass';
        $listingTab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $listingTab->name[$lang['id_lang']] = 'Listing';
        }

        $listingTab->id_parent = (int) Tab::getIdFromClassName('AdminMasterClass');

        $listingTab->module = $this->name;

        $listingTab->add();
    }

    public function uninstallTabs()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminMasterIndexClass');
        $tab = new Tab($id_tab);

        $tab->delete();

        $id_tab = (int) Tab::getIdFromClassName('AdminMasterClass');
        $tab = new Tab($id_tab);

        $tab->delete();
    }
}
