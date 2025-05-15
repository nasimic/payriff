<?php

namespace Nasimic\Payriff\Tests;

use Illuminate\Http\Client\Factory as Http;
use Nasimic\Payriff\Data\Request\CreateOrderRequest;
use Nasimic\Payriff\Data\Request\RefundRequest;
use Nasimic\Payriff\Exceptions\OrderCreationException;
use Nasimic\Payriff\Exceptions\OrderInformationException;
use Nasimic\Payriff\Exceptions\RefundException;
use Nasimic\Payriff\PayriffClient;
use Orchestra\Testbench\TestCase as Orchestra;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PayriffClient::class)]
class PayriffClientTest extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            'Nasimic\Payriff\PayriffServiceProvider'
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
    }

    public function testCreateOrderReturnsResponseObject()
    {
        $this->app['config']->set('payriff.secret_key', 'test-secret-key');

        $httpClientMock = $this->mock(Http::class, function ($mock) {
            $mock->shouldReceive('withHeaders->post->json')->once()->with()->andReturn(
                [
                    "code" => "00000",
                    "payload" => [
                        "orderId" => "test-orderId",
                        "paymentUrl" => "test-paymentUrl",
                        "transactionId" => "test-transactionId",
                    ]
                ]
            );
        });

        $payriffClient = new PayriffClient(
            $this->app['config']->get('payriff.secret_key'),
            $httpClientMock
        );

        $createOrderResponse = $payriffClient->createOrder(
            new CreateOrderRequest(150)
        );

        $this->assertEquals('test-orderId', $createOrderResponse->orderId);
        $this->assertEquals('test-paymentUrl', $createOrderResponse->paymentUrl);
        $this->assertEquals('test-transactionId', $createOrderResponse->transactionId);
    }

    public function testCreateOrderThrowsExceptions()
    {
        $this->app['config']->set('payriff.secret_key', 'test-secret-key');

        $httpClientMock = $this->mock(Http::class, function ($mock) {
            $mock->shouldReceive('withHeaders->post->json')->once()->with()->andReturn(
                ["result" => "error"]
            );
        });

        $payriffClient = new PayriffClient(
            $this->app['config']->get('payriff.secret_key'),
            $httpClientMock
        );

        $this->expectException(OrderCreationException::class);

        $payriffClient->createOrder(
            new CreateOrderRequest(150)
        );
    }

    public function testGetOrderInformationReturnsResponseObject()
    {
        $this->app['config']->set('payriff.secret_key', 'test-secret-key');

        $httpClientMock = $this->mock(Http::class, function ($mock) {
            $mock->shouldReceive('withHeaders->get->json')->once()->with()->andReturn(
                [
                    "code" => "00000",
                    "payload" => [
                        "orderId" => "test-orderId",
                        "amount" => 150.75,
                        "currencyType" => "test-currencyType",
                        "merchantName" => "test-merchantName",
                        "operationType" => "test-operationType",
                        "paymentStatus" => "test-paymentStatus",
                        "auto" => false,
                        "createdDate" => "test-createdDate",
                        "description" => "test-description",
                        "transactions" => [
                            "pan" => "test-pan",
                            "channel" => "test-channel",
                        ],
                    ]
                ]
            );
        });

        $payriffClient = new PayriffClient(
            $this->app['config']->get('payriff.secret_key'),
            $httpClientMock
        );

        $orderInformationResponse = $payriffClient->getOrderInformation("test-orderId");

        $this->assertEquals('test-orderId', $orderInformationResponse->orderId);
        $this->assertEquals(150.75, $orderInformationResponse->amount);
        $this->assertEquals('test-currencyType', $orderInformationResponse->currencyType);
        $this->assertEquals('test-merchantName', $orderInformationResponse->merchantName);
        $this->assertEquals('test-operationType', $orderInformationResponse->operationType);
        $this->assertEquals('test-paymentStatus', $orderInformationResponse->paymentStatus);
        $this->assertFalse($orderInformationResponse->auto);
        $this->assertEquals('test-createdDate', $orderInformationResponse->createdDate);
        $this->assertEquals('test-description', $orderInformationResponse->description);
        $this->assertIsArray($orderInformationResponse->transactions);
    }

    public function testGetOrderInformationThrowsExceptions()
    {
        $this->app['config']->set('payriff.secret_key', 'test-secret-key');

        $httpClientMock = $this->mock(Http::class, function ($mock) {
            $mock->shouldReceive('withHeaders->get->json')->once()->with()->andReturn(
                ["result" => "error"]
            );
        });

        $payriffClient = new PayriffClient(
            $this->app['config']->get('payriff.secret_key'),
            $httpClientMock
        );

        $this->expectException(OrderInformationException::class);

        $payriffClient->getOrderInformation("test-orderId");
    }

    public function testRefundReturnsTrue()
    {
        $this->app['config']->set('payriff.secret_key', 'test-secret-key');

        $httpClientMock = $this->mock(Http::class, function ($mock) {
            $mock->shouldReceive('withHeaders->post->json')->once()->with()->andReturn(
                [
                    "code" => "00000",
                ]
            );
        });

        $payriffClient = new PayriffClient(
            $this->app['config']->get('payriff.secret_key'),
            $httpClientMock
        );

        $refundResult = $payriffClient->refund(
            new RefundRequest("150.75", "test-refundId")
        );

        $this->assertTrue($refundResult);
    }

    public function testRefundThrowsExceptions()
    {
        $this->app['config']->set('payriff.secret_key', 'test-secret-key');

        $httpClientMock = $this->mock(Http::class, function ($mock) {
            $mock->shouldReceive('withHeaders->post->json')->once()->with()->andReturn(
                ["result" => "error"]
            );
        });

        $payriffClient = new PayriffClient(
            $this->app['config']->get('payriff.secret_key'),
            $httpClientMock
        );

        $this->expectException(RefundException::class);

        $payriffClient->refund(
            new RefundRequest("150.75", "test-refundId")
        );
    }
}
