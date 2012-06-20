<?php
class generic_ViewListAction extends change_Action
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$document = $this->getDocumentInstanceFromRequest($request);
		$page = null;
			// retrieve the page to display
		if (!is_null($document))
		{
			$page = TagService::getInstance()->getListPageForDocument($document);
		}

		if (!is_null($page) )
		{
			$request->setParameter('pageref', $page->getId());
			$module = 'website';
			$action = 'Display';
		}
		else
		{
			$module = 'website';
			$action = 'Error404';
		}

		// finally, forward the execution to $module / $action
		$context->getController()->forward($module, $action);

		// no view here since the content is rendered by the page module
		return change_View::NONE;
	}

	/**
	 * @param change_Request $request
	 */
	protected function getDocumentIdArrayFromRequest($request)
	{
		$moduleName   = $this->getModuleName($request);
		$modulesParams = $request->getParameter($moduleName.'Param');
		$ids = $modulesParams[change_Request::DOCUMENT_ID];
		if (!is_array($ids))
		{
			$ids = explode(',', $ids);
		}
		return $ids;
	}


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