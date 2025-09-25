<?php

namespace App\Libraries;

use Config\Momo;
use Exception;

class MomoService
{
    protected $momoConfig;
    protected $httpClient;

    public function __construct()
    {
        $this->momoConfig = new Momo();
        $this->httpClient = \Config\Services::curlrequest();
    }
    
    public function getConfig()
    {
        return $this->momoConfig->getConfig();
    }

    /**
     * Tạo payment URL từ MoMo - SỬA LẠI METHOD NÀY
     */
public function createPayment($orderData)
{
    try {
        $config = $this->getConfig();
        
        // Đảm bảo amount là số nguyên
        $amount = intval($orderData['amount']);
        
        // Tạo extraData đúng format
        $extraData = [
            'order_number' => $orderData['order_number'],
            'customer_id' => $orderData['customer_id']
        ];
        
        $params = [
            'partnerCode' => $config['partnerCode'],
            'partnerName' => "Your Store Name",
            'storeId' => "Store001",
            'requestId' => $orderData['request_id'],
            'amount' => $amount,
            'orderId' => $orderData['order_id'],
            'orderInfo' => $orderData['order_info'],
            'redirectUrl' => $orderData['return_url'],
            'ipnUrl' => $orderData['ipn_url'],
            'lang' => 'vi',
            'extraData' => base64_encode(json_encode($extraData)),
            'requestType' => 'payWithATM',
            'autoCapture' => true,
            'accessKey' => $config['accessKey'] // Thêm accessKey vào params để tạo signature
        ];

        // Tạo chữ ký
        $params['signature'] = $this->generateSignature($params, $config['secretKey']);

        log_message('debug', 'Momo Payment Request: ' . json_encode($params));

        $response = $this->httpClient->request('POST', $config['endpoint'], [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $params,
            'timeout' => 30,
            'http_errors' => false
        ]);

        // Kiểm tra HTTP status code
        $statusCode = $response->getStatusCode();
        $responseBody = $response->getBody();
        
        log_message('debug', 'Momo HTTP Status: ' . $statusCode);
        log_message('debug', 'Momo Response Body: ' . $responseBody);

        if ($statusCode !== 200) {
            throw new Exception('HTTP Error: ' . $statusCode . ' - Body: ' . $responseBody);
        }

        $result = json_decode($responseBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response from MoMo: ' . json_last_error_msg());
        }

        log_message('debug', 'Momo Payment Response: ' . json_encode($result));

        // Kiểm tra resultCode
        if (isset($result['resultCode']) && $result['resultCode'] == 0) {
            if (empty($result['payUrl'])) {
                throw new Exception('MoMo returned success but no payUrl');
            }
            
            return [
                'success' => true,
                'payment_url' => $result['payUrl'],
                'request_id' => $params['requestId'],
                'order_id' => $params['orderId']
            ];
        } else {
            $errorCode = $result['resultCode'] ?? 'unknown';
            $errorMsg = $result['message'] ?? 'No error message from MoMo';
            throw new Exception('MoMo Error ' . $errorCode . ': ' . $errorMsg);
        }

    } catch (Exception $e) {
        log_message('error', 'Momo Payment Error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

    /**
     * Xác minh chữ ký từ MoMo IPN
     */
    public function verifyIPN($data)
    {
        try {
            $config = $this->momoConfig->getConfig();
            
            $expectedSignature = $this->generateSignature([
                'partnerCode' => $data['partnerCode'],
                'accessKey' => $config['accessKey'],
                'requestId' => $data['requestId'],
                'amount' => $data['amount'],
                'orderId' => $data['orderId'],
                'orderInfo' => $data['orderInfo'],
                'orderType' => $data['orderType'],
                'transId' => $data['transId'],
                'message' => $data['message'],
                'localMessage' => $data['localMessage'],
                'responseTime' => $data['responseTime'],
                'errorCode' => $data['errorCode'],
                'payType' => $data['payType'],
                'extraData' => $data['extraData']
            ], $config['secretKey']);

            return $expectedSignature === $data['signature'];
        } catch (Exception $e) {
            log_message('error', 'Momo IPN Verification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo chữ ký
     */
 /**
 * Tạo chữ ký đúng chuẩn MoMo
 */
private function generateSignature($params, $secretKey)
{
    // Chuỗi ký đúng chuẩn MoMo
    $rawHash = "accessKey=" . $params['accessKey'] .
              "&amount=" . $params['amount'] .
              "&extraData=" . $params['extraData'] .
              "&ipnUrl=" . $params['ipnUrl'] .
              "&orderId=" . $params['orderId'] .
              "&orderInfo=" . $params['orderInfo'] .
              "&partnerCode=" . $params['partnerCode'] .
              "&redirectUrl=" . $params['redirectUrl'] .
              "&requestId=" . $params['requestId'] .
              "&requestType=" . $params['requestType'];

    log_message('debug', 'Raw signature string: ' . $rawHash);
    
    $signature = hash_hmac('sha256', $rawHash, $secretKey);
    log_message('debug', 'Generated signature: ' . $signature);
    
    return $signature;
}

    /**
     * Kiểm tra trạng thái giao dịch
     */
    public function checkTransactionStatus($orderId, $requestId)
    {
        try {
            $config = $this->momoConfig->getConfig();
            
            $params = [
                'partnerCode' => $config['partnerCode'],
                'accessKey' => $config['accessKey'],
                'requestId' => $requestId,
                'orderId' => $orderId,
                'requestType' => 'transactionStatus'
            ];

            $params['signature'] = $this->generateSignature($params, $config['secretKey']);

            $response = $this->httpClient->request('POST', 
                str_replace('/create', '/query', $config['endpoint']), [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => $params,
                'timeout' => 30
            ]);

            return json_decode($response->getBody(), true);

        } catch (Exception $e) {
            log_message('error', 'Momo Status Check Error: ' . $e->getMessage());
            return null;
        }
    }
}