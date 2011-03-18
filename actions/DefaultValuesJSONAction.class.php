<?php
class generic_DefaultValuesJSONAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$modelName = $request->getParameter('modelname');
		$model = f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName($modelName);
		$documentService = $model->getDocumentService();
		$document = $documentService->getNewDocumentInstance();	

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
	 * @param integer $documentId
	 * @return string
	 */
	protected function getSecureActionName($documentId)
	{
		$secureAction = parent::getSecureActionName($documentId);		
		$request =  $this->getContext()->getRequest();
		$modelName = $request->getParameter('modelname');
		$model = f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName($modelName);	
		$secureAction .= '.' . $model->getDocumentName();
		return $secureAction;
	}
	
	/**
	 * Returns an array of the documents IDs received by this action.
	 * All the IDs contained in the resulting array are REAL integer values, not strings.
	 * @param Request $request
	 * @return array<integer>
	 */
	protected function getDocumentIdArrayFromRequest($request)
	{	
		$docIds = array();
		$parentNodeId = $request->getParameter('parentref');
		if (empty($parentNodeId) || !is_numeric($parentNodeId) )
		{
			$parentNodeId = ModuleService::getInstance()->getRootFolderId($this->getModuleName($request));
		}
		$docIds[] = intval($parentNodeId);
		return $docIds;
	}
}