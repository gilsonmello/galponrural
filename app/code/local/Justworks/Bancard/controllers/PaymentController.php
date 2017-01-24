<?php

// app/code/local/Envato/Custompaymentmethod/controllers/PaymentController.php
class Justworks_Bancard_PaymentController extends Mage_Core_Controller_Front_Action {

    private $bancard_url = 'https://vpos.infonet.com.py/';
    private $success_url = 'http://www.galponrural.com.py/bancard/payment/success';
    private $cancel_url = 'http://www.galponrural.com.py/bancard/payment/cancel';

    public function gatewayAction() {
        if ($this->getRequest()->get("orderId")) {
            $arr_querystring = array(
                'flag' => 1,
                'orderId' => $this->getRequest()->get("orderId")
            );

            Mage_Core_Controller_Varien_Action::_redirect('bancard/payment/response', array('_secure' => false, '_query' => $arr_querystring));
        }
    }

    public function redirectAction() {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template', 'bancard', array('template' => 'bancard/redirect.phtml'));
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

    public function responseAction() {
        if ($this->getRequest()->get("flag") == "1" && $this->getRequest()->get("orderId")) {
            $orderId = $this->getRequest()->get("orderId");
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            $order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, true, 'Payment Success.');
            $order->save();

            Mage::getSingleton('checkout/session')->unsQuoteId();
            Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure' => false));
        } else {
            Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/error', array('_secure' => false));
        }
    }

    public function singleBuyAction() {

        $order = new Mage_Sales_Model_Order();
        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order->loadByIncrementId($orderId);

        $amount = $order->getGrandTotal();

        foreach ($order->getAllVisibleItems() as $value) {
//            echo $value->getName();
//            echo $value->getSku();
//            echo $value->getPrice();
//            echo $value->getQtyOrdered();
        }

        $url = $this->bancard_url . 'vpos/api/0.3/single_buy';

        $v_shop_process_id = $orderId;
        $v_private_key = Mage::getStoreConfig('payment/bancard/private_key', Mage::app()->getStore());
        $v_public_key = Mage::getStoreConfig('payment/bancard/public_key', Mage::app()->getStore());
        $v_description = 'Galpón Rural';

        $v_return_url = $this->success_url . '?v_shop_process_id=' . $v_shop_process_id;
        $v_cancel_url = $this->cancel_url . '?v_shop_process_id=' . $v_shop_process_id;

        $v_currency = Mage::app()->getStore()->getCurrentCurrencyCode();

        if ($v_currency !== 'PYG') {
            $v_amount = Mage::helper('directory')->currencyConvert($amount, $v_currency, 'PYG');
        }

        $v_amount = number_format($v_amount, 2, '.', '');

        $v_token = md5($v_private_key . $v_shop_process_id . $v_amount . 'PYG');

        $data = array(
            "public_key" => "$v_public_key",
            "operation" => array(
                "token" => "$v_token",
                "shop_process_id" => $v_shop_process_id,
                "amount" => "$v_amount",
                "currency" => "PYG",
                'additional_data' => "",
                "description" => "$v_description",
                "return_url" => "$v_return_url",
                "cancel_url" => "$v_cancel_url"
            )
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);

        if ($result === FALSE) {
            Mage::getSingleton('checkout/session')->addError(curl_error($ch));
            return $this->_redirect('checkout/onepage/failure');
        }

        curl_close($ch);

        $json = json_decode($result);

        if ($json->status === 'success') {
            $process_id = $json->process_id;
            $redirect = $this->bancard_url . 'payment/single_buy?process_id=' . $process_id;
            $this->_redirectUrl($redirect);
        } else {
            Mage::getSingleton('checkout/session')->addError($json->messages[0]->dsc);
            $this->_redirect('checkout/onepage/failure');
        }
    }

    public function successAction() {
        $orderId = Mage::app()->getRequest()->getParam('v_shop_process_id');
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

        if ($order->bancard_status === 'paid') {
            Mage::getSingleton('checkout/session')->unsQuoteId();
            $this->_redirect('checkout/onepage/success');
        } else {
            Mage::getSingleton('checkout/session')->addError($order->bancard_message);
            $this->_redirect('checkout/onepage/failure');
        }
    }

    public function cancelAction() {
        $orderId = Mage::app()->getRequest()->getParam('v_shop_process_id');
        $this->rollbackAction($orderId);

        Mage::getSingleton('checkout/session')->addError($this->__('Order cancelled'));
        $this->_redirect('checkout/onepage/failure');
    }

    public function rollbackAction($v_shop_process_id = FALSE) {

        if (!$v_shop_process_id) {
            $v_shop_process_id = Mage::app()->getRequest()->getParam('v_shop_process_id');
        }

        $url = $this->bancard_url . 'vpos/api/0.3/single_buy/rollback';

        $v_private_key = Mage::getStoreConfig('payment/bancard/private_key', Mage::app()->getStore());
        $v_public_key = Mage::getStoreConfig('payment/bancard/public_key', Mage::app()->getStore());

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
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);

        if ($result) {

            curl_close($ch);

            $json = json_decode($result);

            if ($json->status === 'success') {
                
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
            }
        }
    }

    public function processaPagamentoAction() {

        $return = array(
            'status' => 'success'
        );

        $this->getResponse()
                ->clearHeaders()
                ->setHeader('HTTP/1.0', 200, true)
                ->setHeader('Content-Type', 'application/json') // can be changed to json, xml...
                ->setBody(json_encode($return));

        $body = json_decode(Mage::app()->getRequest()->getRawBody());

        if ($body) {

            $operation = $body->operation;

            $orderId = $operation->shop_process_id;
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            $response_code = $operation->response_code;

            $bancard_status = '';
            $bancard_status_code = '';
            $bancard_message = '';
            $bancard_extended_message = '';
            $erro = FALSE;
            $statusErro = '';

            if ($operation->response === 'S') {

                switch ($response_code) {
                    case '00':// (transacción aprobada)
                        $bancard_status = 'paid';
                        $bancard_status_code = $response_code;
                        $bancard_message = $operation->response_description;
                        $bancard_extended_message = $operation->extended_response_description;
                        break;
                    case '05':// (Tarjeta inhabilitada)
                    case '12':// (Transacción inválida)
                    case '15':// (Tarjeta inválida)
                    case '51':// (Fondos insuficientes)
                    case '57':// (Transacción Denegada)
                        $statusErro = 'payment_refused';
                        $bancard_status = 'unpaid';
                        $bancard_status_code = $response_code;
                        $bancard_message = $operation->response_description;
                        $bancard_extended_message = $operation->extended_response_description;
                        $erro = TRUE;
                        break;
                    default :
                        $statusErro = 'payment_error';
                        $bancard_status = 'unpaid';
                        $bancard_status_code = '99';
                        $bancard_message = $operation->response_details;
                        $bancard_extended_message = $operation->response_details;
                        $erro = TRUE;
                        break;
                }
            } else {
                $statusErro = 'payment_error';
                $bancard_status = 'unpaid';
                $bancard_status_code = '99';
                $bancard_message = $operation->response_details;
                $bancard_extended_message = $operation->response_details;
                $erro = TRUE;
            }

            if ($erro) {
                $quoteId = $order->getQuoteId();
                $quote = Mage::getModel('sales/quote')->load($quoteId);
                Mage::log('Sending email with: '. $quote);
                Mage::helper('checkout')->sendPaymentFailedEmail($quote, $bancard_message);
                $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, $statusErro, $bancard_message);
            } else {
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
            }

            $order->setBancardStatus($bancard_status);
            $order->setBancardStatusCode($bancard_status_code);
            $order->setBancardMessage($bancard_message);
            $order->setBancardResponse(json_encode($operation));
            $order->setBancardExtendedMessage($bancard_extended_message);
            $order->save();

            if ($erro) {
                $this->rollbackAction($orderId);
            } else {
                $invoice = $order->prepareInvoice()
                        ->setTransactionId($order->getId())
//                 ->addComment($comment)
                        ->register()
                        ->pay();

                $order->sendNewOrderEmail();

                $transaction_save = Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder());
                $transaction_save->save();
            }
        }
    }

    public function getConfirmationAction() {

        $url = $this->bancard_url . 'vpos/api/0.3/single_buy/confirmations';

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

}
