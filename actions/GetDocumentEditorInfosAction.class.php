<?php
class generic_GetDocumentEditorInfosAction extends f_action_BaseJSONAction
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
		$data = $document->getDocumentService()->getDocumentEditorInfos($document, $this->getModuleName());
		return $this->sendJSON($data);
	}
}