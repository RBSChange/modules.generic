<?php
class generic_persistentdocument_groupAcl extends generic_persistentdocument_groupAclbase
{
	public function getAccessorId()
	{
		return $this->getGroup()->getId();
	}
}