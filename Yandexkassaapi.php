<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
|  Payment Defines
| -------------------------------------------------------------------
*/
define("PAYMENT_NEW", 0);
define("PAYMENT_WAIT", 1);
define("PAYMENT_PAID", 2);
define("PAYMENT_NOTPAYED", 3);

//init Yandex Kassa library
use YandexCheckout\Client;

//Yandex Kassa Api class
class YandexKassaApi
{
    //add your id from Yandex Kassa account
    private $shopId = 'Your-Id';

    //add your id from Yandex Kassa account (test_ if real account prefix should be live_)
    private $secretKey = 'test_Secret_Key';

    //use for Yandex Kassa Client
    private $client = null;

    //Your Site 
    private $urlSite = "your-site.com";

    //initialization
    public function __construct()
    {
        //get super codeigniter object
        $this->obj =&get_instance();

        //Yandex Kassa
        $this->client = new Client();
        
        //auth Yandex Kassa
        $this->client->setAuth($this->shopId, $this->secretKey);
    }

    //get info about shop
    public function getInfo()
    {
        $response = $this->client->me();

        return $response;
    }

    //create payment Embedded this payment method using by on Your Site side
    public function createPaymentEmbedded($data)
    {
        //create payment 
        $response = $this->client->createPayment
        (
            array
            (
                //amount data
                'amount' => array
                (
                    'value' => $data['amount'],
                    'currency' => 'RUB',
                ),
                
                // for using by site
                'confirmation' => array
                (
                    'type' => 'embedded',
                ),
                
                //if payment payed status in Yandex Kassa will be succeeded automatically  
                'capture' => true,
                
                //desctiption
                'description' => $data['description'],
                
                //your information for saving in Yandex Kassa
                'metadata' => array
                (
                    'id_payment' => $data['id_payment'],
                )
            ),
            uniqid('', true)
        );

        //save log data
        $this->saveLogData('yandex_kassa_create_payment', $response, true);

        //return response data
        return $response;
    }

    //create payment Redirect this payment method using by on Yandex Kassa side
    public function createPaymentRedirect($data)
    {
        //create payment
        $response = $this->client->createPayment
        (
            array
            (
                //amount data
                'amount' => array
                (
                    'value' => $data['amount'],
                    'currency' => 'RUB',
                ),
                
                //for using on site yandex kassa
                'confirmation' => array
                (
                    'type' => 'redirect', //for using on site yandex kassa
                    'return_url' => 'https://'.$this->urlSite.'/account/payments/pay/'.$data['id_payment'].'/success/', 
                ),
                
                //if payment payed status in Yandex Kassa will be succeeded automatically  
                'capture' => true,
                
                //desctiption
                'description' => $data['description'],
                
                //your information for saving in Yandex Kassa
                'metadata' => array
                (
                    'id_payment' => $data['id_payment'],
                )
            ),
            uniqid('', true)
        );

        //save log data
        $this->obj->user->saveLogData('yandex_kassa_create_payment', $response, true);

        //return response data
        return $response;
    }

    //save log data
    private function saveLogData($file, $data, $export = false)
    {
        //path to log file folder
        $path_folder = APPPATH.'/assets/logs/';

        //if not exists folder create
        if(!is_dir($path_folder))
        {
            mkdir($path_folder, 0755, true);
        }
        
        //prepare file path for saving
        $path_to_file = $path_folder.$file.".log";

        //save file
        if(!file_exists($path_to_file) || is_writable($path_to_file))
        {
            //open/create for write
            if($h = fopen($path_to_file, 'a+'))
            {
                if(flock($h, LOCK_EX))
                {
                    $result = fwrite($h, ($export?var_export($data, true):$data));
                    fflush($h);
                    flock($h, LOCK_UN);
                }
                else
                {
                    $result = false;
                }

                fclose($h);

                return $result;
            }
        }

        //return error
        return false;
    }     
}
