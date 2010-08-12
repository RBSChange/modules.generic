<?php
class generic_GroupAclService extends f_persistentdocument_DocumentService
{
	/**
	 * @var generic_GroupAclService
	 */
	private static $instance;

	/**
	 * @return generic_GroupAclService
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
	 * @return generic_persistentdocument_groupAcl
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_generic/groupAcl');
	}

	/**
	 * Create a query based on 'modules_generic/groupAcl' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_generic/groupAcl');
	}

	/**
	 * @param generic_persistentdocument_groupAcl $document
	 * @param Integer $parentNodeId
	 */
	protected function preSave($document, $parentNodeId)
	{
		$document->setLabel($document->getGroup()->getId().'#'.$document->getRole().'#'.$document->getDocumentId());
	}

	/**
	 * @param generic_persistentdocument_groupAcl $document
	 * @param Integer $parentNodeId
	 */
	protected function postSave($document, $parentNodeId)
	{
		$this->pp->compileACL($document);
	}
}