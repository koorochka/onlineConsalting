<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use Bitrix\Main\Localization\Loc;
Loc::loadLanguageFile(__FILE__);
CJSCore::Init(array("ajax"));

if(!function_exists("d"))
{
    function d($value)
    {
        ?><pre class="alert alert-info"><?print_r($value)?></pre><?
    }
}

$POST_RIGHT = $APPLICATION->GetGroupRight("main");
if ($POST_RIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$aTabs = array(array("DIV" => "tab1", "TAB" => Loc::getMessage("CONSALTING_TITLE")));
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$APPLICATION->SetTitle(Loc::getMessage("CONSALTING_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$arParams = array();
$arResult = array();

/**
 * Parameters
 */
for($i = 0; $i < 10; $i++)
{
    $arParams["SECTIONS"][] = "Section " . $i;
}

for($i = 0; $i < 100; $i++)
{
    $arParams["LIMIT"][] = $i;
}

/**
 * CRUD and Resulteren
 */
if (
        $_SERVER["REQUEST_METHOD"] == "POST" &&
        $POST_RIGHT=="W" &&
        empty($_POST["ajax"]) &&
        check_bitrix_sessid()
)
{
    $arResult["TEST"] = $_POST["test"];
    $arResult["SECTIONS"] = $_POST["SECTIONS"];

    d($arResult);
}
elseif(
        $_SERVER["REQUEST_METHOD"] == "POST" &&
        $_POST["ajax"] == "y" &&
        $_POST["num"]
)
{
    $APPLICATION->RestartBuffer();
    CAdminFileDialog::ShowScript
    (
        Array(
            "event" => "BtnClick",
            "arResultDest" => array("FORM_NAME" => "consalting", "FORM_ELEMENT_NAME" => "EDIT_FILE_BEFORE_" . $_POST["num"]),
            "arPath" => array("PATH" => ""),
            "select" => 'D',// F - file only, D - folder only
            "operation" => 'O',// O - open, S - save
            "showUploadTab" => false,
            "showAddToMenuTab" => false,
            "fileFilter" => 'php',
            "allowAllFiles" => true,
            "SaveConfig" => true,
        )
    );
    ?>
    <input type="text"
           name="EDIT_FILE_BEFORE_<?=$_POST["num"]?>"
           size="50"
           maxlength="510"
           value="">&nbsp;
    <input type="button"
           name="browse"
           value="..."
           onсlick="BtnClick<?=$_POST["num"]?>()">
    <?
    die();
}
?>
<form method="post"
      action="<?$APPLICATION->GetCurPage()?>"
      name="consalting"
      id="consalting">
<?
echo bitrix_sessid_post();
$tabControl->Begin();
$tabControl->BeginNextTab();


?>


    <tr>
        <td class="adm-detail-content-cell-l"><strong><?=Loc::getMessage("CONSALTING_SECTION")?>:</strong></td>
        <td class="adm-detail-content-cell-r">

            <?
            CAdminFileDialog::ShowScript
            (
                Array(
                    "event" => "BtnClick",
                    "arResultDest" => array("FORM_NAME" => "consalting", "FORM_ELEMENT_NAME" => "EDIT_FILE_BEFORE"),
                    "arPath" => array("PATH" => GetDirPath($str_EDIT_FILE_BEFORE)),
                    "select" => 'D',// F - file only, D - folder only
                    "operation" => 'O',// O - open, S - save
                    "showUploadTab" => false,
                    "showAddToMenuTab" => false,
                    "fileFilter" => 'php',
                    "allowAllFiles" => true,
                    "SaveConfig" => true,
                )
            );
            ?>
            <input type="text"
                   name="EDIT_FILE_BEFORE"
                   size="50"
                   maxlength="510"
                   value="<?echo $str_EDIT_FILE_BEFORE?>">&nbsp;
            <input type="button"
                   name="browse"
                   value="..."
                   onсlick="BtnClick()">

            <div id="container">container</div>

            <input type="button"
                   value="add"
                   onclick="addMore(2)">
            <script>
                function addMore(num)
                {
                    var post = {};
                    post['num'] = num;
                    post['ajax'] = 'y';

                    node = BX('container'); //сюда будем вставлять полученный html

                    if (!!node) {
                        BX.ajax.post(
                            "/bitrix/admin/koorochka_online_consalting.php",
                            post,
                            function (data) {
                                node.innerHTML = data;
                            }
                        );

                    }
                }
            </script>

        </td>
    </tr>


    <tr>
        <td class="adm-detail-content-cell-l"><strong><?=Loc::getMessage("CONSALTING_LIMIT")?>:</strong></td>
        <td class="adm-detail-content-cell-r">
            <select name="LIMIT" wi>
                <?foreach ($arParams["LIMIT"] as $limit):?>
                    <option><?=$limit?></option>
                <?endforeach;?>
            </select>
        </td>
    </tr>

    <tr>
        <td class="adm-detail-content-cell-l"><strong><?=Loc::getMessage("CONSALTING_CONTENT")?>:</strong></td>
        <td class="adm-detail-content-cell-r">
            <?CFileMan::AddHTMLEditorFrame(
                "test",
                $arResult["TEST"],
                "TEXT_TYPE",
                "html",
                array(
                    'height' => 240,
                    'width' => '100%'
                ),
                "N",
                0,
                "",
                ""
            );?>

        </td>
    </tr>

<?
$tabControl->EndTab();
$tabControl->Buttons(Array("disabled" => $POST_RIGHT<"W", "back_url" =>"/bitrix/admin/currencies.php?lang=".LANGUAGE_ID));
$tabControl->End();
?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>