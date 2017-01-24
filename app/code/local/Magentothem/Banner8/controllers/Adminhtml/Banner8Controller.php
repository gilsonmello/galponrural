<?php
/*------------------------------------------------------------------------
# Websites: http://www.magentothem.com/
-------------------------------------------------------------------------*/ 
class Magentothem_Banner8_Adminhtml_Banner8Controller extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('banner8/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('banner8/banner8')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('banner8_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('banner8/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('banner8/adminhtml_banner8_edit'))
				->_addLeft($this->getLayout()->createBlock('banner8/adminhtml_banner8_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('banner8')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$data['store_id'] = implode(',',$data['store_id']);
			if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != null) {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('image');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS.'magentothem/banner8'.DS ;
					$uploader->save($path, $_FILES['image']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
		        //$basepath=Mage::getBaseUrl().'media/magentothem/banner8/';
				$basepath='yoururl/magentothem/banner8/';
		        //$basepath=str_replace("index.php/","",$basepath);
				$_filename = str_replace(" ","_",$_FILES['image']['name']);
	  			$data['image'] = '<img src="'.$basepath.$_filename.'" height="80px" alt="" />';
			}
	  			
	  			
			$model = Mage::getModel('banner8/banner8');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('banner8')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('banner8')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('banner8/banner8');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $banner8Ids = $this->getRequest()->getParam('banner8');
        if(!is_array($banner8Ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($banner8Ids as $banner8Id) {
                    $banner8 = Mage::getModel('banner8/banner8')->load($banner8Id);
                    $banner8->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($banner8Ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $banner8Ids = $this->getRequest()->getParam('banner8');
        if(!is_array($banner8Ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($banner8Ids as $banner8Id) {
                    $banner8 = Mage::getSingleton('banner8/banner8')
                        ->load($banner8Id)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($banner8Ids))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'banner8.csv';
        $content    = $this->getLayout()->createBlock('banner8/adminhtml_banner8_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'banner8.xml';
        $content    = $this->getLayout()->createBlock('banner8/adminhtml_banner8_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}