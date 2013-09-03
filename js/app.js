$(document).ready(function() {
	$(document).scroll(navbar_ajust);
	navbar_ajust();

	//提交加班时间
	$('#APP_signal_form').submit(index_recordtime);

	//修改密码
	$('#APP_chgpass_form').submit(chgpass);
	
	//个性化用户名监听
	$('#APP_valid_account').bind("input propertychange",regist_listen_account);

	//密码复输正确
	$('#APP_valid_pass2').bind("input propertychange",regist_listen_passwd);
	
	//账号 -> 激活
	$('#APP_valid_regist').bind("click",regist_commit);
	
	//页面焦点 -> 修改密码	
	$('#APP_new_pswda').focus();
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
 * 数据提交 -> 提交加班时间
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