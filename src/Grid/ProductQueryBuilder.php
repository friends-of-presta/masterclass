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

namespace MasterClass\Grid;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class ProductQueryBuilder builds queries for our grid data factory
 */
final class ProductQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param int $contextLangId
     * @param int $contextShopId
     */
    public function __construct(Connection $connection, $dbPrefix, $contextLangId, $contextShopId)
    {
        parent::__construct($connection, $dbPrefix);

        $this->contextLangId = $contextLangId;
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $quantitiesQuery = $this->connection
            ->createQueryBuilder()
            ->select('id_product, SUM(quantity) as quantity')
            ->from($this->dbPrefix.'stock_available', 'sa')
            ->groupBy('id_product');

        $qb = $this->getBaseQuery();
        $qb->select('p.id_product, pl.name, q.quantity')
            ->leftJoin(
                'p',
                sprintf('(%s)', $quantitiesQuery->getSQL()),
                'q',
                'p.id_product = q.id_product'
            )
            ->leftJoin(
                'p',
                $this->dbPrefix . 'product_shop',
                'ps',
                'ps.id_product = p.id_product AND ps.id_shop = :context_shop_id'
            )
            ->setParameter('context_shop_id', $this->contextShopId)
            ->orderBy(
                $searchCriteria->getOrderBy(),
                $searchCriteria->getOrderWay()
            )
            ->setFirstResult($searchCriteria->getOffset())
            ->setMaxResults($searchCriteria->getLimit());

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ('in_stock' === $filterName) {
                (bool) $filterValue ?
                    $qb->where('q.quantity > 0') :
                    $qb->where('q.quantity <= 0');

                continue;
            }

            if ('id_product' === $filterName) {
                $qb->andWhere("p.id_product = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            $qb->andWhere("$filterName LIKE :$filterName");
            $qb->setParameter($filterName, '%'.$filterValue.'%');
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getBaseQuery();
        $qb->select('COUNT(p.id_product)');

        return $qb;
    }

    /**
     * Base query is the same for both searching and counting
     *
     * @return QueryBuilder
     */
    private function getBaseQuery()
    {
        return $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix.'product', 'p')
            ->leftJoin(
                'p',
                $this->dbPrefix.'product_lang',
                'pl',
                'p.id_product = pl.id_product AND pl.id_lang = :context_lang_id AND pl.id_shop = :context_shop_id'
            )
            ->setParameter('context_lang_id', $this->contextLangId)
            ->setParameter('context_shop_id', $this->contextShopId)
        ;
    }
}
