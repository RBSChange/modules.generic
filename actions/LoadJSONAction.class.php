<?php
class generic_LoadJSONAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
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
			if (!in_array($propertyName, $allowedProperties))
			{
				$allowedProperties[] = $propertyName;
			}
		}
		
		$data = $this->exportFieldsData($document, $allowedProperties);
		
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