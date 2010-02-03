<?php
class generic_GetRootfolderDocumentIdAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$result = array();
		
		try
		{
			$moduleName = $this->getModuleName($request);
		    $rootFolderId = ModuleService::getInstance()->getRootFolderId($moduleName);
		    		 
			$resultInfo = array('id' => $rootFolderId);
			$result[] = $resultInfo;
		}
		catch (Exception $e)
		{
			Framework::exception($e);
		}

		echo f_util_StringUtils::JSONEncode($result);
		return View::NONE;
	}
}