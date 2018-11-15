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

$arResult["TEXT_TYPE"] = COption::GetOptionString("koorochka.bellolashes", "TEXT_TYPE");
$arResult["CONTENT"] = COption::GetOptionString("koorochka.bellolashes", "CONTENT");
$arResult["LIMIT"] = COption::GetOptionString("koorochka.bellolashes", "LIMIT");
$arResult["LIMIT"] = intval($arResult["LIMIT"]);
$arResult["SECTIONS_COUNT"] = 0;
$arResult["SECTIONS"] = COption::GetOptionString("koorochka.bellolashes", "SECTIONS");
$arResult["SECTIONS"] = unserialize($arResult["SECTIONS"]);
if(count($arResult["SECTIONS"]) > $arResult["SECTIONS_COUNT"])
{
    $arResult["SECTIONS_COUNT"] = count($arResult["SECTIONS"]);
}

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
    $arResult["TEXT_TYPE"] = $_POST["TEXT_TYPE"];
    $arResult["CONTENT"] = $_POST["CONTENT"];
    $arResult["SECTIONS"] = $_POST["SECTIONS"];
    $arResult["SECTIONS_COUNT"] = 0;
    $sections_count = intval($_POST["sections_count"]);
    if($sections_count > 0){
        $arResult["SECTIONS_COUNT"] = $sections_count;
        for($i = 0; $i < $sections_count; $i++)
        {
            if(empty($_POST["SECTIONS_" . $i]))
                continue;
            $arResult["SECTIONS"][$i] = $_POST["SECTIONS_" . $i];
        }
    }
    $arResult["LIMIT"] = intval($_POST["LIMIT"]);

    COption::SetOptionString("koorochka.bellolashes", "TEXT_TYPE", $arResult["TEXT_TYPE"]);
    COption::SetOptionString("koorochka.bellolashes", "CONTENT", $arResult["CONTENT"]);
    COption::SetOptionString("koorochka.bellolashes", "SECTIONS", serialize($arResult["SECTIONS"]));
    COption::SetOptionString("koorochka.bellolashes", "LIMIT", $arResult["LIMIT"]);

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
            "event" => "BtnClick" . $_POST["num"],
            "arResultDest" => array("FORM_NAME" => "consalting", "FORM_ELEMENT_NAME" => "SECTIONS_" . $_POST["num"]),
            "arPath" => array("PATH" => ""),
            "select" => 'D',// F - file only, D - folder only
            "operation" => 'O',// O - open, S - save
            "showUploadTab" => true,
            "showAddToMenuTab" => false,
            "fileFilter" => 'php',
            "allowAllFiles" => true,
            "SaveConfig" => true,
        )
    );
    ?>
    <p>
        <input type="text"
               name="SECTIONS_<?=$_POST["num"]?>"
               size="50"
               maxlength="510"
               value="" />&nbsp;
        <input type="button"
               name="browse"
               value="..."
               onClick="BtnClick<?=$_POST["num"]?>()" />
    </p>
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

            <input type="hidden"
                   id="sections_count"
                   name="sections_count"
                   value="<?=$arResult["SECTIONS_COUNT"]?>">
            <div id="container">
                <?
                for($i = 0; $i < $arResult["SECTIONS_COUNT"]; $i++):
                    CAdminFileDialog::ShowScript
                    (
                        Array(
                            "event" => "BtnClick",
                            "arResultDest" => array("FORM_NAME" => "consalting", "FORM_ELEMENT_NAME" => "SECTIONS_" . $i),
                            "arPath" => array("PATH" => ""),
                            "select" => 'D',// F - file only, D - folder only
                            "operation" => 'O',// O - open, S - save
                            "showUploadTab" => false,
                            "showAddToMenuTab" => false,
                            "allowAllFiles" => false,
                            "SaveConfig" => true,
                        )
                    );
                    ?>
                    <p>
                        <input type="text"
                               name="SECTIONS_<?=$i?>"
                               size="50"
                               maxlength="510"
                               value="<?=$arResult["SECTIONS"][$i]?>" />&nbsp;
                        <input type="button"
                               name="browse"
                               value="..."
                               onClick="BtnClick()" />
                    </p>
                <?endfor;?>
            </div>

            <input type="button"
                   value="<?=Loc::getMessage("CONSALTING_SECTION_ADD")?>"
                   onclick="addMore()">

        </td>
    </tr>


    <tr>
        <td class="adm-detail-content-cell-l"><strong><?=Loc::getMessage("CONSALTING_LIMIT")?>:</strong></td>
        <td class="adm-detail-content-cell-r">
            <select name="LIMIT" wi>
                <?foreach ($arParams["LIMIT"] as $limit):?>
                    <option value="<?=$limit?>" <?if($arResult["LIMIT"] == $limit) echo "selected";?>><?=$limit?></option>
                <?endforeach;?>
            </select>
        </td>
    </tr>

    <tr>
        <td class="adm-detail-content-cell-l"><strong><?=Loc::getMessage("CONSALTING_CONTENT")?>:</strong></td>
        <td class="adm-detail-content-cell-r">
            <?CFileMan::AddHTMLEditorFrame(
                "CONTENT",
                $arResult["CONTENT"],
                "TEXT_TYPE",
                $arResult["TEXT_TYPE"],
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
    <script>
        function addMore()
        {
            var node = BX("container"),
                num = BX.findChildren(node, {
                        "tag" : "p"
                    },
                    false
                ),
                post = {};

            num = num.length;
            post['num'] = num;
            post['ajax'] = 'y';

            BX("sections_count").value = num + 1;

            if (!!node) {
                BX.ajax.post(
                    "/bitrix/admin/koorochka_online_consalting.php",
                    post,
                    function (data) {
                        node.innerHTML += data;
                    }
                );

            }
        }
    </script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>