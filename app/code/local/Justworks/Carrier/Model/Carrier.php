<?php

class Justworks_Carrier_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    protected $_code = 'justworks_carrier';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        $result = Mage::getModel('shipping/rate_result');
        /* @var $result Mage_Shipping_Model_Rate_Result */

        $valorKg = $this->getConfigData('weight_distance_amount');
        $valorKgMotoboy = $this->getConfigData('weight_distance_amount_motoboy');
        $pesoTotal = 0;

        foreach ($request->getAllItems() as $_item) {
            $pesoTotal += $_item->getWeight();
        }

        $carrierCities = explode(',', $this->getConfigData('carrier_country'));

        $motoboyActive = $this->getConfigData('motoboy_active');
        $motoboyCities = explode(',', $this->getConfigData('motoboy_country'));
        $pesoMaximoMotoboy = $this->getConfigData('max_weight_motoboy');

        $city_id = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getCityId();

        if ($city_id) {
            $city = $this->loadCity($city_id);
            $distancia = $city['distance'];
            $diasEntrega = $city['dias_entrega'];
            $diasEntregaMotoboy = $city['dias_entrega_motoboy'];
            $observacoes = $city['observacoes'];
            $precoFixo = $city['preco_fixo'];

            if (in_array($city_id, $carrierCities)) {
                $result->append($this->_getStandardShippingRate($valorKg, $pesoTotal, $distancia, $diasEntrega, $observacoes, $precoFixo));
            }

            $result->append($this->_getPickupShippingRate());

            if ($motoboyActive && in_array($city_id, $motoboyCities) && floatval($pesoTotal) <= floatval($pesoMaximoMotoboy)) {
                $result->append($this->_getMotoboyShippingRate($valorKgMotoboy, $pesoTotal, $diasEntregaMotoboy, $precoFixo));
            }
        } else {
            $error = Mage::getModel("shipping/rate_result_error");
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);

            $result->append($this->_getPickupShippingRate());
        }

        if ($request->getFreeShipping()) {
            /**
             *  If the request has the free shipping flag,
             *  append a free shipping rate to the result.
             */
            $freeShippingRate = $this->_getFreeShippingRate();
            $result->append($freeShippingRate);
        }

        return $result;
    }

    protected function _getStandardShippingRate($valorPeso, $pesoTotal, $distancia, $diasEntrega, $observacoes, $precoFixo) {
        $rate = Mage::getModel('shipping/rate_result_method');
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */

        $rate->setCarrier($this->_code);

        $estimate = 'working day';

        if (intval($diasEntrega) === 0 || intval($diasEntrega) > 1) {
            $estimate = 'working days';
        }

        /**
         * getConfigData(config_key) returns the configuration value for the
         * carriers/[carrier_code]/[config_key]
         */
        $rate->setCarrierTitle($this->getConfigData('title'));

        $rate->setMethod('standard');
        $rate->setMethodTitle(Mage::helper('shipping')->__('Standard') . ' - ' . $diasEntrega . ' ' . Mage::helper('shipping')->__($estimate) . '' . ($observacoes ? (' ***(' . $observacoes . ')') : ''));

        if ($precoFixo && floatval($precoFixo) > 0) {
            $valorFrete = $precoFixo;
        } else {
            $valorFrete = floatval($valorPeso) * floatval($pesoTotal); // * floatval($distancia);    
        }

//        $valorFrete = $distancia;

        $rate->setPrice($valorFrete);
        $rate->setCost(0);

        return $rate;
    }

    protected function _getMotoboyShippingRate($valorPeso, $pesoTotal, $diasEntrega, $precoFixo) {
        $rate = Mage::getModel('shipping/rate_result_method');
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */

        $rate->setCarrier($this->_code);

        $estimate = 'working day';

        if (intval($diasEntrega) === 0 || intval($diasEntrega) > 1) {
            $estimate = 'working days';
        }

        /**
         * getConfigData(config_key) returns the configuration value for the
         * carriers/[carrier_code]/[config_key]
         */
        $rate->setCarrierTitle($this->getConfigData('title'));

        $rate->setMethod('motoboy');
        $rate->setMethodTitle(Mage::helper('shipping')->__('Motoboy') . ' (' . $diasEntrega . ' ' . Mage::helper('shipping')->__($estimate) . ')');

        if ($precoFixo && floatval($precoFixo) > 0) {
            $valorFrete = $precoFixo;
        } else {
            $valorFrete = floatval($valorPeso) * floatval($pesoTotal);
        }

//        $valorFrete = $distancia;

        $rate->setPrice($valorFrete);
        $rate->setCost(0);

        return $rate;
    }

    protected function _getPickupShippingRate() {
        $rate = Mage::getModel('shipping/rate_result_method');
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('pickup');
        $rate->setMethodTitle(Mage::helper('shipping')->__('Pickup'));
        $rate->setPrice(0);
        $rate->setCost(0);
        return $rate;
    }

    public function getAllowedMethods() {
        return array(
            'standard' => Mage::helper('shipping')->__('Standard'),
            'pickup' => Mage::helper('shipping')->__('Pickup'),
            'free_shipping' => Mage::helper('shipping')->__('Free'),
            'motoboy' => Mage::helper('shipping')->__('Motoboy')
        );
    }

    protected function _getFreeShippingRate() {
        $rate = Mage::getModel('shipping/rate_result_method');
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('free_shipping');
        $rate->setMethodTitle(Mage::helper('shipping')->__('Free'));
        $rate->setPrice(0);
        $rate->setCost(0);
        return $rate;
    }

    protected function loadCity($city_id) {

        $coreResource = Mage::getSingleton('core/resource');
        $connection = $coreResource->getConnection('core_read');

        $select = 'SELECT * FROM directory_region_city WHERE city_id = ' . $city_id;

        $row = $connection->fetchRow($select);

        return $row;
    }

    public function isTrackingAvailable() {
        return true;
    }

    public function isStateProvinceRequired() {
        return true;
    }

}
