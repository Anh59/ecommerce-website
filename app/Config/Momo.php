<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Momo extends BaseConfig
{
    // Môi trường sandbox (test)
    public $sandboxEndpoint = 'https://test-payment.momo.vn/v2/gateway/api/create';
    public $sandboxPartnerCode = 'MOMOBKUN20180529';
    public $sandboxAccessKey = 'klm05TvNBzhg7h7j';
    public $sandboxSecretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
    
    // Môi trường production (thật)
    public $productionEndpoint = 'https://payment.momo.vn/v2/gateway/api/create';
    public $productionPartnerCode = 'YOUR_PRODUCTION_PARTNER_CODE';
    public $productionAccessKey = 'YOUR_PRODUCTION_ACCESS_KEY';
    public $productionSecretKey = 'YOUR_PRODUCTION_SECRET_KEY';
    
    // Cấu hình chung
    public $environment = 'sandbox'; // sandbox hoặc production
    public $ipnUrl = ''; // URL nhận webhook từ MoMo
    
    public function getConfig()
    {
        if ($this->environment === 'production') {
            return [
                'endpoint' => $this->productionEndpoint,
                'partnerCode' => $this->productionPartnerCode,
                'accessKey' => $this->productionAccessKey,
                'secretKey' => $this->productionSecretKey
            ];
        }
        
        return [
            'endpoint' => $this->sandboxEndpoint,
            'partnerCode' => $this->sandboxPartnerCode,
            'accessKey' => $this->sandboxAccessKey,
            'secretKey' => $this->sandboxSecretKey
        ];
    }
}