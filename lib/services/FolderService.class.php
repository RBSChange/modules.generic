<?php
/**
 * @package modules.generic
 * @method generic_FolderService getInstance()
 */
class generic_FolderService extends f_persistentdocument_DocumentService
{
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
		return $this->getPersistentProvider()->createQuery('modules_generic/folder');
	}

	/**
	 * @param generic_persistentdocument_folder $fromFolder
	 * @param string $relativePath
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
	 * @param string $relativePath
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
	 * @param form_persistentdocument_group $document
	 * @param array<string, string> $attributes
	 * @param integer $mode
	 * @param string $moduleName
	 */
	public function completeBOAttributes($document, &$attributes, $mode, $moduleName)
	{
		if ($mode & DocumentHelper::MODE_CUSTOM)
		{
			$attributes['thumbnailsrc'] = MediaHelper::getIcon('folder');
		}
	}
	
	/**
	 * @param integer $rootFolderId
	 * @param dtring $dateString
	 * @return generic_persistentdocument_folder
	 */
	public function getFolderOfDate($rootFolderId, $dateString = null)
	{
		$dateCalendar = date_Calendar::getInstance($dateString);

		$pp = $this->getPersistentProvider();

		// Search if folders exist
		$folderDay = $this->createQuery()->add(Restrictions::descendentOf($rootFolderId))
			->add(Restrictions::eq('label', date_Formatter::format($dateCalendar, 'Y-m-d')))
			->findUnique();

		if ($folderDay === null)
		{
			$this->folderService = generic_FolderService::getInstance();

			// Year folder
			$folderYear = $this->createQuery()->add(Restrictions::childOf($rootFolderId))
				->add(Restrictions::eq('label', date_Formatter::format($dateCalendar, 'Y')))
				->findUnique();

			if ($folderYear === null)
			{
				// Create the year, month and the day folders
				$folderYear = $this->createFolder($rootFolderId, date_Formatter::format($dateCalendar, 'Y'));
				$folderMonth = $this->createFolder($folderYear->getId(), date_Formatter::format($dateCalendar, 'Y-m'));
				$folderDay = $this->createFolder($folderMonth->getId(), date_Formatter::format($dateCalendar, 'Y-m-d'));
			}
			else
			{
				// Month folder
				$folderMonth = $this->createQuery()->add(Restrictions::childOf($folderYear->getId()))
					->add(Restrictions::eq('label', date_Formatter::format($dateCalendar, 'Y-m')))
					->findUnique();

				if ($folderMonth === null)
				{
					// Create the month and the day folders
					$folderMonth = $this->createFolder($folderYear->getId(), date_Formatter::format($dateCalendar, 'Y-m'));
					$folderDay = $this->createFolder($folderMonth->getId(), date_Formatter::format($dateCalendar, 'Y-m-d'));
				}
				else
				{
					$folderDay = $this->createFolder($folderMonth->getId(), date_Formatter::format($dateCalendar, 'Y-m-d'));
				}
			}
		}

		return $folderDay;
	}
	
	/**
	 * @param integer $parentFolderId
	 * @param string $label
	 * @return generic_persistentdocument_folder
	 */
	private function createFolder($parentFolderId, $label)
	{
		$folder = $this->getNewDocumentInstance();
		$folder->setLabel($label);
		$folder->save($parentFolderId);
		return $folder;
	}
}