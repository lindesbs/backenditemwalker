<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

class backenditemwalker extends BackendModule
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = '';

	public $strBackendButtons;



	/**
	 * Generate module
	 */
	protected function compile()
	{

	}

	private function decodeRTContent($matches)
	{
		if (!$matches[0])
		{
			break;
		}

		return $matches[0].$this->strBackendButtons;
	}





	public function parseBackendTemplate($strContent, $strTemplate)
	{

		$strTable =  $this->Input->get("table");
		$intId =  $this->Input->get("id");
		$myAct =  $this->Input->get("act");

		if (($strTable) && ($myAct=='edit') && ($GLOBALS['TL_DCA'][$strTable]['config']['dataContainer'] == 'Table'))
		{
		// ItemSiwtcher next/previous
			$itemSwitcher ='';

			$sqlPID='';
			if ($this->Database->fieldExists('pid', $strTable))
			{
				$sqlPID = $this->Database->prepare("SELECT pid FROM `".$strTable."` WHERE id=?")
							->limit(1)
							->execute($intId);

				$sqlPID = sprintf("WHERE pid='%s'",$sqlPID->pid);
			}

			$sqlSorting='';
			if ($this->Database->fieldExists('sorting', $strTable))
			{

				$sqlSorting = " ORDER BY sorting";
			}


			$sqlItems = $this->Database->prepare("SELECT * FROM `".$strTable."` ".$sqlPID.$sqlSorting)
							->execute();

			$pos=0;
			$previousID=false;
			$nextID=false;
			$previousDummyID=false;
			$itemFound = false;

			while ($sqlItems->next())
			{

				//echo " ".$sqlItems->id;
				if ($itemFound)
				{
					$nextID = $sqlItems->id;
					$itemFound=false;
				}

				if ($sqlItems->id == $intId)
				{
					$itemFound = true;

					if ($previousDummyID!==false)
					{
						$previousID = $previousDummyID;
					}
				}

				$previousDummyID = $sqlItems->id;
			}

			
			if ($previousID!==false)
			{
				$strUrl = preg_replace('/&id=(.*)/i', '&id='.$previousID,$this->Environment->request);
				$itemSwitcher .='<a href="'.$strUrl.'" class="header_previousitem" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['previousitem']).'" onclick="Backend.getScrollOffset();" accesskey="a">'.specialchars($GLOBALS['TL_LANG']['MSC']['previousitem']).'</a>';
			}

			if ($nextID!==false)
			{
				$strUrl = preg_replace('/&id=(.*)/i', '&id='.$nextID,$this->Environment->request);
				
				$itemSwitcher .='<a href="'.$strUrl.'" class="header_nextitem" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['nextitem']).'" onclick="Backend.getScrollOffset();" accesskey="d">'.specialchars($GLOBALS['TL_LANG']['MSC']['nextitem']).'</a>';
			}
		}

		$regEx = '!<div[ ]id="tl_buttons">(.*)!ix';

		$this->strBackendButtons = $itemSwitcher;
		$strContent = preg_replace_callback($regEx, array ($this,"decodeRTContent"), $strContent);

		return $strContent;
	}

}