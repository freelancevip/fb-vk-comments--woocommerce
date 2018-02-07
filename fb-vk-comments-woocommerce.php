<?php
/**
 * Plugin name: Комментарии VK и FB для Woocommerce
 * Description: Выводит виджеты комментариев VK и FB для Woocommerce после поля description
 * Author: freelancevip.pro
 * Author URI: https://freelancevip.pro/
 * Version: 1.0.0
 */

defined( 'ABSPATH' ) || exit();

$FB_Vk_Comments_Woocommerce_options = include plugin_dir_path( __FILE__ ) . 'options.php';

new FB_Vk_Comments_Woocommerce( $FB_Vk_Comments_Woocommerce_options );

class FB_Vk_Comments_Woocommerce {
	private $options;

	function __construct( $options ) {

		$this->options = $options;
		if ( $options['vk']['enabled'] ) {
			add_action( 'wp_head', array( $this, 'vk_script' ) );
			add_shortcode( 'vk_comments_shortcode', array( $this, 'vk_shortcode' ) );
		}
		if ( $options['fb']['enabled'] ) {
			add_action( 'wp_footer', array( $this, 'fb_script' ), 5 );
			add_shortcode( 'fb_comments_shortcode', array( $this, 'fb_shortcode' ) );
		}

		add_filter( 'the_content', array( $this, 'template' ) );
	}

	function vk_script() {
		?>
        <script type="text/javascript" src="//vk.com/js/api/openapi.js?152"></script>
        <script type="text/javascript">
            VK.init({
                apiId: <?php echo $this->options['vk']['apiId'] ?>,
                onlyWidgets: true
            });
        </script>
		<?php
	}

	function vk_shortcode() {
		ob_start();
		?>
        <div id="vk_comments"></div>
        <script type="text/javascript">
            VK.Widgets.Comments("vk_comments", {
                limit: "<?php echo $this->options['vk']['limit'] ?>",
                attach: "*"
            });
        </script>
		<?php
		return ob_get_clean();
	}

	function fb_script( $content ) {
		?>
        <div id="fb-root"></div>
        <script>(function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s);
                js.id = id;
                js.src = 'https://connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v2.12&appId=<?php echo $this->options['fb']['apiId'] ?>&autoLogAppEvents=1';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>
		<?php
	}

	function fb_shortcode() {
		$link = get_permalink();

		return "<div class=\"fb-comments\" data-href=\"{$link}\" data-numposts=\"{$this->options['fb']['limit']}\"></div>";
	}

	function template( $content ) {
		$queried_object = get_queried_object();
		if ( isset( $queried_object->post_type ) && $queried_object->post_type == 'product' ) {
			ob_start();
			include( plugin_dir_path( __FILE__ ) . 'template.php' );

			return $content . ob_get_clean();
		}

		return $content;
	}
}
