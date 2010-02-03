<?php
class generic_GetDocumentResumeAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$document = $this->getDocumentInstanceFromRequest($request);
		
		//Retrouve le document original
		$document = DocumentHelper::getByCorrection($document);
		$data = $document->getDocumentService()->getResume($document, $this->getModuleName($request));
		return $this->sendJSON($data);

	}
}