<?php
class generic_persistentdocument_rootfolder extends generic_persistentdocument_rootfolderbase
{	
	/**
	 * Return the localized value for a rootfolder
	 * @return string
	 */
	public function getLabel()
	{
		return f_Locale::translateUI('&modules.generic.document.rootfolder.Document-name;');
	}
}
