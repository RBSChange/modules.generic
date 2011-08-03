<?php
class generic_CreateWorkflowInstanceAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$result = array();
		
		$document = $this->getDocumentInstanceFromRequest($request);
		$document = DocumentHelper::getCorrection($document);
		$startParameters = array();
		if ($request->hasParameter('comment'))
		{
			$startParameters['START_COMMENT'] = $request->getParameter('comment');
		}
		
		$document->getDocumentService()->createWorkflowInstance($document->getId(), $startParameters);
		$this->logAction($document, $startParameters);
		
		return $this->sendJSON($result);
	}
	
	/**
	 * @see f_action_BaseAction::isDocumentAction()
	 *
	 * @return Boolean
	 */
	protected function isDocumentAction()
	{
		return true;
	}
}