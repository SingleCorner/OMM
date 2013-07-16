$(document).ready(function() {
	$('#APP_login_form').submit(function(evt) {
		// 阻断默认提交过程
		evt.preventDefault();
		
		// 准备变量
		var user = $('#APP_login_user').val();
		var pswd = $('#APP_login_pswd').val();
		var time = $('#APP_login_timestamp').val();
		
		// 检查输入
		if (user.length != 8) {
			$('#APP_login_status').html("No No No,账号不对>_< ");
			$('#APP_login_user').focus();
			return false;
		}
		
		if (pswd.length == 0) {
			$('#APP_login_status').html("乃不能丢掉密码君");
			$('#APP_login_pswd').focus();
			return false;
		}
		
		$('#APP_login_user').attr('disabled', true);
		$('#APP_login_pswd').attr('disabled', true);
		$('#APP_login_submit').attr('disabled', true);
		$('#APP_login_status').html('用力登录中，请稍等…');
		
		// 加密
		pswd = $.sha1($.sha1(pswd) + time);
		
		$.ajax({
			type: 'POST',
			url: './?a=login',
			data: {
				'username': user,
				'password': pswd,
				'encrypto': 'on'
			},
			success: function(data, status, xhr) {
				if (data.code == 0) {
					$('#APP_login_user').attr('disabled', false);
					$('#APP_login_pswd').attr('disabled', false);
					$('#APP_login_submit').attr('disabled', false);
					$('#APP_login_status').html('登录成功~！');
					window.location = data.referer || './';
				} else {
					$('#APP_login_user').attr('disabled', false);
					$('#APP_login_pswd').attr('disabled', false);
					$('#APP_login_submit').attr('disabled', false);
					if (data.code == -1 || data.code == -2) {
						$('#APP_login_pswd').val('');
					}
					$('#APP_login_status').html(data.message);
				}
			},
			dataType: 'json'
		});
	});

	// 设置焦点
	$('#APP_login_user').focus();
});