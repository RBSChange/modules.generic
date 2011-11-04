<?php
class generic_UpdateJSONAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
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
		
		try 
		{
			$documentService->save($document);
		}
		catch (ValidationException $e)
		{
			$model = $document->getPersistentModel();
			$ls = LocaleService::getInstance();
			$k = 'm.' . $model->getModuleName() . '.document.' . $model->getDocumentName() . '.';
			$messages = array();
			foreach ($e->getErrors() as $name => $errors) 
			{
				$fieldLabel = $ls->trans($k . strtolower($name), array('ucf'));
				foreach ($errors as $error) 
				{
					$messages[] = change_Constraints::addFieldLabel($fieldLabel, $error);
				}
			}
			return $this->sendJSONError(implode(' ', $messages) , false);
		}
		
		
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