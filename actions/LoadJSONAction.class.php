<?php
class generic_LoadJSONAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$document = $this->getDocumentInstanceFromRequest($request);
		// The following if is only valid for 3.0.4, Cf. http://www.rbschange.fr/tickets-35142.html
		// In 3.5.0, this is handled by "http://git.rd.devlinux.france.rbs.fr/?p=modules.generic.git;a=commit;h=c1a74e64c901fa8755409ec262dd8c5fe54872f2"
		if (!$request->hasParameter("lang"))
		{
			RequestContext::getInstance()->setLang($document->getLang());
		}
		
		//Retrouve la correction courante
		$document = DocumentHelper::getCorrection($document);
		
		$allowedProperties = array('id', 'lang', 'documentversion');
		$requestedProperties = explode(',', $request->getParameter('documentproperties', ''));
		foreach ($requestedProperties as $propertyName)
		{
			if (!in_array($propertyName, $allowedProperties))
			{
				$allowedProperties[] = $propertyName;
			}
		}
		
		
		
		$data = $this->exportFieldsData($document, $allowedProperties);
		
		return $this->sendJSON($data);
	}

	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param String[]
	 * @return Array
	 */
	protected function exportFieldsData($document, $allowedProperties)
	{
		return uixul_DocumentEditorService::getInstance()->exportFieldsData($document, $allowedProperties);
	}
	
	/**
	 * @return Boolean
	 */
	protected function suffixSecureActionByDocument()
	{
		return true;
	}
}