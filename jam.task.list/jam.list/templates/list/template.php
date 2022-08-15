<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<?php 
    use Bitrix\Main\UI\Extension;
    Extension::load('ui.bootstrap4');

// print_r($arResult);
// echo "<br><br>";
// die( json_encode( $arResult ) );


// foreach($arResult as $ar) { 
//     "ID" =>
//     "NAME" =>
//     "NUMBER_TASK" =>
//     "PRICE_TASK" =>
//     "CLIENT_NAME" =>
//     "MANAGER_NAME" =>
//     "STATUS_TASK" =>
// }


// $result = array_filter($arResult, function ($item) use ($array2) {
//   return in_array($item['id'], $array2);
// });
// $arResultData = $arResult;
// $arFilter = ['Выполнено'];
// // $arResult = var_export(
// //     array_filter($arResultData, function($row) use ($arFilter) {
// //         return $arFilter == array_intersect($needles, $row);
// //     })
// // );

// $needles = [ 'Менеджер1', 'Менеджер3','Выполнено'];
// $output = [];
// array_walk($arResultData, function($element) use ($needles, &$output) {
//     $matches = true;
//     foreach ($needles as $needle) {
//         if (!in_array($needle, $element)) {
//             $matches = false;
//         }
//     }
//     if ($matches) {
//         $output[] = $element;
//     }
// });
// $arIdClient = [];
// foreach($arResult as $ar) {
//   if( !in_array($ar["ID_CLIENT"],$arIdClient) ){
//     $arIdClient[] = $ar["ID_CLIENT"];
//   }
// }

// foreach( $arIdClient as $ar ) {
//   foreach($arResult as $art) {
//     if(  )
//   }
// }

?>

<table class="table">
    <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Задача</th>
          <th scope="col">Номер</th>
          <th scope="col">Стоимость задачи</th>
          <th scope="col">Клиент</th>
          <th scope="col">Менеджер</th>
          <th scope="col">Статус</th>
        </tr>
    </thead>
      <tbody>
        <?php foreach($arResult["task"] as $ar) { ?>
        <tr>
            <th scope="col"><?= $ar["ID"] ?></th>
            <th scope="col"><?= $ar["NAME"] ?></th>
            <th scope="col"><?= $ar["NUMBER_TASK"] ?></th>
            <th scope="col"><?= $ar["PRICE_TASK"] ?> </th>
            <th scope="col"><?= $ar["CLIENT_NAME"] ?></th>
            <th scope="col"><?= $ar["MANAGER_NAME"]?></th>
            <th scope="col"><?= $ar["STATUS_TASK"] ?></th>
      </tr>
      <?php } ?>
    </tbody>
</table>