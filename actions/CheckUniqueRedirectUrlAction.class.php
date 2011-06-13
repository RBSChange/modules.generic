<?php
class generic_CheckUniqueRedirectUrlAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
    {
		$document = $this->getDocumentInstanceFromRequest($request);
		$websiteId = $document->getDocumentService()->getWebsiteId($document);
		if ($websiteId === null) {$websiteId = 0;}
		$checkUrl = $request->getParameter('from_url');
		$lang = $request->getParameter('lang');
		//rule_id, document_id, document_lang, website_id, to_url, redirect_type
		$urlInfo = $this->getPersistentProvider()->getPageForUrl($checkUrl, $websiteId, $lang);
		if ($urlInfo !== null)
		{
			return $this->sendJSON($urlInfo);
		}
		return $this->sendJSON(array('websiteId' => $websiteId, 'checkUrl' => $checkUrl));
    }
}
