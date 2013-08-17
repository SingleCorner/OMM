$(document).ready(function() {
	// 账号创建 - 表单提交过程
	$('#APP_newstaff_form').submit(function(evt) {
		// 阻断默认提交过程
		evt.preventDefault();

		//准备变量
		var name = $('#APP_newStaff_name').val();
		var gender = $('#APP_newStaff_gender').val();
		var department = $('#APP_newStaff_department').val();
		var position = $('#APP_newStaff_position').val();
		var tel = $('#APP_newStaff_tel').val();
		
		// 检查输入
		if (name == "") {
			$('#APP_newStaff_status').html('填写资料不详');
			return false;
		}

		$.ajax({
			type: 'POST',
			url: 'admin.php?a=T_member&p=add',
			data: {
				'name': name,
				'gender': gender,
				'department': department,
				'position': position,
				'tel': tel
			},
			success: function(data, status, xhr) {
				if (data.code == 1) {
					$('#APP_newStaff_status').html(data.message);
					$('#APP_newStaff_name').val('');
					$('#APP_newStaff_tel').val('');
					$('#APP_newStaff_name').focus();
				} else if (data.code == 0) {
					$('#APP_newStaff_status').html(data.message);
					$('#APP_newStaff_name').val('');
					$('#APP_newStaff_tel').val('');
					$('#APP_newStaff_name').focus();
				}
			},
			dataType: 'json'
		});
	});
	//页面默认行为
	$('#APP_queryStaff_id').focus();
	$('#APP_newStaff').hide();
	$('#APP_verifyStaff').hide(function() {
		$.ajax({
			type: 'POST',
			url: 'admin.php?a=T_member&p=listverify',
			success: function(data, status, xhr) {
				$('#APP_verifyStaff').html(data);
			},
			dataType: 'html'
		});
	});
});
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
function load_verifyStaff() {
	if ($('#APP_verifyStaff').is(':hidden')) {
		$('#APP_verifyStaff').show(function() {
			$.ajax({
				type: 'POST',
				url: 'admin.php?a=T_member&p=listverify',
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
function verifyStaff_allow(id) {
	tdclass = ".authorizer_" + id;
	btnclass = "#allowbtn_" + id;
	$.ajax({
		type: 'POST',
		url: 'admin.php?a=T_member&p=allowverify',
		data: {
			'id': id
		},
		success: function(data, status, xhr) {
			$(tdclass).html(data.authorizer);
			$(btnclass).hide();
		},
		dataType: 'json'
	});
}
function verifyStaff_deny(id) {
	trclass = ".verify_" + id;
	$.ajax({
		type: 'POST',
		url: 'admin.php?a=T_member&p=denyverify',
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