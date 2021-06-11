<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="strp-banner strp-banner-left <?if($arParams['MODE']=='adv'):?>strp-banner-mark-palce<?endif;?> <?=$arParams['CLASS']?>">
<?if($arParams['MODE']=='adv'):?>	
	<?=$arResult['BANNERPACE_INFO']?>	
<?else:?>
	<?=$arResult["BANNER"]['HTML'];?>
<?endif;?>
</div>