<?php
class generic_GetDocumentResumeAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
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