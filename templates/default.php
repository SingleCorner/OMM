<?php

/**
 * 文件不允许访问
 *
 */
if (!defined('__ROOT__')) {
	header("Status: 404");
	exit;
}
/**
 * 输出页脚信息与版权信息
 *
 */
function APP_html_footer(){
?>
		</div>
		<!-- 内容区结束 -->
	</div>
	<!-- 主容器结束 -->
	<!-- 页脚开始 -->
	<footer id="APP_foot">
		<div id="APP_foot_copyright">
			Copyright © 2013 - 2015 SingleCorner<br />
			<a  href="https://github.com/SingleCorner/OMM" target="_blank">https://github.com/SingleCorner/OMM</a>
		</div>
		<div id="APP_foot_license">
			<div>本项目基于GPLv2协议开源在github</div>
			<div>运行于 <a href="<?php echo __COM_WEB__;?>" target="_blank"><?php echo __COM__;?></a></div>
		</div>
	</footer>
	<!-- 页脚结束 -->
</body>
</html>
<?php
}

?>