<?php
class generic_LoadRedirectInfoAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$document = $this->getDocumentInstanceFromRequest($request);
		$result = website_UrlRewritingService::getInstance()->getBoDocumentRewriteInfo($document);
		return $this->sendJSON($result);
	}
}
