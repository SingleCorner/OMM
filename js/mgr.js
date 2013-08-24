$(document).ready(function() {
	/* 
	 * 账号管理 -> 新建账号
	 */
	$('#APP_newstaff_form').submit(function(evt) {
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
	});
	/* 
	 * 页面 -> 默认行为
	 */
	$('#APP_queryStaff_id').focus();
	$('#APP_newStaff').hide();
	$('#APP_verifyStaff').hide(function() {
		$.ajax({
			type: 'POST',
			url: 'admin.php?a=staff&p=listverify',
			success: function(data, status, xhr) {
				$('#APP_verifyStaff').html(data);
			},
			dataType: 'html'
		});
	});
});

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
			$(tdclass).html(data.authorizer);
			$(btnclass).hide();
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

