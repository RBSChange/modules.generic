<?php
class generic_ValidateConstraintsJSONAction extends f_action_BaseJSONAction 
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$rc = RequestContext::getInstance();
		try 
		{
			$rc->beginI18nWork($rc->getUILang());
			$data = $request->getParameter('data', '');
		
			$constraintArray = $request->getParameter('constraints', array());
			if (f_util_ArrayUtils::isNotEmpty($constraintArray))
			{
				$constraints = implode(';', $constraintArray);
				$name = $request->getParameter('name', '');
				$property   = new validation_Property($name, $data);
				$errors     = new validation_Errors();
				$constraintsParser = new validation_ContraintsParser();
				$validators = $constraintsParser->getValidatorsFromDefinition($constraints);
		
				foreach ($validators as $validator)
				{
					try
					{
						$validator->validate($property, $errors);
					}
					catch (IllegalArgumentException $e)
					{
						return $this->sendJSONException($e, false);
					}
					if ($errors->count() > 0)
					{
						return $this->sendJSONError($errors[0], false);
					}
				}
			}			
			
			$rc->endI18nWork();
		} 
		catch (Exception $e) 
		{
			$rc->endI18nWork($e);
		}

		return $this->sendJSON(array());
	}
}