<?php
/**
 * generic_UserAclScriptDocumentElement
 * @package modules.generic.persistentdocument.import
 */
class generic_UserAclScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return generic_persistentdocument_userAcl
     */
    protected function initPersistentDocument()
    {
    	return generic_UserAclService::getInstance()->getNewDocumentInstance();
    }
}