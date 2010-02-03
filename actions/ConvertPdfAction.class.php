<?php
class generic_ConvertPdfAction extends generic_Action
{
	private $configuration;
	
	public function _execute($context, $request)
	{
		$controller = $context->getController();
		
		$url = base64_decode($request->getParameter('url'));
		$url = str_replace('&amp;', '&', $url);
		
		$pdfFile = null;
		$pdfService = UrlPDFService::getInstance();
		
		$serverIp = $this->getConfigParameter('server_ip');
		if ($serverIp !== null)
		{
			$pdfService->setServerIP($serverIp);	
		}
		$serverPort = $this->getConfigParameter('server_port');
		if ($serverPort !== null)
		{
			$pdfService->setServerPort($serverPort);
		}

		try
		{
			$pdfConfiguration = $this->getConfiguration();
			$pdfService->setUserConnection($pdfConfiguration['user'])->setPasswordConnection($pdfConfiguration['password'])->setCustomerConnection($pdfConfiguration['customer']);
			
			$pdfService->setCachePath(WEBEDIT_HOME . DIRECTORY_SEPARATOR . MediaHelper::ROOT_MEDIA_PATH . CHANGE_CACHE_PDF);
			$pdfService->forceHTMLFormat();
			$pdfFile = $pdfService->getPDF($url);
		}
		catch (Exception $e)
		{
			echo f_Locale::translate("&framework.pdf.messages.error;");
		}
		
		if ($pdfFile !== null)
		{
			$controller->redirectToUrl("/publicmedia/" . CHANGE_CACHE_PDF . DIRECTORY_SEPARATOR . $pdfFile);
		}
		
		return View::NONE;
	}
	
	/**
	 * @param String $parameterName
	 * @param String $defaultValue
	 * @return String
	 */
	private function getConfigParameter($parameterName, $defaultValue = null)
	{
		try
		{
			$this->loadConfiguration();
			if (isset($this->configuration[$parameterName]))
			{
				return $this->configuration[$parameterName];
			}
		}
		catch (Exception $e)
		{
			// Nothing to do
		}
		return $defaultValue;
	}
	
	/**
	 */
	private function loadConfiguration()
	{
		try
		{
			if ($this->configuration === null)
			{
				$this->configuration = Framework::getConfiguration('pdf');
			}
		}
		catch (Exception $e)
		{
			Framework::exception($e->getMessage());
		}
	}
	
	/**
	 */
	private function getConfiguration()
	{
		$this->loadConfiguration();
		return $this->configuration;
	}
	
	public function getRequestMethods()
	{
		return Request::GET;
	}
	
	public function isSecure()
	{
		return false;
	}
}