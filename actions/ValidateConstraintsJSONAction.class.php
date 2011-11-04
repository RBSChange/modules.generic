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
		$constraints = $request->getParameter('constraints');
		if (is_string($constraints))
		{
			$constraintArray = JsonService::getInstance()->decode($constraints);
		}
		else
		{
			$constraintArray = array();
		}
		
		RequestContext::getInstance()->setLang(RequestContext::getInstance()->getUILang());
		if (f_util_ArrayUtils::isNotEmpty($constraintArray))
		{
			try 
			{
				foreach ($constraintArray as $name => $params) 
				{
					$c = change_Constraints::getByName($name, $params);
					if (!$c->isValid($data))
					{
						$errors = change_Constraints::formatMessages($c, $request->getParameter('name'), $params);
						if (count($errors))
						{
							return $this->sendJSONError($errors[0], false);
						}
					}
				}
			}
			catch (Exception $e)
			{
				return $this->sendJSONException($e, false);
			}
		}
		return $this->sendJSON(array());
	}
}