<?php
class generic_PreviewImageAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	protected function _execute($context, $request)
	{
		f_web_http_Header::setStatus(404);
		return View::NONE;
	}	
	
	public function isSecure()
	{
		return false;
	}
}