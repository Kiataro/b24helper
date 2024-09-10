<?php
namespace App;

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Iblock\IblockTable;

\Bitrix\Main\Loader::includeModule('iblock');

class Helper
{

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
                'fileSrc' => $filePath,
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
                'category' => $category['NAME'],
                'categoryColor' => $category['COLOR'],
                'date' =>  FormatDate("d.m.Y", MakeTimeStamp($arNotes['DATE_CREATE']))
            ];
        }

        return $data;

    }
}