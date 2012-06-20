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

		$data = uixul_DocumentEditorService::getInstance()->getPublicationInfos($document);
		return $this->sendJSON($data);
	}
	
	/**
	 * Tell the permission system this action is a document action ie. the permission
	 * depends on the document the action acts on.
	 * @return boolean by default false
	 */
	protected function isDocumentAction()
	{
		return true;
	}
}