<?php
class generic_FolderScriptDocumentElement extends import_ScriptDocumentElement
{
	/**
	 * @return f_persistentdocument_PersistentDocument
	 */
	protected function initPersistentDocument()
	{
		return generic_FolderService::getInstance()->getNewDocumentInstance();
	}
	
	/**
	 * @return void
	 */
	public function endProcess()
	{
		$document = $this->getPersistentDocument();
		foreach ($this->script->getChildren($this) as $child)
		{
			if ($child instanceof users_PermissionsScriptDocumentElement)
			{
				$child->setPermissions($document);
			}
		}
	}
}