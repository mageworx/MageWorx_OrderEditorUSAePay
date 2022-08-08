<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\OrderEditorUSAePay\Model\Invoice\PaymentMethodProcessor;

use MageWorx\OrderEditor\Api\PaymentMethodProcessorInterface;
use MageWorx\OrderEditor\Model\Invoice\PaymentMethodProcessor\DefaultProcessor;
use Rootways\USAePay\Observer\DataAssignObserver;

class USAePayProcessor extends DefaultProcessor
    implements PaymentMethodProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function isReauthorizationRequired(): bool
    {
        $order   = $this->getOrder();
        $payment = $order->getPayment();

        $baseAmountAuthorized = (float)$payment->getBaseAmountAuthorized();
        $baseOrderGrandTotal  = (float)$order->getBaseGrandTotal();

        $result = $baseAmountAuthorized < $baseOrderGrandTotal;

        $payment->setAdditionalInformation('mw_reauthorization_required', $result);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function isVaultAvailable(): bool
    {
        $order = $this->getOrder();
        $payment = $order->getPayment();
        $paymentExtensionAttributes = $payment->getExtensionAttributes();
        $vaultPaymentToken = $paymentExtensionAttributes->getVaultPaymentToken();

        if ($vaultPaymentToken && $vaultPaymentToken->getEntityId()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function setUseVaultForReauthorizationFlag(): PaymentMethodProcessorInterface
    {
        $order   = $this->getOrder();
        $payment = $order->getPayment();
        $payment->setAdditionalInformation('mw_use_vault', true);

        return $this;
    }
}
