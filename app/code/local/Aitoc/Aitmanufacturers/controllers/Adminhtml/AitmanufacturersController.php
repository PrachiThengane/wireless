<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Adminhtml_AitmanufacturersController extends Mage_Adminhtml_Controller_Action
{

    public function gridAction()
    {
         $this->getResponse()->setBody(
            $this->getLayout()->createBlock('aitmanufacturers/adminhtml_aitmanufacturers_edit_tab_product')->toHtml()
        );
    }
    
    protected function _init()
    {
        if (!is_null($this->getRequest()->getParam('store'))){
	        Mage::getSingleton('adminhtml/session')->setData('aitmanufacturers_store', $this->_getStoreId());
	    }
	    elseif (!is_null(Mage::getSingleton('adminhtml/session')->getData('aitmanufacturers_store'))){
	        $this->getRequest()->setParam('store', Mage::getSingleton('adminhtml/session')->getData('aitmanufacturers_store'));
	    }
	    Mage::register('store_id', $this->_getStoreId());
    }
    
    protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('catalog/aitmanufacturers')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Brands Pages Manager'), Mage::helper('adminhtml')->__('Brands Pages Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
	    $this->_init();
		$this->_initAction()
			->renderLayout();
	}
	
	protected function _getStoreId()
	{
	    if (Mage::app()->isSingleStoreMode()) {
	        return Mage::app()->getStore(true)->getId();
	    }
	    return Mage::app()->getStore((int) $this->getRequest()->getParam('store', 0))->getId();
	}
	
	protected function _redirect($path, $arguments = array())
	{
	    $arguments['store'] = $this->_getStoreId();
	    parent::_redirect($path, $arguments);
	}
	
	public function fillOutAction(){
	    $this->_init();
	    $model  = Mage::getModel('aitmanufacturers/aitmanufacturers');
	    $storeId = $this->_getStoreId();
	    try {
    	    $model->fillOut($storeId);
    	    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('aitmanufacturers')->__('Brands Pages were successfully filled out'));
	    } catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('aitmanufacturers')->__('There was an error during the process'));
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
    	$this->_redirect('*/*/', array('store' => $storeId));
	}

	public function editAction() {
	    $this->_init();
        $session = Mage::getModel('adminhtml/session');
        $sort = $this->getRequest()->getParam('sort');
        $dir = $this->getRequest()->getParam('dir'); 
        if(!empty($sort))
        {
            $session->setSort($sort);
        }
        if(!empty($dir))
        {
            $session->setDir($dir);
        } 
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('aitmanufacturers/aitmanufacturers')->load($id);
		
		if ($delete = $this->getRequest()->getParam('delete')){
		    switch ($delete){
		        case 'small_logo':
		        case 'list_image':
		        case 'image':
		            $params = $this->getRequest()->getParams();
		            unset($params['delete']);
		            if ($model->getFeatured() && 'image' == $delete){
		                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('aitmanufacturers')->__('Image must be uploaded for Featured Brand'));
                        Mage::getSingleton('adminhtml/session')->setFormData($model->getData());
                        $this->_redirect('*/*/*/', $params);
                        return;
		            }
		            $filename = $model->getData($delete);
		            $path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . ($delete == 'small_logo'?'logo'.DS:($delete == 'list_image'?'list'.DS:''));
		            if (file_exists($path.$filename)){
		                @unlink($path.$filename);
		            }
		            $model->setData($delete, '');
		            $model->save();

		            $this->_redirect('*/*/*/', $params);
		            return;
		    }
		}

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('aitmanufacturers_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('catalog/aitmanufacturers');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Brands Pages Manager'), Mage::helper('adminhtml')->__('Brands Pages Manager'));
			//$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('aitmanufacturers/adminhtml_aitmanufacturers_edit'))
				->_addLeft($this->getLayout()->createBlock('aitmanufacturers/adminhtml_aitmanufacturers_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('aitmanufacturers')->__('Brand does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		
		$bEdtiPage = false;
	    $this->_init();
        $session = Mage::getModel('adminhtml/session');
		if ($data = $this->getRequest()->getPost()) {
            $storeId = $data['stores'][0];
            //saving orders
            
            $manufacturer = Mage::getModel('aitmanufacturers/aitmanufacturers')->load($data['manufacturer_id']);
            $productIds = $manufacturer->getProductsByManufacturer($data['manufacturer_id'],$data['stores'][0]);                        
            $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('sort')  
            ->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left')
            ->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $storeId)
            ->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $storeId)
            ->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $storeId)
            ->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $storeId)
            ->joinAttribute('sort', 'catalog_product/aitmanufacturers_sort', 'entity_id', null, 'left', $storeId)
            ->addFieldToFilter('entity_id',array('in'=>$productIds));
            
            if(!empty($data['custom_name']))
            {
                $collection->addFieldToFilter('custom_name',array('like'=> '%'.$data['custom_name'].'%'));
            }
            if(!empty($data['product_order']['from']))
            {
                $collection->addFieldToFilter('aitmanufacturers_sort',array('gteq'=> $data['product_order']['from']));     
            }
            if(!empty($data['product_order']['to']))
            {
                $collection->addFieldToFilter('aitmanufacturers_sort',array('lteq'=> $data['product_order']['to']));     
            }
            $sort =  $session->getData('aitmanufacturersGridsort');
            $dir =  $session->getData('aitmanufacturersGriddir'); 
            if(!empty($sort))

            {
                if($sort == 'product_order')
                {
                    $sort = 'sort';
                }
                else
                {
                    $sort = 'custom_name';
                }
                $collection->addAttributeToSort($sort,$dir);     
            }

            $limit = $data['limit'];
            $page = $data['page'];
            $first = $limit*($page-1);
            $ind = 0;
            $dataInd = 0;
            foreach($collection as $item)
            {  
                $sort = $item->getData('aitmanufacturers_sort');
                if(empty($sort))
                {
                     $item->addAttributeUpdate('aitmanufacturers_sort','9999',$storeId); 
                }
                if(($ind <= ($page)*$limit) && ($ind >= ($page-1)*$limit))
                {
                     if(!empty($data['product_order'][$dataInd]))
                    {
                        if($data['product_order'][$dataInd]>0)
                        {
                             $item->addAttributeUpdate('aitmanufacturers_sort',$data['product_order'][$dataInd],$storeId);
                        }
                    }
                    $dataInd++;               
                }               
                $ind++;   
            } 
            //saving orders
		    $model = Mage::getModel('aitmanufacturers/aitmanufacturers');
		    $manufacturer = $model->getManufacturerName($data['manufacturer_id']);
		    if (empty($data['title'])){
                $data['title'] = $manufacturer;
            }
            if (!empty($data['url_key'])){
                $urlKey = Mage::helper('aitmanufacturers')->toUrlKey($data['url_key']);
            }
            else {
                $urlKey = Mage::helper('aitmanufacturers')->toUrlKey($manufacturer);
            }
            $data['url_key'] = $urlKey;
            
			if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
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
					$path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS;
					//$uploader->save($path, $_FILES['image']['name'] );
					
					if(Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_rename_pic'))
						$imagename = md5($_FILES['image']['name'].time()) . '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);
					else 
						$imagename = $_FILES['image']['name'];
					

					if($model->isImageExists($this->getRequest()->getParam('id'), $imagename))
					{
        				throw new Exception(Mage::helper('aitmanufacturers')->__('Image with the same name already exists. Rename please'));
					}
						
					//$uploader->resize(200);
					//$uploader->convert();
					//print_r($path.$filename);exit;
					$uploader->save($path, $imagename);
					/*$image = new Varien_Image($path . $filename);
					$image->resize(100);
					$image->keepFrame(true);
					$image->save();*/
					/*$processor = new Varien_Image($path.$newimage);
                    $processor->keepAspectRatio(true);
                    $processor->resize(200);
                    $processor->save();exit;*/
					
				} catch (Exception $e) {
					$imagename = '';
					$sError = $e->getMessage();
					if($sError == 'Disallowed file type.')
						$sError = Mage::helper('aitmanufacturers')->__('Can not upload image ') . $sError . Mage::helper('aitmanufacturers')->__(' jpg, jpeg, gif, png allowed only');
					Mage::getSingleton('adminhtml/session')->addError($sError);
					$bEdtiPage = true;

		        }
	        
		        if (isset($imagename)){
    		        //this way the name is saved in DB
    	  			$data['image'] = $imagename;
		        }
			}
			
		    if (isset($_FILES['small_logo']['name']) && $_FILES['small_logo']['name'] != '') {
				try {	
					$uploader = new Varien_File_Uploader('small_logo');
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . 'logo' . DS;
					if(Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_rename_pic'))
						$logoName = md5($_FILES['small_logo']['name'].time()) . '.' . substr(strrchr($_FILES['small_logo']['name'], '.'), 1);
					else 
						$logoName = $_FILES['small_logo']['name'];

					if($model->isLogoExists($this->getRequest()->getParam('id'), $logoName))
					{
        				throw new Exception(Mage::helper('aitmanufacturers')->__('Logo with the same name already exists. Rename please'));
					}	
						   	
					$uploader->save($path, $logoName);
					
				} catch (Exception $e) {
				    
					$logoName = '';
					$sError = $e->getMessage();
					if($sError == 'Disallowed file type.')
						$sError = Mage::helper('aitmanufacturers')->__('Can not upload logo ') . $sError . Mage::helper('aitmanufacturers')->__(' jpg, jpeg, gif, png allowed only');
					Mage::getSingleton('adminhtml/session')->addError($sError);
					$bEdtiPage = true;

		        }
	        
		        if (isset($logoName)){
    		        //this way the name is saved in DB
    	  			$data['small_logo'] = $logoName;
		        }
			}
			
			if (isset($_FILES['list_image']['name']) && $_FILES['list_image']['name'] != '') {
				try {	
					$uploader = new Varien_File_Uploader('list_image');
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . 'list' . DS;
					if(Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_rename_pic'))
						$listImageName = md5($_FILES['list_image']['name'].time()) . '.' . substr(strrchr($_FILES['list_image']['name'], '.'), 1);
					else 
						$listImageName = $_FILES['list_image']['name'];
					
					if($model->isListImageExists($this->getRequest()->getParam('id'), $listImageName))
					{
						throw new Exception(Mage::helper('aitmanufacturers')->__('Brand icon with the same name already exists. Rename please'));
					}
						
					$uploader->save($path, $listImageName);
					
				} catch (Exception $e) {
				   	$listImageName = '';
					$sError = $e->getMessage();
					if($sError == 'Disallowed file type.')
						$sError = Mage::helper('aitmanufacturers')->__('Can not upload brand icon. ') . $sError . Mage::helper('aitmanufacturers')->__(' jpg, jpeg, gif, png allowed only');
					Mage::getSingleton('adminhtml/session')->addError($sError);
					$bEdtiPage = true;
		        }
	        
		        if (isset($listImageName)){
    		        //this way the name is saved in DB
    	  			$data['list_image'] = $listImageName;
		        }
			}
			
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));	
			try {
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('aitmanufacturers')->__('Brand Page was successfully saved'));  
                Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				    if($bEdtiPage)
        				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        			else
						$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('aitmanufacturers')->__('Unable to find Brand Page to save'));
       	$this->_redirect('*/*/');
	}
 
	public function deleteAction() {
	    $this->_init();
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('aitmanufacturers/aitmanufacturers');
				$model->deletePictures($this->getRequest()->getParam('id'));
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Brand Page was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $this->_init();
        $aitmanufacturersIds = $this->getRequest()->getParam('aitmanufacturers');
        if(!is_array($aitmanufacturersIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Brands Page(s)'));
        } else {
            try {
                foreach ($aitmanufacturersIds as $aitmanufacturersId) {
                	Mage::getModel('aitmanufacturers/aitmanufacturers')->deletePictures($aitmanufacturersId);
                    $aitmanufacturers = Mage::getModel('aitmanufacturers/aitmanufacturers')->load($aitmanufacturersId);
                    $aitmanufacturers->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($aitmanufacturersIds)
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
        $this->_init();
        $aitmanufacturersIds = $this->getRequest()->getParam('aitmanufacturers');
        if(!is_array($aitmanufacturersIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Brands Page(s)'));
        } else {
            try {
                foreach ($aitmanufacturersIds as $aitmanufacturersId) {
                    $aitmanufacturers = Mage::getSingleton('aitmanufacturers/aitmanufacturers')
                        ->load($aitmanufacturersId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($aitmanufacturersIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    /*public function exportCsvAction()
    {
        $fileName   = 'aitmanufacturers.csv';
        $content    = $this->getLayout()->createBlock('aitmanufacturers/adminhtml_aitmanufacturers_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'aitmanufacturers.xml';
        $content    = $this->getLayout()->createBlock('aitmanufacturers/adminhtml_aitmanufacturers_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }*/

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
