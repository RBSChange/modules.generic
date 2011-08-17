<?php
class generic_RemoveAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$parentdoc = DocumentHelper::getDocumentInstance($request->getParameter('parentref'));
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
	protected function isDocumentAction()
	{
		return true;
	}
}