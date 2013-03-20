<?php
class generic_ViewDetailAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param ChangeRequest $request
	 */
	public function _execute($context, $request)
	{
		$document = DocumentHelper::getDocumentInstanceIfExists($this->getDocumentIdFromRequest($request));
		$page = null;
		
		// Retrieve the page to display.
		if ($document !== null)
		{
			$request->setParameter('detail_cmpref', $document->getId());
			$page = $document->getDocumentService()->getDisplayPage($document);
		}
		
		if ($page !== null)
		{
			foreach ($document->getPersistentModel()->getAncestorModelNames() as $modelName)
			{
				$parts = f_persistentdocument_PersistentDocumentModel::getModelInfo($modelName);
				$moduleName = $parts['module'];
				if (!$request->hasModuleParameter($moduleName, 'cmpref'))
				{
					$request->setModuleParameter($moduleName, 'cmpref', $document->getId());
				}
			}
			
			// Set pageref parameter into the request.
			$request->setParameter(K::PAGE_REF_ACCESSOR, $page->getId());
			$module = 'website';
			$action = 'Display';
		}
		else
		{
			$module = AG_ERROR_404_MODULE;
			$action = AG_ERROR_404_ACTION;
		}
		
		// Finally, forward the execution to $module / $action.
		$context->getController()->forward($module, $action);
		return View::NONE;
	}
	
	/**
	 * @param Request $request
	 */
	protected function getDocumentIdArrayFromRequest($request)
	{
		$tids = array();
		$value = $request->getModuleParameter($this->getModuleName(), 'cmpref');
		if (is_int($value))
		{
			$tids[] = $value;
		}
		elseif (is_array($value))
		{
			$tids = $value;
		}
		elseif (is_string($value))
		{
			$tids = explode(',', $value);
		}
		
		$ids = array();
		foreach ($tids as $id)
		{
			if (is_int($id))
			{
				$ids[] = $id;
			}
		}
		return $ids;
	}
	
	/**
	 * @return boolean
	 */
	public function isSecure()
	{
		return false;
	}
	
	/**
	 * @return boolean
	 */
	protected function isDocumentAction()
	{
		return true;
	}
}