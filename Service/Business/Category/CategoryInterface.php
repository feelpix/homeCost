<?php
/**
* This file is part of the Numeric Workshop Serval project
*
* (c) IncentiveOffice - 2014
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace Service\Business\Category;

use Serval\Base\Table\ServiceTableInterface;

/**
* Interface CategoryInterface
*/
interface CategoryInterface extends ServiceTableInterface
{
    /**
     * guess category from a label
     *
     * @param $label
     *
     * @return int|null
     */
    public function guess($label);

    /**
     * @return array
     */
    public function listMajor();

    /**
     * @param string $order
     *
     * @return array
     */
    public function listOrderBy($order = 'label');
}
