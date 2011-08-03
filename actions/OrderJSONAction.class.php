<?php
/**
 * generic_OrderJSONAction
 * @package modules.generic.actions
 */
class generic_OrderJSONAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$result = array();
		
		if (!$request->hasParameter('co'))
		{
			return $this->sendJSONError(f_Locale::translate("&modules.generic.backoffice.OrderChildrenInvalidParametersErrorMessage;"));
		}
		$childrenOrder = array_flip($request->getParameter('co'));
		
		try
		{
			$parent = $this->getDocumentInstanceFromRequest($request);
			if ($request->hasParameter('relationName'))
			{
				$this->orderRelations($parent, $childrenOrder, $request->getParameter('relationName'));
			}
			else
			{
				$parentNode = TreeService::getInstance()->getInstanceByDocument($parent);
				TreeService::getInstance()->order($parentNode, $childrenOrder);
			}
		}
		catch (BaseException $e)
		{
			return $this->sendJSONError($e->getLocaleMessage());
		}
		catch (Exception $e)
		{
			return $this->sendJSONError($e->getMessage());
		}
		
		return $this->sendJSON($result);
	}
	
	/**
	 * @param f_persistentodcument_PersistentDocument $parent
	 * @param array<id=>order> $childrenOrder
	 * @param string $relationName
	 */
	private function orderRelations($parent, $childrenOrder, $relationName)
	{
		// Build an array with the actual children.
		// For each actual child, if a position exists in the desired order array this position overrides the current one.
		$actualChildArray = f_util_ClassUtils::callMethodOn($parent, 'get' . ucfirst($relationName) . 'Array');
		$actualChildrenOrder = array();
		$pos = 0;
		foreach ($actualChildArray as $child)
		{
			$id = $child->getId();
			if (isset($childrenOrder[$id]))
			{
				$actualChildrenOrder[$id] = $childrenOrder[$id];
			}
			else
			{
				$actualChildrenOrder[$id] = strval($pos);
			}
			$pos++;
		}
		
		// Sort the actual chidren by position (asort preserves the key associations).
		asort($actualChildrenOrder);
		
		// Remove all the relations.
		f_util_ClassUtils::callMethodOn($parent, 'removeAll' . ucfirst($relationName));
		
		// Recreate relations with the right order.
		$pos = 0;
		$setter = 'set' . ucfirst($relationName);
		foreach (array_keys($actualChildrenOrder) as $id)
		{
			$pos++;
			$parent->{$setter}($pos, DocumentHelper::getDocumentInstance($id));
		}
		
		// Save the document.
		$parent->save();
	}
}