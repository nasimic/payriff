<?php

namespace Nasimic\Payriff;

use Illuminate\Http\Client\Factory as HttpClient;
use Nasimic\Payriff\Data\Request\RefundRequest;
use Nasimic\Payriff\Exceptions\OrderCreationException;
use Nasimic\Payriff\Exceptions\OrderInformationException;
use Nasimic\Payriff\Exceptions\OrderStatusException;
use Nasimic\Payriff\Exceptions\RefundException;
use Nasimic\Payriff\Interfaces\PayriffClientInterface;
use Nasimic\Payriff\Data\Request\CreateOrderRequest;
use Nasimic\Payriff\Data\Response\OrderInformationResponse;
use Nasimic\Payriff\Data\Response\CreateOrderResponse;

class PayriffClient implements PayriffClientInterface
{
    private string $baseUrl = "https://api.payriff.com/api/v3/";
    private const SUCCESS = "00000";
    private const PAID = "APPROVED";

    public function __construct(
        private readonly string $merchantSecretKey,
        private readonly HttpClient $httpClient
    ) {
    }

    public function sendPostRequest(string $uri, array $body): array
    {
        return
            $this->httpClient->withHeaders([
                "Authorization" => $this->merchantSecretKey,
                "Content-Type" => 'application/json'
            ])->post($this->baseUrl . $uri, $body)
                ->json();
    }

    public function sendGetRequest(string $uri): array
    {
        return
            $this->httpClient->withHeaders([
                "Authorization" => $this->merchantSecretKey,
                "Content-Type" => 'application/json'
            ])->get($this->baseUrl . $uri)
                ->json();
    }

    /**
     * @throws OrderCreationException
     */
    public function createOrder(CreateOrderRequest $orderRequest): CreateOrderResponse
    {
        $body = [
            "amount" => $orderRequest->amount,
            "callbackUrl" => $orderRequest->callbackUrl,
            "description" => $orderRequest->description,
            "currency" => $orderRequest->currency,
            "language" => $orderRequest->language,
            "cardSave" => $orderRequest->cardSave,
            "operation" => $orderRequest->operation,
            "installment" => $orderRequest->installment,
        ];

        $response = $this->sendPostRequest(uri: 'orders', body: $body);

        try {
            return CreateOrderResponse::fromPayload(payload: $response['payload']);
        } catch (\Exception $exception) {
            throw new OrderCreationException('Failed to construct CreateOrderResponse class from payload. Message: '. $exception->getMessage());
        }
    }

    /**
     * @throws OrderInformationException
     */
    public function getOrderInformation(string $orderId): OrderInformationResponse
    {
        $response = $this->sendGetRequest(uri: 'orders/' . $orderId);

        try {
            return OrderInformationResponse::fromPayload(payload: $response['payload']);
        } catch (\Exception $exception) {
            throw new OrderInformationException('Failed to construct OrderInformationResponse class from payload. Message: '. $exception->getMessage());
        }
    }

    /**
     * @throws OrderStatusException
     */
    public function getOrderStatus(string $orderId): string
    {
        try {
            return $this->getOrderInformation($orderId)->paymentStatus;
        } catch (\Exception $exception) {
            throw new OrderStatusException('Failed to get order status from Payriff. Message: '. $exception->getMessage());
        }
    }

    /**
     * @throws OrderStatusException
     */
    public function isOrderPaid(string $orderId): bool
    {
        $status = $this->getOrderStatus($orderId);

        return $status === self::PAID;
    }


    /**
     * @throws RefundException
     */
    public function refund(RefundRequest $refundRequest): bool
    {
        $body = [
            "refundAmount" => $refundRequest->amount,
            "orderId" => $refundRequest->orderId,
        ];

        $response = $this->sendPostRequest('refund', $body);

        try {
            return $response['code'] === self::SUCCESS;
        } catch (\Exception $exception) {
            throw new RefundException('Failed to refund. Message: '. $exception->getMessage());
        }
    }
}
