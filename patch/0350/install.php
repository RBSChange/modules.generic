<?php
/**
 * generic_patch_0350
 * @package modules.generic
 */
class generic_patch_0350 extends patch_BasePatch
{
 
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$this->execChangeCommand('compile-locales', array('generic'));
		$this->execChangeCommand('compile-locales', array('framework'));
		$this->executeModuleScript('lists.xml', 'generic');
	}
}