<?php

namespace Stagem\OrderToXml\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class DataRecipient
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected SearchCriteriaBuilder    $criteriaBuilder
    ) {
    }

    /**
     * @throws NoSuchEntityException
     */
    protected function getOrderByIncrementId(string $incrementId): OrderInterface
    {
        $searchCriteria = $this->criteriaBuilder
            ->addFilter('increment_id', $incrementId)
            ->setPageSize(1)
            ->create();

        $orderList = $this->orderRepository->getList($searchCriteria);

        if ($orderList->getTotalCount() === 0 || empty($orderList->getItems())) {
            throw new NoSuchEntityException(
                __("Order with increment ID $incrementId does not exist.")
            );
        }

        $order = $orderList->getItems();

        return reset($order);
    }

    public function getOrderInformation(string $incrementId): array|null
    {
        $orderData = [];

        try {
            $order                        = $this->getOrderByIncrementId($incrementId);
            $orderData['orderId']         = $order->getEntityId() ?? '';
            $orderData['createdDate']     = $order->getCreatedAt() ?? '';
            $orderData['subTotal']        = $order->getSubtotal() ?? 0;
            $orderData['shippingAmount']  = $order->getShippingAmount() ?? 0;
            $orderData['discountAmount']  = $order->getDiscountAmount() ?? 0;
            $orderData['taxAmount']       = $order->getTaxAmount() ?? 0;
            $orderData['orderGrandTotal'] = $order->getGrandTotal() ?? 0;
            $orderData['client']          = $this->getClientData($order);
            $orderData['billingAddress']  = $this->getBillingAddress($order);
            $orderData['shipping']        = $this->getShippingInfo($order);
            $orderData['customerComment'] = $this->getCustomerComment($order);
            $orderData['paidAmount']      = $order->getTotalPaid() ?? 0;
            $orderData['items']           = $this->getOrderItems($order);
        } catch (NoSuchEntityException $e) {
            echo $e->getMessage() . PHP_EOL;
            return null;
        }

        return $orderData;
    }

    protected function getClientData(OrderInterface $order): array
    {
        return [
            'id'          => $order->getCustomerId() ?? '',
            'firstName'   => $order->getCustomerFirstname() ?? '',
            'secondName'  => $order->getCustomerLastname() ?? '',
            'phoneNumber' => $order->getBillingAddress() !== null ? $order->getBillingAddress()->getTelephone() : '',
            'email'       => $order->getCustomerEmail() ?? '',
        ];
    }

    protected function getBillingAddress(OrderInterface $order): string
    {
        $billingAddress = $order->getBillingAddress();
        return $billingAddress !== null ? implode(', ', $order->getBillingAddress()->getStreet()) : '';
    }

    /**
     * Gets shipping streets and method
     */
    protected function getShippingInfo(OrderInterface $order): array
    {
        $shippingInfo = ['address' => '', 'method' => ''];

        $shippingAssignments = $order->getExtensionAttributes() !== null ? $order->getExtensionAttributes(
        )->getShippingAssignments() : [];
        if (empty($shippingAssignments) === false) {
            $shippingAddress         = $shippingAssignments[0]->getShipping()->getAddress();
            $shippingInfo['address'] = $shippingAddress !== null ? implode(", ", $shippingAddress->getStreet()) : '';
            $shippingInfo['method']  = $shippingAssignments[0]->getShipping()->getMethod() ?? '';
        }

        return $shippingInfo;
    }

    protected function getCustomerComment(OrderInterface $order): string
    {
        $comment = '';

        if (empty($order->getStatusHistories()) === false) {
            foreach ($order->getStatusHistories() as $statusHistory) {
                if ($statusHistory->getComment() && $statusHistory->getIsCustomerNotified()) {
                    $comment = $statusHistory->getComment();
                    break;
                }
            }
        }

        return $comment;
    }

    protected function getOrderItems(OrderInterface $order): array
    {
        $increment  = 1;
        $itemsData  = [];
        $orderItems = $order->getItems();

        if (empty($orderItems) === false) {
            foreach ($orderItems as $item) {
                if ($item->getParentItemId() === null) {
                    $itemsKey             = 'item_' . $increment++;
                    $itemsData[$itemsKey] = [
                        'sku'      => $item->getSku(),
                        'count'    => (int)$item->getQtyOrdered(),
                        'price'    => $item->getPrice(),
                        'discount' => $item->getDiscountAmount(),
                        'tax'      => $item->getTaxAmount(),
                        'total'    => $item->getRowTotal()
                    ];
                }
            }
        }

        return $itemsData;
    }
}
