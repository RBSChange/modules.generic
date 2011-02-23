<?php
class generic_FolderService extends f_persistentdocument_DocumentService
{
	/**
	 * @var generic_FolderService
	 */
	private static $instance;

	/**
	 * @return generic_FolderService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

	/**
	 * @return generic_persistentdocument_folder
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_generic/folder');
	}

	/**
	 * Create a query based on 'modules_generic/folder' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_generic/folder');
	}

	/**
	 * @param generic_persistentdocument_folder $fromFolder
	 * @param String $relativePath
	 * @return generic_persistentdocument_folder|null
	 */
	public function getByPathFrom($fromFolder, $relativePath)
	{
		if (f_util_StringUtils::isEmpty($relativePath) || $relativePath == "/")
		{
			return $fromFolder;
		}
		$pathParts = explode("/", $relativePath);
		foreach ($pathParts as $pathPart)
		{
			$childFolder = $this->createQuery()->add(Restrictions::eq("label", $pathPart))->add(Restrictions::childOf($fromFolder->getId()))->findUnique();
			if ($childFolder === null)
			{
				return null;
			}
			$fromFolder = $childFolder;
		}
		return $fromFolder;
	}

	/**
	 * @param generic_persistentdocument_folder $fromFolder
	 * @param String $relativePath
	 * @return generic_persistentdocument_folder the freshfly created folder (or the existant folder if relevant)
	 */
	public function mkdir($fromFolder, $relativePath)
	{
		if (f_util_StringUtils::isNotEmpty($relativePath))
		{
			try
			{
				$this->getTransactionManager()->beginTransaction();
				$pathParts = explode("/", $relativePath);
				foreach ($pathParts as $pathPart)
				{
					$pathPart = trim($pathPart);
					if (f_util_StringUtils::isEmpty($pathPart))
					{
						continue;
					}
					$query = $this->createQuery()
						->add(Restrictions::eq("label", $pathPart))
						->add(Restrictions::childOf($fromFolder->getId()));
					$childFolder = f_util_ArrayUtils::firstElement($query->find());
					if ($childFolder === null)
					{
						$childFolder = $this->getNewDocumentInstance();
						$childFolder->setLabel($pathPart);
						$childFolder->save($fromFolder->getId());
					}
					$fromFolder = $childFolder;
				}
				$this->getTransactionManager()->commit();
			}
			catch (Exception $e)
			{
				$this->getTransactionManager()->rollBack($e);
				throw $e;
			}
		}
		return $fromFolder;
	}
	
	/**
	 * @param generic_persistentdocument_folder $document
	 * @param string $moduleName
	 * @param string $treeType
	 * @param array<string, string> $nodeAttributes
	 */	
	public function addTreeAttributes($document, $moduleName, $treeType, &$nodeAttributes)
	{
	    $blocClass = $moduleName .'_BlockFolderAction';
		if (f_util_ClassUtils::classExists($blocClass))
	    {
	        $nodeAttributes['block'] = 'modules_' . $moduleName . '_folder';
	    }
	    else
	    {
	        $nodeAttributes['block'] = '';
	    }
	    
		if ($treeType == 'wlist')
		{
	    	$nodeAttributes['thumbnailsrc'] = MediaHelper::getIcon('folder');
		}	    
	}
}