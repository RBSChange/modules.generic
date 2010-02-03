<?php
/**
 * This action regenerates all tags data.
 */
class generic_CompileTagsAction extends f_action_BaseAction
{


    /**
	 * @param Context $context
	 * @param Request $request
	 */
    public function _execute($context, $request)
    {
    	TagService::getInstance()->regenerateTags();
        return self::getSuccessView();
    }
}