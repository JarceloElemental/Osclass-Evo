<?php if ( ! defined('OC_ADMIN')) exit('Direct access is not allowed.');
/*
 * Copyright 2014 Osclass
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

osc_enqueue_script('tiny_mce5');

function customPageHeader(){ ?>
    <h1><?php _e('Settings'); ?></h1>
    <?php
}
osc_add_hook('admin_page_header','customPageHeader');
//customize Head
function customHead() { ?>
    <script type="text/javascript">
        tinyMCE.init({
            mode : "textareas",
            mobile: {
                // theme: 'mobile',
                menubar: 'edit view insert format table'
            },
            menu: {
                edit: {title: 'Edit', items: 'undo redo | selectall'}
            },
            menubar: 'edit view insert format table',
            width: "100%",
            height: "440px",
            language: 'en',
            branding: false,
            plugins : 'advlist autolink lists link image imagetools media charmap preview anchor searchreplace visualblocks code codesample fullscreen insertdatetime media table contextmenu',
            toolbar: 'undo redo | styleselect bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | codesample code',
            entity_encoding : "raw",
            relative_urls: false,
            remove_script_host: false,
            convert_urls: false,
            media_live_embeds: true,
            image_advtab: true,
            paste_data_images: true,
            link_assume_external_targets: true,
            link_quicklink: true,
            file_picker_types: 'image media',
            file_picker_callback: function(callback, value, meta) {
                if (meta.filetype == 'image') {
                    $('#upload').trigger('click');

                    $('#upload').on('change', function() {
                        var file = this.files[0];
                        var reader = new FileReader();

                        reader.onload = function(e) {
                            callback(e.target.result, {
                                alt: ''
                            });
                        };

                        reader.readAsDataURL(file);
                    });
                }
            }
        });

        $(document).ready(function(){
            // dialog filters
            $('#dialog-test-it').dialog({
                autoOpen: false,
                modal: true,
                width: 360,
                minHeight: 42,
                title: '<?php echo osc_esc_js( __('Send email') ); ?>'
            });
            $('#btn-display-test-it').click(function(){
                $('#dialog-test-it').dialog('open');
                return false;
            });

            $('#btn-test-it').click(function() {
                var name   = $('input[name*="#s_title"]:visible').attr('name');
                var locale = name.replace("#s_title","");

                var idTinymce = locale+"#s_text";

                $.post('<?php echo osc_admin_base_url(true); ?>',
                    {
                        page: 'ajax',
                        action: 'test_mail_template',
                        email:  $('input[name="test_email"]:visible').val(),
                        title:  $('input[name*="s_title"]:visible').val(),
                        body: tinyMCE.get(idTinymce).getContent({format : 'html'})
                    },
                    function(data) {
                        alert(data.html);
                    }, 'json');
                return false;
            });
        });

    </script>
    <?php
}
osc_add_hook('admin_header','customHead', 10);

function customPageTitle($string) {
    return sprintf(__('Edit email template &raquo; %s'), $string);
}
osc_add_filter('admin_title', 'customPageTitle');

$email      = __get("email");
$aEmailVars = EmailVariables::newInstance()->getVariables( $email );

$locales = OSCLocale::newInstance()->listAllEnabled();

osc_current_admin_theme_path('parts/header.php'); ?>

<div class="grid-row no-bottom-margin">
    <div class="row-wrapper">
        <h2 class="render-title"><?php _e('Edit email template'); ?></h2>
    </div>
</div>
<div id="pretty-form">
    <div class="grid-row grid-100">
        <div class="row-wrapper">
            <div id="item-form">
                <div id="left-side">

                    <?php printLocaleTabs(); ?>
                    <form action="<?php echo osc_admin_base_url(true); ?>" method="post">
                        <input type="hidden" name="page" value="emails" />
                        <input type="hidden" name="action" value="edit_post" />
                        <input id="upload" class="hide" type="file" name="image" >
                        <?php PageForm::primary_input_hidden($email); ?>
                        <div id="left-side">
                            <?php printLocaleTitlePage($locales, $email); ?>
                            <div>
                                <label><?php _e('Internal name'); ?></label>
                                <?php PageForm::internal_name_input_text($email); ?>
                                <div class="flashmessage flashmessage-warning flashmessage-inline">
                                    <p><?php _e('Used to identify the email template'); ?></p>
                                </div>
                            </div>
                            <div class="input-description-wide">
                                <?php printLocaleDescriptionPage($locales, $email); ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="form-actions form-inline">
                            <input type="submit" value="<?php echo osc_esc_html(__('Save changes')); ?>" class="btn btn-submit" />
                            <a id="btn-display-test-it" class="btn btn-submit"><?php _e('Test it'); ?></a>
                        </div>
                    </form>
                </div>
                <div id="right-side">
                    <div class="well ui-rounded-corners">
                        <h3 style="margin: 0;margin-bottom: 10px;text-align: center; color: #616161;"><?php _e('Legend'); ?></h3>
                        <?php foreach($aEmailVars as $key => $value) { ?>
                            <label><b><?php echo $key; ?></b><br/><?php echo $value;?></label><hr/>
                        <?php } ?>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>
<div id="dialog-test-it" class="hide">
    <input type="text" name="test_email" class="input-actions"/>
    <input type="submit" id="btn-test-it" href="#" class="btn btn-blue submit-right" value="<?php _e('Send email'); ?>"/>
</div>
<?php osc_current_admin_theme_path('parts/footer.php'); ?>