<?php
/**
 * generic_PurgeDocumentAction
 * @package modules.generic.actions
 */
class generic_PurgeDocumentAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$result = array();
		$document = $this->getDocumentInstanceFromRequest($request);
		$result['cmpref'] = $document->getId();
		$document->getDocumentService()->purgeDocument($document);
		$this->logAction($document);
		return $this->sendJSON($result);
	}
	
	/**
	 * @return boolean
	 */
	protected function isDocumentAction()
	{
		return true;
	}
}