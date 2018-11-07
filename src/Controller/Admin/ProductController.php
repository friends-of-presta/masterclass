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

namespace MasterClass\Controller\Admin;

use MasterClass\Filter\ProductFilter;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProductController manages our custom products page
 */
class ProductController extends Controller
{
    /**
     * @AdminSecurity(
     *     "is_granted(['read'], request.get('_legacy_controller'))",
     *     message="You do not have permission to access Products listing page."
     * )
     * Show products listing
     *
     * @param ProductFilter $filters
     *
     * @return Response
     */
    public function listingAction(ProductFilter $filters)
    {
        $productGridFactory = $this->get('demogrid.grid.product_grid_factory');
        $productGrid = $productGridFactory->getGrid($filters);

        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        return $this->render('@Modules/masterclass/views/admin/products.html.twig', [
            'layoutTitle' => $this->get('translator')->trans('Products', [], 'Modules.DemoGrid.Admin'),
            'productGrid' => $gridPresenter->present($productGrid),
        ]);
    }

    /**
     * Perform search on products list
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function listingSearchAction(Request $request)
    {
        $definitionFactory = $this->get('demogrid.grid.product_grid_definition_factory');
        $productDefinition = $definitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');
        $filtersForm = $gridFilterFormFactory->create($productDefinition);
        $filtersForm->handleRequest($request);

        $filters = [];

        if ($filtersForm->isSubmitted()) {
            $filters = $filtersForm->getData();
        }

        return $this->redirectToRoute('masterclass_admin_products', ['filters' => $filters]);
    }
}
