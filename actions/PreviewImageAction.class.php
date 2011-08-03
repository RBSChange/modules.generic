<?php
class generic_PreviewImageAction extends change_Action
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	protected function _execute($context, $request)
	{
		f_web_http_Header::setStatus(404);
		return change_View::NONE;
	}	
	
	public function isSecure()
	{
		return false;
	}
}