# yandexkassa
Yandex Kassa Codeigniter Library payment integration

This Codeigniter Library Class using by Yandex Kassa Library. 

1) Yandex Kassa Api and other documentation.

https://kassa.yandex.ru/developers/api

2) For working Yandex Kassa should add The Yandex.Checkout API PHP Client Library.
to Codeigniter in vendor folder.

https://github.com/yandex-money/yandex-checkout-sdk-php.git

3) include this Codeigniter Class. 

1) Create peyment in Yandex Kassa and save data to log and/or to database

//load library
$this->load->library('yandexkassaapi');

//prepare data for sending to Yandex Kassa
$data_payment = Array();
$data_payment['id_payment'] = 'payment_id';
$data_payment['amount'] = 'amount';
$data_payment['description'] = 'description';

//creapte payment

//on the side of our site
$response = $this->yandexkassaapi->createPaymentEmbedded($data_payment);

//or

//on the side of Yandex kassa
$response = $this->yandexkassaapi->createPaymentRedirect($data_payment);

//update response data
$data_payment = Array();
$data_payment['ya_data'] = ($response)?serialize($response):"";

//save response from Yandex Kassa to Database
$this->model->saveResponseToDatabase($id_payment, $data_payment);

2) Get Callback from Yandex Kassa and save data to log and/or to database
  
