<?php
class generic_UserAclService extends f_persistentdocument_DocumentService
{

	/**
	 * @var generic_UserAclService
	 */
	private static $instance;

	/**
	 * @return generic_UserAclService
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
	 * @return generic_persistentdocument_userAcl
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_generic/userAcl');
	}

	/**
	 * Create a query based on 'modules_generic/userAcl' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_generic/userAcl');
	}

	/**
	 * @param generic_persistentdocument_userAcl $document
	 * @param Integer $parentNodeId
	 */
	protected function preSave($document, $parentNodeId = null)
	{
		$document->setLabel($document->getUser()->getId().'#'.$document->getRole().'#'.$document->getDocumentId());
	}

	/**
	 * @param generic_persistentdocument_userAcl $document
	 * @param Integer $parentNodeId
	 */
	protected function postSave($document, $parentNodeId = null)
	{
		$this->pp->compileACL($document);
	}
}