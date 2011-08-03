<?php
class generic_GetDocumentEditorInfosAction extends change_JSONAction
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
		$data = $document->getDocumentService()->getDocumentEditorInfos($document, $this->getModuleName());
		return $this->sendJSON($data);
	}
}