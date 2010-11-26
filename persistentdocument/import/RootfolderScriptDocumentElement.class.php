<?php
class generic_RootfolderScriptDocumentElement extends import_ScriptDocumentElement
{
	private $topics = null;
	
	/**
	 * @return f_persistentdocument_PersistentDocument
	 */
	protected function initPersistentDocument()
	{
		if (isset($this->attributes['topics']))
		{
			$this->topics = explode(',', $this->attributes['topics']);
		}
		if (isset($this->attributes['module']))
		{
			$folderId = ModuleService::getInstance()->getRootFolderId($this->attributes['module']);
			return DocumentHelper::getDocumentInstance($folderId);
		}
		throw new Exception('Invalid argument "rootfolder[@module]"');
	}
	
	protected function getDocumentProperties()
	{
		$properties = parent::getDocumentProperties();
		unset($properties['module']);
		unset($properties['topics']);
		return $properties;
	}
	
	public function process()
	{
		parent::process();
		if ($this->topics !== null)
		{
			$rootFolder = $this->getPersistentDocument();
			foreach ($this->topics as $topicId)
			{
				$topic = import_ScriptReader::getInstance()->getElementById($topicId)->getPersistentDocument();
				$rootFolder->addTopics($topic);
			}
			$rootFolder->save();
		}
	}
}