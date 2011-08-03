<?php
/**
 * generic_GetPathToIdAction
 * @package modules.generic.actions
 */
class generic_GetPathToIdAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$document = $this->getDocumentInstanceFromRequest($request);
		$moduleName = $this->getModuleName($request);
		
		$ms = ModuleBaseService::getInstanceByModuleName($moduleName);
		if ($ms === null)
		{
			$ms = ModuleBaseService::getInstance();
		}
		$ids = $ms->getPathTo($document, $moduleName);
		if ($request->getParameter("withRootFolder") == "true")
		{
			array_unshift($ids, ModuleService::getInstance()->getRootFolderId($moduleName));
		}
		return $this->sendJSON($ids);
	}
}