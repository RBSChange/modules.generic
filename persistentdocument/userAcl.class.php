<?php
class generic_persistentdocument_userAcl extends generic_persistentdocument_userAclbase implements f_permission_ACL 
{
	public function getAccessorId()
	{
		return $this->getUser()->getId();
	}
}