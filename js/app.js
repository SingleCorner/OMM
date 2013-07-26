$(document).ready(function() {
	$(document).scroll(navbar_ajust);
	navbar_ajust();
	$('#APP_chgpass_form').submit(function(evt) {
		// 阻断默认提交过程
		evt.preventDefault();

		//准备变量
		var newpassa = $('#APP_new_pswda').val();
		var newpassb = $('#APP_new_pswdb').val();

		// 检查输入
		if (newpassa != newpassb) {
			$('#APP_chgpass_status').html("密码对比失败");
			$('£APP_new_pswda').focus();
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
				} else {
					if (data.code == 0) {
						$('#APP_new_pswda').val('');
						$('#APP_new_pswdb').val('');
					}
					$('#APP_chgpass_status').html(data.message);
				}
			},
			dataType: 'json'
		});
	});

});

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