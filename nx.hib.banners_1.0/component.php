<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$requiredModules = array('highloadblock');

foreach ($requiredModules as $requiredModule) {
	if (!CModule::IncludeModule($requiredModule)) {
		ShowError(GetMessage('F_NO_MODULE'));
		return 0;
	}
}

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;


if($arParams['BANNERPLACE_SRC']){
	$arParams['BANNERPLACE_SRC'] = intval($arParams['REGION_ID']);
}
else $arResult['ERRORS'][] = GetMessage('BHIB_NO_BPL');

if($arParams['BANNERPLACE_ID']){
	$arParams['BANNERPLACE_ID'] = intval($arParams['BANNERPLACE_ID']);
}
else $arResult['ERRORS'][] = GetMessage('BHIB_NO_BPL_ID');

if($arParams['BANNERPLACE_FIELD']){
	$arParams['BANNERPLACE_FIELD'] = trim($arParams['BANNERPLACE_FIELD']);
}
else $arResult['ERRORS'][] = GetMessage('BHIB_NO_BPL_FIELD');

if($arParams['BANNER_SRC']){
	$arParams['BANNER_SRC'] = intval($arParams['BANNER_SRC']);
}
else $arResult['ERRORS'][] = GetMessage('BHIB_NO_BPL_SRC');


if($arParams['MODE']){
	$arParams['MODE'] = trim($arParams['MODE']);
}

if($arParams['CLASS']){
	$arParams['CLASS'] = trim($arParams['CLASS']);
}

if(count($arResult['ERRORS'] == 0)) {

	$hlblock_id = intval($arParams['BANNER_SRC']);
	$hlblock = HL\HighloadBlockTable::getById($hlblock_id)->fetch();
	$entity = HL\HighloadBlockTable::compileEntity($hlblock);

	$dbFieldRes = CUserTypeEntity::GetList(array('ID'=>'ASC'), array('FIELD_NAME' => 'UF_TYPE', "ENTITY_ID" => "HLBLOCK_".$hlblock_id));

	if($arFieldRes = $dbFieldRes->GetNext()) {
		$arResult['TYPE'] = CUserTypeEntity::GetByID($arFieldRes['ID']); 		
	};

	$dbFieldRes = CUserTypeEntity::GetList(array('ID'=>'ASC'), array('FIELD_NAME' => $arParams['BANNERPLACE_FIELD'], "ENTITY_ID" => "HLBLOCK_".$hlblock_id));
	if($arFieldRes = $dbFieldRes->GetNext()) {
		$arResult['BANNERPACE'] = CUserTypeEntity::GetByID($arFieldRes['ID']); 

		if($arResult['BANNERPACE']['USER_TYPE_ID'] == 'hlblock') {

			$hlblockbp = HL\HighloadBlockTable::getById($arResult['BANNERPACE']['SETTINGS']['HLBLOCK_ID'])->fetch();
			$entitybp = HL\HighloadBlockTable::compileEntity($hlblockbp);

			$query = new Entity\Query($entitybp);
			$query->setSelect(array('*'));
			$query->setFilter(array('=ID' => $arParams['BANNERPLACE_ID']));

			$dbBannerPlace = $query->exec();
			$dbBannerPlace = new CDBResult($dbBannerPlace);

			if($arResult['BANNERPACE']['INFO'] = $dbBannerPlace->Fetch()) {
				$arResult['BANNERPACE_INFO'] = $arResult['BANNERPACE']['INFO']['UF_NAME'];
			}
		}		
	}

	if(!$arResult['BANNERPACE_INFO']) $arResult['BANNERPACE_INFO'] = 'Баннерное место';

	$arFilterBanners = array('='.$arParams['BANNERPLACE_FIELD'] => $arParams['BANNERPLACE_ID'], '=UF_ACTIVE' => 1);
	
	$query = new Entity\Query($entity);
	$query->setSelect(array('*'));
	$query->setFilter($arFilterBanners);
	$query->registerRuntimeField(
	  'RAND', array('data_type' => 'float', 'expression' => array('RAND()'))
	);
	$query->addOrder('RAND', 'ASC');
	$dbBanner = $query->exec();
	$dbBanner = new CDBResult($dbBanner);
	
	if ($arBanner = $dbBanner->Fetch()) {
		$arResult['BANNER'] = $arBanner;

		$dbTupeValue = CUserFieldEnum::GetList(array(), array("ID" => $arResult['BANNER']['UF_TYPE'], "USER_FIELD_ID"=>$arResult['TYPE']['ID']));
		$arType = $dbTupeValue->GetNext();

		$arResult['BANNER']['TYPE'] = $arType['XML_ID'];

		switch ($arResult['BANNER']['TYPE']) {
			case 'img':
				if($arResult['BANNER']['UF_IMG_BANNER']) {
					$arResult['BANNER']['IMG'] = CFile::GetFileArray($arResult['BANNER']['UF_IMG_BANNER']);

					$arResult['BANNER']['HTML'] = '<img src="'.$arResult['BANNER']['IMG']['SRC'].'" alt="'.$arResult['UF_NAME'].'" class="nx-banner-img" />';
				}

				break;
			
			case 'html':
				if($arResult['BANNER']['UF_HTML']) {
					$arResult['BANNER']['HTML'] = $arResult['BANNER']['UF_HTML'];	
				}
				break;

			default:
				$arResult['BANNER']['HTML'] = '';
				break;
		}

		if($arResult['BANNER']['UF_LINK']) 
			$arResult['BANNER']['HTML'] .= '<a href="'.$arResult['BANNER']['UF_LINK'].'" target="'.$arResult['BANNER']['UF_TAGET'].'" class="nx-banner-link">'.$arResult['BANNER']['UF_NAME'].'</a>';
	}	
}

$this->IncludeComponentTemplate();