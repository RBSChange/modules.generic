<?php
/**
 * generic_GroupAclScriptDocumentElement
 * @package modules.generic.persistentdocument.import
 */
class generic_GroupAclScriptDocumentElement extends import_ScriptDocumentElement
{
	/**
	 * @return generic_persistentdocument_groupAcl
	 */
	protected function initPersistentDocument()
	{
		return generic_GroupAclService::getInstance()->getNewDocumentInstance();
	}
}