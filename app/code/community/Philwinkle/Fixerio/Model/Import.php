<?php

class Philwinkle_Fixerio_Model_Import extends Mage_Directory_Model_Currency_Import_Abstract
{
//    protected $_url = 'https://api.fixer.io/latest?base=%1$s&symbols=%2$s';
    protected $_url = 'http://www.apilayer.net/api/live?access_key=72c805b21ed5a9159325b27076d0340e&format=1&currencies=%1';
    protected $_messages = array();

     /**
     * HTTP client
     *
     * @var Varien_Http_Client
     */
    protected $_httpClient;

    public function __construct()
    {
        $this->_httpClient = new Varien_Http_Client();
    }

    protected function _convert($currencyFrom, $currencyTo, $retry = 0)
    {
//        $url = sprintf($this->_url, $currencyFrom, $currencyTo);
        $url = sprintf($this->_url, $currencyTo);

        try {
            $response = $this->_httpClient
                ->setUri($url)
                ->setConfig(array('timeout' => Mage::getStoreConfig('currency/fixerio/timeout')))
                ->request('GET')
                ->getBody();
            
            $curr = "USD$currencyTo";

            $converted = json_decode($response);
            $rate = $converted->quotes->$curr;

            if (!$rate) {
                $this->_messages[] = Mage::helper('directory')->__('Cannot retrieve rate from %s.', $url);
                return null;
            }

            // test for bcmath to retain precision
            if (function_exists('bcadd')) {
                return bcadd($rate, '0', 12);
            }

            return (float) $rate;
        } catch (Exception $e) {
            if ($retry == 0) {
                return $this->_convert($currencyFrom, $currencyTo, 1);
            } else {
                $this->_messages[] = Mage::helper('directory')->__('Cannot retrieve rate from %s.', $url);
                return null;
            }
        }
    }

}