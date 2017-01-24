<?php
class MST_Onepagecheckout_Model_DeliveryDate
{
    public function toOptionArray()
    {
        $types = array(
                        'Sunday'=>'0', 
                        'Monday'=>'1', 
                        'Tuesday'=>'2', 
                        'Wednesday'=>'3', 
                        'Thursday'=>'4', 
                        'Friday'=>'5', 
                        'Saturday'=>'6'
                      );
        $data = array();
        foreach($types as $key=>$value)	
        {
            $data[] = array('label' => $key, 'value' => strtolower($value));
        }
        return $data;
    }
}