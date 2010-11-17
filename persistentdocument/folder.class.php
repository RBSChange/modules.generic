<?php
class generic_persistentdocument_folder extends generic_persistentdocument_folderbase
{
	/**
	 * @param string $moduleName
	 * @param string $treeType
	 * @param array<string, string> $nodeAttributes
	 */	
	protected function addTreeAttributes($moduleName, $treeType, &$nodeAttributes)
	{
	    $blocClass = $moduleName .'_BlockFolderAction';
		if (f_util_ClassUtils::classExists($blocClass))
	    {
	        $nodeAttributes['block'] = 'modules_' . $moduleName . '_folder';
	    }
	    else
	    {
	        $nodeAttributes['block'] = '';
	    }
	    
		if ($treeType == 'wlist')
		{
	    	$nodeAttributes['thumbnailsrc'] = MediaHelper::getIcon('folder');
		}	    
	}
}