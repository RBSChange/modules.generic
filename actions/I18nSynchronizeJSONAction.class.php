<?php
/**
 * generic_I18nSynchronizeJSONAction
 * @package modules.generic.actions
 */
class generic_I18nSynchronizeJSONAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$documentId = intval($this->getDocumentIdFromRequest($request));
		LocaleService::getInstance()->synchronizeDocumentId($documentId);
		$d = DocumentHelper::getDocumentInstance($documentId);
		$result = LocaleService::getInstance()->getI18nSynchroForDocument($d);
		return $this->sendJSON($result);
	}
}