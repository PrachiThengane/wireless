<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard
{
    const MODULE     = 'Aitoc_Aitmanufacturers';
    const CONTROLLER = 'index';
     
    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();

        $router = new Aitoc_Aitmanufacturers_Controller_Router();
        $front->addRouter('aitmanufacturers', $router);
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        
        $d = explode('/', $identifier, 3);
        if ($d[0] != Mage::helper('aitmanufacturers')->getUrlPrefix())
            return false;
        
        $action = 'index';

        $request->setModuleName(self::MODULE)
            ->setControllerName(self::CONTROLLER)
            ->setActionName($action);
            
		$request->setAlias(
			Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
			$identifier
		);
		
		$front = $this->getFront();
		$controllerClassName = $this->_validateControllerClassName(self::MODULE, self::CONTROLLER);
		$controllerInstance = new $controllerClassName($request, $front->getResponse());
		$request->setDispatched(true);
        $controllerInstance->dispatch($action);
        
        return true;
    }
    
    /**
     * Generating and validating class file name,
     * class and if evrything ok do include if needed and return of class name
     *
     * @return mixed
     */
    protected function _validateControllerClassName($realModule, $controller)
    {
        $controllerFileName = $this->getControllerFileName($realModule, $controller);
        if (!$this->validateControllerFileName($controllerFileName)) {
            return false;
        }

        $controllerClassName = $this->getControllerClassName($realModule, $controller);
        if (!$controllerClassName) {
            return false;
        }

//         include controller file if needed
        if (!$this->_inludeControllerClass($controllerFileName, $controllerClassName)) {
            return false;
        }

        return $controllerClassName;
    }
    
    /**
     * Including controller class if checking of existense class before include
     *
     * @param string $controllerFileName
     * @param string $controllerClassName
     * @return bool
     */
    protected function _inludeControllerClass($controllerFileName, $controllerClassName)
    {
        if (!class_exists($controllerClassName, false)) {
            if (!file_exists($controllerFileName)) {
                return false;
            }
            include $controllerFileName;

            if (!class_exists($controllerClassName, false)) {
                throw Mage::exception('Mage_Core', Mage::helper('core')->__('Controller file was loaded but class does not exist'));
            }
        }
        return true;
    }
}
