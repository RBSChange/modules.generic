<?php
/**
 * @package modules.generic
 * @method generic_UserAclService getInstance()
 */
class generic_UserAclService extends f_persistentdocument_DocumentService
{
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
		return $this->getPersistentProvider()->createQuery('modules_generic/userAcl');
	}

	/**
	 * @param generic_persistentdocument_userAcl $document
	 * @param integer $parentNodeId
	 */
	protected function preSave($document, $parentNodeId)
	{
		$document->setLabel($document->getUser()->getId().'#'.$document->getRole().'#'.$document->getDocumentId());
	}

	/**
	 * @param generic_persistentdocument_userAcl $document
	 * @param integer $parentNodeId
	 */
	protected function postSave($document, $parentNodeId)
	{
		$this->getPersistentProvider()->compileACL($document);
	}
}