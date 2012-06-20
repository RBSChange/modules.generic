<?php
class generic_DuplicateAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{	
		$documents = $this->getDocumentInstanceArrayFromRequest($request);
		$parentref = $request->getParameter('parentref');
		$docIds = array();
		foreach ($documents as $document)
		{
			$docIds[] = $document->getId();
			$document->getDocumentService()->duplicate($document->getId(), $parentref);
			$this->logAction($document);
		}
		return $this->sendJSON(array('cmpref' => $docIds));
	}
	
	/**
	 * @return boolean
	 */
	protected function isDocumentAction()
	{
		return true;
	}
}