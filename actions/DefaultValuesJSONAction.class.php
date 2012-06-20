<?php
class generic_DefaultValuesJSONAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
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
		$parentId = $request->getParameter('parentref');
		$parentId = (empty($parentId) || !is_numeric($parentId) ) ? null : intval($parentId);
		$data = $this->exportFieldsData($document, $allowedProperties, $parentId);	
		return $this->sendJSON($data);
	}
	
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param string[] $allowedProperties
	 * @param integer $parentId
	 * @return Array
	 */
	protected function exportFieldsData($document, $allowedProperties, $parentId = null)
	{
		return uixul_DocumentEditorService::getInstance()->exportFieldsData($document, $allowedProperties, $parentId);
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
	 * @param change_Request $request
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