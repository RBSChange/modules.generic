<?php
class generic_ViewTagAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param ChangeRequest $request
	 */
	public function _execute($context, $request)
	{
		$moduleName = $this->getModuleName();
		$tagName = $request->getModuleParameter($moduleName, 'tagName');
		if (!empty($tagName))
		{
			$ts  = TagService::getInstance();
			try 
			{
				if ($ts->isContextualTag($tagName))
				{
					$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
					$document = $ts->getDocumentByContextualTag($tagName, $website);
				}
				else
				{
					$document = f_util_ArrayUtils::firstElement($ts->getDocumentsByTag($tagName));
				}
				if ($document !== null)
				{
					$model = $document->getPersistentModel();
					$moduleName = 	$model->getModulename();
					$request->setModuleParameter($moduleName, 'cmpref', $document->getId());
					$context->getController()->forward($moduleName, 'ViewDetail');
					return View::NONE;
				}		
			} 
			catch (Exception $e) 
			{
				Framework::exception($e);
			}
		}
		else
		{
			Framework::error(__METHOD__ . ' parameter tagName not defined');
		}
		// Finally, forward the execution to $module / $action.
		$context->getController()->forward('website', 'Error404');
		return View::NONE;
	}
	
	/**
	 * @return Boolean
	 */
	public function isSecure()
	{
		return false;
	}
}