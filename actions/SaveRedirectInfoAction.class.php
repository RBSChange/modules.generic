<?php
class generic_SaveRedirectInfoAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
    {
        $document = $this->getDocumentInstanceFromRequest($request);
        $ds = $document->getDocumentService();
        $documentId = $document->getId();
        $websiteId = $ds->getWebsiteId($document);
        if ($websiteId === null) {$websiteId = 0;}
        
        $data = JsonService::getInstance()->decode($request->getParameter('data'));       
        $tm = $this->getTransactionManager();
        $pp = $tm->getPersistentProvider();
        try 
        {
        	$tm->beginTransaction();
        	foreach ($data['langs'] as $lang) 
        	{
        		$rewritingInfos = $data[$lang];
        		$currentUrl = $rewritingInfos['current'];
        		
        		$pp->removeUrlRewriting($documentId, $lang);
        		
        		if ($currentUrl != $rewritingInfos['generated'])
        		{
        			$pp->setUrlRewriting($documentId, $lang, $websiteId, $rewritingInfos['current'], null, 200);
        			$ds->setUrlRewriting($document, $lang, $rewritingInfos['current']);
        		}
        		else
        		{
        			$ds->setUrlRewriting($document, $lang, null);
        		}
        		
        		foreach ($rewritingInfos['redirect'] as $redirectInfos) 
        		{
        			$pp->setUrlRewriting($documentId, $lang, $websiteId, $redirectInfos['from_url'], $currentUrl, $redirectInfos['redirect_type']);
        		}
        	}
        	$tm->commit();
        }
        catch (Exception $e)
        {
        	$tm->rollBack($e);
        	throw $e;	
        }
        $this->logAction($document);  
		$context->getController()->forward($this->getModuleName(), 'LoadRedirectInfo');
		return View::NONE;
    }
}
