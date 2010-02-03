<?php
class generic_persistentdocument_systemfolder extends generic_persistentdocument_systemfolderbase 
{
	
	/**
	 * Return the localized value for a systemfolder
	 * @return string
	 */
	public function getLabel()
	{
		return f_Locale::translateUI( parent::getLabel() );
	}
	
}
