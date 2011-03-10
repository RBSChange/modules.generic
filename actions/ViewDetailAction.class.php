<?php
class generic_ViewDetailAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$document = $this->getDocumentInstanceFromRequest($request);
		$page = null;

		// Retrieve the page to display.
		if ($document !== null)
		{
			$page = $document->getDocumentService()->getDisplayPage($document);
		}

		if ($page !== null)
		{
			$model = $document->getPersistentModel();
			if ($model->isInjectedModel() && $this->getModuleName($request) != $model->getOriginalModuleName())
			{
				$request->setParameter($model->getOriginalModuleName().'Param', array('cmpref' => $document->getId()));
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
		$moduleName = $this->getModuleName($request);
		$modulesParams = $request->getParameter($moduleName.'Param');
		$ids = $modulesParams['cmpref'];
		if (!is_array($ids))
		{
			$ids = explode(',', $ids);
		}
		return $ids;
	}

	/**
	 * @return Boolean
	 */
	public function isSecure()
	{
		return false;
	}

	/**
	 * @return Boolean
	 */
	protected function isDocumentAction()
	{
		return true;
	}
}