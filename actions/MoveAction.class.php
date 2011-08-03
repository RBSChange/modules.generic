<?php
class generic_MoveAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$parentNode =  DocumentHelper::getDocumentInstance($request->getParameter('destref'));
		$docArray   = $this->getDocumentInstanceArrayFromRequest($request);
		$result = array();
		foreach ($docArray as $doc)
		{
			$doc->getDocumentService()->moveTo($doc, $parentNode->getId(), $request->getParameter('beforeid'), $request->getParameter('afterid'));
			$this->logAction($doc, array('destinationlabel' => $parentNode->getLabel()));
			$result[] = $doc->getId();
		}
		return $this->sendJSON(array('movedids' => $result));
	}
	
	/**
	 * @return Boolean
	 */
	protected function isDocumentAction()
	{
		return true;
	}
	
	/**
	 * Check permissions on the destination node, not on the moved one.
	 * @return Array<Integer>
	 */
	protected function getSecureNodeIds()
	{
		$request = $this->getContext()->getRequest();
		$destNodeId = $request->getParameter('destref');
		return array($destNodeId);
	}
}