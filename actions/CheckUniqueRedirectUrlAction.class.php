<?php
class generic_CheckUniqueRedirectUrlAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
    {
		$website = $this->getDocumentInstanceFromRequest($request);
		$websiteId = $website->getId();
		$checkUrl = $request->getParameter('from_url');
		$lang = RequestContext::getInstance()->getLang();
		
		//rule_id, origine, modulename, actionname, document_id, website_lang, website_id, to_url, redirect_type
		$urlInfo = $this->getPersistentProvider()->getUrlRewritingInfoByUrl($checkUrl, $websiteId, $lang);
		if ($urlInfo !== null)
		{
			return $this->sendJSON($urlInfo);
		}
		return $this->sendJSON(array('website_id' => $websiteId, 'checkUrl' => $checkUrl));
    }
}
