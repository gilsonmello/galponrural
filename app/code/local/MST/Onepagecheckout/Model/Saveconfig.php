<?php
class MST_Onepagecheckout_Model_Saveconfig extends Mage_Core_Model_Config_Data {

    protected function _afterSave() {
		
        $path = $this->getPath();
        $value = trim($this->getValue());
		//echo $value.'aa<br>';
		//echo $label.'bb<br>';
		/*
		$arr_domain = explode('.',$_SERVER['SERVER_NAME']);
		$main_domain = $_SERVER['SERVER_NAME'];
		if ( count($arr_domain) == 3 ) { 
		$main_domain = $arr_domain[1].'.'.$arr_domain[2];
		}
		*/
		$main_domain = Mage::helper('onepagecheckout')->get_domain( $_SERVER['SERVER_NAME'] );
		if ( $main_domain != 'dev' ) { 
		$url = base64_decode('aHR0cDovL21hZ2ViYXkuY29tL21zdC5waHA/a2V5PQ==').$value.'&domain='.$main_domain;
		//$file = file_get_contents($url);
		$ch = curl_init(); 
		// set url 
		curl_setopt($ch, CURLOPT_URL, $url); 
		//return the transfer as a string 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		// $output contains the output string 
		$file = curl_exec($ch); 
		// close curl resource to free up system resources 
		curl_close($ch);
		
		$get_content_id = Mage::helper('onepagecheckout')->get_div($file,"valid_licence");
		//print_r($get_content_id);
		
		
		if(!empty($get_content_id[0])) {
			$return_valid = $get_content_id[0][0];
			$domain_count = $get_content_id[0][1];
			$domain_list = $get_content_id[0][2];
			$created_time = $get_content_id[0][3];
			if ( $return_valid == '1' ) {
			//echo $return_valid.'--'.$domain_count.'--'.$domain_list.'--'.$created_time;
			$rakes = Mage::getModel('onepagecheckout/license')->getCollection();
			$rakes->addFieldToFilter('path', 'onepagecheckout/license/key' );
			if ( count($rakes) > 0 ) {
				foreach ( $rakes as $rake )  {
					$update = Mage::getModel('onepagecheckout/license')->load( $rake->getLicenseId() );
					$update->setPath($path);
					$update->setExtensionCode( md5($main_domain.$value) );
					$update->setLicenseKey($value);
					$update->setDomainCount($domain_count);
					$update->setDomainList($domain_list);
					$update->setCreatedTime($created_time);
					$update->save();
				}
			} else {  
				$new = Mage::getModel('onepagecheckout/license');
				$new->setPath($path);
				$new->setExtensionCode( md5($main_domain.$value) );
				$new->setLicenseKey($value);
				$new->setDomainCount($domain_count);
				$new->setDomainList($domain_list);
				$new->setCreatedTime($created_time);
				$new->save();
			}
			}
			/*foreach($get_content_id[0] as $key => $val){
				$return_valid = $val;
			}*/
		}
		}
		
		//print_r($this);
		//exit();
        // $value is the text in the text field
    }

}