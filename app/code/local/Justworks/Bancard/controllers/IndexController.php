<?php

class Justworks_Bancard_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        echo "Hello tuts+ World";
    }

    public function testAction() {

        $v_currency = Mage::app()->getStore()->getCurrentCurrencyCode();
        $v_amount = '165.00';

        if ($v_currency !== 'PYG') {
            $v_amount = Mage::helper('directory')->currencyConvert($v_amount, $v_currency, 'PYG');
        }

        $v_amount = number_format($v_amount, 2, '.', '');
        echo $v_amount;
    }

    public function singleBuyAction() {

        $url = 'https://vpos.infonet.com.py:8888/vpos/api/0.3/single_buy';

        $v_shop_process_id = '48';
        $v_private_key = 'DJcIstLeX3Ug,qGcwGgnvASP5i8jNHTNpa.(9JOC';
        $v_public_key = 'ekOWXWNJwVhug52xDM1iKJv538fErNPZ';
        $v_description = 'Compra teste';

        $v_return_url = 'http://galponru.nextmp.net/index.php/bancard/index/success';
        $v_cancel_url = 'http://galponru.nextmp.net/index.php/bancard/index/error';

        $v_amount = '5000.00';
        $v_currency = 'PYG';

        $v_token = md5($v_private_key . $v_shop_process_id . $v_amount . $v_currency);

        $data = array(
            "public_key" => "$v_public_key",
            "operation" => array(
                "token" => "$v_token",
                "shop_process_id" => $v_shop_process_id,
                "amount" => "$v_amount",
                "currency" => "$v_currency",
                'additional_data' => "",
                "description" => "$v_description",
                "return_url" => "$v_return_url",
                "cancel_url" => "$v_cancel_url"
            )
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_PORT, '8888');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die("Curl Failed: " . curl_error($ch));
        }

        curl_close($ch);

        $json = json_decode($result);

        if ($json->status === 'success') {
            $process_id = $json->process_id;
            $redirect = 'https://vpos.infonet.com.py:8888/payment/single_buy?process_id=' . $process_id;
            $this->_redirectUrl($redirect);
        } else {
            echo 'Erro: ' . $json->messages[0]->dsc;
        }

        //success: {"status":"success","process_id":"Q0qKRMr1_hFfQhJmIswd"}
        // error: {"status":"error","messages":[{"level":"error","key":"InvalidJsonError","dsc":"Invalid JSON"}]}
    }

    public function rollbackAction() {

        $url = 'https://vpos.infonet.com.py:8888/vpos/api/0.3/single_buy/rollback';

        $v_shop_process_id = '48';
        $v_private_key = 'DJcIstLeX3Ug,qGcwGgnvASP5i8jNHTNpa.(9JOC';
        $v_public_key = 'ekOWXWNJwVhug52xDM1iKJv538fErNPZ';

        $v_token = md5($v_private_key . $v_shop_process_id . 'rollback' . '0.00');

        $data = array(
            "public_key" => "$v_public_key",
            "operation" => array(
                "token" => "$v_token",
                "shop_process_id" => $v_shop_process_id
            )
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_PORT, 8888);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die("Curl Failed: " . curl_error($ch));
        }

        curl_close($ch);

        $json = json_decode($result);

        if ($json->status === 'success') {
            echo 'Rollback com sucesso';
        } else {

            $errorKey = $json->messages[0]->key;
            $errorDesc = $json->messages[0]->dsc;

            switch ($errorKey) {
                case 'InvalidJsonError':
                case 'UnauthorizedOperationError':
                case 'ApplicationNotFoundError':
                case 'InvalidPublicKeyError':
                case 'InvalidTokenError':
                case 'InvalidOperationError':
                    break;
                case 'BuyNotFoundError':
                case 'PaymentNotFoundError':
                case 'AlreadyRollbackedError':
                case 'PosCommunicationError':
                case 'TransactionAlreadyConfirmed':
                    break;
                default :
                    break;
            }

            print_r($json);
//            echo 'Erro: ' . $json->messages[0]->dsc;
        }
    }

    public function processaPagamentoAction() {
        $return = array(
            'status' => 'success'
        );

//        { 
//	operation: { 
//		token: 'aa43002d927fe387dfff2fbc16e780b9',
//	    shop_process_id: 12,
//	    response: 'S',
//	    response_details: 'Procesado Satisfactoriamente',
//	  	amount: '5000.00',
//	    currency: 'PYG',
//	    authorization_number: null,
//	    ticket_number: '0',
//	    response_code: '57',
//	    response_description: 'Transacción Denegada',
//	    extended_response_description: 'IMPORTE DE LA TRN INFERIOR AL MÍNIMO PERMITIDO',
//	    security_information: {
//	     	customer_ip: '191.184.223.85',
//	      	card_source: 'I',
//	        card_country: 'UNITED STATES',
//	       	version: '0.3',
//	       	risk_index: '0' 
//		}
//	}
//}

        $this->getResponse()
                ->clearHeaders()
                ->setHeader('HTTP/1.0', 200, true)
                ->setHeader('Content-Type', 'application/json') // can be changed to json, xml...
                ->setBody(json_encode($return));
    }

    public function getConfirmationAction() {

        $url = 'https://vpos.infonet.com.py:8888/vpos/api/0.3/single_buy/confirmations';

        $v_shop_process_id = '48';
        $v_private_key = 'DJcIstLeX3Ug,qGcwGgnvASP5i8jNHTNpa.(9JOC';
        $v_public_key = 'ekOWXWNJwVhug52xDM1iKJv538fErNPZ';

        $v_token = md5($v_private_key . $v_shop_process_id . 'get_confirmation');

        $data = array(
            "public_key" => "$v_public_key",
            "operation" => array(
                "token" => "$v_token",
                "shop_process_id" => $v_shop_process_id
            )
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_PORT, 8888);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die("Curl Failed: " . curl_error($ch));
        }

        curl_close($ch);

        $json = json_decode($result);

        if ($json->status === 'success') {
            print_r($json);
        } else {
            print_r($json);
//            echo 'Erro: ' . $json->messages[0]->dsc;
        }

//        {
//    "status": "success"
//    "confirmation": {
//        "token": "[generated token]",
//        "shop_process_id": "12313",
//        "response": "S",
//        "response_details": "respuesta S",
//        "extended_response_description": "respuesta extendida",
//        "currency": "PYG",
//        "amount": "10100.00",
//        "authorization_number": "123456",
//        "ticket_number": "123456789123456",
//        "response_code": "00",
//        "response_description": "Transacción aprobada.",
//        "security_information": {
//            "customer_ip": "123.123.123.123",
//            "card_source": "I",
//            "card_country": "Croacia",
//            "version": "0.3",
//            "risk_index": "0"
//        }
//    }
//}
    }

    public function successAction() {
        echo 'Tudo certo';
    }

    public function errorAction() {
//        echo 'Operação Cancelada';
        $this->rollbackAction();
    }
    
    public function emailAction(){
        
//        $data = $this->getRequest()->getPost();
//        $name = $data['name'];
//        $cemail = $data['email'];
//        $number = $data['number'];
//        $message = $data['message'];
        $emailTemplate = Mage::getModel('core/email_template')
                ->loadByCode('alerta_cliente_fila_espera');

        $emailTemplateVariables = array();
        $emailTemplateVariables['cliente_nome'] = 'Alexandre';
        $emailTemplateVariables['cliente_email'] = 'alexandredpl@gmail.com';
        $emailTemplateVariables['cliente_telefone'] = '(67) 99220-8338';
        $emailTemplateVariables['produto_nome'] = 'produto';
        $emailTemplateVariables['produto_link'] = 'link';
        $email = Mage::getStoreConfig('trans_email/ident_general/email');
        $name = Mage::getStoreConfig('trans_email/ident_general/name'); 
        $emailTemplate->getProcessedTemplate($emailTemplateVariables);
        $emailTemplate->setTemplateSubject('Novo cliente aguardando estoque de produto');
        $emailTemplate->setSenderName($name);
        $emailTemplate->setSenderEmail($email); 
        try {
            $emailTemplate->send($emailTemplateVariables['cliente_email'], 'Galpón Rural', $emailTemplateVariables);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

}
