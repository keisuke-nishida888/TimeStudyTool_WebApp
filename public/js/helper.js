function datechx()
{
    var measufrom = document.getElementById('measufrom').value;
    var measuto = document.getElementById('measuto').value;

    document.getElementById('jobfrom_h').value = ( '00' + document.getElementById('jobfrom_h').value ).slice( -2 );
    document.getElementById('jobfrom_m').value = ( '00' + document.getElementById('jobfrom_m').value ).slice( -2 );
    document.getElementById('jobto_h').value = ( '00' + document.getElementById('jobto_h').value ).slice( -2 );
    document.getElementById('jobto_m').value = ( '00' + document.getElementById('jobto_m').value ).slice( -2 );

    var jobfrom_tm = document.getElementById('jobfrom_h').value + document.getElementById('jobfrom_m').value;
    var jobto_tm = document.getElementById('jobto_h').value + document.getElementById('jobto_m').value;

    if(Number(document.getElementById('jobfrom_h').value) < 0 || 59 < Number(document.getElementById('jobfrom_h').value))
    {
        document.getElementById('err_job').innerHTML = "0~59の間で入力してください。";
        Ctrl_pop('','collapse','');
        return false;
    }
    if(Number(document.getElementById('jobfrom_m').value) < 0 || 59 < Number(document.getElementById('jobfrom_m').value))
    {
        document.getElementById('err_job').innerHTML = "0~59の間で入力してください";
        Ctrl_pop('','collapse','');
        return false;
    }
    if(Number(document.getElementById('jobto_h').value) < 0 || 59 < Number(document.getElementById('jobto_h').value))
    {
        document.getElementById('err_job').innerHTML = "0~59の間で入力してください";
        Ctrl_pop('','collapse','');
        return false;
    }
    if(Number(document.getElementById('jobto_m').value) < 0 || 59 < Number(document.getElementById('jobto_m').value))
    {
        document.getElementById('err_job').innerHTML = "0~59の間で入力してください";
        Ctrl_pop('','collapse','');
        return false;
    }

    
    if(jobfrom_tm.match(/[^0-9]+/))
    {
        document.getElementById('err_job').innerHTML = "半角数字のみを入力してください。";
        Ctrl_pop('','collapse','');
        return false;
    }
    if(jobto_tm.match(/[^0-9]+/))
    {
        document.getElementById('err_job').innerHTML = "半角数字のみを入力してください。";
        Ctrl_pop('','collapse','');
        return false;
    }


    
    //ありえない時刻が入力されていないか確認する
    if(2359 < jobfrom_tm || 2359 < jobto_tm)
    {
        document.getElementById('pop_alert_back').style.visibility = 'collapse';
        document.getElementById("btn_yes").style.visibility = 'collapse';
        if(document.getElementById("btn_addhelper"))document.getElementById("btn_addhelper").style.visibility = 'collapse';
        if(document.getElementById("btn_fixhelper"))document.getElementById("btn_fixhelper").style.visibility = 'collapse';
        if(document.getElementById('addmess'))document.getElementById('addmess').style.visibility = 'collapse';
        if(document.getElementById('fixmess'))document.getElementById('fixmess').style.visibility = 'collapse';
        Ctrl_pop("error","visible",39);
        return false;
    }


    //値が入っているか確認
    if(measufrom != "" && measuto != "" )
    {
         //前後入れ違いになっていないか確認
        if(measuto.replace("-","") < measufrom.replace("-",""))
        {            
            document.getElementById('pop_alert_back').style.visibility = 'collapse';
            document.getElementById("btn_yes").style.visibility = 'collapse';
            if(document.getElementById("btn_addhelper"))document.getElementById("btn_addhelper").style.visibility = 'collapse';
            if(document.getElementById("btn_fixhelper"))document.getElementById("btn_fixhelper").style.visibility = 'collapse';
            Ctrl_pop("error","visible",33);
            if(document.getElementById('addmess'))document.getElementById('addmess').style.visibility = 'collapse';
            if(document.getElementById('fixmess'))document.getElementById('fixmess').style.visibility = 'collapse';
            return false;
        }    
    }
    else if(measufrom != "" && measuto == "" )
    {
        document.getElementById('pop_alert_back').style.visibility = 'collapse';
        document.getElementById("btn_yes").style.visibility = 'collapse';
        if(document.getElementById("btn_addhelper"))document.getElementById("btn_addhelper").style.visibility = 'collapse';
        if(document.getElementById("btn_fixhelper"))document.getElementById("btn_fixhelper").style.visibility = 'collapse';
        Ctrl_pop("error","visible",33);
        return false;
    }
    else if(measufrom == "" && measuto != "" )
    {
        document.getElementById('pop_alert_back').style.visibility = 'collapse';
        document.getElementById("btn_yes").style.visibility = 'collapse';
        if(document.getElementById("btn_addhelper"))document.getElementById("btn_addhelper").style.visibility = 'collapse';
        if(document.getElementById("btn_fixhelper"))document.getElementById("btn_fixhelper").style.visibility = 'collapse';
        Ctrl_pop("error","visible",33);
        return false;
    }
    
    return true;
}


function select_change(id)
{
    // var select_obj_h = id.replace("_sel","");
    var select_obj_m = id.replace("_sel","");
    // var index = document.getElementById(id).value.indexOf(":");
	// var hh = document.getElementById(id).value.substring(0, index);
    // var mm = document.getElementById(id).value.slice(index + 1);
    // var hh = document.getElementById(id).value;
    // var mm = document.getElementById(id).value;
    // document.getElementById(select_obj_h).value =  hh;
    // document.getElementById(select_obj_m).value =  mm;

    document.getElementById(select_obj_m).value =  document.getElementById(id).value;
}

    
    function select_ctrl(id)
    {
                
        var target = id + "_sel";
        // var tmp = id.indexOf("jobfrom");
        // if(tmp < 0)
        // {
        //     tmp = id.indexOf("jobto");
        //     if(tmp >= 0)
        //     {                
        //         var target_h = document.getElementById('jobto_h').value;
        //         var target_m = document.getElementById('jobto_m').value;
        //         var target = "jobto_sel";
        //     }
        // }
        // else
        // {
        //     tmp = id.indexOf("jobto");
        //     var target_h = document.getElementById('jobfrom_h').value;
        //     var target_m = document.getElementById('jobfrom_m').value;
        //     var target = "jobfrom_sel";
        // }


        //セレクトボックスは3桁目までで検索
        //セレクトボックス内の値で近いものを検索
        var select = document.getElementById(target);
        var options = document.getElementById(target).options;
        // var rst_rep = target_h + target_m;
        // for(var i=0;i<select.length;i++)
        // {
        //     var options_rep = options[i].value.replace(":","");
        //     options_rep = options_rep.slice(0,options_rep.length);
            
        //     if(options_rep.indexOf(rst_rep) >= 0)
        //     {
        //         document.getElementById(target).selectedIndex = i;
        //         break;
        //     } 
        // }
        for(var i=0;i<select.length;i++)
        {
            if(options[i].value == document.getElementById(id).value)
            {
                document.getElementById(target).selectedIndex = i;
                break;
            } 
        }
        
    }


    var pre_val = "";
    $(function(){
    //  $('input[name="test1"]').on('input keypress keydown change', function(){
        $('input[name="jobfrom_h"],input[name="jobfrom_m"],input[name="jobto_h"],input[name="jobto_m"]').on('keyup', function(){
        check_numtype($(this).attr("id"))
     });
    });

    $(function(){
            $('input[name="jobfrom_h"],input[name="jobfrom_m"],input[name="jobto_h"],input[name="jobto_m"]').on('keydown', function(){

                pre_val = document.getElementById($(this).attr("id")).value;
         });
        });


    // $(function() {
    // // テキストボックスにフォーカス時、フォームの背景色を変化
    // $('#jobfrom_h,#jobfrom_m,#jobto_h,#jobto_m')
    //     //　フォーカスイン
    //     .focusin(function(e)
    //     {
    //         var tmp = $(this).attr("id").indexOf("jobfrom");
    //         if(tmp < 0)
    //         {
    //             tmp = $(this).attr("id").indexOf("jobto");
    //             if(tmp >= 0)
    //             {
    //                 tmp = $(this).attr("id").indexOf("_h");
    //                 if(tmp >= 0) var target = "jobto_h_sel";
    //                 else var target = "jobto_m_sel";
    //             } 
    //         }
    //         else
    //         {
    //             tmp = $(this).attr("id").indexOf("_h");
    //             if(tmp >= 0) var target = "jobfrom_h_sel";
    //             else var target = "jobfrom_m_sel";
    //         } 
            
    //         document.getElementById(target).style.visibility = "visible";
            
    //     })
    //     //　フォーカスアウト
    //     .focusout(function(e)
    //     {
    //         // 
            
    //     });
    // });

    // $(document).click(function(event) {
    //     // event.targetをjQueryオブジェクトに変換する
    //     // closest()を使って自分から先祖要素までjobfrom_selのidがある要素を選択する
    //     if(!$(event.target).closest('#jobfrom_h,#jobfrom_h_sel').length)
    //     {
    //         document.getElementById('jobfrom_h_sel').style.visibility = "hidden";
    //     }
    //     if(!$(event.target).closest('#jobfrom_m,#jobfrom_m_sel').length)
    //     {
    //         document.getElementById('jobfrom_m_sel').style.visibility = "hidden";
    //     }

    //     if(!$(event.target).closest('#jobto_h,#jobto_h_sel').length)
    //     {
    //         document.getElementById('jobto_h_sel').style.visibility = "hidden";
    //     }
    //     if(!$(event.target).closest('#jobto_m,#jobto_m_sel').length)
    //     {
    //         document.getElementById('jobto_m_sel').style.visibility = "hidden";
    //     }
    // });
   

    // 入力値の半角数字チェック
    function check_numtype(id)
    {        
        // //:の位置
        // var target = document.getElementById(id).value;
        // // 入力した文字が半角数字かどうかチェック
        // if(target.match(/^[0-9]+$/))
        // {            
        //     pre_val = target;
        // }
        // else
        // {
        //     // 入力した文字が半角数字ではないとき            
        //     document.getElementById(id).value = pre_val;
        // }
    }

    
    var readAsData = new Array();
    var reader =new Array();
    // $(function()
    // {
    //     $("input[name='img1'],input[name='img2'],input[name='img3'],input[name='img4'],input[name='img5']").on('change', function (e)
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
        // $("input[name='img1'],input[name='img2'],input[name='img3'],input[name='img4'],input[name='img5']").on('change', function (e)
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
                        if(250 < img_w)
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
        for(var i=1;i<6;i++)
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
        for(var i=1;i<6;i++)
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
        for(var i=1;i<6;i++)
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
        
            for(var i = 1; i < 6; i++)
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
            if(now_img <= 1) var target_img = 5;
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
                for(var i = 5; i > target_img; i--)
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
            if(now_img >= 5) var target_img = 1;
            else var target_img = now_img + 1;
            //renderされたかどうか
            var exist_flag = 0;
            for(var i = target_img; i < 6; i++)
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
    
