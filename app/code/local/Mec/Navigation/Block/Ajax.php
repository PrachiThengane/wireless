<?php

	class Mec_Navigation_Block_Ajax extends Mage_Core_Block_Abstract{
		
		
		protected $eval_js = array();
		
		public function addEvalJs($str, $param = 'eval_js'){
			
			$this->setData($param, $this->getData($param).";".$str);
			
		}
		
	}
