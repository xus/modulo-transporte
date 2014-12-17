<?php
class Empresa_Transportista_Model_Carrier
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'empresa_transportista';

    public function collectRates(
        Mage_Shipping_Model_Rate_Request $request
    )
    {
        $result = Mage::getModel('shipping/rate_result');
        /* @var $result Mage_Shipping_Model_Rate_Result */

        $result->append($this->_getStandardShippingRate());

        $expressWeightThreshold = $this->getConfigData('express_weight_threshold');

        $eligibleForExpressDelivery = true;
        foreach ($request->getAllItems() as $_item) {
            if ($_item->getWeight() > $expressWeightThreshold) {
                $eligibleForExpressDelivery = false;
            }
        }

        //if ($eligibleForExpressDelivery) {
            $result->append($this->_getExpressShippingRate());
        //}

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

    protected function _getStandardShippingRate()
    {
        $rate = Mage::getModel('shipping/rate_result_method');
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */

        $rate->setCarrier($this->_code);
        /**
         * getConfigData(config_key) returns the configuration value for the
         * carriers/[carrier_code]/[config_key]
         */
        $rate->setCarrierTitle($this->getConfigData('title'));

        $rate->setMethod('estandand');
        $rate->setMethodTitle('Standard, de 5 a 10 días');

        $rate->setPrice(14000);
        $rate->setCost(0);

        return $rate;
    }

    protected function _getExpressShippingRate()
    {
        $rate = Mage::getModel('shipping/rate_result_method');
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('express');
        $rate->setMethodTitle('Urgente, antes de 5 días');
        $rate->setPrice(20000);
        $rate->setCost(0);

        return $rate;
    }

    protected function _getFreeShippingRate()
    {
        $rate = Mage::getModel('shipping/rate_result_method');
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('gratis');
        $rate->setMethodTitle('Gratis (5 - 15 days)');
        $rate->setPrice(0);
        $rate->setCost(0);
        return $rate;
    }



    public function getAllowedMethods()
    {
        return array(
            'standard' => 'Standard',
            'express' => 'Express',
            'gratis' => 'Gratis'
        );
    }

    public function isTrackingAvailable()
    {
        return true;
    }

}
