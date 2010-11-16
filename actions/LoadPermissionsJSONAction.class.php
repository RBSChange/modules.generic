<?php
class generic_LoadPermissionsJSONAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$document = $this->getDocumentInstanceFromRequest($request);
		//Retrouve le document original
		$document = DocumentHelper::getByCorrection($document);

		
		$requestedProperties = explode(',', $request->getParameter('documentproperties', ''));
		$comps = $this->generateMappedRoleArray($document->getId());
		$data = array("DefNode" => $comps["DefNode"]);
		if (isset($comps['messageinfo'])) {$data['messageinfo'] = $comps['messageinfo'];}
		foreach ($requestedProperties as $role) 
		{
			if (isset($comps[$role]) && count($comps[$role]) > 0)
			{
				$data[$role] = implode(',', $comps[$role]);
			}
		}		
		return $this->sendJSON($data);
	}

	private function generateMappedRoleArray($documentId)
	{
		$moduleName = $this->getModuleName();
		$result = array();
		$ps = f_permission_PermissionService::getInstance();
		$defId = $ps->getDefinitionPointForPackage($documentId, 'modules_'.$moduleName);
		if ($defId === null)
		{
			$result["messageinfo"] = f_Locale::translateUI('&modules.generic.bo.general.Permissions-herited-rootfolder;'); 
			$defId = ModuleService::getInstance()->getRootFolderId($moduleName);
		}
		else if ($defId != $documentId)
		{
			$defDoc = DocumentHelper::getDocumentInstance($defId);
			$result["messageinfo"] = f_Locale::translateUI('&modules.generic.bo.general.Permissions-herited-from;', array('label' => $defDoc->getTreeNodeLabel())); 
		}

		$result["DefNode"] = $defId;
		
		$ACLs = $ps->getACLForNode($defId);

		foreach ($ACLs as $acl)
		{
			$roleName = $acl->getRole();
			$elems = preg_split('/[_.]/', $roleName);
			if ($elems[1] == $moduleName)
			{
				$roleName = $elems[2];
				if (array_key_exists($roleName, $result))
				{
					array_push($result[$roleName], $acl->getAccessorId());
				}
				else
				{
					$result[$roleName] = array($acl->getAccessorId());
				}
			}
		}
		return $result;
	}
	
	/**
	 * @return Boolean
	 */
	protected function isDocumentAction()
	{
		return true;
	}
}