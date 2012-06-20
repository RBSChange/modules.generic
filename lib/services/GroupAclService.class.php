<?php
/**
 * @package modules.generic
 * @method generic_GroupAclService getInstance()
 */
class generic_GroupAclService extends f_persistentdocument_DocumentService
{
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
		return $this->getPersistentProvider()->createQuery('modules_generic/groupAcl');
	}

	/**
	 * @param generic_persistentdocument_groupAcl $document
	 * @param integer $parentNodeId
	 */
	protected function preSave($document, $parentNodeId)
	{
		$document->setLabel($document->getGroup()->getId().'#'.$document->getRole().'#'.$document->getDocumentId());
	}

	/**
	 * @param generic_persistentdocument_groupAcl $document
	 * @param integer $parentNodeId
	 */
	protected function postSave($document, $parentNodeId)
	{
		$this->getPersistentProvider()->compileACL($document);
	}
}