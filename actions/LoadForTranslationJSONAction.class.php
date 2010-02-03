<?php
class generic_LoadForTranslationJSONAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		
		$allowedProperties = array('id', 'documentversion');
		
		$requestedProperties = explode(',', $request->getParameter('documentproperties', ''));
		foreach ($requestedProperties as $propertyName)
		{
			if (!in_array($propertyName, $allowedProperties))
			{
				$allowedProperties[] = $propertyName;
			}
		}
		
		$document = $this->getDocumentInstanceFromRequest($request);
		//Retrouve le document original
		$document = DocumentHelper::getByCorrection($document);
		$vo = $document->getLang();
		
		$rc = RequestContext::getInstance();		
		$supportedLanguages = $rc->getSupportedLanguages();
		
		$result = array('langs' => array(), 'langslabel' => array());
	
		foreach ($supportedLanguages as $lang) 
		{
			$result['langslabel'][$lang] = f_Locale::translateUI('&modules.uixul.bo.languages.'.ucfirst($lang).';');
			if ($document->isLangAvailable($lang))
			{
				if ($lang === $vo)
				{
					$result['langs'][$lang] = 'vo';
				} 
				else
				{
					$result['langs'][$lang] = 'update';
				}
			}
			else
			{
				$result['langs'][$lang] = 'new';
			}
		}
		
		if ($request->hasParameter('fromlang'))
		{
			$fromLang =  $request->getParameter('fromlang');
			if ($fromLang == '' || !$document->isLangAvailable($fromLang))
			{
				$fromLang = $vo;
			}
			$result['fromlang'] = $fromLang;
			try 
			{
				$rc->beginI18nWork($fromLang);
				$result['from'] = uixul_DocumentEditorService::getInstance()->exportReadOnlyFieldsData($document, $allowedProperties);
				$rc->endI18nWork();
			}
			catch (Exception $e)
			{
				$rc->endI18nWork($e);
			}
		}
		
		if ($request->hasParameter('tolang'))
		{
			$toLang = $request->getParameter('tolang');
			if ($toLang == '' || $toLang == $vo)
			{
				foreach ($result['langs'] as $toLang => $state) 
				{
					if ($state !== 'vo') {break;}
				}
			}
			$result['tolang'] = $toLang;
			try 
			{
				$rc->beginI18nWork($toLang);
				if ($document->getPersistentModel()->useCorrection() && $document->getCorrectionid())
				{
					$document = DocumentHelper::getDocumentInstance($document->getCorrectionid());
				}
				$result['to'] = uixul_DocumentEditorService::getInstance()->exportFieldsData($document, $allowedProperties);
				$rc->endI18nWork();
			}
			catch (Exception $e)
			{
				$rc->endI18nWork($e);
			}	
		}
		
		return $this->sendJSON($result);
	}

	/**
	 * @return Boolean
	 */
	protected function suffixSecureActionByDocument()
	{
		return true;
	}
}