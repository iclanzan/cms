<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php Assets::css('user', 'media/css/user.css', array('weight' => 2)); ?>

<ul id="oauth-providers">
        
	<li class="oprovider base">
		<?php
                        $url = Route::get('user')->uri( array('action' => 'login'));
			$url .= URL::query( array('destination' => Request::current()->uri()));
			echo HTML::anchor($url, __('Log In'), array('class' => 'base',
					'title' =>__('Login with :provider', array(':provider' => ucfirst($site_name)) ) ) );
			unset($url);
		?>
	</li>
	
	<?php foreach($providers as $provider => $key): ?>
		<li class="oprovider <?php echo $provider; ?>">
		<?php
                        $url = Route::get('user/oauth')->uri( array('controller' => $provider, 'action' => 'login'));
			$url .= URL::query( array('destination' => Request::current()->uri()));
			echo HTML::anchor($url, __('Log In'), array('class' => $provider,
					'title' =>__('Login with :provider', array(':provider' => ucfirst($provider)) ) ) );
			unset($url);
		?>
		</li>
	<?php endforeach ?>
</ul>