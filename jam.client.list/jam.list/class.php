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

class JamClientList extends \CBitrixComponent
{
    // const DEF_CACHE_ID = 'info_page';
    // const DEF_CACHE_TIME = 2592000; // 30 дней
    // const CACHE_PATH = '/kruche.info_page';
    
    // protected $_nLangCode = 0;
    
    protected function _getData()
    {

        
        \CModule::IncludeModule("iblock");

        $arData = [];
        $arClientIn = [];
        $arFilter = [
	        'IBLOCK_ID' => 5, 'ACTIVE' => 'Y',
        ];
        $arSelect = [
            "ID", "NAME", "CODE"
        ];

        $rs = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ( $element = $rs->GetNextElement() ) {
            $arFields = $element->GetFields();
            $arStatusTask[$arFields["ID"]] = array( "NAME" => $arFields["NAME"], "CODE" => $arFields["CODE"] , "ID" => $arFields["ID"] );
        }


        $arData = [];
        $arFilter = [
	        'IBLOCK_ID' => 6, 'ACTIVE' => 'Y',
        ];
        $arSelect = [
            "ID", "NAME",
        ];

        $rs = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ( $element = $rs->GetNextElement() ) {
            $arFields = $element->GetFields();
            $arManager[$arFields["ID"]] = array( "NAME" => $arFields["NAME"] );
        }

        $arData = [];
        $arFilter = [
	        'IBLOCK_ID' => 7, 'ACTIVE' => 'Y', "PROPERTY_MANAGERID" => $_GET["manager"]
        ];
        $arSelect = [
            "ID", "NAME", "PROPERTY_MANAGERID",
        ];

        $rs = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ( $element = $rs->GetNextElement() ) {
            $arFields = $element->GetFields();
            $arClientIn[] = $arFields["ID"];
            $arClient[$arFields["ID"]] = array( "NAME" => $arFields["NAME"], "MANAGERID" => $arFields["~PROPERTY_MANAGERID_VALUE"] ,"MANAGERNAME" => $arManager[$arFields["~PROPERTY_MANAGERID_VALUE"]] );
        }

        $arData = [];
        $arFilter = [
	        'IBLOCK_ID' => $this->arParams["IBLOCK_ID"], 'ACTIVE' => 'Y',
        ];
        $arSelect = [
            "ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PREVIEW_TEXT", "DETAIL_TEXT", "PROPERTY_PRICE", "PROPERTY_CLIENT_ID", "PROPERTY_STATUSID", "PROPERTY_NUMBER",
        ];
        $rs = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ( $element = $rs->GetNextElement() ) {
            $arFields = $element->GetFields();
            $arProps = $element->GetProperties();

            $arDataClientFull[] = $arFields["~PROPERTY_CLIENT_ID_VALUE"];

        }
           $arDataClientCountTask = array_count_values($arDataClientFull);

        $arData = [];
        $arFilter = [
	        'IBLOCK_ID' => $this->arParams["IBLOCK_ID"], 'ACTIVE' => 'Y', 'PROPERTY_STATUSID' => $_GET["status"]
        ];
        $arSelect = [
            "ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PREVIEW_TEXT", "DETAIL_TEXT", "PROPERTY_PRICE", "PROPERTY_CLIENT_ID", "PROPERTY_STATUSID", "PROPERTY_NUMBER",
        ];

        $rs = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ( $element = $rs->GetNextElement() ) {
            $arFields = $element->GetFields();
            $arProps = $element->GetProperties();
            $arImg = [
                "DETAIL_PICTURE_SRC" => CFile::GetPath($arFields["PREVIEW_PICTURE"]),
                "PREVIEW_PICTURE_SRC" => CFile::GetPath($arFields["DETAIL_PICTURE"]),
            ];
            $arData = [
                "CLIENT" =>  $arClient[$arFields["~PROPERTY_CLIENT_ID_VALUE"] ], 
                "CLIENT_ID" => $arFields["~PROPERTY_CLIENT_ID_VALUE"], 
                "STATUS_TASK" => $arStatusTask[$arFields["~PROPERTY_STATUSID_VALUE"]], 
            ]; 
            if( in_array( $arFields["PROPERTY_CLIENT_ID_VALUE"], $arClientIn ) ){
                $resultAdd[] = array_merge(
                    $arFields, 
                    $arProps, 
                    $arImg, 
                    $arData,
                );
            }
        }
        foreach($resultAdd as $ar) { 
            $resultData[] = array( 
                "ID" => $ar["ID"],
                "ID_CLIENT" => $ar["CLIENT_ID"],
                "NAME" => $ar["NAME"],
                "NUMBER_TASK" => $ar["~PROPERTY_NUMBER_VALUE"],
                "PRICE_TASK" => $ar["~PROPERTY_PRICE_VALUE"],
                "CLIENT_NAME" => $ar["CLIENT"]["NAME"],
                "MANAGER_NAME" => $ar["CLIENT"]["MANAGERNAME"]["NAME"],
                "STATUS_TASK" => $ar["STATUS_TASK"]["NAME"],
                "MANAGER_ID" => $ar["CLIENT"]["MANAGERID"],
                "STATUS_ID" => $ar["STATUS_TASK"]["ID"],
                "COUNT_TASK" => $arDataClientCountTask[$ar["CLIENT_ID"]],
            );
        }
        //$resultData["task"] = $resultAdd;
        $arIdClient = [];
        foreach($resultData as $ar) {
          if( !in_array($ar["ID_CLIENT"],$arIdClient) ){
            $arIdClient[] = $ar["ID_CLIENT"];
          }
        }
        foreach( $arIdClient as $ar ) {
          $f = 0;
          $p = 0;
          foreach($resultData as $art) {
            if( $ar == $art["ID_CLIENT"]){
              if( $art["STATUS_TASK"] == "В процессе" ){
                $p = $p + $art["PRICE_TASK"];
              }
              if( $art["STATUS_TASK"] == "Выполнено" ){
                $f = $f + $art["PRICE_TASK"];
              }
            }
              $CLIENT_NAME = $art["CLIENT_NAME"];
              $COUNT_TASK = $art["COUNT_TASK"];
          }
          $arNew[] = array(
            "ID" => $ar,
            "STATUS_TASK_P" => $p,
            "STATUS_TASK_F" =>  $f,
            "CLIENT_NAME" => $CLIENT_NAME,
            "COUNT_TASK" => $COUNT_TASK,
          );
        }


        $resultDataFull["task"] = $resultData;
        $resultDataFull["client"] = $arNew;
        return $resultDataFull;
    }
    

    // protected function _addData()
    // {
    //     global $USER;
    //     \CModule::IncludeModule("iblock");
    //     $imgTF = false;
    //     if (!empty($_FILES) && !empty($_FILES)) { 
    //             $imgTF = true;
    //             $arFiles = $_FILES;
    //             $ind = 0;
    //             foreach( $arFiles as   $arF){
    //                 $name_file = $arF['files'.$ind]['name'];
    //                 $uploads_dir = $_SERVER['DOCUMENT_ROOT'].'/tmp_img';
    //                 $is_moved = move_uploaded_file($arF['files'.$ind]['tmp_name'], "$uploads_dir/$name_file");
    //             }
    //     }
    //     $el = new CIBlockElement;
    //     $arLoadProductArray = Array(
    //       "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
    //       "IBLOCK_ID"      => $this->arParams["IBLOCK_ID"],
    //       "NAME"           => $this->arParams["TEXT_NAME"],
    //       "ACTIVE"         => "Y",
    //       "PREVIEW_TEXT"   => $this->arParams["TEXT_TEXT"],
    //       "DETAIL_TEXT"    => $this->arParams["TEXT_TEXT"],
    //       "DETAIL_PICTURE" => CFile::MakeFileArray( $uploads_dir."/".$name_file),
    //       "PREVIEW_PICTURE" => CFile::MakeFileArray( $uploads_dir."/".$name_file),
    //       );

    //     if($PRODUCT_ID = $el->Add($arLoadProductArray))
    //         return "New ID: ".$PRODUCT_ID;
    //     else
    //         return "Error: ".$el->LAST_ERROR;
    // }
    


    
    public function executeComponent()
    {

        //if($this->arParams["TYPE_METHOD"] === "GET"){
        // $data = FALSE;
        // $cache_id = "cache_jam_".$_GET["id"];
        // $cach_path = "cache_jam_".$_GET["id"];
        
        // $obCache = new CPHPCache;
        // if ( 3600 > 0 && $obCache->InitCache(3600, $cache_id, $cach_path) ) {
        //     $res = $obCache->GetVars();
        //     if ( isset($res['data']) ) {
        //         $data = $res['data'];
        //     }
        // }
        // if ( !$data )
        // {
        // $data = $this->_getData();
        //     if ( $data && is_array($data) && 3600 > 0 ) {
        //         $obCache->StartDataCache(3600, $cache_id, $cach_path);
        //         $obCache->EndDataCache(array('data' => $data));
        //     }
        // }
        $data = $this->_getData();
        $this->arResult = $data;
        //}

        // if($this->arParams["TYPE_METHOD"] === "POST"){
        //     $this->arResult = $this->_addData();
        // }

        
        
        $this->IncludeComponentTemplate();
    }
}
?>