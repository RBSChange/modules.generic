<?php
class generic_UpdateJSONAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$propertiesNames = explode(',', $request->getParameter('documentproperties', ''));
		$propertiesNames[] = 'documentversion';
		
		$propertiesValue = array();
		foreach ($propertiesNames as $propertyName)
		{
			if ($request->hasParameter($propertyName))
			{
				$propertiesValue[$propertyName] = $request->getParameter($propertyName);
			}			
		}

		$document = $this->getDocumentInstanceFromRequest($request);
		//recupere la correction en cours
		$document = DocumentHelper::getCorrection($document);
		
		$documentService = $document->getDocumentService();
		if ($documentService->correctionNeeded($document))
		{
			$document = $documentService->createDocumentCorrection($document);
		}
		
		uixul_DocumentEditorService::getInstance()->importFieldsData($document, $propertiesValue);	
		$documentService->save($document);
		
		$this->logAction($document);

		$propertiesNames[] = 'id';
		$propertiesNames[] = 'lang';
		$data = $this->exportFieldsData($document, $propertiesNames);
		return $this->sendJSON($data); 
	}

	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param String[]
	 * @return Array
	 */
	protected function exportFieldsData($document, $allowedProperties)
	{
		return uixul_DocumentEditorService::getInstance()->exportFieldsData($document, $allowedProperties);
	}
	
	/**
	 * @return Boolean
	 */
	protected function isDocumentAction()
	{
		return true;
	}
}