<?php defined('BASEPATH') OR exit('No direct script access allowed');

//Callback class get data from Yandex Kassa
class CallBack extends Controller
{
    function __construct()
    {
        parent::__construct();

        //load Yandex Kassa Library
        $this->load->library('yandexkassaapi');
    }

    //json callback
    function payment()
    {
        //init response var
        $return_data['success'] = false;

        //save log data
        $data = Array();
        $data['date'] = date("Y-m-d H:i:s");

        //get data from yandex kassa in json format
        $source = file_get_contents('php://input');
        $json = json_decode($source, true);
        $data['json'] = $json;

        //if data exists
        if(is_array($json))
        {
            //update response data
            $data_update = Array();

            //if isset callback data
            if(isset($json['event']))
            {
                //serialize
                $data_update['ya_callback'] = serialize($json);

                //get id_payment
                $id_payment = intval($json['object']['metadata']['id_payment']);

                //check payment in case if yandex kassa send data second time
                //this is imitation model
                $payment = $this->model->checkAddedPayment($id_payment);

                if(count($payment))
                {
                    //payment status
                    switch($json['event'])
                    {
                        case "payment.succeeded":
                        {
                            //payed
                            $data_update['status'] = PAYMENT_PAID;
                        }break;
                        default:
                        {
                            //not payed
                            $data_update['status'] = PAYMENT_NOTPAYED;
                        }
                        break;
                    }

                    //update data
                    if($id_payment)
                    {
                        //update payment data
                        //this is imitation model
                        $this->model->updatePayment($id_payment, $data_update);

                        //return successful response
                        $return_data['success'] = true;
                    }
                }
            }
        }

        //save log
        $this->yandexkassaapi->saveLogData('yandex_kassa_callback', $data, true);

        //get answer
        $return_data = json_encode($return_data);        
        
        //show json answer
        echo $return_data;

        //exit
        exit;        
    }
}