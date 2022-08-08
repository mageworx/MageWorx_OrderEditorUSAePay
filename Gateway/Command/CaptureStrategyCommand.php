<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\OrderEditorUSAePay\Gateway\Command;

use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use MageWorx\OrderEditorUSAePay\Helper\Data as Helper;
use Magento\Payment\Gateway\Helper\SubjectReader;

class CaptureStrategyCommand extends \Rootways\USAePay\Gateway\Command\CaptureStrategyCommand
    implements CommandInterface
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var CommandPoolInterface
     */
    protected $_commandPool;

    /**
     * We use factory to support both old and new versions of the braintree payment.
     * Old namespace was \Magento\Braintree, new - \PayPal\Braintree.
     *
     * @param Helper $helper
     * @param CommandPoolInterface $commandPool
     */
    public function __construct(
        Helper               $helper,
        CommandPoolInterface $commandPool
    ) {
        $this->helper       = $helper;
        $this->_commandPool = $commandPool;
        parent::__construct($commandPool);
    }

    /**
     * @param array $commandSubject
     * @return \Magento\Payment\Gateway\Command\ResultInterface|void|null
     * @throws NotFoundException
     * @throws \Magento\Payment\Gateway\Command\CommandException
     */
    public function execute(array $commandSubject)
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = SubjectReader::readPayment($commandSubject);

        /** @var OrderPaymentInterface $payment */
        $paymentInfo = $paymentDO->getPayment();

        $useVault            = $paymentInfo->getAdditionalInformation('mw_use_vault');
        $needReauthorization = $paymentInfo->getAdditionalInformation('mw_reauthorization_required');
        if ($this->helper->isEnabled() && $needReauthorization && $useVault) {
            $command = 'adjust_positive';
            $this->_commandPool->get($command)->execute($commandSubject);
        } else {
            return parent::execute($commandSubject);
        }
    }
}
