<?php
class generic_persistentdocument_userAcl extends generic_persistentdocument_userAclbase 
{
	public function getAccessorId()
	{
		return $this->getUser()->getId();
	}
}