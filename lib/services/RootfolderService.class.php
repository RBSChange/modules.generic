<?php
class generic_RootfolderService extends f_persistentdocument_DocumentService
{

	/**
	 * @var generic_RootfolderService
	 */
	private static $instance;

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
			if ($index != -1)
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