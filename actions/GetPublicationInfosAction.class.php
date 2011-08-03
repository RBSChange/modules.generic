<?php
class generic_GetPublicationInfosAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{

		$document = $this->getDocumentInstanceFromRequest($request);
		
		//Retrouve le document original	
		$document = DocumentHelper::getByCorrection($document);
				
		$data = uixul_DocumentEditorService::getInstance()->getPublicationInfos($document);
		return $this->sendJSON($data);
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