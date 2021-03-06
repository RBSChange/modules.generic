<?php
class generic_CancelAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$docIds = $this->getDocumentIdArrayFromRequest($request);
		$ds = $this->getDocumentService();
		foreach ($docIds as $docId)
		{
			$ds->cancel($docId);
		}

		return self::getSuccessView();
	}

	/**
	 * @return Boolean
	 */
	protected function isDocumentAction()
	{
		return true;
	}
}