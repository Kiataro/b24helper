<?php
namespace App;

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Iblock\IblockTable;

\Bitrix\Main\Loader::includeModule('iblock');

class Helper
{

    /*-- get Article --*/

    public static function getArticle($id) {

        $table = '\Bitrix\Iblock\Elements\ElementNotesTable';

        $dbNote = $table::getList([
            'filter' => [
                'ID' => $id,
                'ACTIVE' => 'Y',
            ],
            'order' => ['SORT'=>'ASC' , 'ID' => 'DESC'],
            'select' => [
                'ID', 'NAME', 'PREVIEW_TEXT', 'IBLOCK_SECTION_ID', 'DATE_CREATE', 'IBLOCK_ID',
                'DATA_VAL' => 'DATA.VALUE',
                'EXAMPLE_VAL' => 'EXAMPLE.VALUE'
            ],
            "cache" => ["ttl" => 3600],
        ]);

        if($arNote = $dbNote->Fetch()) {

            $category = Helper::getCategoryById($arNote['IBLOCK_SECTION_ID'], $arNote['IBLOCK_ID']);

            return [
                'id' => $arNote['ID'],
                'title' => $arNote['NAME'],
                'description' => $arNote['PREVIEW_TEXT'],
                'categoryLabel' => $category['NAME'],
                'file' =>  \CFile::GetPath($arNote['EXAMPLE_VAL']),
                'date' =>  FormatDate("d.m.Y", MakeTimeStamp($arNote['DATE_CREATE'])),
                'content' => $arNote['DATA_VAL']
            ];

        }

    }

    /*-- add Article --*/

    public static function addArticle($data) {

        $el = new \CIBlockElement;

        $iblockId = Helper::getIblockIdByCode('notes');

        $elementData = [

            'NAME' => $data['title'],

            'IBLOCK_ID' => $iblockId,
            'IBLOCK_SECTION_ID' => $data['category'],

            'PROPERTY_VALUES'=> [
                'EXAMPLE' => $data['fileId']
            ],

            'ACTIVE' => 'Y',

            'PREVIEW_TEXT' => $data['subtitle']
        ];

        if($data['elements']) {
            $elementData['PROPERTY_VALUES']['DATA'] = json_encode($data['elements']);
        }

        $result = $el->Add($elementData);

        if ($result) {

            $response = [
                'status' => 'success',
                'message' => 'Статья успешно добавлена',
            ];

        } else {

            $errorMessage = $el->LAST_ERROR;

            $response = [
                'status' => 'error',
                'message' => 'Ошибка при добавлении статьи ' . $errorMessage,
            ];
        }

        return $response;

    }

    /*-- remove File --*/

    public static function removeFile($fileId) {

        \CFile::Delete($fileId);

        $response = [
            'status' => 'success',
            'message' => 'Файл успешно удален',
        ];

        return $response;

    }

    /*-- load File --*/

    public static function loadFile($file) {

        $arFile = $file['file'];
        $arFile['MODULE_ID'] = 'main';

        $fileId = \CFile::SaveFile($arFile, "upload");

        if ($fileId) {
            $filePath = \CFile::GetPath($fileId);

            $response = [
                'status' => 'success',
                'fileSrc' => 'https://'.$_SERVER['HTTP_HOST'].$filePath,
                'fileId' => $fileId
            ];

        } else {

            $response = [
                'status' => 'error',
                'message' => 'Ошибка сохранения файла'
            ];

        }

        return $response;
    }

    /*-- get all Categories --*/

    public function getAllCategories() {

        $iblockId = Helper::getIblockIdByCode('notes');

        $entity = \Bitrix\Iblock\Model\Section::compileEntityByIblock($iblockId);

        $dbSection = $entity::getList([
            "select" => ['ID', 'NAME', 'CODE', 'UF_COLOR'],
            "filter" => [],
            "cache"  => ['ttl' => 3600],
        ]);

        $data = [];

        while ($arSection = $dbSection->fetch()) {

            $data[] = [
                'id' => $arSection['ID'],
                'label' => $arSection['NAME'],
                'code' => $arSection['CODE'],
                'color' => $arSection['UF_COLOR']
            ];
        }

        return $data;

    }

    /*-- get Category --*/

    public static function getCategoryById($id, $iblock_id) {

        $entity = \Bitrix\Iblock\Model\Section::compileEntityByIblock($iblock_id);

        $dbSection = $entity::getList([
            "select" => [ 'ID', 'NAME', 'CODE', 'UF_COLOR'],
            "filter" => ['ID' => $id],
            "cache"  => ['ttl' => 3600],
        ]);

        if ($arSection = $dbSection->fetch()) {

            return [
                'ID' => $arSection['ID'],
                'NAME' => $arSection['NAME'],
                'CODE' => $arSection['CODE'],
                'COLOR' => $arSection['UF_COLOR']

            ];
        }

        return null;

    }

    /*-- get Posts --*/

    public function getArticles() {

        $table = '\Bitrix\Iblock\Elements\ElementNotesTable';

        $dbNotes = $table::getList([
            'filter' => [
                'ACTIVE' => 'Y',
            ],
            'order' => ['SORT'=>'ASC' , 'ID' => 'DESC'],
            'select' => [
                'ID', 'NAME', 'PREVIEW_TEXT', 'IBLOCK_SECTION_ID', 'DATE_CREATE', 'IBLOCK_ID'
            ],
            "cache" => ["ttl" => 3600],
        ]);

        $data = [];

        while($arNotes = $dbNotes->Fetch()) {

            $category = Helper::getCategoryById($arNotes['IBLOCK_SECTION_ID'], $arNotes['IBLOCK_ID']);

            $data[] = [
                'id' => $arNotes['ID'],
                'title' => $arNotes['NAME'],
                'description' => $arNotes['PREVIEW_TEXT'],
                'categoryId' => $category['ID'],
                'categoryLabel' => $category['NAME'],
                'categoryColor' => $category['COLOR'],
                'date' =>  FormatDate("d.m.Y", MakeTimeStamp($arNotes['DATE_CREATE']))
            ];
        }

        return $data;

    }

    /*-- get Iblock Id by Code --*/

    public static function getIblockIdByCode($code) {

        $result = false;

        $dbRes = \CIBlock::GetList(
            [],
            ['CODE' => $code],
            false,
            false,
            ['ID']
        );

        if ($arRes = $dbRes->Fetch()) {
            $result = $arRes['ID'];
        }

        return $result;

    }

}