<?php
class generic_Setup extends object_InitDataSetup
{
	public function install()
	{
		try
		{
			$this->executeModuleScript('lists.xml');
		}
		catch (Exception $e)
		{
			echo "ERROR: " . $e->getMessage() . "\n";
			Framework::exception($e);
		}
	}
}