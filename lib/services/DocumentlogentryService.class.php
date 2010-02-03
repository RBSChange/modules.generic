<?php
class generic_DocumentlogentryService extends f_persistentdocument_DocumentService
{
	/**
	 * @var generic_DocumentlogentryService
	 */
	private static $instance;

	/**
	 * @return generic_DocumentlogentryService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

	/**
	 * @return generic_persistentdocument_documentlogentry
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_generic/documentlogentry');
	}

	/**
	 * Create a query based on 'modules_generic/documentlogentry' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_generic/documentlogentry');
	}

	/**
	 * Add new log entry.
	 * @param integer $documentId
	 * @param string $actorName
	 * @param string $actionLabel
	 * @param string $decision
	 * @param string $commentary
	 * @return boolean true if the log entry is correctly added, false else.
	 */
	public function addLogEntry($documentId, $actorName, $actionLabel, $decision = '', $commentary = '')
	{
		// TODO : test
		if (empty($documentId) || empty($actorName) || empty($actionLabel))
		{
			if (Framework::isDebugEnabled())
			{
				Framework::debug(__METHOD__ . ' : documentId, actorname and actionLabel are mandatory !');
			}
			return false;
		}

		$logEntry = $this->getNewDocumentInstance();
		$logEntry->setDocumentid($documentId);
		$logEntry->setActor($actorName);
		$logEntry->setLabel($actionLabel);
		if (!empty($decision))
		{
			$logEntry->setDecision($decision);
		}
		if (!empty($commentary))
		{
			$logEntry->setCommentary($commentary);
		}
		$logEntry->save();
		return true;
	}

	/**
	 * Get all log entry associated to the given document ordered by creation date.
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param string $order "ASC" or "DESC".
	 * @return generic_persistentdocument_documentlogentry
	 */
	public function getLogsForDocument($document, $order = 'ASC')
	{
		$this->getLogsForDocumentId($document->getId(), $order);
	}

	/**
	 * Get all log entry associated to the given document ordered by creation date.
	 * @param integer $documentId
	 * @param string $order "ASC" or "DESC".
	 * @return generic_persistentdocument_documentlogentry
	 */
	public function getLogsForDocumentId($documentId, $order = 'ASC')
	{

		$query = $this->createQuery()->add(Restrictions::eq('documentid', $documentId));

		if ($order == 'ASC')
		{
			$query->addOrder(Order::asc('document_creationdate'));
		}
		else
		{
			$query->addOrder(Order::desc('document_creationdate'));
		}

		return $query->find();
	}
}