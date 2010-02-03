<?php
class generic_InfoAction extends f_action_BaseJSONAction
{
	
	/**
	 * @see f_action_BaseAction::_execute()
	 *
	 * @param Context $context
	 * @param Request $request
	 */
	protected function _execute($context, $request)
	{
		$result = array();
		try
		{
			$rc = RequestContext::getInstance();
			$documents = $this->getDocumentInstanceArrayFromRequest($request);
			foreach ($documents as $document)
			{
				
				$resultInfo = array('id' => $document->getId(), 
					'model' => $document->getDocumentModelName(), 
					'lang' => $document->getLang(),
					'icon' =>$document->getPersistentModel()->getIcon());
				
				if ($document->isLocalized())
				{
					$resultInfo['labels'] = array();
					foreach ($rc->getSupportedLanguages() as $lang)
					{
						if ($document->isLangAvailable($lang))
						{
							$rc->beginI18nWork($lang);
							$resultInfo['labels'][$lang] = $document->getTreeNodeLabel();
							$rc->endI18nWork();
						}
					}
				}
				else
				{
					$resultInfo['labels'] = array($document->getLang() => $document->getTreeNodeLabel());
				}
				
				$result[] = $resultInfo;
			}
		}
		catch (Exception $e)
		{
			Framework::exception($e);
		}
		
		return $this->sendJSON($result);
	}
	
	/**
	 * Returns an array of the documents IDs received by this action.
	 *
	 * All the IDs contained in the resulting array are REAL integer values, not
	 * strings.
	 *
	 * @param Request $request
	 * @return array<integer>
	 */
	protected function getDocumentIdArrayFromRequest($request)
	{
		$docIds = $request->getParameter(K::COMPONENT_ID_ACCESSOR, array());
		if (is_string($docIds))
		{
			if (strpos($docIds, ',') !== false)
			{
				$docIds = explode(',', $docIds);
			}
			else if (intval($docIds) == $docIds)
			{
				$docIds = array(intval($docIds));
			}
		}
		elseif (is_int($docIds))
		{
			$docIds = array($docIds);
		}
		else if (is_array($docIds))
		{
			foreach ($docIds as $index => $docId)
			{
				if (strval(intval($docId)) === $docId)
				{
					$docIds[$index] = intval($docId);
				}
				else if (! is_int($docId))
				{
					unset($docIds[$index]);
				}
			}
		}
		return $docIds;
	}
}