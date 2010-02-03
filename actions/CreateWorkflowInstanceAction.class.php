<?php
class generic_CreateWorkflowInstanceAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$document = $this->getDocumentInstanceFromRequest($request);
		$document = DocumentHelper::getCorrection($document);
		$startParameters = array();
		if ($request->hasParameter('comment'))
		{
			$startParameters['START_COMMENT'] = $request->getParameter('comment');
		}
		
		$document->getDocumentService()->createWorkflowInstance($document->getId(), $startParameters);
		$this->logAction($document, $startParameters);
		return self::getSuccessView();
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