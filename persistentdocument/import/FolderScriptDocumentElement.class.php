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
}