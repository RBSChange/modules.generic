<?php
class generic_DeleteJSONAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$document = DocumentHelper::getByCorrection($this->getDocumentInstanceFromRequest($request));		
		$info = array();
		$info['documentlabel'] = $document->getLabel();	
		$documentToDelete = DocumentHelper::getCorrection($document);
		if ($documentToDelete !== $document)
		{
			$info['correctionofid'] = $document->getId();
		}
		$documentToDelete->delete($document);	
		$this->logCustomDelete($document, $info);
		if ($document->isDeleted())
		{
			return $this->sendJSON(array('id' => 0));	
		}
		return $this->sendJSON(array('id' => $document->getId(), 'lang'=> $document->getLang(), 'documentversion'=> $document->getDocumentversion()));	
	}

	
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 */
	protected function logCustomDelete($document, $info)
	{		
		$moduleName = $this->getModuleName();
		if (isset($info['correctionofid']))
		{
				$actionName = 'deletecorrection';
		}		
		else if ($document->isDeleted())
		{
			$actionName = 'delete';
		}
		else
		{
			$actionName = 'deletelocalization';
		}
		$actionName .= '.' . strtolower($document->getPersistentModel()->getDocumentName());
		UserActionLoggerService::getInstance()->addCurrentUserDocumentEntry($actionName, $document, $info, $moduleName);
	}
	
	/**
	 * Tell the permission system this action is a document action ie. the permission
	 * depends on the document the action acts on.
	 * @return Boolean by default false
	 */
	protected function isDocumentAction()
	{
		return true;
	}
}