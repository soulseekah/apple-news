<?php
/**
 * Publish to Apple News tests: Byline_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

/**
 * A class to test the behavior of the Apple_Exporter\Components\Byline class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Byline_Test extends Apple_News_Testcase {

	/**
	 * A filter function to modify the text style in the generated JSON.
	 *
	 * @param array $json The JSON array to modify.
	 *
	 * @return array The modified JSON.
	 */
	public function filter_apple_news_byline_json( $json ) {
		$json['layout'] = 'fancy-layout';

		return $json;
	}

	/**
	 * Test the `apple_news_byline_json` filter.
	 */
	public function test_filter() {
		$this->set_theme_settings( [ 'meta_component_order' => [ 'byline' ] ] );
		add_filter( 'apple_news_byline_json', [ $this, 'filter_apple_news_byline_json' ] );

		// Create a test post and get JSON for it.
		$post_id = self::factory()->post->create();
		$json    = $this->get_json_for_post( $post_id );
		$this->assertEquals( 'byline', $json['components'][0]['role'] );
		$this->assertEquals( 'fancy-layout', $json['components'][0]['layout'] );

		// Teardown.
		remove_filter( 'apple_news_byline_json', [ $this, 'filter_apple_news_byline_json' ] );
	}

	/**
	 * Tests the render method for the component.
	 */
	public function test_render() {
		$this->set_theme_settings( [ 'meta_component_order' => [ 'byline' ] ] );

		// Create a test post and get JSON for it.
		$user_id = self::factory()->user->create( [ 'display_name' => 'Test Author' ] );
		$post_id = self::factory()->post->create( [ 'post_author' => $user_id, 'post_date_gmt' => '1970-01-01 12:00:00' ] );
		$json    = $this->get_json_for_post( $post_id );
		$this->assertEquals( 'byline', $json['components'][0]['role'] );
		$this->assertEquals( 'by Test Author | Jan 1, 1970 | 12:00 PM', $json['components'][0]['text'] );
	}

	/**
	 * Tests byline settings.
	 */
	public function test_settings() {
		$this->set_theme_settings(
			[
				'byline_color'       => '#abcdef',
				'byline_color_dark'  => '#123456',
				'byline_font'        => 'AmericanTypewriter',
				'byline_line_height' => 12,
				'byline_size'        => 34,
				'byline_tracking'    => 56,
			]
		);

		// Create a test post and get JSON for it.
		$user_id = self::factory()->user->create( [ 'display_name' => 'Test Author' ] );
		$post_id = self::factory()->post->create( [ 'post_author' => $user_id, 'post_date_gmt' => '1970-01-01 12:00:00' ] );
		$json    = $this->get_json_for_post( $post_id );

		// Validate byline settings in generated JSON.
		$this->assertEquals( '#abcdef', $json['componentTextStyles']['default-byline']['textColor'] );
		$this->assertEquals( '#123456', $json['componentTextStyles']['default-byline']['conditional']['textColor'] );
		$this->assertEquals( 'AmericanTypewriter', $json['componentTextStyles']['default-byline']['fontName'] );
		$this->assertEquals( 12, $json['componentTextStyles']['default-byline']['lineHeight'] );
		$this->assertEquals( 34, $json['componentTextStyles']['default-byline']['fontSize'] );
		$this->assertEquals( 0.56, $json['componentTextStyles']['default-byline']['tracking'] );
	}
}
