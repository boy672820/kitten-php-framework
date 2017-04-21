<?php get_header(); ?>

<main class="page-main">

	<div class="container">

		<form action="post" class="login-form">
			<div class="login-form__group">
				<div>
					<label for="user_login">아이디</label>
					<input type="text" name="user_login" id="user_login" value="" placeholder="아이디를 입력해주세요." class="form-control">
				</div>
				<div>
					<label for="user_pass">패스워드</label>
					<input type="text" name="user_pass" id="user_pass" value="" placeholder="패스워드를 입력해주세요." class="form-control">
				</div>
			</div>
			<div class="login-form__group">
				<button type="submit" class="login-button">로그인</button>
			</div>
		</form><!-- //.login-form -->

	</div>

</main><!-- //.page-main -->

<?php get_footer(); ?>
