<?php
class generic_DisplayAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		if (Framework::inDevelopmentMode())
		{
			throw new Exception("You may have to implement getDisplayPage on the service of the document");
		}
		throw new Exception("Bad configuration: please contact the webmaster so he configures the website correctly.");
	}
	
	/**
	 * Basic Display action is not secure.
	 *
	 * @return boolean Always false.
	 */
	public function isSecure()
	{
		return false;
	}
}