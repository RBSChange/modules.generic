<?php
class generic_patch_0300 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		// Remove the following line and implement the patch here.
		parent::execute();
		
		$file = f_util_FileUtils::buildChangeBuildPath('modules', 'generic', 'dataobject', 'm_generic_doc_reference.mysql.sql');
		$this->log('Delete : ' . $file);
		unlink($file);
		
		$this->log('Drop table : m_generic_doc_reference');
		$this->executeSQLQuery('DROP TABLE `m_generic_doc_reference`');
	}

	/**
	 * Returns the name of the module the patch belongs to.
	 *
	 * @return String
	 */
	protected final function getModuleName()
	{
		return 'generic';
	}

	/**
	 * Returns the number of the current patch.
	 * @return String
	 */
	protected final function getNumber()
	{
		return '0300';
	}
}