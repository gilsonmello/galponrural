<?php
class MST_Onepagecheckout_Model_DeliveryFormat
{
    public function toOptionArray()
    {
        $types = array(
                        'Month / Day / Year'=>'%m/%d/20%y', 
                        'Day / Month / Year'=>'%d/%m/20%y', 
                        'Year / Month / Day'=>'20%y/%m/%d'
                    );
        $data = array();
        foreach($types as $key=>$value)	{
            $data[] = array('label' => $key, 'value' => strtolower($value));
        }
        return $data;
    }
}