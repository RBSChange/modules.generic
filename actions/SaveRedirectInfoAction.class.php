<?php
class generic_SaveRedirectInfoAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
    {
        $data = JsonService::getInstance()->decode($request->getParameter('data')); 
        $document = $this->getDocumentInstanceFromRequest($request);
		website_UrlRewritingService::getInstance()->setBoDocumentRewriteInfo($document, $data);
		$this->logAction($document);  
		$result = website_UrlRewritingService::getInstance()->getBoDocumentRewriteInfo($document);
		return $this->sendJSON($result);
    }
}