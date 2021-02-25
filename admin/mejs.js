$(function () {

    $('#add_btn').click(function () {
        methods.addHandle()
    })

    $('#show_tbody').on('click','.edit', function () {
        trIndex = $('.edit', '#show_tbody').index($(this));
        addEnter = false;
        $(this).parents('tr').addClass('has_case');
        methods.editHandle(trIndex);
    })

    $('#search_btn').click(function () {
        methods.seachName();
    })

    $('#back_btn').click(function () {
        $('#Ktext').val(' ');
        methods.resectList();
    })

    $('#show_tbody').on('click','.del', function () {
        console.log($(this).parents('tr').find('td').eq(1).text()); ///Ajax异步提交信息

        var jsonObj={"type":0,"data":$(this).parents('tr').find('td').eq(1).text()};
        ajax(jsonObj);

        $(this).parents('tr').remove();
    })

    $('#renyuan').on('hide.bs.modal',function() {
        addEnter = true;
        $('#show_tbody tr').removeClass('has_case');
        $('#xztb input').val('');
        $('#xztb select').find('option:first').prop('selected', true);
    });

})

var addEnter = true,
    noRepeat = true,
    level_id_arr = [],
    level_name_arr = [],
    tdStr = '',
    trIndex,
    hasNullMes = false,
    tarInp = $('#xztb input'),
    tarSel = $('#xztb select');

var methods = {

    addHandle: function (the_index) {
        hasNullMes = false;
        methods.checkMustMes();
        if (hasNullMes) {
            return;
        }
        if (addEnter) {
            methods.checkRepeat();
            if (noRepeat) {
                methods.setStr();
                var new_Arr=[];
                
                for (var a=0; a<tarInp.length; a++) {
                    new_Arr.push(tarInp.eq(a).val());
                }
                console.log(new_Arr.valueOf()); ///新添信息

                var jsonObj={"type":1,"data":JSON.stringify(new_Arr)};
                ajax(jsonObj);
                
                $('#show_tbody').append('<tr>' + tdStr + '</tr>');
                $('#renyuan').modal('hide');
            }
        }else{
            methods.setStr();
            
            var tar_old = $('#show_tbody tr').eq(trIndex);
            var old_Arr = [],new_Arr=[];
            for (var i=0; i<tar_old.find('td').length-1;i++) {
                var a = tar_old.children('td').eq(i).html();
                old_Arr.push(a);
            }
            console.log(old_Arr.valueOf()); ///旧信息

            for (var a=0; a<tarInp.length; a++) {
                new_Arr.push(tarInp.eq(a).val());
            }
            console.log(new_Arr.valueOf()); ///新信息

            var jsonObj={"type":2,"data_old":JSON.stringify(old_Arr),"data_new":JSON.stringify(new_Arr)};
            ajax(jsonObj);
            console.log(jsonObj.valueOf());
            $('#show_tbody tr').eq(trIndex).empty().append(tdStr);
            $('#renyuan').modal('hide');

        }
    },
    editHandle: function (the_index) {

        var tar = $('#show_tbody tr').eq(the_index);
        var nowConArr = [];
        for (var i=0; i<tar.find('td').length-1;i++) {
            var a = tar.children('td').eq(i).html();
            nowConArr.push(a);
        }


        $('#renyuan').modal('show');

        for (var j=0;j<tarInp.length;j++) {
            tarInp.eq(j).val(nowConArr[j])
        }
        for (var p=0;p<tarSel.length;p++) {
            var the_p = p+tarInp.length;
            tarSel.eq(p).val(nowConArr[the_p]);
        }

    },
    setStr: function () {

        tdStr = '';
        for (var a=0; a<tarInp.length; a++) {
            tdStr+= '<td>' + tarInp.eq(a).val() + '</td>'
        }
        for (var b=0; b<tarSel.length; b++) {
            tdStr+= '<td>' + tarSel.eq(b).val() + '</td>'
        }
        tdStr+= '<td><a href="#" class="edit">编辑</a> <a href="#" class="del">删除</a></td>';

    },
    seachName: function () {

        var a = $('#show_tbody tr');
        var nameVal = $('#Ktext').val().trim();
        var nameStr = '',
            nameArr = [];

        if (nameVal==='') {
            bootbox.alert({
                title: "来自火星的提示",
                message: "搜索内容不能为空",
                closeButton:false
            })
            return;
        }

        for (var c=0;c<a.length;c++) {
            var txt = $('td:first', a.eq(c)).html().trim();
            nameArr.push(txt);
        }

        a.hide();
        for (var i=0;i<nameArr.length;i++) {
            if (nameArr[i].indexOf(nameVal)>-1) {
                a.eq(i).show();
            }
        }
    },
    resectList: function () {
        $('#show_tbody tr').show();
    },
    checkMustMes: function () {

        if ($('.level_id').val().trim()==='') {
            bootbox.alert({
                title: "来自火星的提示",
                message: "段位等级为必选项，请填写",
                closeButton:false
            })
            hasNullMes = true;
            return
        }
        if ($('.level_name').val().trim()==='') {
            bootbox.alert({
                title: "来自火星的提示",
                message: "段位名称为必选项，请填写",
                closeButton:false
            })
            hasNullMes = true;
            return
        }
        if ($('.level_problem_min').val().trim()==='') {
            bootbox.alert({
                title: "来自火星的提示",
                message: "段位最低题目要求为必选项，请填写",
                closeButton:false
            })
            hasNullMes = true;
            return
        }

    },
    checkRepeat: function () {

        level_id_arr = [], level_name_arr = [];

        for (var i = 0; i<$('#show_tbody tr:not(".has_case")').length;i++) {
            var par = '#show_tbody tr:not(".has_case"):eq(' + i + ')';
            var a = $('td:eq(0)', par).html().trim(),
                b = $('td:eq(1)', par).html().trim();
            level_id_arr.push(a);
            level_name_arr.push(b);
        }
        var level_id = $('.level_id').val().trim(),
            level_name = $('.level_name').val().trim();

        if (level_id_arr.indexOf(level_id)>-1) {
            noRepeat = false;
            bootbox.alert({
                title: "来自火星的提示",
                message: "段位等级重复了，请重新输入",
                closeButton:false
            })
            return;
        }
        if (level_name_arr.indexOf(level_name)>-1) {
            noRepeat = false;
            bootbox.alert({
                title: "来自火星的提示",
                message: "段位名称重复了，请重新输入",
                closeButton:false
            })
            return;
        }
        noRepeat = true;
    }
}
function ajax(jsonObj)
{
    myajax=$.ajax({
        type:"post",						//提交方式
        url:"/admin/level_ajax.php",		//执行的url(控制器/操作方法)
        async:true,							//是否异步
        data:jsonObj,						//获取form表单的数据
        datatype:'json',					//数据格式
        success:function(data){
            console.log(data);				//打印
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(XMLHttpRequest.status);
            alert(XMLHttpRequest.readyState);
            alert(textStatus);
        }
    });
    $.when(myajax).done(function () {
        window.location.reload();
    });
}