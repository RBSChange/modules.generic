<?php
class generic_ValidateConstraintsJSONAction extends change_JSONAction 
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$data = $request->getParameter('data', '');
		$constraintArray = $request->getParameter('constraints', array());
		RequestContext::getInstance()->setLang(RequestContext::getInstance()->getUILang());
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
		return $this->sendJSON(array());
	}
}