<?php
/**
 * USAePay Payment Module.
 *
 * @category  Payment Integration
 * @package   Rootways_USAePay
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2021 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Custom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
 */

namespace MageWorx\OrderEditorUSAePay\Gateway\Request;

/**
 * Class AdjustTransactionDataBuilder
 */
class AdjustPositiveTransactionDataBuilder extends \Rootways\USAePay\Gateway\Request\GeneralDataBuilder
{
    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject): array
    {
        $amount = $this->subjectReader->readAmount($buildSubject);

        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $payment   = $paymentDO->getPayment();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();

        $taxAmt    = $discountAmt = $shippingAmt = 0.00;
        $isTaxable = 0;

        if (!empty($order->getBaseTaxAmount())) {
            $taxAmt    = $order->getBaseTaxAmount() - $order->getBaseTaxInvoiced();
            $isTaxable = 1;
        }

        if (!empty($order->getBaseShippingAmount())) {
            $shippingAmt = $order->getBaseShippingAmount() - $order->getBaseShippingInvoiced();
        }
        if (!empty($order->getBaseDiscountAmount())) {
            $discountAmt = abs($order->getBaseDiscountAmount()) - abs($order->getBaseDiscountInvoiced());
        }

        $result = [
            "amount"   => $amount,
            "command"  => 'sale',
            "comments" => 'MageWorx OrderEditor: Automatic Payment After Editing'
        ];

        $subtotal = $amount - $taxAmt - $shippingAmt + $discountAmt;

        // Overwrite original amount details
        $result['amount_detail'] = [
            "subtotal"   => $subtotal,
            "tax"        => $taxAmt,
            "nontaxable" => $isTaxable,
            "discount"   => $discountAmt,
            "shipping"   => $shippingAmt
        ];

        return $result;
    }
}
