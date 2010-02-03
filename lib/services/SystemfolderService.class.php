<?php
class generic_SystemfolderService extends f_persistentdocument_DocumentService
{
	/**
	 * @var generic_SystemfolderService
	 */
	private static $instance;

	/**
	 * @return generic_SystemfolderService
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
	 * @return generic_persistentdocument_systemfolder
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_generic/systemfolder');
	}

	/**
	 * Create a query based on 'modules_generic/systemfolder' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_generic/systemfolder');
	}

	/**
	 * @param generic_persistentdocument_systemfolder $systemFolder
	 * @return string
	 */
	public function getOwnerModuleName($systemFolder)
	{
	    $package = ModuleService::getInstance()->getPersistentProvider()->getSettingPackage($systemFolder->getId(), ModuleService::SETTING_SYSTEM_FOLDER_ID);

	    list($owner, $related) = explode('/', $package);

		return ModuleService::getInstance()->getShortModuleName($owner);
	}

	/**
	 * @param generic_persistentdocument_systemfolder $systemFolder
	 * @return string
	 */
	public function getRelatedModuleName($systemFolder)
	{
	    $package = ModuleService::getInstance()->getPersistentProvider()->getSettingPackage($systemFolder->getId(), ModuleService::SETTING_SYSTEM_FOLDER_ID);

	    list($owner, $related) = explode('/', $package);

		return ModuleService::getInstance()->getShortModuleName($related);
	}

}