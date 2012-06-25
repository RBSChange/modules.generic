<?php

class generic_GetTreeChildrenJSONAction extends f_action_BaseJSONAction
{
	private $config;
	private $childType;
	private $permissionned_nodes;
	private $rootFolder;
	private $currentUser;
	private $columns;
	private $pageSize;
	private $startIndex;
	private $total;
	private $locateDocument;
	
	private $treeType = 'wtree'; //wlist, wmultitree, wmultilist

	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$moduleName = $this->getModuleName();
		$parentId = $this->getDocumentIdFromRequest($request);
		$this->rootFolder = DocumentHelper::getDocumentInstance(ModuleService::getInstance()->getRootFolderId($moduleName));
		$this->childType = $request->getParameter('childType');
		$this->pageSize = $request->getParameter('pageSize', -1);
		$this->startIndex = $request->getParameter('startIndex', 0);
		$this->locateDocument = $request->getParameter('locateDocument');
		$this->treeType = $request->getParameter('treetype', 'wtree');
		
		if ($this->pageSize <= 0)
		{
			$this->pageSize = 1000;
		}
		
		if (!is_array($this->childType))
		{
			$this->childType = array();
		}
		
		$this->columns = $request->getParameter('columns');
		if (!is_array($this->columns))
		{
			$this->columns = array();
		}
		
		if (!$parentId)
		{
			$document = $this->rootFolder;
		}
		else
		{
			$document = DocumentHelper::getDocumentInstance($parentId);
		}
		
		$this->containerOnly = $request->getParameter('containeronly', true);
		$this->permissionned_nodes = f_persistentdocument_PersistentProvider::getInstance()->getPermissionDefinitionPoints('modules_' . $moduleName);
		
		return $this->sendJSON($this->getChildren($document));
	}
	
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 */
	private function getChildren($document)
	{
		$node = $this->buildJSNode($document);
		if (count($this->childType) > 0 && ($document !== $this->rootFolder || $this->checkDocumentVisibility($document)))
		{
			$node['nodes'] = $this->getChildrenDocument($document);
		}
		
		return array('nodes' => array($node), 'total' => intval($this->total), 'startIndex' => intval($this->startIndex));
	}
	
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param string $modelName
	 * @return string
	 */
	protected function resolveFrom($document, $modelName)
	{
		$model = $document->getPersistentModel();
		foreach ($model->getPropertiesInfos() as $propertyName => $propertyValue)
		{
			if ($propertyValue->getType() === $modelName && $propertyValue->isTreeNode())
			{
				return $propertyName;
			}
		}
		
		foreach ($model->getInverseProperties() as $propertyName => $propertyValue)
		{
			if ($propertyValue->getType() === $modelName && $propertyValue->isTreeNode())
			{
				return $propertyName;
			}
		}
		return 'treenode';
	}
	
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @return array<f_persistentdocument_PersistentDocument>
	 */
	private function getChildrenDocument($document)
	{
		$nodes = array();
		$treeNodeModels = array();
		$virtualModels = array();
		foreach ($this->childType as $modelName => $from) 
		{
			if ($from === 'autodetect')
			{
				$from = $this->resolveFrom($document, $modelName);
			}
			if ($from === 'treenode')
			{
				$treeNodeModels[] = $modelName;
			}
			else
			{
				$virtualModels[$from][] = $modelName;
			}
		}		
		$documents = (count($treeNodeModels) > 0) ? $this->getTreeChildren($document, $treeNodeModels) : array();
		if (count($virtualModels) > 0)
		{
			
			foreach ($virtualModels as $from => $subModelNames) 
			{
				$documents = array_merge($documents, $this->getVirtualChildren($document, $subModelNames, $from));
			}
		}
		
		if ($this->total === null)
		{
			$count = 0;
			$locateDocument = $this->locateDocument;
			$maxIndex = $this->startIndex + $this->pageSize;
			foreach ($documents as $subDocument)
			{
				if ($this->checkDocumentVisibility($subDocument))
				{
					if ($locateDocument)
					{
						if ($count >= $maxIndex)
						{
							$this->startIndex += $this->pageSize;
							$nodes = array();
							$maxIndex = $this->startIndex + $this->pageSize;
						}
						if ($locateDocument == $subDocument->getId())
						{
							$locateDocument = null;
						}
					}
					if ($count >= $this->startIndex && $count < $maxIndex)
					{
						$nodes[] = $this->buildJSNode($subDocument);
					}
					$count++;
				}
			}
			$this->total = $count;
		}
		else 
		{
			foreach ($documents as $subDocument)
			{
				if ($this->checkDocumentVisibility($subDocument))
				{
					$nodes[] = $this->buildJSNode($subDocument);
				}
			}
		}
		return $nodes;
	}
	
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param string[] $subModelNames
	 * @param string $propertyName
	 * @return array<f_persistentdocument_PersistentDocument>
	 */
	protected function getVirtualChildren($document, $subModelNames, $propertyName)
	{
		if (f_util_ClassUtils::methodExists($document->getDocumentService(), 'getVirtualChildrenAt'))
		{
			$totalCount = $this->getTotal();
			$startIndex = $this->getStartIndex();
			$locateDocumentId = $this->getLocateDocument();
			$result = $document->getDocumentService()->getVirtualChildrenAt($document, $subModelNames, $locateDocumentId, $this->getPageSize(), $startIndex, $totalCount);
			$this->setStartIndex($startIndex);
			$this->setTotal($totalCount);
			return $result;
		}
		$result = array();
		
		// Direct property.
		$propertyValue = $document->getPersistentModel()->getProperty($propertyName);
		if ($propertyValue)
		{
			if ($propertyValue->isArray())
			{
				$result = $document->{'get' . ucfirst($propertyName) . 'Array'}();
			}
			else
			{
				$subdoc = $document->{'get' . ucfirst($propertyName)}();
				if ($subdoc !== null)
				{
					$result[] = $subdoc;
				}
			}			
		}
		else
		{
			// Inverse property.
			$propertyValue = $document->getPersistentModel()->getInverseProperty($propertyName);
			if ($propertyValue)
		{
			$result = $document->{'get' . ucfirst($propertyName) . 'ArrayInverse'}();
		}
			else
			{
				// Serialized property.
				$propertyValue = $document->getPersistentModel()->getSerializedProperty($propertyName);
				if ($propertyValue)
		{
			if ($propertyValue->isArray())
			{
				$result = $document->{'get' . ucfirst($propertyName) . 'Array'}();
			}
			else
			{
				$subdoc = $document->{'get' . ucfirst($propertyName)}();
				if ($subdoc !== null)
				{
					$result[] = $subdoc;
				}
			}				
		}
				// Method on document.
		else if (f_util_ClassUtils::methodExists($document, $getterName = 'get'.ucfirst($propertyName)))
		{
			$result = $document->{$getterName}();
			if (!is_array($result))
			{
				$result = array($result);
					}
				}
			}
		}
		
		if (count($result) > 0)
		{
			$result2 = array();
			foreach ($result as $document) 
			{
				if (in_array($document->getPersistentModel()->getOriginalModelName(), $subModelNames))
				{
					$result2[] = $document;
				}
			}
			return $result2;
		}
		return $result;
	}
	
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @return array<f_persistentdocument_PersistentDocument>
	 */
	protected function getTreeChildren($document, $subModelNames)
	{
		$treenode = TreeService::getInstance()->getInstanceByDocument($document);
		if (!is_null($treenode))
		{
			$query = f_persistentdocument_PersistentProvider::getInstance()->createQuery()
						->add(Restrictions::childOf($document->getId()));
			if ($subModelNames != null)
			{
				$query->add(Restrictions::in('document_model', $subModelNames));
			}
			return $query->find();
		}
		return array();
	}
	
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @return Boolean
	 */
	private function checkDocumentVisibility($document)
	{
		if ($document instanceof website_persistentdocument_websitetopicsfolder)
		{
			foreach ($document->getTopicsArray() as $topic) 
			{
				if ($this->checkDocumentVisibility($topic))
				{
					return true;
				}
			}
			return false;			
		}

		$nodeId = $document->getId();
		if (array_search($nodeId, $this->permissionned_nodes) !== false)
		{
			$backEndUser = $this->getCurrentBackEndUser();
			$permission = $this->getPermissionName($document);
			$result = f_permission_PermissionService::getInstance()->hasPermission($backEndUser, $permission, $nodeId);
			return $result;
		}
		return true;
	}
	
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 */
	private function getPermissionName($document)
	{
		return 'modules_' . $this->getModuleName() . '.List.' . $document->getPersistentModel()->getDocumentName();
	}
	
	/**
	 * @return users_persistentdocument_backenduser
	 */
	private function getCurrentBackEndUser()
	{
		if (is_null($this->currentUser))
		{
			$this->currentUser = users_UserService::getInstance()->getCurrentBackEndUser();
		}
		return $this->currentUser;
	}
	
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param Integer $parentId
	 * @return array
	 */
	private function buildJSNode($document)
	{
		$lang = RequestContext::getInstance()->getLang();
		$langs = $document->getI18nInfo()->getLangs();
		
		$isContextLangAvailable = $document->isContextLangAvailable();
		$label = $document->getTreeNodeLabel();
		if (!$isContextLangAvailable)
		{
			$label .= ' [' . f_Locale::translateUI('&modules.uixul.bo.languages.' . ucfirst($document->getLang()) . ';') . ']';
		}
		$modelName = $document->getDocumentModelName();
		
		$currentNode = array();
		$currentNode['i'] = $document->getId();
		$currentNode['v'] = $document->getDocumentversion();
		$currentNode['mn'] = $modelName;
		$currentNode['t'] = str_replace('/', '_', $modelName);
		$currentNode['label'] = $label;
		$currentNode['l'] = $lang;
		$currentNode['dl'] = join(' ', $langs);
		$currentNode['la'] = $isContextLangAvailable;
		$currentNode['s'] = $document->getPublicationstatus();
		
		//$currentNode['c'] = $this->isContainer($modelName);
		
		$persistantModel = $document->getPersistentModel();
		if ($isContextLangAvailable && $persistantModel->useCorrection())
		{
			$correctionId = $document->getCorrectionid();
			if ($correctionId)
			{
				$currentNode['cr'] = $correctionId;
				$correction = DocumentHelper::getDocumentInstance($correctionId);
				$currentNode['crs'] = $correction->getPublicationstatus();
				$currentNode['label'] = $correction->getLabel() . ' (' . $currentNode['label'] . ')';
			}
		}
		
		$this->addPermissionInfo($document, $currentNode);
		$nodeAttributes = array();
		$document->getDocumentService()->addTreeAttributes($document, $this->getModuleName(), $this->treeType, $nodeAttributes);
		if (isset($nodeAttributes['s']))
		{
			$currentNode['s'] = $nodeAttributes['s'];
		}
		
		if (isset($nodeAttributes['label']))
		{
			$currentNode['label'] = $nodeAttributes['label'];
		}
		
		if (!isset($nodeAttributes['block']))
		{
			$models = block_BlockService::getInstance()->getBlocksDocumentModelToInsert();
			if (isset($models[$modelName]))
			{
				$nodeAttributes['block'] = f_util_ArrayUtils::firstElement($models[$modelName]);
			}
		}
		if (!isset($nodeAttributes['htmllink']) && $isContextLangAvailable && $persistantModel->hasURL())
		{
			$nodeAttributes['htmllink'] = '<a class="link" href="#" rel="cmpref:' . $currentNode['i'] . '" lang="' . $lang . '">' . htmlspecialchars($label, ENT_NOQUOTES, 'UTF-8') . '</a>';
		}
		
		if (!isset($nodeAttributes['hasWorkflow']) && workflow_ModuleService::getInstance()->hasPublishedWorkflowByModel($persistantModel))
		{
			$nodeAttributes['hasWorkflow'] = true;
		}
		
		if (count($this->columns) > 0)
		{
			foreach ($this->columns as $columnName)
			{
				if (!isset($nodeAttributes[$columnName]))
				{
					$getter = ucfirst(str_replace(array('-', '.'), '', $columnName));
					if (f_util_ClassUtils::methodExists($document, 'getUI' . $getter))
					{
						$value = $document->{'getUI' . $getter}();
						if (is_string($value) && strlen($value) === 19)
						{
							$value = date_Formatter::toDefaultDateTimeBO($value);
						}
						$nodeAttributes[$columnName] = $value;
					}
					elseif (f_util_ClassUtils::methodExists($document, 'get' . $getter))
					{
						$nodeAttributes[$columnName] = $document->{'get' . $getter}();
					}
				}
			}
		}
		
		$currentNode['properties'] = $nodeAttributes;
		
		return $currentNode;
	}
	
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param array $currentNode;
	 */
	private function addPermissionInfo($document, &$currentNode)
	{
		if (array_search($document->getId(), $this->permissionned_nodes) !== false)
		{
			$currentNode['r'] = true;
			$ps = f_permission_PermissionService::getInstance();
			$permissions = $ps->getPermissionsForUserByDefPointNodeId($this->getCurrentBackEndUser(), $document->getId());
			
			if (count($permissions) === 1 && $permissions[0] === f_permission_PermissionService::ALL_PERMISSIONS)
			{
				$currentNode['pe'] = array(f_permission_PermissionService::ALL_PERMISSIONS => true);
			}
			else if (count($permissions) == 0)
			{
				$currentNode['pe'] = array();
			}
			else
			{
				$flper = array();
				foreach ($permissions as $permission)
				{
					$perInfo = explode('.', $permission);
					if (count($perInfo) === 2)
					{
						$flper[$perInfo[1]] = true;
					}
					elseif (count($perInfo) === 3)
					{
						$flper[$perInfo[1] . '_' . $perInfo[2]] = true;
					}
				}
				$currentNode['pe'] = $flper;
			}
		}
	}
	/**
	 * @return String wtree, wlist
	 */
	protected function getTreeType()
	{
		return $this->treeType;
	}	
	
	
	/**
	 * @return Integer
	 */
	protected function getPageSize()
	{
		return $this->pageSize;
	}
	
	/**
	 * @return Integer
	 */
	protected function getStartIndex()
	{
		return $this->startIndex;
	}
	
	
	/**
	 * @param Integer $index
	 */
	protected function setStartIndex($index)
	{
		$this->startIndex = $index;
	}
	
	/**
	 * @return Integer
	 */
	protected function getTotal()
	{
		return $this->total;
	}
	
	/**
	 * @param Integer $total
	 */
	protected function setTotal($total)
	{
		$this->total = $total;
	}
	/**
	 * @return Integer
	 */
	protected function getLocateDocument()
	{
		return $this->locateDocument;
	}
	
	protected function hasBlockClassNameFromType($type)
	{
		$typeInfo = explode("_", $type);
		if (count($typeInfo) == 3)
		{
			$models = block_BlockService::getInstance()->getBlocksDocumentModelToInsert();
			return isset($models[$typeInfo[0] . '_' . $typeInfo[1] .  '/' . $typeInfo[2]]);
			
		}
		return false;
	}
}
