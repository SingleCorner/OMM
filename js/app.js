$(document).ready(function() {
	$(document).scroll(navbar_ajust);
	navbar_ajust();

	//修改密码过程
	$('#APP_chgpass_form').submit(function(evt) {
		// 阻断默认提交过程
		evt.preventDefault();

		//准备变量
		var newpassa = $('#APP_new_pswda').val();
		var newpassb = $('#APP_new_pswdb').val();

		// 检查输入
		if (newpassa != newpassb) {
			$('#APP_chgpass_status').html("密码对比失败");
			$('#APP_new_pswdb').val('');
			$('#APP_new_pswdb').focus();
			return false;
		}
		$.ajax({
			type: 'POST',
			url: './?a=chgpasswd&p=TRUE',
			data: {
				'password': newpassa,
				'password2': newpassb,
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
	});
	/* 
	 * 账号 -> 个性化用户名监听
	 */
	$('#APP_valid_account').bind("input propertychange",function() {
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
	});
	/* 
	 * 账号 -> 密码复输正确
	 */
	$('#APP_valid_pass2').bind("input propertychange",function() {
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
	});
	/* 
	 * 账号 -> 激活授权
	 */
	$('#APP_valid_regist').bind("click",function() {
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
	});
	$('#APP_new_pswda').focus();
});
/* 
 * 效果 -> 控制导航栏缩位显示
 */
function navbar_ajust() {
	//var top = $(document).scrollTop();
	if ($(document).scrollTop() > 60) {
		if (!$('body').hasClass('scroll')) {
			$('body').addClass('scroll');
		}
	} else {
		$('body').removeClass('scroll');
	}
}
