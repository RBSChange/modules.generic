<?php
class generic_DocumentlogentryScriptDocumentElement extends import_ScriptDocumentElement
{
	/**
	 * @return generic_persistentdocument_documentlogentry
	 */
	protected function initPersistentDocument()
	{
		return generic_DocumentlogentryService::getInstance()->getNewDocumentInstance();
	}
}