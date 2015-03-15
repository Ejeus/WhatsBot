<?php
	require_once 'Lang.php';

	require_once 'Others/Std.php';
	
	require_once 'Others/Command.php';
	require_once 'Others/Unirest.php';


	const WARNING_NOT_LOADED = -1;
	const WARNING_GET_ERROR = -2;
	const WARNING_NOT_FILE = -3;
	const WARNING_LANG_ERROR = -4;

	const SEND_USAGE = 2;
	
	const INTERNAL_ERROR = false;


	trait ModuleManagerCaller
	{
		private function CallModule($Key, $ModuleName, Array $Params)
		{
			if($this->WhatsBot->GetStartTime() > intval($Params['Time']))
				return true;

			if($this->ModuleExists($Key, $ModuleName))
			{
				$Module = $this->GetModule($Key, $ModuleName);

				if($Module !== false)
				{
					if(is_readable($Module['Path']))
					{
						$LangSection = "{$Key}_{$Module['File']}";

						$this->WhatsApp->SetLangSection($LangSection);
						$Lang = new Lang($LangSection);
						
						$WhatsApp = $this->WhatsApp;
						$ModuleManager = $this;

						extract($Params);

						return include $Module['Path'];
					}

					Std::Out("[WARNING] [MODULES] Can't call {$Key}::{$ModuleName}. PHP file is not readable");

					return WARNING_NOT_FILE;
				}

				Std::Out("[WARNING] [MODULES] Can't call {$Key}::{$ModuleName}. Get error (not exists?)");

				return WARNING_GET_ERROR;
			}

			// This will be parsed (WhatsBotParser->SendResponse($Code)), so we don't need to test if module exists before calling

			return WARNING_NOT_LOADED;
		}

		public function CallCommandModule($ModuleName, $Me, $From, $User, $ID, $Type, $Time, $Name, $Text, $Params)
		{
			return $this->CallModule('Command', $ModuleName, array
			(
				'Me' => $Me,
				'From' => $From,
				'User' => $User,
				'ID' => $ID,
				'Type' => $Type,
				'Time' => $Time,
				'Name' => $Name,
				'Text' => $Text,
				'Params' => $Params
			));
		}

		public function CallDomainModule($Domain, $Me, $From, $User, $ID, $Type, $Time, $Name, $Text, $URL)
		{
			return $this->CallModule('Domain', $Domain, array
			(
				'Me' => $Me,
				'From' => $From,
				'User' => $User,
				'ID' => $ID,
				'Type' => $Type,
				'Time' => $Time,
				'Name' => $Name,
				'Text' => $Text,
				'URL' => $URL,
				'Domain' => $Domain
			));
		}

		public function CallExtensionModule($Extension, $Me, $From, $User, $ID, $Type, $Time, $Name, $Text, $URL)
		{
			return $this->CallModule('Extension', $Extension, array
			(
				'Me' => $Me,
				'From' => $From,
				'User' => $User,
				'ID' => $ID,
				'Type' => $Type,
				'Time' => $Time,
				'Name' => $Name,
				'Text' => $Text,
				'URL' => $URL,
				'Extension' => $Extension
			));
		}

		public function CallMediaModule($Type, $Me, $From, $User, $ID, $Time, $Name, Array $Data)
		{
			$Data = array_merge($Data, array
			(
				'Me' => $Me,
				'From' => $From,
				'User' => $User,
				'ID' => $ID,
				'Time' => $Time,
				'Name' => $Name
			));

			return $this->CallModule('Media', $Type, $Data);
		}
	}