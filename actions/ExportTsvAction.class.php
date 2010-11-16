<?php
class generic_ExportTsvAction extends generic_ExportAction
{
    /**
     * Generic CSV export feature.
      * @param Context $context
	 * @param Request $request
	 * @return unknown
	 */
	public function _execute($context, $request)
    {
    	$this->separator = "\t";
    	$this->fileName = $request->getParameter(AG_MODULE_ACCESSOR) . "_" . date('Y-m-d_H\Hi') . ".txt" ;    
    	
    	return parent::_execute($context, $request);
	}
	
	/**
	 * @return Boolean
	 */
	protected function isDocumentAction()
	{
		return true;
	}
}