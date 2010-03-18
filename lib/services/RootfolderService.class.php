<?php
class generic_RootfolderService extends f_persistentdocument_DocumentService
{
	
	/**
	 * @var generic_RootfolderService
	 */
	private static $instance;
	
	/**
	 * @see f_persistentdocument_DocumentService::preUpdate()
	 *
	 * @param generic_persistentdocument_rootfolder $document
	 * @param Integer $parentNodeId
	 */
	protected function preUpdate($document, $parentNodeId)
	{
		if ($document->isPropertyModified('topics'))
		{
			$package = $this->getPackageName($document);
			if ($package !== null)
			{
				$shortModuleName = ModuleService::getInstance()->getShortModuleName($package);
				$module = ModuleService::getInstance()->getModule($shortModuleName);
				if ($module->hasPerspectiveConfigFile())
				{
					$this->updateWebsiteFolderName($document);
				}
			}
		}
	}
	
	/**
	 * @param generic_persistentdocument_rootfolder $document
	 * @return string
	 */
	private function updateWebsiteFolderName($document)
	{
		$oldWebsiteTopics = website_WebsitetopicsfolderService::getInstance()->createQuery()
						->add(Restrictions::childOf($document->getId()))
						->find();
		

		$topics = $document->getTopicsArray();
		if (count($topics))
		{
			//Index Website Topic
			$searchArray = array();
			foreach ($oldWebsiteTopics as $websiteTopic)
			{
				if ($websiteTopic->getWebsite() === null)
				{
					//Remove invalid Website Topic
					$websiteTopic->delete();
					continue;
				}
				$searchArray[$websiteTopic->getWebsite()->getId()] = $websiteTopic;
			}
			
			//Ajout des nouveaux
			foreach ($topics as $topic)
			{
				$website = website_WebsiteModuleService::getInstance()->getParentWebsite($topic);
				if (isset($searchArray[$website->getId()]))
				{
					$websiteTopic = $searchArray[$website->getId()];
					$websiteTopic->setLabel($website->getVoLabel());
					$websiteTopic->addTopics($topic);
				}
				else
				{
					$websiteTopic = website_WebsitetopicsfolderService::getInstance()->getNewDocumentInstance();
					$searchArray[$website->getId()] = $websiteTopic;
					$websiteTopic->setWebsite($website);
					$websiteTopic->setLabel($website->getVoLabel());
					$websiteTopic->addTopics($topic);
				}
			}
			
			//Supression Des anciens
			foreach ($searchArray as $websiteTopic)
			{
				if ($websiteTopic->isNew())
				{
					continue;
				}
				foreach ($websiteTopic->getTopicsArray() as $ot)
				{
					if (! in_array($ot, $topics))
					{
						$websiteTopic->removeTopics($ot);
					}
				}
			}
			
			//Sauvegarde
			foreach ($searchArray as $websiteTopic)
			{
				if ($websiteTopic->isNew())
				{
					$websiteTopic->save($document->getId());
				}
				else if ($websiteTopic->getTopicsCount())
				{
					$websiteTopic->save();
				}
				else
				{
					$websiteTopic->delete();
				}
			}
		}
		else if (count($oldWebsiteTopics))
		{
			foreach ($oldWebsiteTopics as $websiteTopic)
			{
				$websiteTopic->delete();
			}
		}
	}
	
	/**
	 * @param generic_persistentdocument_rootfolder $document
	 * @return string
	 */
	private function getPackageName($document)
	{
		return $this->pp->getSettingPackage($document->getId(), ModuleService::SETTING_ROOT_FOLDER_ID);
	}
	
	/**
	 * @return generic_RootfolderService
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
	 * @return generic_persistentdocument_rootfolder
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_generic/rootfolder');
	}
	
	/**
	 * Create a query based on 'modules_generic/rootfolder' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_generic/rootfolder');
	}
	
	/**
	 * @param generic_persistentdocument_rootfolder $parentDocument
	 * @param Integer $childDocmunentId
	 */
	public function removeDocumentId($parentDocument, $childDocmunentId)
	{
		parent::removeDocumentId($parentDocument, $childDocmunentId);
		$childDocument = DocumentHelper::getDocumentInstance($childDocmunentId);
		if ($childDocument instanceof website_persistentdocument_topic)
		{
			$index = $parentDocument->getIndexofTopics($childDocument);
			if ($index != - 1)
			{
				$parentDocument->removeTopics($index);
				$this->save($parentDocument);
			}
			else
			{
				if (Framework::isDebugEnabled())
				{
					Framework::debug(__METHOD__ . ' child topic ' . $childDocmunentId . ' not found');
				}
			}
		}
		else
		{
			if (Framework::isDebugEnabled())
			{
				Framework::debug(__METHOD__ . ' cant remove child ' . $childDocmunentId);
			}
		}
	}
}