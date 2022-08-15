<?
if ( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE ) {
    die();
}

/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

/** @global CIntranetToolbar $INTRANET_TOOLBAR */
global $INTRANET_TOOLBAR;

use Bitrix\Main\Context,
	Bitrix\Main\Type\DateTime,
	Bitrix\Main\Loader,
	Bitrix\Iblock;

class CJamForm extends \CBitrixComponent
{
    // const DEF_CACHE_ID = 'info_page';
    // const DEF_CACHE_TIME = 2592000; // 30 дней
    // const CACHE_PATH = '/kruche.info_page';
    
    // protected $_nLangCode = 0;
    
    protected function _addData()
    {
        global $USER;
        \CModule::IncludeModule("iblock");

        $name_file = $_POST["exampleFile"];
        $uploads_dir = $_SERVER['DOCUMENT_ROOT'].'/tmp_img';
        $is_moved = move_uploaded_file($arF['files'.$ind]['tmp_name'], "$uploads_dir/$name_file");

        $el = new CIBlockElement;
        $PROP = array();
        $PROP["CATEG"] = $_POST["exampleCateg"];
        $PROP["VID_ZAV"] = $_POST["exampleVid"];
        $PROP["SCLAD"] = $_POST["selectSrlad"];
        $PROP["SOSTAV_BRAND"] = $_POST["inputBrand"];
        $PROP["SOSTAV_NAME"] = $_POST["inputName"];
        $PROP["SOSTAV_COUNT"] = $_POST["inputCount"];
        $PROP["SOSTAV_FASOVKA"] = $_POST["inputFasovka"];
        $PROP["SOSTAV_KLIENT"] = $_POST["inputklient"];
        $PROP["FILE_ADD"] = CFile::MakeFileArray( $uploads_dir."/".$name_file);
        $PROP["COMMENT"] = $_POST["textAreaComment"];


        $arLoadProductArray = Array(
          "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
          "IBLOCK_ID"      => $this->arParams["IBLOCK_ID"],
          "NAME"           => $_POST["nameElement"],
          "ACTIVE"         => "Y",
          "PREVIEW_TEXT"   => $this->arParams["TEXT_TEXT"],
          "DETAIL_TEXT"    => $this->arParams["TEXT_TEXT"],
          "DETAIL_PICTURE" => CFile::MakeFileArray( $uploads_dir."/".$name_file),
          "PREVIEW_PICTURE" => CFile::MakeFileArray( $uploads_dir."/".$name_file),
          "PROPERTY_VALUES"=> $PROP,  
        );

        if($PRODUCT_ID = $el->Add($arLoadProductArray))
            return ["yes" => true];
        else
            return ["yes" => false]; 

    }
    
    
    public function executeComponent()
    {

        $this->arResult = $this->_addData();
        
        $this->IncludeComponentTemplate();
    }
}
?>