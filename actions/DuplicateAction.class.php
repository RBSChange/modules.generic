<?php
class generic_DuplicateAction extends f_action_BaseJSONAction
{
    /**
     * @param Context $context
	 * @param Request $request
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
	 * @return Boolean
	 */
	protected function suffixSecureActionByDocument()
	{
		return true;
	}
}