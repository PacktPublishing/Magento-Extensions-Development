<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blackbird\TicketBlaster\Block\Catalog\Product;

use \Magento\Catalog\Block\Product\View;

class TimedCheckout extends View
{
    public function displayTimedCheckoutMessage()
    {
        return !$this->getProduct()->isSalable();
    }
}
