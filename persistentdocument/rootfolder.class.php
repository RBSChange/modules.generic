<?php
class generic_persistentdocument_rootfolder extends generic_persistentdocument_rootfolderbase
{		
	/**
	 * Define the label of the tree node of the document.
	 * By default, this method returns the label property value.
	 * @return String
	 */
	public function getTreeNodeLabel()
	{
		return LocaleService::getInstance()->trans('m.generic.document.rootfolder.document-name', array('ucf'));
		
	}
}
