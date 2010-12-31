<?php
class generic_ExportCsvAction extends generic_ExportAction
{
    /**
     * Generic CSV export feature.
     * @param Context $context
	 * @param Request $request
	 * @return unknown
	 */
	public function _execute($context, $request)
    {	
    	$this->separator = ";";
    	$this->fileName = $request->getParameter('module') . "_" . date('Y-m-d_H\Hi') . ".csv" ;    
    	
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