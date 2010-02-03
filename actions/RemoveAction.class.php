<?php
class generic_RemoveAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$parentdoc = DocumentHelper::getDocumentInstance($request->getParameter(K::PARENT_ID_ACCESSOR));
		$docIds   = $this->getDocumentIdArrayFromRequest($request);
		$result = array();
		foreach ($docIds as $docId)
		{
			$parentdoc->getDocumentService()->removeDocumentId($parentdoc, $docId);
			$this->logAction(DocumentHelper::getDocumentInstance($docId), array('from' => $parentdoc->getId()));
			$result[] = $docId;
		}
		return $this->sendJSON(array('removedids' => $result));
	}

	/**
	 * @return Boolean
	 */
	protected function suffixSecureActionByDocument()
	{
		return true;
	}
}