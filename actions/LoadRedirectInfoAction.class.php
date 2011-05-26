<?php
class generic_LoadRedirectInfoAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
    {
		$document = $this->getDocumentInstanceFromRequest($request);
		$result = website_UrlRewritingService::getInstance()->getBoDocumentRewriteInfo($document);
		return $this->sendJSON($result);
    }
}
