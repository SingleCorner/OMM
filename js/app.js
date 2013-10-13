$(document).ready(function() {
	//控制导航栏

	$(document).scroll(navbar_ajust);
	navbar_ajust();

	//修改密码
	$('#APP_chgpass_form').submit(chgpass);

	//提交加班时间
	$('#APP_signal_form').submit(index_recordtime);
	
	//个性化用户名监听
	$('#APP_valid_account').bind("input propertychange",regist_listen_account);

	//密码复输正确
	$('#APP_valid_pass2').bind("input propertychange",regist_listen_passwd);
	
	//账号 -> 激活
	$('#APP_valid_regist').bind("click",regist_commit);
	
	//账号 -> 修改密码 -> 页面焦点
	$('#APP_new_pswda').focus();

	//wiki -> 默认隐藏
	$('#APP_newWIKI').hide();

	//wiki -> 默认焦点
	$('#APP_queryWIKI_title').focus();

	//wiki -> 提交新知识
	$('#APP_newWIKI_form').submit(addwiki);

	//服务报告单 -> 默认隐藏
	$('#APP_newSrvs').hide();

	//服务报告单 -> 默认焦点
	$('#APP_querySrvs_id').focus();

	//服务报告单 -> 创建报告单
	$('#APP_newSrvs_form').submit(addsrvs);

	//服务报告单 -> 加载客户信息
	$('#APP_newSrvs_customer').bind("change",getcustomer);

	//服务报告单 -> 加载操作步骤
	$('#APP_newSrvs_fixid').bind("change",getopmethod);

	//服务报告单 -> 查询
	$('.query_srvs').bind("click",query_srvs);
});

/* 
 * 数据提交 -> 修改密码
 */
function chgpass(evt) {
	// 阻断默认提交过程
	evt.preventDefault();

	//准备变量
	var newpassa = $('#APP_new_pswda').val();
	var newpassb = $('#APP_new_pswdb').val();

	// 检查输入
	if (newpassa =="") {
		$('#APP_chgpass_status').html("密码不能为空");
		$('#APP_new_pswda').focus();
		return false;
	}
	if (newpassa != newpassb) {
		$('#APP_chgpass_status').html("密码对比失败");
		$('#APP_new_pswdb').val('');
		$('#APP_new_pswdb').focus();
		return false;
	}

	//提交数据
	$.ajax({
		type: 'POST',
		url: './?a=chgpasswd&p=TRUE',
		data: {
			'password': newpassa,
			'password2': newpassb
		},
		success: function(data, status, xhr) {
			if (data.code == 1) {
				$('#APP_new_pswda').val('');
				$('#APP_new_pswdb').val('');
				$('#APP_chgpass_status').html(data.message);
				$('#APP_new_pswda').focus();
			} else if (data.code == 0) {
				$('#APP_new_pswda').val('');
				$('#APP_new_pswdb').val('');
				$('#APP_chgpass_status').html(data.message);
				$('#APP_new_pswda').focus();
			}
		},
		dataType: 'json'
	});
}
/* 
 * 数据提交 -> 提交加班信息
 */
function index_recordtime(evt) {
	evt.preventDefault();

	//准备变量
	var onwork = $('#signal_onwork').val();
	var overwork = $('#signal_overwork').val();
	var rest = $('#signal_rest').val();

	//检查输入
	if (isNaN(overwork) == true || overwork + 1 == 1) {
		if (overwork == "0") {
			overwork = 0;
		} else {
			$('#signal_overwork').val('');
			$('#signal_overwork').focus();
			alert("加班时间数据错误");
			return false;
		}
	}
	if (onwork == "") {
		onwork = 0;
	} else if (rest == "") {
		rest = 0;
	}

	//提交数据
	$.ajax({
		type: 'POST',
		url: './?a=&p=recordtime',
		data: {
			'onwork': onwork,
			'overwork': overwork,
			'rest': rest
		},
		success: function(data, status, xhr) {
			if (data.code == 1) {
				alert("信息已录入");
				window.location.reload();
			} else if (data.code == 0) {
				alert("信息未录入");
				$('#signal_overwork').focus();
			}
		},
		dataType: 'json'
	});
}
/* 
 * 数据提交 -> 上下班记录
 * param @op 操作类型
 * param @item 操作项
 */
function check_op(op,item) {
	var obj = $('#APP_signal_outtimeh').length;
	if (obj) {
		var hour = Number($('#APP_signal_outtimeh').val());
		var min = Number($('#APP_signal_outtimem').val());
		var time = hour + min;
	} else {
		var time = 0;
	}
	var weekend = $('input[name="signal_weekend"]:checked').val();
	$.ajax({
		type: 'POST',
		url: './?a=&p=workrecord',
		data: {
			'op': op,
			'item': item,
			'time': time,
			'weekend': weekend
		},
		success: function(data, status, xhr) {
			if (data.code == 1) {
				alert("操作成功");
				window.location.reload();
			} else if (data.code == 0) {
				alert("操作失败");
				window.location.reload();
			}
		},
		dataType: 'json'
	});
}


/* 
 * 效果 -> 控制导航栏缩位显示
 */
function navbar_ajust() {
	if ($(document).scrollTop() > 60) {
		if (!$('body').hasClass('scroll')) {
			$('body').addClass('scroll');
		}
	} else {
		$('body').removeClass('scroll');
	}
}

/* 
 * 账号 -> 个性化用户名监听
 */
function regist_listen_account() {
	if ($('#APP_valid_account').val().length == 8) {
		account = $('#APP_valid_account').val();
		posturl = "valid.php" + window.location.search + "&p=check";
		$.ajax({
			type: 'POST',
			url: posturl,
			data: {
				'account': account
			},
			success: function(data, status, xhr) {
				if (data.code == 1) {
					$('#APP_account_status').html(data.message);
					$('#APP_valid_pass').focus();
				} else if (data.code == 0) {
					$('#APP_account_status').html(data.message);
					$('#APP_valid_account').focus();
				}
			},
			dataType: 'json'
		});
	}
}
/* 
 * 账号 -> 密码复输正确
 */
function regist_listen_passwd() {
	pass1 = $('#APP_valid_pass').val();
	pass2 = $('#APP_valid_pass2').val();
	if ($('#APP_valid_pass').val().length <= $('#APP_valid_pass2').val().length){
		if (pass1 == pass2) {
			$('.APP_passwd_status').html('');
			$('#APP_valid_regist').focus();
		} else {
			$('.APP_passwd_status').html('！两次密码不匹配！');
			$('#APP_valid_pass2').focus();
		}
	}
}
/* 
 * 账号 -> 激活
 */
function regist_commit() {
	if ($('#APP_valid_account').val().length == 8 && $('#APP_valid_pass2').val() == $('#APP_valid_pass').val()) {
		account = $('#APP_valid_account').val();
		passwd = $('#APP_valid_pass2').val();
		posturl = "valid.php" + window.location.search + "&p=regist";
		$.ajax({
			type: 'POST',
			url: posturl,
			data: {
				'account': account,
				'passwd': passwd
			},
			success: function(data, status, xhr) {
				if (data.code == 1) {
					$('#APP_regist_status').html(data.message);
				} else if (data.code == 0) {
					$('#APP_regist_status').html(data.message);
				}
			},
			dataType: 'json'
		});
	}
}

/* 
 * WIKI -> 加载模块
 */
function load_newWIKI() {
	if ($('#APP_newWIKI').is(':hidden')) {
		$('#APP_newWIKI').show();
		$('#APP_newWIKI_title').focus();
	} else {
		$('#APP_newWIKI').hide();
		$('#APP_queryWIKI_title').focus();
	}
}
/* 
 * WIKI -> 新建文档
 */
function addwiki(evt) {
	// 阻断默认提交过程
	evt.preventDefault();

	//准备变量
	var title = $('#APP_newWIKI_title').val();
	var type = $('#APP_newWIKI_type').val();
	var content = CKEDITOR.instances.APP_newWIKI_content.getData();
	
	// 检查输入
	if (title == "" || content == "") {
		alert('OMM：禁止空信息');
		return false;
	}

	$.ajax({
		type: 'POST',
		url: '?a=wiki&p=add',
		data: {
			'title': title,
			'type': type,
			'content': content,
		},
		success: function(data, status, xhr) {
			if (data.code == 1) {
				alert(data.message);
				window.location.reload();
			} else if (data.code == 0) {
				alert(data.message);
				window.location.reload();
			}
		},
		dataType: 'json'
	});
}

/* 
 * 服务报告单 -> 模块加载
 */
function load_newSrvs() {
	if ($('#APP_newSrvs').is(':hidden')) {
		$('#APP_newSrvs').show();
		$('#APP_newSrvs_title').focus();
	} else {
		$('#APP_newSrvs').hide();
		$('#APP_querySrvs_id').focus();
	}
}
/* 
 * 服务报告单 -> 提交报告单
 */
function addsrvs(evt) {
	// 阻断默认提交过程
	evt.preventDefault();

	//准备变量
	var customer = $('#APP_newSrvs_customer').val();
	var fixid = $('#APP_newSrvs_fixid').val();
	var stype = $('input[name="stype"]:checked').val();
	var mtype = $('input[name="mtype"]:checked').val();
	var start = $('#APP_newSrvs_start').val();
	var end = $('#APP_newSrvs_end').val();
	var main = $('#APP_newSrvs_main').val();
	var sub = $('#APP_newSrvs_sub').val();
	var sysdescr = CKEDITOR.instances.APP_newSrvs_sysdescr.getData();
	var workdescr = CKEDITOR.instances.APP_newSrvs_workdescr.getData();
	
	// 检查输入
	if (customer == 0) {
		alert('OMM：请选择客户');
		$('#APP_newSrvs_customer').focus();
		return false;
	} else if (fixid == 0) {
		alert('OMM：请选择客户需求');
		$('#APP_newSrvs_fixid').focus();
		return false;
	} else if (start == "") {
		alert('OMM：请输入开始时间');
		$('#APP_newSrvs_start').focus();
		return false;
	} else if (end == "") {
		alert('OMM：请输入结束时间');
		$('#APP_newSrvs_end').focus();
		return false;
	} else if (main == "") {
		alert('OMM：请选择主实施人');
		$('#APP_newSrvs_main').focus();
		return false;
	}

	$.ajax({
		type: 'POST',
		url: '?a=services&p=add',
		data: {
			'customer': customer,
			'fixid': fixid,
			'stype': stype,
			'mtype': mtype,
			'start': start,
			'end': end,
			'main': main,
			'sub': sub,
			'sysdescr': sysdescr,
			'workdescr': workdescr
		},
		success: function(data, status, xhr) {
			if (data.code == 1) {
				alert(data.message);
				window.location.reload();
			} else if (data.code == 0) {
				alert(data.message);
				window.location.reload();
			}
		},
		dataType: 'json'
	});
}
/* 
 * 服务报告单 -> 获取客户信息
 */
function getcustomer() {
	//准备变量
	var id = $(this).val();
	$('#APP_newSrvs_cname').html('获取中...');
	$('#APP_newSrvs_ccontact').html('获取中...');
	$('#APP_newSrvs_ctel').html('获取中...');
	$('#APP_newSrvs_caddr').html('获取中...');
	
	if (id == 0) {
		$('#APP_newSrvs_cname').html('');
		$('#APP_newSrvs_ccontact').html('');
		$('#APP_newSrvs_ctel').html('');
		$('#APP_newSrvs_caddr').html('');
		return false;
	}
	$.ajax({
		type: 'POST',
		url: '?a=services&p=query_customer',
		data: {
			'id': id
		},
		success: function(data, status, xhr) {
			if (data.code == 0) {
				alert(data.message);
				window.location.reload();
			} else {
				$('#APP_newSrvs_cname').html(data.name);
				$('#APP_newSrvs_ccontact').html(data.contact);
				$('#APP_newSrvs_ctel').html(data.tel);
				$('#APP_newSrvs_caddr').html(data.addr);
			}
		},
		dataType: 'json'
	});
}
/* 
 * 服务报告单 -> 获取操作步骤
 */
function getopmethod() {
	//准备变量
	var id = $(this).val();

	if (id == 0) {
		$('#APP_newSrvs_opmethod').html('');
		return false;
	}
	
	$.ajax({
		type: 'POST',
		url: '?a=services&p=query_opmethod',
		data: {
			'id': id
		},
		success: function(data, status, xhr) {
			if (data.code == 0) {
				alert(data.message);
				window.location.reload();
			} else {
				$('#APP_newSrvs_opmethod').html(data.content);
			}
		},
		dataType: 'json'
	});
}
/* 
 * 服务报告单 -> 查询
 */
function query_srvs() {
//	var id = $(this).val();
//	alert(id);
	window.open("http://192.168.235.251/?a=services&p=query&id=1");
}