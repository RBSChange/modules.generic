<?php
class generic_ActionBase extends f_action_BaseAction
{

	/**
	 * Returns the generic_DocumentlogentryService to handle documents of type "modules_generic/documentlogentry".
	 *
	 * @return generic_DocumentlogentryService
	 */
	public function getDocumentlogentryService()
	{
		return generic_DocumentlogentryService::getInstance();
	}

	/**
	 * Returns the generic_FolderService to handle documents of type "modules_generic/folder".
	 *
	 * @return generic_FolderService
	 */
	public function getFolderService()
	{
		return generic_FolderService::getInstance();
	}

	/**
	 * Returns the generic_GroupAclService to handle documents of type "modules_generic/groupAcl".
	 *
	 * @return generic_GroupAclService
	 */
	public function getGroupAclService()
	{
		return generic_GroupAclService::getInstance();
	}

	/**
	 * Returns the generic_ReferenceService to handle documents of type "modules_generic/reference".
	 *
	 * @return generic_ReferenceService
	 */
	public function getReferenceService()
	{
		return generic_ReferenceService::getInstance();
	}

	/**
	 * Returns the generic_RootfolderService to handle documents of type "modules_generic/rootfolder".
	 *
	 * @return generic_RootfolderService
	 */
	public function getRootfolderService()
	{
		return generic_RootfolderService::getInstance();
	}

	/**
	 * Returns the generic_SystemfolderService to handle documents of type "modules_generic/systemfolder".
	 *
	 * @return generic_SystemfolderService
	 */
	public function getSystemfolderService()
	{
		return generic_SystemfolderService::getInstance();
	}

	/**
	 * Returns the generic_UserAclService to handle documents of type "modules_generic/userAcl".
	 *
	 * @return generic_UserAclService
	 */
	public function getUserAclService()
	{
		return generic_UserAclService::getInstance();
	}

}