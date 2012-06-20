<?php
class generic_GetDocumentHistoryAction extends change_JSONAction
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
		
		$logs = array();
		$rows = $this->getPersistentProvider()->getUserActionEntry($userId, null, null, $documentId, 0, 100, null, 'DESC');
		foreach ($rows as $row) 
		{
			$logEntry = unserialize($row['info']);
			$logEntry['entry_id'] = $row['entry_id'];
			$logEntry['logdescription'] = f_Locale::translateUI('&modules.' . $row['module_name']. '.bo.useractionlogger.' .ucfirst(str_replace('.', '-',$row['action_name'])) .';', $logEntry);
			$logEntry['entry_date'] = $row['entry_date'];
			$logEntry['date'] = date_Formatter::toDefaultDateTimeBO(date_Converter::convertDateToLocal($row['entry_date']));
			$logs[] = $logEntry;
		}
		return $logs;
	}
	
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @return array
	 */
	protected function getDatas($document)
	{
		$logs = $this->getLogs($document, null);
		$datas = array('log' => $logs);
		$datas['id'] = $document->getId();
		$datas['lang'] = $document->getLang();
		$datas['documentversion'] = $document->getDocumentversion();
		$datas['creationdate'] = date_Formatter::toDefaultDateTimeBO($document->getUICreationdate());
		$datas['modificationdate'] = date_Formatter::toDefaultDateTimeBO($document->getUIModificationdate());
		$datas['author'] = $document->getAuthor();
		$datas['type'] =  LocaleService::getInstance()->trans($document->getPersistentModel()->getLabelKey(), array('ucf'))
			. ' (' .$document->getDocumentModelName() . ')';
		return $datas;
	}

	/**
	 * @param array[] $logsArray
	 * @return array
	 */
	protected function mergeLogs($logsArray)
	{
		$mergedLogs = array();
		foreach ($logsArray as $logs)
		{
			foreach ($logs as $log)
			{
				$mergedLogs[$log['entry_id']] = $log;
			}
		}
		return $this->sortLogs(array_values($mergedLogs));
	}

	/**
	 * @param array $logs
	 * @return array
	 */
	protected function sortLogs($logs)
	{
		usort($logs, array($this, 'compareLogEntries'));
		return $logs;
	}
	
	/**
	 * @param array $a
	 * @param array $b
	 * @return integer
	 */
	public function compareLogEntries($a, $b)
	{
		if ($a['entry_date'] == $b['entry_date'])
		{
			return (($a['entry_id'] > $b['entry_id']) ? -1 : 1); 
		}
		return (($a['entry_date'] > $b['entry_date']) ? -1 : 1);
	}
}