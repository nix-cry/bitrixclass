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

class JamCatalogList extends \CBitrixComponent
{
    // const DEF_CACHE_ID = 'info_page';
    // const DEF_CACHE_TIME = 2592000; // 30 дней
    // const CACHE_PATH = '/kruche.info_page';
    
    // protected $_nLangCode = 0;
    
    protected function _getData()
    {

        
        \CModule::IncludeModule("iblock");

        $arData = [];
        $arFilter = [
	        'IBLOCK_ID' => $this->arParams["IBLOCK_ID"], "ID" => $_GET["id"], 'ACTIVE' => 'Y',
        ];
        $arSelect = [
            "ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PREVIEW_TEXT", "DETAIL_TEXT"
        ];

        $rs = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
        while ( $element = $rs->GetNextElement() ) {
            $arFields = $element->GetFields();
            $arProps = $element->GetProperties();
            $arImg = [
                "DETAIL_PICTURE_SRC" => CFile::GetPath($arFields["PREVIEW_PICTURE"]),
                "PREVIEW_PICTURE_SRC" => CFile::GetPath($arFields["DETAIL_PICTURE"]),
            ];
        }
        $result = array_merge($arFields, $arProps, $arImg);
        return $result;
    }
    

    protected function _addData()
    {
        global $USER;
        \CModule::IncludeModule("iblock");
        $imgTF = false;
        if (!empty($_FILES) && !empty($_FILES)) { 
                $imgTF = true;
                $arFiles = $_FILES;
                $ind = 0;
                foreach( $arFiles as   $arF){
                    $name_file = $arF['files'.$ind]['name'];
                    $uploads_dir = $_SERVER['DOCUMENT_ROOT'].'/tmp_img';
                    $is_moved = move_uploaded_file($arF['files'.$ind]['tmp_name'], "$uploads_dir/$name_file");
                }
        }
        $el = new CIBlockElement;
        $arLoadProductArray = Array(
          "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
          "IBLOCK_ID"      => $this->arParams["IBLOCK_ID"],
          "NAME"           => $this->arParams["TEXT_NAME"],
          "ACTIVE"         => "Y",
          "PREVIEW_TEXT"   => $this->arParams["TEXT_TEXT"],
          "DETAIL_TEXT"    => $this->arParams["TEXT_TEXT"],
          "DETAIL_PICTURE" => CFile::MakeFileArray( $uploads_dir."/".$name_file),
          "PREVIEW_PICTURE" => CFile::MakeFileArray( $uploads_dir."/".$name_file),
          );

        if($PRODUCT_ID = $el->Add($arLoadProductArray))
            return "New ID: ".$PRODUCT_ID;
        else
            return "Error: ".$el->LAST_ERROR;
    }
    


    
    public function executeComponent()
    {

        if($this->arParams["TYPE_METHOD"] === "GET"){
            $data = FALSE;
            $cache_id = "cache_jam_".$_GET["id"];
            $cach_path = "cache_jam_".$_GET["id"];
            
            $obCache = new CPHPCache;
            if ( 3600 > 0 && $obCache->InitCache(3600, $cache_id, $cach_path) ) {
                $res = $obCache->GetVars();
                if ( isset($res['data']) ) {
                    $data = $res['data'];
                }
            }
            if ( !$data )
            {
            $data = $this->_getData();
                if ( $data && is_array($data) && 3600 > 0 ) {
                    $obCache->StartDataCache(3600, $cache_id, $cach_path);
                    $obCache->EndDataCache(array('data' => $data));
                }
            }
            $this->arResult = $data;
        }

        if($this->arParams["TYPE_METHOD"] === "POST"){
            $this->arResult = $this->_addData();
        }

        
        
        $this->IncludeComponentTemplate();
    }
}
?>