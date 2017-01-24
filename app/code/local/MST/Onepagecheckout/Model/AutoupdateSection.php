<?php
class MST_Onepagecheckout_Model_AutoupdateSection
{
    public function toOptionArray()
    {
        $types = array('Country', 'Postcode', 'State/region', 'City');
        $data = array();

        foreach($types as $type)	{
            $data[] = array('label' => $type, 'value' => strtolower($type));
        }

        return $data;
    }
}