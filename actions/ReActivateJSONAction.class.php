<?php
class generic_ReActivateJSONAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$document = $this->getDocumentInstanceFromRequest($request);
		if ($document->getPublicationstatus() === 'DEACTIVATED')
		{
			$document->getDocumentService()->activate($document->getId());
			$this->logAction($document);
		}
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