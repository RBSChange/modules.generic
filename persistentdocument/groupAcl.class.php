<?php
class generic_persistentdocument_groupAcl extends generic_persistentdocument_groupAclbase implements f_permission_ACL 
{
	public function getAccessorId()
	{
		return $this->getGroup()->getId();
	}
}