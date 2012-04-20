<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Christian de la Haye 2012
 * @author     Christian de la Haye 
 * @package    tabimporter 
 * @license    LGPL
 * @filesource
 */

/**
 * Class tabImporter
 *
 * Provides methods used for table imports.
 * @copyright  Christian de la Haye 2012
 * @author     Christian de la Haye 
 * @package    tabimporter 
 */
class TabimporterSource_csv extends Backend
{

	/**
	 * Import classes
	 */
	public function __construct()
	{
		$this->import('String');
	}

	/**
	 * read complete csv data
	 */
	public function getAllData($arrTableimport)
	{
		if(!is_file(TL_ROOT . '/' . $arrTableimport['sourceFile']))
		{
			return false;
		}
		
		$objFile = new File($arrTableimport['sourceFile']);

		switch($arrTableimport['fieldDelimiter'])
		{
			case 'semicolon':
				$strDelimiter = ';';
				break;
			case 'comma':
				$strDelimiter = ',';
				break;
			case 'tab':
				$strDelimiter = '\t';
				break;
			default:
				$strDelimiter = ';';
				break;
		}

		if($objFile)
		{
			$arrContent = $objFile->getContentAsArray();

			for($i=0;$i < sizeof($arrContent); $i++)
			{
				$arrContent[$i] = $this->String->splitCsv($arrContent[$i], $strDelimiter);
				
				// get field names ba first line
				if($arrTableimport['hasFieldnames'])
				{
					for($ii=0;$ii<sizeof($arrContent[$i]);$ii++)
					{
						$arrContent[$i][$arrContent[0][$ii]] = $arrContent[$i][$ii];
						if($i>0)
						{
							unset($arrContent[$i][$ii]);
						}
					}
				}

			}

			if($arrTableimport['hasFieldnames'])
			{
				array_shift($arrContent);
			}

			return $arrContent;
		}

		return false;		
	}


	/**
	 * get keys of the source
	 */
	public function getExistentKeysSource($arrTableimport)
	{
		$arrKeys = array();
		if(!$_SESSION['tl_tabimporter']['arrAllData'])
		{
			return false;
		}

		foreach($_SESSION['tl_tabimporter']['arrAllData'] as $arrData)
		{
			$arrKeys[] = 
				$arrData[($arrTableimport['hasFieldnames'] ? 
				$arrTableimport['uniqueSource'] : 
				str_replace('field_','',$arrTableimport['uniqueSource']))];
		}

 		if(!$arrKeys || count($arrKeys)<1)
		{
			return false;
		}

		return $arrKeys;
	}

}

?>