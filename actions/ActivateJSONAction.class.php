<?php
class generic_ActivateJSONAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$document = $this->getDocumentInstanceFromRequest($request);
		$document = DocumentHelper::getCorrection($document);
		$document->getDocumentService()->activate($document->getId());
		
		$this->logAction($document);
		return $this->sendJSON(array('id' => $document->getId(), 'lang'=> $document->getLang(), 'documentversion'=> $document->getDocumentversion()));
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