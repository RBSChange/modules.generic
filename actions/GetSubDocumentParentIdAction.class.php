<?php
class generic_GetSubDocumentParentIdAction extends f_action_BaseJSONAction
{
	/**
	 * @see f_action_BaseAction::_execute()
	 * @param Context $context
	 * @param Request $request
	 */
	protected function _execute($context, $request)
	{
		$resultInfo = array();
		try 
		{
			
			$newModelName = $request->getParameter('newmodelname');
			$parentId = intval($request->getParameter('cmpref'));
			$model = f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName($newModelName);
			$moduleName = $this->getModuleName($request);
			if ($moduleName != $model->getModuleName())
			{
				$parentId = 0;
			}
			if ($parentId <= 0)
			{
				$parentId = ModuleService::getInstance()->getRootFolderId($model->getModuleName());
			}
			 
			$resultInfo = array('id' => $parentId);			
		}
		catch (Exception $e)
		{
			Framework::exception($e);
		}
		return $this->sendJSON($resultInfo);
	}
}