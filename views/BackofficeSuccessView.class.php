<?php
class generic_BackofficeSuccessView extends f_view_BaseView
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$this->setTemplateName('Generic-Response', K::XML);
		$this->setStatus(self::STATUS_OK);

		if ($request->hasAttribute('message'))
		{
			$this->setAttribute('message', $request->getAttribute('message'));
		}

		if ($request->hasAttribute('document'))
		{
			$document = $request->getAttribute('document');
			$this->setAttribute('id', $document->getId());
			$this->setAttribute('label', '<![CDATA[' .$document->getLabel() . ']]>');
			if (method_exists($document, 'getLang'))
			{
				$this->setAttribute('lang', $document->getLang());
			}
		}
		else
		{
			$this->setAttribute('id', '0');
		}

		if ($request->hasAttribute(K::PARENT_ID_ACCESSOR))
		{
		    $this->setAttribute('parentref', $request->getAttribute(K::PARENT_ID_ACCESSOR));
		}

		$this->setAttribute('workinglang', RequestContext::getInstance()->getLang());
		$this->setAttribute('uilang', RequestContext::getInstance()->getUILang());

		if ($request->hasAttribute('contents'))
		{
			$this->setAttribute('contents', $request->getAttribute('contents'));
		}

		if ($request->hasAttribute('access'))
		{
			$this->setAttribute('access', $request->getAttribute('access'));
		}
	}
}