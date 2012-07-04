<?php
class generic_LoadJSONAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$document = $this->getDocumentInstanceFromRequest($request);
		//Retrouve la correction courante
		$document = DocumentHelper::getCorrection($document);
		
		$allowedProperties = array('id', 'lang', 'documentversion');
		$requestedProperties = explode(',', $request->getParameter('documentproperties', ''));
		foreach ($requestedProperties as $propertyName)
		{
			if (f_util_StringUtils::isEmpty($propertyName)) {continue;}
			if (in_array($propertyName, $allowedProperties)) {continue;}
			
			$allowedProperties[] = $propertyName;
		}
		
		$data = $this->exportFieldsData($document, $allowedProperties);
		
		return $this->sendJSON($data);
	}

	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param string[]
	 * @return Array
	 */
	protected function exportFieldsData($document, $allowedProperties)
	{
		return uixul_DocumentEditorService::getInstance()->exportFieldsData($document, $allowedProperties);
	}
	
	/**
	 * @return boolean
	 */
	protected function isDocumentAction()
	{
		return true;
	}
}