


//アップロードを許可する拡張子
var allow_exts = new Array('xlsx');

function getExt(filename) {
    return filename.slice((filename.lastIndexOf('.') - 1 >>> 0) + 2);
}

//アップロード予定のファイル名の拡張子が許可されているか確認する関数
function checkExt(filename)
{
    //比較のため小文字にする
    var ext = getExt(filename).toLowerCase();
    //許可する拡張子の一覧(allow_exts)から対象の拡張子があるか確認する
    if (allow_exts.indexOf(ext) === -1) return false;
    return true;
}

$(function()
    {
        $("input[type='file']").on('change', function (e)
        {
            var id = $(this).attr("id");
            
            //拡張子チェック
            if(checkExt(e.target.files[0].name) == false)
            {
                document.getElementById(id).value = "";

                Ctrl_pop("error","visible",40);
                return;
            }

        });
    });



function file_upload(id)
{
    var form = id.replace("btn_","form_");

    var fname = id.replace("btn_","") + "_file";
    // ファイルリストを取得
    var fileList = document.getElementById(fname).files;
    if(fileList.length<=0)
    {
        Ctrl_pop("error","visible",15);
        return ;
    }

    // フォームデータを取得
    var formdata = new FormData(document.getElementById(form));

    
    // XMLHttpRequestによるアップロード処理
    var url = "costctrl_upload";
    var xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    var token = document.getElementsByName('csrf-token').item(0).content;
    xhr.setRequestHeader('X-CSRF-Token', token);
    xhr.send(formdata);
    xhr.onreadystatechange = function()
    {
        if (xhr.readyState == 4 && xhr.status == 200)
        {
            // rtn : -1/エラー ,-2/アップロード失敗,-3/拡張子がxlslのファイルを選択してください,-4/ファイルを選択してください
            var rtn = xhr.responseText;
            if(rtn == 0)
            {
                Ctrl_pop("error","visible",16);
            }
            else
            {
                Ctrl_pop("error","visible",38);
            }
        }
    };
}



function file_download(id)
{
    var form = id.replace("btn_","form_");

    // フォームデータを取得
    var formdata = new FormData(document.getElementById(form));
    // XMLHttpRequestによるアップロード処理
    var url = "costctrl_download";
    var xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    var token = document.getElementsByName('csrf-token').item(0).content;
    xhr.setRequestHeader('X-CSRF-Token', token);

    xhr.responseType = 'arraybuffer'; //arraybuffer型のレスポンスを受け付ける
    xhr.dataType = 'binary';
    xhr.send(formdata);
    xhr.onreadystatechange = function()
    {
        if (xhr.readyState == 4 && xhr.status == 200)
        {
            var response_data = this.response;
            var uint8_array = new Uint8Array(response_data);
            //エラーの場合
            if(uint8_array.length == 2)
            {
                var text_decoder = new TextDecoder("utf-8");
                var str = text_decoder.decode(Uint8Array.from(uint8_array).buffer);
                
                if(str == "-1")
                {
                    Ctrl_pop("error","visible",17);
                }
                return ;
            }
            // rtn : -1/エラー ,-2/アップロード失敗,-3/拡張子がxlslのファイルを選択してください,-4/ファイルを選択してください
            var downloadData = new Blob([uint8_array], {type: "application/vnd.ms-excel;"});
            var filename = "xlsx";
            if(formdata.get('fname') == "currentfile") filename = "CurrentFile.xlsx";
            else if(formdata.get('fname') == "introfile") filename = "IntroFile.xlsx";
            
            //ファイルのダウンロードにはブラウザ毎に処理を分ける
            if(window.navigator.msSaveBlob)
            { // for IE
                window.navigator.msSaveBlob(downloadData, filename);
            }
            else
            {
                //レスポンスからBlobオブジェクト＆URLの生成
                var downloadUrl  = (window.URL || window.webkitURL).createObjectURL(downloadData);
                var link = document.createElement("a");
                //URLをセット
                link.href = downloadUrl;
                // //ダウンロードさせるファイル名の生成
                link.download = filename;
                // //クリックイベント発火
                link.click();
                
                link.remove();
                URL.revokeObjectURL(downloadData);
                file_download_fin(formdata);
            }

        }
    };
}


function file_download_fin(formdata)
{

    var url = "costctrl_downloadfin";
    var xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    var token = document.getElementsByName('csrf-token').item(0).content;
    xhr.setRequestHeader('X-CSRF-Token', token);
    xhr.send(formdata);
    xhr.onreadystatechange = function()
    {
        if (xhr.readyState == 4 && xhr.status == 200)
        {
            if(this.response != 0) Ctrl_pop("error","visible",17);
        }
    };

}