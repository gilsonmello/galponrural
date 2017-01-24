<?php
class MST_Onepagecheckout_Block_Adminhtml_Sales_Order_View_Suvery extends Mage_Adminhtml_Block_Sales_Order_View_Items
{
    public function _toHtml(){
        $html = parent::_toHtml();
        $onepagecheckout_order = $this->getOnepagecheckoutOrderHtml();
        return $html.$onepagecheckout_order;
    }

    public function getOnepagecheckoutOrderHtml(){
        $order_id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($order_id);
        $o_i = $order['increment_id'];
        //Get comment customer
        try{
            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
            $select = $connection->select()
                ->from(Mage::getConfig()->getTablePrefix().'sales_flat_order_status_history', array('comment')) 
                ->where('parent_id=?',$order_id);                         
            $data = $connection->fetchRow($select);  
            $comment = $data['comment'];
            if($comment != ''){
                $html .= '<div id="customer_feedback" class="giftmessage-whole-order-container"><div class="entry-edit">';
                $html .= '<div class="entry-edit-head"><h4>'.$this->helper('onepagecheckout')->__('Customer comment').'</h4></div>';
                $html .= '<fieldset>'.nl2br(Mage::helper('core')->escapeHtml($comment)).'</fieldset>';
                $html .= '</div></div>';
            }
        }catch(exception $e){}
        //get delivery customer
        try{
            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
            $select = $connection->select()
                ->from(Mage::getConfig()->getTablePrefix().'onepagecheckout_delivery', array('date','time')) 
                ->where('order_id=?',$o_i);                         
            $data = $connection->fetchRow($select);  
            $delivery = $data['date'].' - '.$data['time'];
            if ($this->isShowCustomerDeliveryEnabled()){
                $html .= '<div id="customer_delivery" class="giftmessage-whole-order-container"><div class="entry-edit">';
                $html .= '<div class="entry-edit-head"><h4>'.$this->helper('onepagecheckout')->__('Time of delivery').'</h4></div>';
                $html .= '<fieldset>'.nl2br(Mage::helper('core')->escapeHtml($delivery)).'</fieldset>';
                $html .= '</div></div>';
            }
        }catch(exception $e){}
        //get suvery customer
        try{
            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
            $select = $connection->select()
                ->from(Mage::getConfig()->getTablePrefix().'onepagecheckout_suvery', array('content')) 
                ->where('order_id=?',$o_i);                         
            $data = $connection->fetchRow($select);  
            $suvery = $data['content'];
            if ($this->isShowCustomerSuveryEnabled() && $suvery){
                $html .= '<div id="customer_suvery" class="giftmessage-whole-order-container"><div class="entry-edit">';
                $html .= '<div class="entry-edit-head"><h4>'.$this->helper('onepagecheckout')->__('How did you hear about us?').'</h4></div>';
                $html .= '<fieldset>'.nl2br(Mage::helper('core')->escapeHtml($suvery)).'</fieldset>';
                $html .= '</div></div>';
            }
        }catch(exception $e){}
        return $html;
    }

    public function isShowCustomerSuveryEnabled(){
        return Mage::getStoreConfig('onepagecheckout/suvery/enabled', $this->getOrder()->getStore());
    }
    public function isShowCustomerDeliveryEnabled(){
        return Mage::getStoreConfig('onepagecheckout/delivery/enabled', $this->getOrder()->getStore());
    }
}
