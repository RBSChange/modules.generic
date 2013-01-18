<?php
class generic_InfoAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	protected function _execute($context, $request)
	{
		$result = array();
		$rc = RequestContext::getInstance();
		foreach ($this->getDocumentIdArrayFromRequest($request) as $documentId)
		{
			try
			{
				$document = DocumentHelper::getDocumentInstance($documentId);
				$vo = $document->getLang();
				try
				{
					$rc->beginI18nWork($vo);
					$resultInfo = array('id' => $document->getId(), 'model' => $document->getDocumentModelName(), 'lang' => $vo);
					
					DocumentHelper::completeBOAttributes($document, $resultInfo, DocumentHelper::MODE_ITEM);
					if (!isset($resultInfo['icon']))
					{
						$resultInfo['icon'] = $document->getPersistentModel()->getIcon();
					}
					
					$resultInfo['labels'] = array();
					$resultInfo['labels'][$vo] = $resultInfo['label'];
					unset($resultInfo['label']);
					if ($document->isLocalized())
					{
						foreach ($document->getI18nInfo()->getLangs() as $lang)
						{
							if (!isset($resultInfo['labels'][$lang]))
							{
								$rc->beginI18nWork($lang);
								$resultInfo['labels'][$lang] = $document->getTreeNodeLabel();
								$rc->endI18nWork();
							}
						}
					}
					$result[] = $resultInfo;
					$rc->endI18nWork();
				}
				catch (Exception $e)
				{
					$rc->endI18nWork($e);
					throw $e;
				}
			}
			catch (Exception $e)
			{
				Framework::exception($e);
			}
		}
		
		return $this->sendJSON($result);
	}
	
	/**
	 * Returns an array of the documents IDs received by this action.
	 * All the IDs contained in the resulting array are REAL integer values, not strings.
	 *
	 * @param change_Request $request
	 * @return array<integer>
	 */
	protected function getDocumentIdArrayFromRequest($request)
	{
		$docIds = $request->getParameter(change_Request::DOCUMENT_ID, array());
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
			else
			{
				$docIds = array();
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
				else if (!is_int($docId))
				{
					unset($docIds[$index]);
				}
			}
		}
		else
		{
			$docIds = array();
		}
		return $docIds;
	}
}