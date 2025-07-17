   
    var readAsData = new Array();
    var reader =new Array();
    // $(function()
    // {
    //     $("input[type='file']").on('change', function (e)
    //     {
    //         var id = $(this).attr("id").replace("img","");
    //         reader[0] =0;
    //         reader[id] = new FileReader();
    //         reader[id].onload = function (e)
    //         {
    //             $("#prev_"+id).attr('src', e.target.result);
    //         }
    //         readAsData[id] = e.target.files[0];
    //         // reader.readAsDataURL(e.target.files[0]);
        
    //     });
    // });
    function ini_img()
    {
        for(var i=1;i<21;i++)
        {
            if(document.getElementById("prev_"+i))
            {
                var img = new Image();
                img.src = document.getElementById("prev_"+i).src;//高さと幅を取得したいURLを入力
             
                var img_w  = img.width;  // 幅
                var img_h = img.height; // 高さ

                var aspectRatio = img_w / img_h//横幅÷縦幅の値をaspectRatioに代入
                
                if(aspectRatio >= 1)
                {
                    var aspc = 150 /img_w;
                    if(250 < img_w)
                    {
                        //横長画像の場合の処理（横幅÷縦幅が1以上になる場合）
                        document.getElementById("prev_"+i).style.width = "180px";
                        document.getElementById("prev_"+i).style.height = img_h*aspc + "px";
                    }
                }
                else
                {
                    var aspc = 150 /img_h;
                    if(150 < img_h)
                    {
                        //縦長画像の場合の処理
                        document.getElementById("prev_"+i).style.height = "130px";
                        document.getElementById("prev_"+i).style.width = img_w*aspc + "px";
                    }
                    
                }

            }
        }
    }

    //アップロードを許可する拡張子
    var allow_exts = new Array('jpg');

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
            var id = $(this).attr("id").replace("img","");

            //拡張子チェック
            if(checkExt(e.target.files[0].name) == false)
            {
                document.getElementById("chx"+id).checked = true;
                document.getElementById("prev_" + id).value = "";
                document.getElementById("prev_" + id).style.visibility = "collapse";
                document.getElementById("img" + id).value = "";

                Ctrl_pop("error","visible",41);
                return;
            }

            reader[0] =0;
            reader[id] = new FileReader();
            var img_w =0;
            var img_h =0;
            reader[id].onload = function (e)
            {
                
                var image = new Image();
                image.src = e.target.result;
            
                image.onload = function() {
                    // access image size here 
                    img_w = this.width;
                    img_h = this.height;
                    var aspectRatio = img_w / img_h//横幅÷縦幅の値をaspectRatioに代入
			
                    if(aspectRatio >= 1)
                    {
                        var aspc = 150 /img_w;
                        if(200 < img_w)
                        {
                            //横長画像の場合の処理（横幅÷縦幅が1以上になる場合）
                            document.getElementById(img).style.width = "180px";
                            document.getElementById(img).style.height = img_h*aspc + "px";
                        }
                    }
                    else
                    {
                        var aspc = 150 /img_h;
                        if(150 < img_h)
                        {
                            //縦長画像の場合の処理
                            document.getElementById(img).style.height = "130px";
                            document.getElementById(img).style.width = img_w*aspc + "px";
                        }
                        
                    }
                };
                $("#prev_"+id).attr('src', e.target.result);
            }

            readAsData[id] = e.target.files[0];

            
            // reader.readAsDataURL(e.target.files[0]);
        
            //ファイルが存在する場合
            var img = "prev_" + id;
            if(readAsData[id] != undefined)
            {
                reader[id].readAsDataURL(readAsData[id]);
            }

            
            document.getElementById(img).style.visibility = "visible";
            
        });
    });


    function img_clear()
    {
        for(var i=1;i<21;i++)
        {
            readAsData[i] = "";
            document.getElementById("prev_" + i).value = "";
            document.getElementById("prev_" + i).style.visibility = "collapse";
            document.getElementById("img" + i).value = "";
        }
    }



    function img_clear_uni(imgno)
    {
        var chx = imgno;
        //ファイル未選択
        if(document.getElementById("chx"+chx).checked == true)
        {
            document.getElementById("chx"+chx).checked = true;
            readAsData[imgno] = "";
            document.getElementById("prev_" + imgno).value = "";
            document.getElementById("prev_" + imgno).style.visibility = "collapse";
            document.getElementById("img" + imgno).value = "";
        }
        else
        {
            document.getElementById("chx"+chx).checked = false;
        }
    }

    var now_img = 0;
    function _img_disp(id)
    {
        var img_chx = new Array();
        img_chx[0] =0;
        for(var i=1;i<21;i++)
        {
            var obj = document.getElementById("prev_"+i);
            var image = new Image();
            image.src = obj.src;
            if(image.width != "" || image.width != null || image.width != undefined)
            {
                if(image.width > 0) img_chx[i] = 1;
                else img_chx[i] = 0;
            }
            else img_chx[i] = 0;
        }

        var render_chx = new Array();
        render_chx[0] = 0;
        for(var i=1;i<21;i++)
        {
            //renderされたかどうか
            if(readAsData[i] == "" || readAsData[i] == null || readAsData[i] == undefined)
            {
                render_chx[i] = 0;
            }
            else render_chx[i] = 1;
        }
        //見つからなければ -1
        if(render_chx.indexOf(1) < 0 && img_chx.indexOf(1) < 0)
        {
            alert("ファイルが選択されていません。");
            return;
        }

        if(id == "img_disp")
        {
            if(now_img == 0) var target_img = 1;
            var exist_flag = 0;
            for(var i = 1; i < 21; i++)
            {
                //ファイルない場合
                if(render_chx[i] == 0 && img_chx[i]  == 0) ;
                else
                {
                    exist_flag = 1;
                    target_img = i;
                    break;
                } 
            }
            if(exist_flag == 0)
            {
                alert("ファイルが見つかりませんでした。");
                target_img = now_img;
            }
            //ファイルが存在する場合
            now_img = target_img;
            var img = "prev_" + target_img;
            if(render_chx.indexOf(1) > 0)
            {
                if(readAsData[target_img] != undefined)
                {
                    reader[target_img].readAsDataURL(readAsData[target_img]);
                }
            }
            document.getElementById(img).style.visibility = "visible";
        }
        else if(id == "img_pre")
        {            
            if(now_img <= 1) var target_img = 20;
            else var target_img = now_img - 1;
            //renderされたかどうか
            var exist_flag = 0;
            for(var i = target_img; i > 0; i--)
            {
                //ファイルない場合
                if(render_chx[i] == 0 && img_chx[i]  == 0) ;
                else
                {
                    exist_flag = 1;
                    target_img = i;
                    break;
                } 
            }
            if(exist_flag == 0)
            {
                for(var i = 20; i > target_img; i--)
                {
                    //ファイル内場合
                    if(render_chx[i] == 0 && img_chx[i]  == 0) ;
                    else
                    {
                        exist_flag = 1;
                        target_img = i;
                        break;
                    }
                }
            }
            if(exist_flag == 0)
            {
                alert("ファイルが現在のファイル以外に見つかりませんでした。");
                target_img = now_img;
            }
            var img_now = "prev_" + now_img;
            var img = "prev_" + target_img;
            now_img = target_img;
            if(render_chx.indexOf(1) > 0)
            {
                if(readAsData[target_img] != undefined)
                {
                    reader[target_img].readAsDataURL(readAsData[target_img]);
                }
            }
            document.getElementById(img_now).style.visibility = "collapse";
            document.getElementById(img).style.visibility = "visible";
            
            
        }
        else if(id == "img_next")
        {
            if(now_img >= 20) var target_img = 1;
            else var target_img = now_img + 1;
            //renderされたかどうか
            var exist_flag = 0;
            for(var i = target_img; i < 21; i++)
            {
                //ファイルない場合
                if(render_chx[i] == 0 && img_chx[i]  == 0) ;
                else
                {
                    exist_flag = 1;
                    target_img = i;
                    break;
                } 
            }
            if(exist_flag == 0)
            {
                for(var i = 1; i < target_img + 1; i++)
                {
                    //ファイル内場合
                    if(render_chx[i] == 0 && img_chx[i]  == 0) ;
                    else
                    {
                        exist_flag = 1;
                        target_img = i;
                        break;
                    }
                }
            }
            if(exist_flag == 0)
            {
                alert("ファイルが現在のファイル以外に見つかりませんでした。");
                target_img = now_img;
            }
            var img_now = "prev_" + now_img;
            var img = "prev_" + target_img;
            
            now_img = target_img;
            if(render_chx.indexOf(1) > 0)
            {
                if(readAsData[target_img] != undefined)
                {
                    reader[target_img].readAsDataURL(readAsData[target_img]);
                }
            } 
            document.getElementById(img_now).style.visibility = "collapse";
            document.getElementById(img).style.visibility = "visible";
        }
    }
    
    function changeFile(obj)
    {
        var chx = obj.id.replace("img","");
        //ファイル未選択
        if(document.getElementById(obj.id).value.length <= 0)
        {
            document.getElementById("chx"+chx).checked = true;
        }
        else
        {
            document.getElementById("chx"+chx).checked = false;
        }
    }

    $(function()
    {
        $("input[type='checkbox']").on('change', function (e)
        {
            var id = $(this).attr("id").replace("chx","");
            img_clear_uni(id);
        });
            
    });


    