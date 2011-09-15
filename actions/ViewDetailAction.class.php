<?php
class generic_ViewDetailAction extends change_Action
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$document = $this->getDocumentInstanceFromRequest($request);
		$page = null;

		// Retrieve the page to display.
		if ($document !== null)
		{
			list($module, $action) = $document->getDocumentService()->getResolveDetail($document, $request);
		}
		else
		{
			$module = 'website';
			$action = 'Error404';
		}

		// Finally, forward the execution to $module / $action.
		$context->getController()->forward($module, $action);
		return change_View::NONE;
	}

	/**
	 * @param change_Request $request
	 */
	protected function getDocumentIdArrayFromRequest($request)
	{
		$moduleName = $this->getModuleName();
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