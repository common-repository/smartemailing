<?php

namespace Smartemailing\Managers;

use Smartemailing\Widgets\WidgetDownloader;
use Smartemailing\Widgets\WidgetWebform;

class WidgetsManager {
	public function __construct() {
		add_action( 'widgets_init', [ $this, 'register_widgets' ] );
	}

	public function register_widgets() {
		register_widget( WidgetDownloader::class );
		register_widget( WidgetWebform::class );
	}
}
