<?php
class generic_SystemfolderScriptDocumentElement extends import_ScriptDocumentElement
{

	/**
	 * @return f_persistentdocument_PersistentDocument
	 */
	protected function initPersistentDocument()
	{
		if (isset($this->attributes['module']) && isset($this->attributes['relatedmodule']))
		{
			$folderId = ModuleService::getInstance()->getSystemFolderId($this->attributes['module'], $this->attributes['relatedmodule']);
			return DocumentHelper::getDocumentInstance($folderId);
		}
		throw new Exception('Invalid argument "systemfolder[@module OR @relatedmodule]"');
	}
	
	protected function getDocumentProperties()
	{
		$properties = parent::getDocumentProperties();
		unset($properties['module']);
		unset($properties['relatedmodule']);
		return $properties;
	}
	
	/**
	 * @return import_ScriptDocumentElement
	 */
	protected function getParentDocument()
	{
		return null;
	}
}