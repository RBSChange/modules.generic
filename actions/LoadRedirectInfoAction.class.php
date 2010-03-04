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
		$result = array('isLocalized' => $document->isLocalized());
		$result['langs'] = array_values($document->getI18nInfo()->getLangs());
		
		$rc = RequestContext::getInstance();
		foreach ($result['langs'] as $lang) 
		{
			try 
			{
				$rc->beginI18nWork($lang);
				
				$urs = website_UrlRewritingService::getInstance();
				$urs->beginOnlyUseRulesTemplates();
				try 
				{
					$generated = LinkHelper::getDocumentUrl($document, $lang);
				}
				catch (Exception $e)
				{
					Framework::warn(__METHOD__ . ' '. $e->getMessage());
					$generated = '';
				}
				$urs->endOnlyUseRulesTemplates();
				
				$matches = array();
				preg_match('/^https?:\/\/([^\/]*)(\/'.$lang.')?(\/.*)$/', $generated, $matches);
				$currentURL = $generated = $matches[3];

				$rewritings =  $this->getPersistentProvider()->getUrlRewritingInfo($document->getId(), $lang);
				$redirect = array();
				foreach ($rewritings as $row) 
				{
					if (!isset($row['to_url'])) 
					{
						$currentURL = $row['from_url'];
					}
					else
					{
						$redirect[] = $row;
					}
				}
				
				$langInfo = array('current' => $currentURL, 'generated' => $generated, 'redirect' => $redirect);
				
				$result[$lang] = $langInfo;
				$rc->endI18nWork();
			}
			catch (Exception $e)
			{
				$rc->endI18nWork($e);
			}
		}
		
		return $this->sendJSON($result);
    }
}
