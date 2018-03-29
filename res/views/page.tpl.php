<!DOCTYPE html>
<html>
	<head>
		<title><?=$Title?></title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<?=$CSSLinks?>
	</head>
	<?=$JSLinks?>
	<body>
		<div id="wrapper">
			<div id="nav" class="navbar">
				<div class="navbar-section">
					<a id="logo" class="float-left" href="/"><?=$L['appname']?></a>
				</div>
				<div class="navbar-section" id="profile">
					<?php if($User): ?>
						<div class="dropdown">
							<div class="btn-group">
								<button id="accountBut" class="btn btn-link dropdown-toggle"><?=$User['email']?> <i class="icon-down"></i></button>
									<ul class="menu">
										<li class="menu-item">
											<a href="/login/logout"><i class="icon-logout"></i> <?=$L['navbar:logout']?></a>
										</li>
									</ul>
							</div>
						</div>
					<?php else: ?>
						<a class="btn" href="/login"><i class="icon-account"></i> <?=$L['navbar:signin']?></a>
					<?php endif; ?>
				</div>
			</div>
			<div id="content"><?=$Content?></div>
			<div id="footer"></div>
		</div>
	</body>
	<?=$JSCode?>
</html>
