<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap fma" style="background:#fff; padding: 20px; border:1px solid #ccc;">
<h3><?php _e('Shortcodes','file-manager-advanced')?> <a href="https://advancedfilemanager.com/documentation/" class="button" target="_blank">
    
<?php _e('Documentation','file-manager-advanced')?></a>

</h3> 
<?php echo class_fma_admin_menus::shortcodeUpdateNotice();?>
<?php if(class_exists('file_manager_advanced_shortcode')) { ?>
    <div id="setting-error-settings_updated" class="updated settings-error notice">
    <p><strong><?php _e('Congratulations,','file-manager-advanced')?> </strong><?php _e('You have Installed Advanced File Manager Shortcode Successfully. Start working with shortcode.','file-manager-advanced')?></p>
    </div>
<?php } else { ?>
    <div id="setting-error-settings_updated" class="error settings-error notice">
    <p style="color:red">
        <strong>
            <?php
            _e( 'This is Pro Feature of Advanced File Manager, Please Buy <a href="https://advancedfilemanager.com/pricing/?utm_source=plugin&utm_medium=shortcodes_screen_top_button&utm_campaign=plugin" target="_blank">Advanced File Manager Pro</a> Addon to make shortcode work for frontend. <a href="https://advancedfilemanager.com/pricing/?utm_source=plugin&utm_medium=shortcodes_screen_top_button&utm_campaign=plugin" target="_blank" class="button button-primary">Get AFM Pro</a>', 'file-manager-advanced' );
            ?>
        </strong>
    </p>
    </div>
<h3><?php _e('Shortcode Addon Demo:', 'file-manager-advanced'); ?></h3>
<p><?php _e('If you want to check the demo of shortcode addon then click on link given below.', 'file-manager-advanced');?></p>
<a href="https://advancedfilemanager.com/shortcode-demo/" target="_blank" class="">Click here for demo</a>

<h3><?php _e('Shortcode for Logged In Users:', 'file-manager-advanced'); ?></h3>
<p><code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="wp-content" hide="plugins" operations="upload,download" block_users="5" view="grid" theme="light" lang ="en" upload_allow="all" upload_max_size="2G"]</code></p>

<h3><?php _e('Shortcode for Non Logged In Users (visitors):', 'file-manager-advanced'); ?></h3>
<p><code>[file_manager_advanced login="no" path="wp-content" hide="plugins" operations="upload,download" view="grid" theme="light" lang ="en" upload_allow="all" upload_max_size="2G"]</code></p>

<h3><?php _e('Conditions for User Roles:', 'file-manager-advanced'); ?></h3>
<p><code>[fma_user_role role="subscriber,editor"]<br/>
[file_manager_advanced login="yes" roles="subscriber,editor" path="wp-content" hide="plugins" operations="upload"  view="list" theme="light" lang ="en"]<br/>
[/fma_user_role]<br/>
[fma_user_role role="administrator"]<br/>
[file_manager_advanced login="yes" roles="administrator" path="wp-content/plugins" operations="upload" view="list" theme="light" lang ="en"]<br/>[/fma_user_role]</code><br/> <strong>and many more conditions.</strong></p>

<h3><?php _e('Conditions for Users:', 'file-manager-advanced'); ?></h3>
<p><code>[fma_user user="1,2"]<br/>
[file_manager_advanced login="yes" roles="subscriber,editor" path="wp-content" hide="plugins" operations="upload"  view="list" theme="light" lang ="en"]<br/>
[/fma_user]<br/>
[fma_user user="3"]<br/>
[file_manager_advanced login="yes" roles="administrator" path="wp-content/plugins" operations="upload" view="list" theme="light" lang ="en"]<br/>[/fma_user]</code><br/> <strong>and many more conditions.</strong> <p style="color:red">Note: user="1,2" here 1,2 are user ids.</p></p>

<h3><?php _e('Parameters:', 'file-manager-advanced'); ?> </h3>
<table class="form-table" border="1" style="text-align:center">
<tr>
<td><strong>Parameter</strong></td>
<td><strong>Value</strong></td>
<td><strong>Description</strong></td>
<td><strong>Usage</strong></td>
</tr>
<tr>
<td>login</td>
<td>yes/no</td>
<td>yes -> For Logged In users, no -> For Non Logged In users</td>
<td><code>[file_manager_advanced login="yes"]</code> - logged in users<br><br>
<code>[file_manager_advanced login="no"]</code> - non logged in  users or visitors<br><br>
<strong>You can use given parameters for both shortcodes.</strong></td>
</tr>

<tr>
<td>roles</td>
<td>all / administrator, author</td>
<td>all -> Allow all user roles , use: roles="all"</td>
<td><code>[file_manager_advanced login="yes" roles="author,editor,administrator"]</code></td>
</tr>
<tr>
<td>path</td>
<td><p>(1)Any Valid Folder Path. Eg. wp-content/uploads</p>
<p>(2) <strong>%</strong> - Root Directory</p>
<p>(3) <strong>$</strong> - Will generate logged in users personal folder of their username (unique) under location <strong>"wp-content/uploads/file-manager-advanced/users"</strong>, use path="$" in shortcode.</p>
<p>(4) <strong>wp-content/uploads/file-manager-advanced/users</strong> - you can view and access all user's personal folders under this path.</p>
</td>
<td>Any valid folder path or suggested symbols like % and $.</td>
<td><code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="wp-content/uploads"]</code><div> <strong>Use given shortcode to auto assign their autogenerated folders to users.<strong> <code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="$"]</code></div></td>
</tr>
<tr>
<td>path_type</td>
<td>inside/outside</td>
<td>use "outside", if you want to use any directory (Folder) outside wordpress root directory, default: inside</td>
<td><code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="wp-content/uploads" path_type="inside"]</code><div><strong>Use "url" parameter with outside as url = "https://anyoutsidewebsite.com"</strong></div></td>
</tr>
<tr>
<td>hide</td>
<td>plugins</td>
<td>will hide plugins folder</td>
<td><code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="wp-content" path_type="inside" hide="plugins"]</code></td>
</tr>
<tr>
<td>operations</td>
<td>all / mkdir, mkfile, rename, duplicate, paste, ban, archive, extract, copy, cut, edit, rm, download, upload, resize, search, info, help, empty</td>
<td>all -> allow all operations, you can select according to your use </td>
<td><code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="wp-content" path_type="inside" hide="plugins" operations="mkdir,download"]</code></td>
</tr>
<tr>
<td>block_users</td>
<td>Any User ID like 1,5</td>
<td>Restrict any user to access file manager by assigning User's ID, Like block_users="1,5". Here 1 and 5 are the user's ids.</td>
<td><code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="wp-content" path_type="inside" hide="plugins" operations="mkdir,download" block_users="1,5"]</code></td>
</tr>
<tr>
<td>view</td>
<td>list / grid</td>
<td>The option 'list' will return the file manager files layout in list format and the option 'grid' will return the file manager files layout in grid format.</td>
<td><code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="wp-content" path_type="inside" hide="plugins" operations="mkdir,download" block_users="1,5" view="grid"]</code></td>
</tr>
<tr>
<td>theme</td>
<td>light / dark / grey / windows10 / bootstrap</td>
<td>With this option you can assign any theme to file manager to change file manager user experience (UX).</td>
<td><code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="wp-content" path_type="inside" hide="plugins" operations="mkdir,download" block_users="1,5" view="grid" theme="light"]</code></td>
</tr>
<tr>
<td>lang</td>
<td>en or any other language code.</td>
<td>At the bottom of this page, there is a list of all languages with codes. You can copy the code and use like lang ="en".</td>
<td><code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="wp-content" path_type="inside" hide="plugins" operations="mkdir,download" block_users="1,5" view="grid" theme="light" lang ="en"]</code></td>
</tr>
<tr>
<td>dateformat</td>
<td>M d, Y h:i A</td>
<td>Files creation or modification date format. You can change this formar as per your requirement. Example: dateformat : 'M d, Y h:i A' will return Mar 13, 2012 05:27 PM</td>
<td><code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="wp-content" path_type="inside" hide="plugins" operations="mkdir,download" block_users="1,5" view="grid" theme="light" dateformat="M d, Y h:i A"]</code></td>
</tr>
<tr>
<td>hide_path</td>
<td>yes/no</td>
<td>The option 'yes' will hide the real file path on preview. Default: no</td>
<td><code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="wp-content" path_type="inside" hide="plugins" operations="mkdir,download" block_users="1,5" view="grid" theme="light" dateformat="M d, Y h:i A" hide_path="no"]</code></td>
</tr>
<tr>
<td>enable_trash</td>
<td>yes/no</td>
<td>The option 'yes' will display trash in file manager on front shortcode page and all the deleted files will go to the trash folder. You can restore the deleted files from Trash. Default: no</td>
<td><code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="wp-content" path_type="inside" hide="plugins" operations="mkdir,download" block_users="1,5" view="grid" theme="light" dateformat="M d, Y h:i A" hide_path="no" enable_trash="no"]</code></td>
</tr>
<tr>
<td>height</td>
<td>500</td>
<td>This option is used to adjust in file manager height on front shortcode page. Default: blank (auto)</td>
<td><code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="wp-content" path_type="inside" hide="plugins" operations="mkdir,download" block_users="1,5" view="grid" theme="light" dateformat="M d, Y h:i A" hide_path="no" enable_trash="no" height=""]</code></td>
</tr>
<tr>
<td>width</td>
<td>800</td>
<td>This option is used to adjust in file manager width on front shortcode page. Default: blank (auto)</td>
<td><code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="wp-content" path_type="inside" hide="plugins" operations="mkdir,download" block_users="1,5" view="grid" theme="light" dateformat="M d, Y h:i A" hide_path="no" enable_trash="no" height="" width=""]</code></td>
</tr>
<tr>
<td>ui</td>
<td>
    With this option to can control the UI of the file manager. There are some options given.<br>
    1) files<br>2) toolbar<br>3) tree<br>4) path<br>5) stat
   </td>
<td> 1) files -> This option will display only files (no toolbar, no left side bar) (use ui="files" in shortcode)<br>
2) toolbar,tree,path,stat -> These options will display whole file manager ui like toolbar etc. Use: ui="toolbar,tree,path,stat". Also you can remove any option from the list as per your requirements. Default: blank (all)</td>
<td><code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="wp-content" path_type="inside" hide="plugins" operations="mkdir,download" block_users="1,5" view="grid" theme="light" dateformat="M d, Y h:i A" hide_path="no" enable_trash="no" height="" width="" ui="toolbar,tree,path,stat"]</code></td>
</tr>
<tr>
<td>allowed_upload</td>
<td>1) If you want to upload all file formats then use upload_allow="all" option.<br>
    2) If you want to upload specific mime types like "image/png" then upload_allow= "image/vnd.adobe.photoshop,image/png". You can add more mime type separated by comma(,). <a href="https://advancedfilemanager.com/advanced-file-manager-mime-types/" target="_blank">Click here</a> for more mime types. </td>
<td>By using this option you can allow and restrict specific file mime types to upload. Default: all</td>
<td><code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="wp-content" path_type="inside" hide="plugins" operations="mkdir,download" block_users="1,5" view="grid" theme="light" dateformat="M d, Y h:i A" hide_path="no" enable_trash="no" height="" width="" ui="toolbar,tree,path,stat" upload_allow= "image/vnd.adobe.photoshop,image/png"]</code></td>
</tr>
<tr>
<td>upload_max_size</td>
<td>If you want to restrict users to upload heavy files then use upload_allow="10M" option.  Default: 0 (No Limit)</td>
<td>Maximum upload file size. This size is per files. Can be set as number with unit like 10M, 500K, 1G. 0 means unlimited upload. By using this option you can restrict users to upload a specific size file.</td>
<td><code>[file_manager_advanced login="yes" roles="author,editor,administrator" path="wp-content" path_type="inside" hide="plugins" operations="mkdir,download" block_users="1,5" view="grid" theme="light" dateformat="M d, Y h:i A" hide_path="no" enable_trash="no" height="" width="" ui="toolbar,tree,path,stat" upload_allow= "image/vnd.adobe.photoshop,image/png" upload_max_size="2G"]</code></td>
</tr>

</table>
<h3><?php _e('File Commands Supported', 'file-manager-advanced');?></h3>
<?php $commands = array(
    'mkdir' => 'Create new directory or folder',
    'mkfile' => 'Create new file',
    'rename' => 'Rename a file or folder',
    'duplicate' => 'Duplicate or clone a folder or file',
    'paste' => 'Paste a file or folder',
    'archive' => 'Create a archive or zip',
    'extract' => 'Extract archive or zipped file',
    'copy' => 'Copy files or folders',
    'cut' => 'Simple cut a file or folder',
    'edit' => 'Edit a file like php, js, html, text etc',
    'rm' => 'Remove or delete files and folders',
    'download' => 'Download files and folders',
    'upload' => 'Upload files',
    'search' => 'Search files and folders',
    'info' => 'Info of files and folders',
    ); ?>
  <table class="form-table" border="1" style="text-align:center">
	<tr>
		<td><strong><?php _e('Command', 'file-manager-advanced');?></strong></td>
		<td><strong><?php _e('Description', 'file-manager-advanced');?></strong></td>
	</tr>
   <?php 
   foreach($commands as $c => $d){ ?>
    <tr>
	    <td><?php echo esc_attr($c);?></td>
		<td><?php echo esc_attr($d);?></td>
	</tr>
   <?php } ?>
   </table>
<h3><?php _e('Languages Supported', 'file-manager-advanced');?></h3>
<?php $locales = $this->langs->locales(); ?>
<table class="form-table" border="1" style="text-align:center">
	<tr>
		<td><strong><?php _e('Code', 'file-manager-advanced');?></strong></td>
        <td><strong><?php _e('Language', 'file-manager-advanced');?></strong></td>
	</tr>
	<?php foreach($locales as $lang => $code) {?>
	<tr>
		<td><?php echo esc_attr($code);?></td>
        <td><?php echo esc_attr($lang);?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>
</div>