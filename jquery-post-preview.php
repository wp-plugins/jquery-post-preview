<?php
/*
Plugin Name: jQuery Post Preview
Plugin URI: http://articlesss.com/jquery-post-preview-wordpress-plugin/
Description: Live post preview on "Write/Edit post" page of WordPress admin area using jQuery.
Version: 0.2
Author: Dimox
Author URI: http://dimox.net/
*/


$jpp_plugin_path = 'wp-content/plugins/jquery-post-preview';

add_action('init', 'jpp_textdomain');
function jpp_textdomain() {
	global $jpp_plugin_path;
	load_plugin_textdomain('jquery-post-preview', $jpp_plugin_path);
}


function jquery_post_preview() {

	header('Content-type: text/javascript');
?>

var $j = jQuery.noConflict();

$j(document).ready(function() {

	var show_text = '<?php _e('Preview', 'jquery-post-preview'); ?>';
	var hide_text = '<?php _e('Hide preview', 'jquery-post-preview'); ?>';
	var textarea = $j('textarea[name="content"]');
	var textarea_id = '#' + $j(textarea).attr('id');
	var content = '';
	var textarea_height = $j(textarea).height();

	if ( $j('#wp-content-editor-container').length ) {
		$j('#wp-content-editor-container').append('<input type="button" id="preview-tab" value="'+show_text+'" />');
	} else {
		$j('#ed_toolbar').append('<input type="button" id="preview-tab" value="'+show_text+'" />');
	}

	$j('#preview-tab').toggle(
		function() {
			content = $j(textarea_id).val();
			if ($j(textarea_id).val() != '') content = content + '\n\n';
			content_preview = content.replace(/(<\/?)script/g,'$1noscript')
			.replace(/(<blockquote[^>]*>)/g, '\n$1')
			.replace(/(<\/blockquote[^>]*>)/g, '$1\n')
			.replace(/\r\n/g, '\n')
			.replace(/\r/g, '\n')
			.replace(/\n\n+/g, '\n\n')
			.replace(/\n?(.+?)(?:\n\s*\n)/g, '<p>$1</p>')
			.replace(/<p>\s*?<\/p>/g, '')
			.replace(/<p>\s*(<\/?blockquote[^>]*>)\s*<\/p>/g, '$1')
			.replace(/<p><blockquote([^>]*)>/ig, '<blockquote$1><p>')
			.replace(/<\/blockquote><\/p>/ig, '</p></blockquote>')
			.replace(/<p>\s*<blockquote([^>]*)>/ig, '<blockquote$1>')
			.replace(/<\/blockquote>\s*<\/p>/ig, '</blockquote>')

			$j('#preview-tab').val(hide_text);
			$j(textarea).after('<div id="textarea_clone"></div>');
			$j(textarea).clone().appendTo($j('#textarea_clone'));
			$j('#textarea_clone textarea').text(content);
			$j('#textarea_clone').hide();
			$j(textarea).replaceWith('<div id="content_preview"></div>');
			$j('#content_preview').height(textarea_height);
			$j('#content_preview').html(content_preview);

		},
		function() {
			$j('#preview-tab').val(show_text);
			$j('#textarea_clone').remove();
			$j('#content_preview').replaceWith(textarea);
			$j(textarea_id).text(comment);
		}
	)

})// end .ready(function()

<?php

	die();

}


function RichEditingOn() {
	global $current_user;
	$rich_editing = get_usermeta($current_user->ID,'rich_editing');
	if ($rich_editing == 'true') {
?>

<script type="text/javascript">
/*<![CDATA[*/
var $j = jQuery.noConflict();
$j(function() {
	var home = '<?php echo get_option("home") ?>';
	$j('#post').before('<div style="border: 1px solid #FC6; background: #FFC; padding: 10px"><?php _e("<strong style=\"color: #C00\">Attention!</strong> Visual Editor is activated in <a href=\"'+home+'/wp-admin/profile.php\">your user profile</a>. It means that <strong>jQuery Post Preview</strong> plugin may work incorrectly. Deactivate Visual Editor to remove this warning.", "jquery-post-preview"); ?></div>');
})
/*]]>*/
</script>

<?php
	}
}
add_action('edit_form_advanced', 'RichEditingOn');



// Прицепляем CSS-файл к форме поста
add_action('admin_head', 'jpp_css');
function jpp_css() {
	global $jpp_plugin_path;
	echo "\n".'<link rel="stylesheet" href="'.get_option('home').'/'.$jpp_plugin_path.'/jquery-post-preview.css" type="text/css" media="screen" />';
}


// Прицепляем JavaScript к форме поста
add_action('admin_head', 'jpp_echo_script');
function jpp_echo_script() {
	echo '<script src="' . get_option('home') . '/?jquery-post-preview.js" type="text/javascript"></script>';
}


if( stristr($_SERVER['REQUEST_URI'], 'jquery-post-preview.js') ) {
	add_action('template_redirect', 'jquery_post_preview');
}

?>