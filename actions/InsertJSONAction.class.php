<?php
class generic_InsertJSONAction extends f_action_BaseJSONAction
{

	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$propertiesNames = explode(',', $request->getParameter('documentproperties', ''));
		$propertiesValue = array();
		foreach ($propertiesNames as $propertyName)
		{
			if ($request->hasParameter($propertyName))
			{
				$propertiesValue[$propertyName] = $request->getParameter($propertyName);
			}			
		}		
		
		$modelName = $request->getParameter('modelname');
		$model = f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName($modelName);
		$documentService = $model->getDocumentService();
		$document = $documentService->getNewDocumentInstance();

		uixul_DocumentEditorService::getInstance()->importFieldsData($document, $propertiesValue);

		$parentNodeId = intval($request->getParameter(K::PARENT_ID_ACCESSOR));
		if ($parentNodeId <= 0) { $parentNodeId = null; }

		$documentService->save($document, $parentNodeId);
		$this->logAction($document);

		return $this->sendJSON(array('id' => $document->getId(), 'lang' => $document->getLang(), 'label' => $document->getLabel()));
	}
	
	protected function getSecureActionName($documentId)
	{
		$secureAction = parent::getSecureActionName($documentId);		
		$request =  $this->getContext()->getRequest();
		$modelName = $request->getParameter('modelname');
		$model = f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName($modelName);	
		$secureAction .= '.' . $model->getDocumentName();

		if (Framework::isDebugEnabled())
		{
			Framework::debug(__METHOD__ . "($modelName) -> $secureAction");
		}
		
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
		$parentNodeId = intval($request->getParameter(K::PARENT_ID_ACCESSOR));
		if ($parentNodeId <= 0)
		{
			$parentNodeId = intval(ModuleService::getInstance()->getRootFolderId($this->getModuleName($request)));
		}
		$docIds[] = $parentNodeId;
		return $docIds;
	}
}