<?php
class generic_GetDocumentHistoryAction extends f_action_BaseJSONAction
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
		
		return $this->sendJSON($this->getDatas($document));
	}
	
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param users_persistentdocument_user $user
	 */
	protected function getLogs($document, $user)
	{
		$userId = ($user !== null) ? $user->getId() : null;
		$documentId = ($document !== null) ? $document->getId() : null;
		$uiLang = RequestContext::getInstance()->getUILang();
		$logs = array();
		$rows = $this->getPersistentProvider()->getUserActionEntry($userId, null, null, $documentId, 0, 100, null, 'DESC');
		foreach ($rows as $row) 
		{
			$logEntry = unserialize($row['info']);
			$logEntry['logdescription'] = f_Locale::translateUI('&modules.' . $row['module_name']. '.bo.useractionlogger.' .ucfirst(str_replace('.', '-',$row['action_name'])) .';', $logEntry);
			$logEntry['entry_date'] = $row['entry_date'];
			$logEntry['date'] = date_DateFormat::format(date_Converter::convertDateToLocal($row['entry_date']), null, $uiLang);
			$logs[] = $logEntry;
		}
		return $logs;
	}
	
	/**
	 * @param f_persistentdocument_PersistentDocument $documentId
	 * @param users_persistentdocument_user $user
	 */
	protected function getDatas($document)
	{
		$logs = $this->getLogs($document, null);
		$datas = array('log' => $logs);
		$uiLang = RequestContext::getInstance()->getUILang();
		$datas['id'] = $document->getId();
		$datas['lang'] = $document->getLang();
		$datas['documentversion'] = $document->getDocumentversion();
		$datas['creationdate'] = date_DateFormat::format($document->getUICreationdate(), null, $uiLang);
		$datas['modificationdate'] = date_DateFormat::format($document->getUIModificationdate(), null, $uiLang);
		$datas['author'] = $document->getAuthor();
		
		return $datas;
	}
}