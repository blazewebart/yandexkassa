#Yandex Kassa Codeigniter Library payment integration

#This Codeigniter Library Class using by Yandex Kassa Library. 

#1) Yandex Kassa Api and other documentation.

[Yandex Kassa Developers Api](https://kassa.yandex.ru/developers/api)

#2) For working Yandex Kassa should add The Yandex.Checkout API PHP Client Library.
to Codeigniter in vendor folder.

[The Yandex.Checkout API PHP Client Library](https://github.com/yandex-money/yandex-checkout-sdk-php.git)

#3) include this Codeigniter Class. 

  ##Create peyment in Yandex Kassa and save data to log and/or to database

  ##load library
  ```php
  $this->load->library('yandexkassaapi');
  ```

  ##prepare data for sending to Yandex Kassa
  ```php
  $data_payment = Array();
  $data_payment['id_payment'] = 'payment_id';
  $data_payment['amount'] = 'amount';
  $data_payment['description'] = 'description';
  ```

  ##create payment
  on the side of our site
  ```php
  $response = $this->yandexkassaapi->createPaymentEmbedded($data_payment);
  ```

  or

  on the side of Yandex kassa
  ```php
  $response = $this->yandexkassaapi->createPaymentRedirect($data_payment);
  ```
  
  ##update response data
  ```php
  $data_payment = Array();
  $data_payment['ya_data'] = ($response)?serialize($response):"";
  ```

  ##save response from Yandex Kassa to Database
  ```php
  $this->model->saveResponseToDatabase($id_payment, $data_payment);
  ```
#Get Callback from Yandex Kassa and save data to log and/or to database
  
  ##get data from yandex kassa in json format
  ```php
  $source = file_get_contents('php://input');
  $json = json_decode($source, true);
  $data['json'] = $json;
  ```
