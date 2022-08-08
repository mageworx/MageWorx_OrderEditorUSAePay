<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\OrderEditorUSAePay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use MageWorx\OrderEditor\Helper\Data as OrderEditorHelper;

class Data extends AbstractHelper
{
    const XML_PATH_IS_ENABLED =
        'mageworx_order_management/order_editor/invoice_shipment_refund/enable_usaepay_reauthorization';

    /**
     * @var OrderEditorHelper
     */
    protected $orderEditorHelper;

    /**
     * @param Context $context
     * @param OrderEditorHelper $orderEditorHelper
     */
    public function __construct(
        Context           $context,
        OrderEditorHelper $orderEditorHelper
    ) {
        $this->orderEditorHelper = $orderEditorHelper;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(static::XML_PATH_IS_ENABLED)
            && $this->orderEditorHelper->isReauthorizationAllowed();
    }
}
