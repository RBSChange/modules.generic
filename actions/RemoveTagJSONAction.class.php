<?php
/**
 * generic_RemoveTagJSONAction
 */
class generic_RemoveTagJSONAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$result = array();
		
		$doc = $this->getDocumentInstanceFromRequest($request);
		$tag  = $request->getParameter('tag');
		TagService::getInstance()->removeTag($doc, $tag);
		$this->logAction($doc, array('tag' => $tag));
	
		return $this->sendJSON($result);
	}

	/**
	 * @return Boolean
	 */
	protected function isDocumentAction()
	{
		return true;
	}
}