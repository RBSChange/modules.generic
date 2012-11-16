<?php
class generic_SavePermissionsJSONAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$moduleName = $this->getModuleName($request);
		$document = $this->getDocumentInstanceFromRequest($request);
		$id = $document->getId();
		$rs = change_PermissionService::getRoleServiceByModuleName($moduleName);
		
		$data = array('id' => $id);
		
		if (!is_null($rs))
		{
			$ps = change_PermissionService::getInstance();
			// Permissions are redefined on this node...
			$modifiedRoles = $this->clearNodePermissions($id, 'modules_' . $moduleName);
			$roles = $rs->getRoles();
			
			foreach ($roles as $roleName)
			{
				$elems = preg_split('/[_.]/', $roleName);
				$shortName = $elems[2];
				if ($request->hasParameter($shortName))
				{
					$accessorIds = explode(',', $request->getParameter($shortName));
					foreach ($accessorIds as $accessor)
					{
						if (intval($accessor) > 0)
						{
							$doc = DocumentHelper::getDocumentInstance(intval($accessor));
							if ($doc instanceof users_persistentdocument_user)
							{
								$modifiedRoles[] = $roleName;
								$ps->addRoleToUser($doc, $roleName, array($id));
							}
							else if ($doc instanceof users_persistentdocument_group)
							{
								$modifiedRoles[] = $roleName;
								$ps->addRoleToGroup($doc, $roleName, array($id));
							}
						}
					}
				}
			}
			$this->logAction($document);
			// The event is dispatched at the end of the loop
			$eventParam = array('nodeId' => $id, 'updatedRoles' => array_unique($modifiedRoles), 
				'module' => $moduleName);
			$ps->dispatchPermissionsUpdatedEvent($eventParam);
			$data['updatedRoles'] = $eventParam['updatedRoles'];
		}
		return $this->sendJSON($data);
	}
	
	/**
	 * @param integer $id
	 * @param string $packageName
	 * @return string[]
	 */
	protected function clearNodePermissions($id, $packageName)
	{
		return change_PermissionService::getInstance()->clearNodePermissions($id, $packageName);
	}
	
	/**
	 * @return boolean
	 */
	protected function isDocumentAction()
	{
		return true;
	}
}