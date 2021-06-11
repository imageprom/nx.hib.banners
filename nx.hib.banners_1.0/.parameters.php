<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


if(!CModule::IncludeModule("iblock")) return;
if(!CModule::IncludeModule("highloadblock")) return;


use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Highloadblock as HL;

$rsHIBlock = HL\HighloadBlockTable::getList(array('select'=>array('*'), 'filter'=>array('!=TABLE_NAME' => '')));
while($arr = $rsHIBlock->Fetch()) {$arHIBlock[$arr['ID']] = $arr['NAME']; $arHIBlocks[$arr['ID']] = $arr;}

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"BANNERPLACE_SRC" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage('HLLIST_COMPONENT_BLOCK_ID_PARAM'),
			"TYPE"=>"LIST",
			"VALUES"=>$arHIBlock,
			"DEFAULT"=>'1',
			"MULTIPLE"=>"N",
			"ADDITIONAL_VALUES"=>"N",
			"REFRESH" => "Y",
		),

		"BANNERPLACE_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage('HLLIST_COMPONENT_BLOCK_ID'),
			"TYPE" => "STRING"
		),
		
		"BANNER_SRC" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage('HLLIST_COMPONENT_HBLOCK_OBJECT'),
			"TYPE"=>"LIST",
			"VALUES"=>$arHIBlock,
			"DEFAULT"=>'1',
			"MULTIPLE"=>"N",
			"ADDITIONAL_VALUES"=>"N",
			"REFRESH" => "Y",
			
		),		
		
		"BANNERPLACE_FIELD" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage('HLLIST_COMPONENT_BANNERPLACE_FIELD'),
			"TYPE" => "STRING"
		),			
			
		"MODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage('HLLIST_COMPONENT_MODE'),
			"TYPE" => "STRING"
		),

		"CLASS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage('HLLIST_COMPONENT_CLASS'),
			"TYPE" => "STRING"
		),			

	),
);