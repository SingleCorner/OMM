$(document).ready(function() {
	//控制导航栏

	$(document).scroll(navbar_ajust);
	navbar_ajust();

	//账号管理 -> 新建账号
	
	$('#APP_newstaff_form').submit(addAccount);

	//客户管理 -> 新建客户

	$('#APP_newcustomer_form').submit(addCustomer);

	//客户管理 -> 变更数据初始化
	$('.APP_customer_chdata').bind("click",chdataCustomer);

	//客户管理 -> 提交变更资料
	$('.cmtdataCustomer').live("click",cmtdataCustomer);

	//页面 -> 默认行为

	$('#APP_queryStaff_id').focus();
	$('#APP_queryCustomer_name').focus();	
	$('#APP_newStaff').hide();
	$('#APP_verifyStaff').hide();
	$('#APP_newCustomer').hide();
//	$('#APP_verifyStaff').hide(function() {
//		$.ajax({
//			type: 'POST',
//			url: 'admin.php?a=staff&p=listverify',
//			success: function(data, status, xhr) {
//				$('#APP_verifyStaff').html(data);
//			},
//			dataType: 'html'
//		});
//	});
});

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
 * 账号管理 -> 新建账号
 */
function addAccount(evt) {
	// 阻断默认提交过程
	evt.preventDefault();

	//准备变量
	var name = $('#APP_newStaff_name').val();
	var gender = $('#APP_newStaff_gender').val();
	var department = $('#APP_newStaff_department').val();
	var position = $('#APP_newStaff_position').val();
	var tel = $('#APP_newStaff_tel').val();
	var mail = $('#APP_newStaff_mail').val();
	
	// 检查输入
	if (name == "") {
		$('#APP_newStaff_status').html('填写资料不详');
		return false;
	}

	$.ajax({
		type: 'POST',
		url: 'admin.php?a=staff&p=add',
		data: {
			'name': name,
			'gender': gender,
			'department': department,
			'position': position,
			'tel': tel,
			'mail': mail
		},
		success: function(data, status, xhr) {
			if (data.code == 1) {
				$('#APP_newStaff_status').html(data.message);
				$('#APP_newStaff_name').val('');
				$('#APP_newStaff_tel').val('');
				$('#APP_newStaff_mail').val('');
				$('#APP_newStaff_name').focus();
			} else if (data.code == 0) {
				$('#APP_newStaff_status').html(data.message);
				$('#APP_newStaff_name').val('');
				$('#APP_newStaff_tel').val('');
				$('#APP_newStaff_mail').val('');
				$('#APP_newStaff_name').focus();
			}
		},
		dataType: 'json'
	});
}
/* 
 * 账号管理 -> 加载新账号添加模块
 */
function load_newStaff() {
	if ($('#APP_newStaff').is(':hidden')) {
		$('#APP_newStaff').show();
		$('#APP_newStaff_name').focus();
		$('#APP_verifyStaff').hide();
	} else {
		$('#APP_newStaff').hide();
		$('#APP_queryStaff_id').focus();
	}
}
/* 
 * 账号管理 -> 加载新账号授权模块
 */
function load_verifyStaff() {
	if ($('#APP_verifyStaff').is(':hidden')) {
		$('#APP_verifyStaff').show(function() {
			$.ajax({
				type: 'POST',
				url: 'admin.php?a=staff&p=listverify',
				success: function(data, status, xhr) {
					$('#APP_verifyStaff').html(data);
				},
				dataType: 'html'
			});
		});
		$('#APP_queryStaff_id').focus();
		$('#APP_newStaff').hide();
	} else {
		$('#APP_verifyStaff').hide();
		$('#APP_queryStaff_id').focus();
	}
}
/* 
 * 账号管理 -> 账号授权允许操作
 */
function verifyStaff_allow(id,mail) {
	tdclass = ".authorizer_" + id;
	btnclass = "#allowbtn_" + id;
	$(tdclass).html('邮件正在发送。。。');
	$.ajax({
		type: 'POST',
		url: 'admin.php?a=staff&p=allowverify',
		data: {
			'id': id,
			'mail': mail
		},
		success: function(data, status, xhr) {
			if (data.code == 1) {
				$(tdclass).html(data.authorizer);
				$(btnclass).hide();
			} else {
				$(tdclass).html(data.authorizer);
			}
		},
		dataType: 'json'
	});
}
/* 
 * 账号管理 -> 账号授权拒绝操作
 */
function verifyStaff_deny(id) {
	trclass = ".verify_" + id;
	$.ajax({
		type: 'POST',
		url: 'admin.php?a=staff&p=denyverify',
		data: {
			'id': id
		},
		success: function(data, status, xhr) {
			if (data.code == 1)
			{
				$(trclass).hide();
			}
		},
		dataType: 'json'
	});
}


/* 
 * 客户管理 -> 加载新客户添加模块
 */
function load_newCustomer() {
	if ($('#APP_newCustomer').is(':hidden')) {
		$('#APP_newCustomer').show();
	} else {
		$('#APP_newCustomer').hide();
	}
}
/* 
 * 客户管理 -> 添加客户
 */
function addCustomer(evt) {
	// 阻断默认提交过程
	evt.preventDefault();

	//准备变量
	var name = $('#APP_newCustomer_name').val();
	var nickname = $('#APP_newCustomer_nickname').val();
	var contact = $('#APP_newCustomer_contact').val();
	var tel = $('#APP_newCustomer_tel').val();
	var addr = $('#APP_newCustomer_addr').val();
	
	// 检查输入
	if (name == "") {
		alert('请至少填入客户名称');
		return false;
	}
	if (nickname == "") {
		nickname = name;
	}

	$.ajax({
		type: 'POST',
		url: 'admin.php?a=customer&p=add',
		data: {
			'name': name,
			'nickname': nickname,
			'contact': contact,
			'tel': tel,
			'addr': addr,
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
 * 客户管理 -> 变更数据初始化
 */
function chdataCustomer() {
	//提醒仅能变更别名
	alert('OMM：客户名称的变更仅能变更客户别名！');

	//定义到父级元素
	var e = $(this).parent().parent();

	//获取到需要修改的表格元素
	var customer_name = e.children('td.customer_name');
	var customer_contacter = e.children('td.customer_contacter');
	var customer_tel = e.children('td.customer_tel');
	var customer_address = e.children('td.customer_address');
	var customer_op = customer_address.next();

	//获取元素的值
	var data_name = e.children('input').val();
	var data_contacter = customer_contacter.html();
	var data_tel = customer_tel.html();
	var data_address = customer_address.html();

	//替换原表格元素
	customer_name.html('<input id="APP_updtCustomer_name" type="text" value='+data_name+' />');
	customer_contacter.html('<input id="APP_updtCustomer_contacter" type="text" value='+data_contacter+' />');
	customer_tel.html('<input id="APP_updtCustomer_tel" type="text" value='+data_tel+' />');
	customer_address.html('<input id="APP_updtCustomer_address" type="text" value='+data_address+' />');
	customer_op.html('<button class="cmtdataCustomer">提交变更</button><button onclick="window.location.reload()">取消变更</button>');
}
/* 
 * 客户管理 -> 提交变更资料
 */
function cmtdataCustomer() {
	//定义到元素
	var e = $(this).parent().parent().children('td');

	//提取数据
	var id = e.html();
	e = e.next();
	var name = e.children('input').val();
	e = e.next();
	var contacter = e.children('input').val();
	e = e.next();
	var tel = e.children('input').val();
	e = e.next();
	var addr = e.children('input').val();

//	alert(id+name+contacter+tel+addr);
	if (name == "") {
		alert('OMM：别名不能丢！');
		return false;
	}

	//发送数据
	$.ajax({
		type: 'POST',
		url: 'admin.php?a=customer&p=chdata',
		data: {
			'id': id,
			'name': name,
			'contacter': contacter,
			'tel': tel,
			'addr': addr
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
 * 客户管理 -> 变更合作状态
 */
 function chstatCustomer(id) {
	$.ajax({
		type: 'POST',
		url: 'admin.php?a=customer&p=freeze',
		data: {
			'id': id
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
 * 客户管理 -> 前端服务报告单显示
 */
function chDisplay(id) {
	$.ajax({
		type: 'POST',
		url: 'admin.php?a=customer&p=display',
		data: {
			'id': id
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
