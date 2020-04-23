<?php

$popups  = russia_html5map_plugin_popup_builder_list_available_popups();
$options = russia_html5map_plugin_get_options();
$option_keys = is_array($options) ? array_keys($options) : array();
$map_id  = (isset($_REQUEST['map_id'])) ? intval($_REQUEST['map_id']) : array_shift($option_keys) ;

$states  = $options[$map_id]['map_data'];
$states  = json_decode($states, true);

$maxRadius = 15;
$doptions= russia_html5map_plugin_map_defaults();
$pointTypes = array(
    ""  => "Point",
    "marker" => "Marker"
);

$defOptions = russia_html5map_plugin_map_defaults('', 1, true);
foreach ($defOptions as $k => $v) {
    if (!isset($options[$map_id][$k]))
        $options[$map_id][$k] = $v;
}




if(isset($_POST['act_type']) && $_POST['act_type'] == 'russia-html5-map-points-save') {
    $points = (isset($_POST['map_points']) and $_POST['map_points']) ?  stripcslashes($_POST['map_points']) : '{}';
    if (($dcd = json_decode($points, true)) !== null AND is_array($dcd)) {
        foreach ($dcd as $pid => &$pointData) {
            if (!empty($pointData['info']))
                $options[$map_id]['state_info'][$pid] = wp_kses_post($pointData['info']);
            else
                unset($options[$map_id]['info'][$pid]);
            unset($pointData['info']);

            if (!empty($pointData['comment']))
                $pointData['comment'] = wp_kses_post($pointData['comment']);
            if (!empty($pointData['color']) and $pointData['color'][0] != '#')
                $pointData['color'] = russia_html5map_plugin_chk_color($pointData['color']);
            if (!empty($pointData['colorOver']) and $pointData['colorOver'][0] != '#')
                $pointData['colorOver'] = russia_html5map_plugin_chk_color($pointData['colorOver']);
        }
        unset($pointData);
        $options[$map_id]['points'] = $dcd;
    }
    if (isset($_POST['clear_saved_state']) and $_POST['clear_saved_state']) {
        unset($options[$map_id]['point_editor_settings']);
    } else {
        $es = (isset($_POST['editor_settings']) and $_POST['editor_settings']) ?  stripcslashes($_POST['editor_settings']) : '{}';
        $options[$map_id]['point_editor_settings'] = $es;
    }
    $options[$map_id]['pointColor']             = russia_html5map_plugin_chk_color($_POST['dPointColor']);
    $options[$map_id]['pointColorOver']         = russia_html5map_plugin_chk_color($_POST['dPointColorOver']);
    $options[$map_id]['pointBorderColor']       = russia_html5map_plugin_chk_color($_POST['dPointBorderColor']);
    $options[$map_id]['pointBorderColorOver']   = russia_html5map_plugin_chk_color($_POST['dPointBorderColorOver']);
    $options[$map_id]['pointNameColor']         = russia_html5map_plugin_chk_color($_POST['dPointNameColor']);
    $options[$map_id]['pointNameColorOver']     = russia_html5map_plugin_chk_color($_POST['dPointNameColorOver']);
    $options[$map_id]['pointNameStroke']        = (($stroke = sanitize_text_field($_POST['dPointNameStroke'])) ? ($stroke == 'no' ? false : true) : null);
    $options[$map_id]['pointNameStrokeWidth']   = (($width = sanitize_text_field($_POST['dPointNameStrokeWidth'])) ? (max(0.1, min(3, (float) str_replace(',', '.', $width)))) : null);
    $options[$map_id]['pointNameStrokeOpacity'] = (($opacity = sanitize_text_field($_POST['dPointNameStrokeOpacity'])) ? (max(0, min(1, (float) str_replace(',', '.', $opacity)))) : null);
    $options[$map_id]['pointNameStrokeColor']   = russia_html5map_plugin_chk_color($_POST['dPointNameStrokeColor']);
    $options[$map_id]['pointNameStrokeColorOver'] = russia_html5map_plugin_chk_color($_POST['dPointNameStrokeColorOver']);
    $options[$map_id]['defaultPointRadius']     = min($maxRadius, max(1, (int) $_POST['dPointRadius']));
    $options[$map_id]['pointNameFontSize']      = min(20, max(3, (int) $_POST['dPointFontSize']));
    $options[$map_id]['pointNameFontFamily']    = sanitize_text_field(stripcslashes($_POST['dPointFontFamily']));
    $options[$map_id]['update_time'] = time();
    russia_html5map_plugin_save_options($options);
}

$mce_options = array(
    //'media_buttons' => false,
    'editor_height'   => 150,
    'textarea_rows'   => 20,
    'textarea_name'   => 'pointAddInfo',
    'tinymce' => array(
        'add_unload_trigger' => false,
    )
);

$defaultEditorSettings = array(
    'color'                 => null,
    'colorOver'             => null,
    'borderColor'           => null,
    'borderColorOver'       => null,
    'nameColor'             => null,
    'nameColorOver'         => null,
    'nameStrokeColor'       => null,
    'nameStrokeColorOver'   => null,
    'nameFontSize'          => null,
    'radius'                => null,
    'textPosition'          => 'right-middle'
);

$editorSettings = (isset($options[$map_id]['point_editor_settings']) and ($settings = json_decode($options[$map_id]['point_editor_settings'], true))) ? $settings : array();
foreach ($defaultEditorSettings as $k => $v) {
    if (!array_key_exists($k, $editorSettings)) {
        $editorSettings[$k] = $v;
    }
}

echo "<div class=\"wrap\"><h2>" . __('Configuration of Map points', 'russia-html5-map') . "</h2>";
?>
<style>
.tipsy-w {
    z-index: 50500;
}
#TB_overlay, #TB_window {
    z-index: 50150 !important;
}
.ui-dialog {
    z-index: 50000 !important;
}
#adminmenuwrap {
    z-index: 40000 !important:
}
</style>
<script>
    var imageFieldId = false;
    jQuery(function($){

        $('.tipsy-q').tipsy({gravity: 'w'}).css('cursor', 'default');

        $('.color~.colorpicker').each(function(){
            var me = this;

            $(this).farbtastic(function(color){
                var textColor = this.hsl[2] > 0.5 ? '#000' : '#fff';

                $(me).prev().prev().css({
                    background: color,
                    color: textColor
                }).val(color);

                if($(me).next().find('input').attr('checked') == 'checked') {
                    return;
                    var dirClass = $(me).prev().prev().hasClass('colorSimple') ? 'colorSimple' : 'colorOver';

                    $('.'+dirClass).css({
                        background: color,
                        color: textColor
                    }).val(color);
                }
            });

            $.farbtastic(this).setColor($(this).prev().prev().val());

            $($(this).prev().prev()[0]).bind('change', function(){
                $.farbtastic(me).setColor(this.value);
            });

            $(this).hide();
            $(this).prev().prev().bind('focus', function(){
                $(this).next().next().fadeIn();
            });
            $(this).prev().prev().bind('blur', function(){
                $(this).next().next().fadeOut();
            });
        });


        window.send_to_editorArea = window.send_to_editor;

        window.send_to_editor = function(html) {
            if(imageFieldId === false) {
                window.send_to_editorArea(html);
            }
            else {
                var imgurl = $('img',html).attr('src');

                $('#'+imageFieldId).val(imgurl);
                imageFieldId = false;

                tb_remove();
            }

        }

        if (typeof tinyMCE !== 'undefined') tinyMCE.execCommand('mceAddControl', true, 'pointAddInfo')

        $('input[type=submit]').attr('disabled',false);

    });

    function adjustSubmit() {
        jQuery('#map_points').val(map.mapConfig.points ? JSON.stringify(map.mapConfig.points) : '');
        jQuery('#editor_settings').val(JSON.stringify(editorSettings));
    }

</script>
<br />
<form method="POST" class="russia-html5-map main" onsubmit="adjustSubmit()">
<?php 
    russia_html5map_plugin_map_selector('points', $map_id, $options);
    echo "<br /><br />\n";
    russia_html5map_plugin_nav_tabs('points', $map_id);
?>

    <p><?php _e("Double-click to add a point; click and hold to drag; double-click a point to edit it", "russia-html5-map"); ?></p>

    <fieldset>
        <legend><?php _e('Points Configuration', 'russia-html5-map'); ?></legend>

        <div id="point_info"></div>
        <div>
        <div style="border-top: 1px solid #ddd">
                <label style="position: relative; top: -13px; background: white"><a href="javascript:void(0);" onclick="show_default_options(this.innerHTML.indexOf('+')!==-1)"><?php echo sprintf(__("Points defaults [%s]", "russia-html5-map"), "<span>+</span>") ?></a></label>
                <div id="pointDefOptionsWrapp" style="display: none">
                <table style="width: 100%">
                    <thead>
                    <tr style="font-weight: bold">
                        <td><?php _e('Option', 'russia-html5-map'); ?></td>
                        <td><?php _e('Color', 'russia-html5-map'); ?></td>
                        <td><?php _e('Hover color', 'russia-html5-map'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?php _e('Point color:', 'russia-html5-map'); ?></td>
                        <td><input class="color colorSimple" type="text" name="dPointColor" id="dPointColor" value="<?php echo $options[$map_id]['pointColor'] ?>" style="background-color: <?php echo $options[$map_id]['pointColor'] ?>"  />
                        <span class="tipsy-q" original-title='<?php esc_attr_e('The color of a point.', 'russia-html5-map'); ?>'>[?]</span><div class="colorpicker" style="margin-left: 100px"></div>
                        </td>
                        <td><input class="color colorOver" type="text" name="dPointColorOver" id="dPointColorOver" value="<?php echo $options[$map_id]['pointColorOver'] ?>" style="background-color: <?php echo $options[$map_id]['pointColorOver'] ?>"  />
                        <span class="tipsy-q" original-title='<?php esc_attr_e('The color of a point when the mouse cursor is over it.', 'russia-html5-map'); ?>'>[?]</span><div class="colorpicker" style="margin-left: 100px"></div>
                        </td>
                    </tr>
                    <tr>
                        <td><?php _e('Border color:', 'russia-html5-map'); ?></td>
                        <td><input class="color colorSimple" type="text" name="dPointBorderColor" id="dPointBorderColor" value="<?php echo $options[$map_id]['pointBorderColor'] ?>" style="background-color: <?php echo $options[$map_id]['pointBorderColor'] ?>"  />
                        <span class="tipsy-q" original-title='<?php esc_attr_e('The color of a point border.', 'russia-html5-map'); ?>'>[?]</span><div class="colorpicker" style="margin-left: 100px"></div></td>
                        <td><input class="color colorOver" type="text" name="dPointBorderColorOver" id="dPointBorderColorOver" value="<?php echo $options[$map_id]['pointBorderColorOver'] ?>" style="background-color: <?php echo $options[$map_id]['pointBorderColorOver'] ?>"  />
                        <span class="tipsy-q" original-title='<?php esc_attr_e('The color of a point border when the mouse cursor is over it.', 'russia-html5-map'); ?>'>[?]</span><div class="colorpicker" style="margin-left: 100px"></div></td>
                    </tr>
                    <tr>
                        <td><?php _e('Shortname color:', 'russia-html5-map'); ?></td>
                        <td><input class="color colorSimple" type="text" name="dPointNameColor" id="dPointNameColor" value="<?php echo $options[$map_id]['pointNameColor'] ?>" style="background-color: <?php echo $options[$map_id]['pointNameColor'] ?>"  />
                        <span class="tipsy-q" original-title='<?php esc_attr_e('The color of a point\'s shortname.', 'russia-html5-map'); ?>'>[?]</span><div class="colorpicker" style="margin-left: 100px"></div></td>
                        <td><input class="color colorOver" type="text" name="dPointNameColorOver" id="dPointNameColorOver" value="<?php echo $options[$map_id]['pointNameColorOver'] ?>" style="background-color: <?php echo $options[$map_id]['pointNameColorOver'] ?>"  />
                        <span class="tipsy-q" original-title='<?php esc_attr_e('The color of a point\'s shortname when the mouse cursor is over it.', 'russia-html5-map'); ?>'>[?]</span><div class="colorpicker" style="margin-left: 100px"></div></td>
                    </tr>
                    <tr>
                        <td><?php _e('Shortname stroke color:', 'russia-html5-map'); ?></td>
                        <td><input class="color colorSimple" type="text" name="dPointNameStrokeColor" id="dPointNameStrokeColor" value="<?php echo $options[$map_id]['pointNameStrokeColor'] ?>" style="background-color: <?php echo $options[$map_id]['pointNameStrokeColor'] ?>"  />
                        <span class="tipsy-q" original-title='<?php esc_attr_e('The color of a point\'s shortname stroke.', 'russia-html5-map'); ?>'>[?]</span><div class="colorpicker" style="margin-left: 100px"></div></td>
                        <td><input class="color colorOver" type="text" name="dPointNameStrokeColorOver" id="dPointNameStrokeColorOver" value="<?php echo $options[$map_id]['pointNameStrokeColorOver'] ?>" style="background-color: <?php echo $options[$map_id]['pointNameStrokeColorOver'] ?>"  />
                        <span class="tipsy-q" original-title='<?php esc_attr_e('The color of a point\'s shortname stroke when the mouse cursor is over it.', 'russia-html5-map'); ?>'>[?]</span><div class="colorpicker" style="margin-left: 100px"></div></td>
                    </tr>
                    </tbody>
                </table>
                <hr>
                <div style="float:left; width: 30%">
                    <label><span class="title"><?php _e('Radius:', 'russia-html5-map') ?> </span>
                    <input type="number" name="dPointRadius" id="dPointRadius" value="<?php echo (int)$options[$map_id]['defaultPointRadius'] ?>" style="width: 50px" min="1" max="<?php echo $maxRadius ?>"/>
                    <span class="tipsy-q" original-title='<?php esc_attr_e('The default point radius.', 'russia-html5-map'); ?>'>[?]</span>
                </div>
                <div style="float:left; width: 35%">
                    <label><span class="title"><?php _e('Name font family:', 'russia-html5-map') ?> </span>
                    <input type="text" name="dPointFontFamily" id="dPointFontFamily" value="<?php echo htmlspecialchars($options[$map_id]['pointNameFontFamily']) ?>" style="width: 200px" />
                    <span class="tipsy-q" original-title='<?php esc_attr_e('The default font family for point\'s shortname.', 'russia-html5-map'); ?>'>[?]</span></td>
                </div>
                <div style="float:left; width: 35%">
                    <label><span class="title"><?php _e('Name font size:', 'russia-html5-map') ?> </span>
                    <input type="number" name="dPointFontSize" id="dPointFontSize" value="<?php echo (int)$options[$map_id]['pointNameFontSize'] ?>" min="3" max="20" style="width: 100px" /> px
                    <span class="tipsy-q" original-title='<?php esc_attr_e('The default font size for point\'s shortname.', 'russia-html5-map'); ?>'>[?]</span></td>
                </div>
                <div style="float:left; width: 30%">
                    <label><span class="title"><?php _e('Name stroke:', 'russia-html5-map') ?> </span>
                    <select name="dPointNameStroke">
                        <option value="" <?php if (is_null($options[$map_id]['pointNameStroke'])) echo 'selected="selected"'; ?>><?php _e('Use general setting', 'russia-html5-map') ?></option>
                        <option value="yes" <?php if ($options[$map_id]['pointNameStroke'] === true) echo 'selected="selected"'; ?>><?php _e('Yes', 'russia-html5-map') ?></option>
                        <option value="no" <?php if ($options[$map_id]['pointNameStroke'] === false) echo 'selected="selected"'; ?>><?php _e('No', 'russia-html5-map') ?></option>
                    </select>
                    <span class="tipsy-q" original-title='<?php esc_attr_e('The default point radius.', 'russia-html5-map'); ?>'>[?]</span>
                </div>
                <div style="float:left; width: 35%">
                    <label><span class="title"><?php _e('Stroke width:', 'russia-html5-map') ?> </span>
                    <input type="text" name="dPointNameStrokeWidth" id="dPointNameStrokeWidth" value="<?php echo $options[$map_id]['pointNameStrokeWidth'] ?>" style="width: 50px" /> px
                    <span class="tipsy-q" original-title='<?php esc_attr_e('The default stroke width for point\'s shortname. General setting will be used if empty.', 'russia-html5-map'); ?>'>[?]</span></td>
                </div>
                <div style="float:left; width: 35%">
                    <label><span class="title"><?php _e('Stroke opacity:', 'russia-html5-map') ?> </span>
                    <input type="text" name="dPointNameStrokeOpacity" id="dPointNameStrokeOpacity" value="<?php echo $options[$map_id]['pointNameStrokeOpacity'] ?>" style="width: 50px" />
                    <span class="tipsy-q" original-title='<?php esc_attr_e('The default stroke opacity for point\'s shortname. General setting will be used if empty.', 'russia-html5-map'); ?>'>[?]</span></td>
                </div>
                <div style="clear:both"></div>
        </div>
            <div style="clear:both"></div>
            <hr>
            <div style="width: 50%; float: left">
            <label><?php _e('Enable zoom:', 'russia-html5-map') ?> <input type="checkbox" onchange="map.enableZoom(jQuery(this).prop('checked'), true)"></label>
            </div>
            <div style="width: 50%; float: left; display: none">
            <label><?php _e('Ignore editor saved state:', 'russia-html5-map') ?> <input type="checkbox" id="ignoreES"></label>
            </div>
            <div style="clear:both"></div>
        </div>
        <hr>
        <div id="map_container"></div>
    </fieldset>
    <div style="display: none" id="dialogs">
        <div id="point_cfg">
        <?php
        $w = 32;
        $cp = count($pointTypes) > 1;
        if ($cp) $w = 25;
        if ($cp) {
        ?>
            <div style="float: left; width: <?php echo $w ?>%;">
                <label><span class="title" style="width: 80px"><?php _e('Point type:', 'russia-html5-map') ?> </span><select name="pointType" id="pointType" >
                <?php foreach ($pointTypes as $pt => $pn) { ?>
                <option value="<?php echo $pt ?>"><?php _e($pn, 'russia-html5-map') ?></option>
                <?php } ?>
                </select></label>
                <span class="tipsy-q" original-title="<?php esc_attr_e('Point type', 'russia-html5-map'); ?>">[?]</span><br />
            </div>
        <?php } ?>
            <div style="float: left; width: <?php echo $w ?>%;">
                <label><span class="title" style="width: 80px"><?php _e('X position:', 'russia-html5-map') ?> </span><input type="text" name="pointX" id="pointX" value="0" style="width: 50px"/></label>
                <span class="tipsy-q" original-title="<?php esc_attr_e('X position of the point', 'russia-html5-map'); ?>">[?]</span><br />
            </div>
            <div style="float: left; width: <?php echo $w ?>%;">
                <label><span class="title" style="width: 80px"><?php _e('Y position:', 'russia-html5-map') ?> </span><input type="text" name="pointY" id="pointY" value="0" style="width: 50px"/></label>
                <span class="tipsy-q" original-title="<?php esc_attr_e('Y position of the point', 'russia-html5-map'); ?>">[?]</span><br />
            </div>
            <div style="float: left; width: <?php echo $w ?>%;">
                <label><span class="title" style="width: 80px"><?php _e('Radius:', 'russia-html5-map') ?> </span><input type="number" name="pointRadius" id="pointRadius" value="4" style="width: 50px" min="1" max="<?php echo $maxRadius ?>"/></label>
                <span class="tipsy-q" original-title="<?php esc_attr_e('Radius of the point', 'russia-html5-map'); ?>">[?]</span><br />
            </div>
            <hr style="clear: both"/>
            <div style="float:left; min-width: 500px">
            <label><span class="title"><?php _e('Name:', 'russia-html5-map') ?> </span><input type="text" name="pointName" id="pointName" value=""/></label>
            <span class="tipsy-q" original-title="<?php esc_attr_e('This name will be show when mouse will be over this point', 'russia-html5-map'); ?>">[?]</span><br />
            <label><span class="title"><?php _e('Short name:', 'russia-html5-map') ?> </span><input type="text" name="pointShortname" id="pointShortname" value="" /></label>
            <span class="tipsy-q" original-title="<?php esc_attr_e('This name will be show near point on the map. Use \n to break the lines.', 'russia-html5-map'); ?>">[?]</span><br />
            </div>
            <div style="float:left; min-width: 500px">
            <label><span class="title"><?php _e('Text position:', 'russia-html5-map') ?></span><select name="pointTextPos" id="pointTextPos" style=" width: 190px">
                <option value="left-top"><?php _e('Left Top', 'russia-html5-map') ?></option>
                <option value="left-middle"><?php _e('Left Middle', 'russia-html5-map') ?></option>
                <option value="left-bottom"><?php _e('Left Bottom', 'russia-html5-map') ?></option>
                <option value="middle-top"><?php _e('Center Top', 'russia-html5-map') ?></option>
                <option value="middle-middle"><?php _e('Center Middle', 'russia-html5-map') ?></option>
                <option value="middle-bottom"><?php _e('Center Bottom', 'russia-html5-map') ?></option>
                <option value="right-top"><?php _e('Right Top', 'russia-html5-map') ?></option>
                <option value="right-middle"><?php _e('Right Middle', 'russia-html5-map') ?></option>
                <option value="right-bottom"><?php _e('Right Bottom', 'russia-html5-map') ?></option>
            </select></label>
            <span class="tipsy-q" original-title="<?php esc_attr_e('Shortname position relative to the point', 'russia-html5-map'); ?>">[?]</span>
            <br/>
            <label><span class="title"><?php _e('Font size:', 'russia-html5-map'); ?></span><input type="number" name="pointFS" id="pointFS" min="3" max="20" style="width: 190px"/> px
            <span class="tipsy-q" original-title='<?php _e('Font size of the shortname displayed near the point.', 'russia-html5-map'); ?>'>[?]</span>&nbsp;&nbsp;&nbsp;</label>
            <label for="pointFSDef"><input name="pointFSDef" id="pointFSDef" type="checkbox" /> <?php _e('Use default', 'russia-html5-map'); ?></label>
            <br />
            </div>
            <div style="clear: both"></div>

            <div style="border-top: 1px solid #ddd">
                <label style="position: relative; top: -13px; background: white"><a href="javascript:void(0);" onclick="show_comment(this.innerHTML.indexOf('+')!==-1)"><?php echo sprintf(__("Tooltip [%s]", "russia-html5-map"), "<span>+</span>") ?></a></label>
                <div id="pointCommentWrapp">
                    <?php russia_html5map_plugin_wp_editor_for_tooltip('', 'pointComment', 'pointComment'); ?>
                    <br />
                    <span class="title"><?php _e('Image URL:', 'russia-html5-map'); ?> </span>
                    <input onclick="imageFieldId = this.id; tb_show('Image', 'media-upload.php?type=image&tab=library&TB_iframe=true');" class="" type="text" id="pointImage" name="pointImage" value="" />
                    <span style="font-size: 10px; cursor: pointer;" onclick="jQuery('#pointImage').val('')"><?php _e('clear', 'russia-html5-map'); ?></span>
                    <span class="tipsy-q" original-title="<?php esc_attr_e('The path to file of the image to display in a popup', 'russia-html5-map'); ?>">[?]</span><br />
                </div>
            </div>
            <hr/>

            <div style="float:left; min-width: 500px">
            <span class="title"><?php _e('Point color:', 'russia-html5-map'); ?> </span><input class="color colorSimple" type="text" name="pointColor" id="pointColor" value="" style="background-color: white"  />
            <span class="tipsy-q" original-title='<?php esc_attr_e('The color of a point.', 'russia-html5-map'); ?>'>[?]</span><div class="colorpicker"></div>
            <label for="colorDef"><input name="colorDef" id="colorDef" class="colorOverCh" type="checkbox" /> <?php _e('Use default', 'russia-html5-map'); ?></label>
            <br />
            </div>
            <div style="float:left; min-width: 500px">
            <span class="title"><?php _e('Point hover color:', 'russia-html5-map'); ?> </span><input class="color colorOver" type="text" name="pointColorOver" id="pointColorOver" style="background-color: white"  />
            <span class="tipsy-q" original-title='<?php _e('The color of a point when the mouse cursor is over it.', 'russia-html5-map'); ?>'>[?]</span><div class="colorpicker"></div>
            <label for="colorOverDef"><input name="colorOverDef" id="colorOverDef" class="colorOverCh" type="checkbox" /> <?php _e('Use default', 'russia-html5-map'); ?></label>
            <br />
            </div>
            <div style="clear: both"></div>
            <div style="border-top: 1px solid #ddd">
                <label style="position: relative; top: -13px; background: white"><a href="javascript:void(0);" onclick="show_more_colors(this.innerHTML.indexOf('+')!==-1)"><?php echo sprintf(__("More color settings [%s]", "russia-html5-map"), "<span>+</span>") ?></a></label>
                <div id="pointColorsWrapp">
                    <div style="float:left; min-width: 500px">
                    <span class="title"><?php _e('Border color:', 'russia-html5-map'); ?> </span><input class="color colorSimple" type="text" name="borderColor" id="borderColor" value="" style="background-color: white"  />
                    <span class="tipsy-q" original-title='<?php esc_attr_e('The color of a point.', 'russia-html5-map'); ?>'>[?]</span><div class="colorpicker"></div>
                    <label for="borderColorDef"><input name="borderColorDef" id="borderColorDef" class="colorOverCh" type="checkbox" /> <?php _e('Use default', 'russia-html5-map'); ?></label>
                    <br />
                    <span class="title"><?php _e('Name color:', 'russia-html5-map'); ?> </span><input class="color colorSimple" type="text" name="nameColor" id="nameColor" value="" style="background-color: white"  />
                    <span class="tipsy-q" original-title='<?php esc_attr_e('The color of a point.', 'russia-html5-map'); ?>'>[?]</span><div class="colorpicker"></div>
                    <label for="nameColorDef"><input name="nameColorDef" id="nameColorDef" class="colorOverCh" type="checkbox" /> <?php _e('Use default', 'russia-html5-map'); ?></label>
                    <br />
                    <span class="title"><?php _e('Name stroke color:', 'russia-html5-map'); ?> </span><input class="color colorSimple" type="text" name="nameStrokeColor" id="nameStrokeColor" value="" style="background-color: white"  />
                    <span class="tipsy-q" original-title='<?php esc_attr_e('The color of a point.', 'russia-html5-map'); ?>'>[?]</span><div class="colorpicker"></div>
                    <label for="nameStrokeColorDef"><input name="nameStrokeColorDef" id="nameStrokeColorDef" class="colorOverCh" type="checkbox" /> <?php _e('Use default', 'russia-html5-map'); ?></label>
                    <br />
                    </div>

                    <div style="float:left; min-width: 500px">
                    <span class="title"><?php _e('Border hover color:', 'russia-html5-map'); ?> </span><input class="color colorOver" type="text" name="borderColorOver" id="borderColorOver" style="background-color: white"  />
                    <span class="tipsy-q" original-title='<?php _e('The color of a point when the mouse cursor is over it.', 'russia-html5-map'); ?>'>[?]</span><div class="colorpicker"></div>
                    <label for="borderColorOverDef"><input name="borderColorOverDef" id="borderColorOverDef" class="colorOverCh" type="checkbox" /> <?php _e('Use default', 'russia-html5-map'); ?></label>
                    <br />
                    <span class="title"><?php _e('Name hover color:', 'russia-html5-map'); ?> </span><input class="color colorOver" type="text" name="nameColorOver" id="nameColorOver" style="background-color: white"  />
                    <span class="tipsy-q" original-title='<?php _e('The color of a point when the mouse cursor is over it.', 'russia-html5-map'); ?>'>[?]</span><div class="colorpicker"></div>
                    <label for="nameColorOverDef"><input name="nameColorOverDef" id="nameColorOverDef" class="colorOverCh" type="checkbox" /> <?php _e('Use default', 'russia-html5-map'); ?></label>
                    <br />
                    <span class="title"><?php _e('Name stroke hover color:', 'russia-html5-map'); ?> </span><input class="color colorOver" type="text" name="nameStrokeColorOver" id="nameStrokeColorOver" style="background-color: white"  />
                    <span class="tipsy-q" original-title='<?php _e('The color of a point when the mouse cursor is over it.', 'russia-html5-map'); ?>'>[?]</span><div class="colorpicker"></div>
                    <label for="nameStrokeColorOverDef"><input name="nameStrokeColorOverDef" id="nameStrokeColorOverDef" class="colorOverCh" type="checkbox" /> <?php _e('Use default', 'russia-html5-map'); ?></label>
                    <br />
                    </div>
                </div>
            </div>
            <hr style="clear: both"/>
            <span class="title"><?php _e('On click action:', 'russia-html5-map'); ?> </span>
            <label><input type="radio" name="clickaction" id="ca-nothing" value="nothing" checked="checked" autocomplete="off"/> <?php _e('nothing', 'russia-html5-map') ?></label>&nbsp;&nbsp;&nbsp;
            <label><input type="radio" name="clickaction" id="ca-url"     value="url"  autocomplete="off"/> <?php _e('open link', 'russia-html5-map') ?></label>&nbsp;&nbsp;&nbsp;
            <label><input type="radio" name="clickaction" id="ca-info"    value="info" autocomplete="off"/> <?php _e('show additional information', 'russia-html5-map') ?></label>&nbsp;&nbsp;&nbsp;
            <label><input type="radio" name="clickaction" id="ca-popup"   value="popup" autocomplete="off" <?php echo (!count($popups)) ? "disabled" : ""; ?>/> <?php _e('Show lightbox popup', 'russia-html5-map') ?></label>&nbsp;&nbsp;&nbsp;
            <br>
            <div id="action-url" style="display: none">
            <hr style="clear: both"/>
                <span class="title"><?php _e('URL:', 'russia-html5-map'); ?> </span><input style="width: 270px;" class="" type="text" name="pointURL" id="pointURL" value="" />
                <span class="tipsy-q" original-title="<?php esc_attr_e('Open url on click (if specified)', 'russia-html5-map'); ?>">[?]</span></br>
                <span class="title"> </span>
                <label for="pointURLNW"><input name="pointURLNW" id="pointURLNW" class="" type="checkbox" /> <?php _e('Open url in a new window', 'russia-html5-map'); ?></label>
            </div>
            <div id="action-info" style="display: none">
            <hr style="clear: both"/>
            <span class="title"><?php _e('Description:', 'russia-html5-map'); ?> <span class="tipsy-q" original-title="<?php esc_attr_e('The description is displayed to the right of the map and contains contacts or some other additional information', 'russia-html5-map'); ?>">[?]</span> </span>
            <?php wp_editor('', 'pointAddInfo', $mce_options); ?>
            </div>

            <div style="display: none" id="action-popup"><br />
                <span class="title"><?php _e('Select lightbox popup:', 'russia-html5-map'); ?> </span>
                <select name="popup-id">
                    <?php foreach($popups as $pId => $pTitle) { ?>
                    <option value="<?php echo $pId; ?>"><?php echo $pTitle ?></option>
                    <?php } ?>
                </select>
            </div>
            <hr style="clear: both"/>
            <span class="title"><?php _e('CSS class:', 'russia-html5-map'); ?> </span><input  type="text" name="pointClass" id="pointClass"  />
            <span class="tipsy-q" original-title='<?php _e('You can specify several CSS classes separated by space', 'russia-html5-map'); ?>'>[?]</span>
        </div>
    </div>
    <link rel='stylesheet' href='<?php echo russia_html5map_plugin_get_static_url('css/map.css') ?>'>
    <script type='text/javascript' src='<?php echo russia_html5map_plugin_get_raphael_js_url() ?>'></script>
    <script type='text/javascript' src='<?php echo russia_html5map_plugin_get_map_js_url($options[$map_id]) ?>'></script>
    <style>
        <?php echo russia_html5map_plugin_prepare_tooltip_css($options, "#map_container") ?>
    </style>
<?php
if (isset($options[$map_id]['points'])) foreach ($options[$map_id]['points'] as $pid => &$pointData) {
    if (isset($options[$map_id]['state_info'][$pid]))
        $pointData['info'] = $options[$map_id]['state_info'][$pid];
}
unset($pointData);
if (isset($options[$map_id]['hideSN']) AND $options[$map_id]['hideSN']) {
    $data = json_decode($doptions['map_data'], true);
    $protected_shortnames = array();
    foreach ($data as $sid => &$d) {
        if (!in_array($sid, $protected_shortnames)) {
            $d['shortname'] = '';
        }
    }
    $doptions['map_data'] = json_encode($data);
}

?>
    <script>
        var map_cfg = {

        mapWidth        : 0,
        mapHeight       : 0,

        shadowAllow     : false,

        zoomMax   : <?php echo $options[$map_id]['zoomMax'] ?>,
        zoomStep   : <?php echo $options[$map_id]['zoomStep'] ?>,

        iPhoneLink      : <?php echo $doptions['iPhoneLink']; ?>,

        isNewWindow     : <?php echo $doptions['isNewWindow']; ?>,

        borderColor     : "<?php echo $doptions['borderColor']; ?>",
        borderColorOver     : "<?php echo $doptions['borderColorOver']; ?>",

        nameColor       : "<?php echo $doptions['nameColor']; ?>",
        nameFontSize        : "<?php echo $options[$map_id]['nameFontSize'].'px'; ?>",
        nameFontFamily      : "<?php echo russia_html5map_plugin_escape_fonts($options[$map_id]['nameFontFamily'], 'Arial, sans-serif'); ?>",
        nameFontWeight      : "<?php echo $options[$map_id]['nameFontWeight']; ?>",

        pointColor            : "<?php echo $options[$map_id]['pointColor']?>",
        pointColorOver        : "<?php echo $options[$map_id]['pointColorOver']?>",
        pointBorderColor        : "<?php echo $options[$map_id]['pointBorderColor']?>",
        pointBorderColorOver    : "<?php echo $options[$map_id]['pointBorderColorOver']?>",
        pointNameColor        : "<?php echo $options[$map_id]['pointNameColor']?>",
        pointNameColorOver    : "<?php echo $options[$map_id]['pointNameColorOver']?>",
        pointNameStrokeColor        : "<?php echo $options[$map_id]['pointNameStrokeColor']?>",
        pointNameStrokeColorOver    : "<?php echo $options[$map_id]['pointNameStrokeColorOver']?>",
        pointNameStroke : <?php echo is_null($options[$map_id]['pointNameStroke']) ? ($options[$map_id]['nameStroke'] ? 'true' : 'false') : ($options[$map_id]['pointNameStroke'] ? 'true' : 'false') ?>,
        pointNameStrokeWidth : "<?php echo (is_null($options[$map_id]['pointNameStrokeWidth']) ? $options[$map_id]['nameStrokeWidth'] : $options[$map_id]['pointNameStrokeWidth']); ?>",
        pointNameStrokeOpacity : "<?php echo (is_null($options[$map_id]['pointNameStrokeOpacity']) ? $options[$map_id]['nameStrokeOpacity'] : $options[$map_id]['pointNameStrokeOpacity']); ?>",
        pointNameFontSize    : "<?php echo intval($options[$map_id]['pointNameFontSize']).'px'?>",
        pointNameFontFamily    : "<?php echo russia_html5map_plugin_escape_fonts($options[$map_id]['pointNameFontFamily'] ? $options[$map_id]['pointNameFontFamily'] : $options[$map_id]['nameFontFamily'], 'Arial, sans-serif'); ?>",

        overDelay       : <?php echo $doptions['overDelay']; ?>,
        nameStroke      : <?php echo $doptions['nameStroke']?'true':'false'; ?>,
        nameStrokeColor : "<?php echo $doptions['nameStrokeColor']; ?>",
        nameStrokeWidth : "<?php echo $doptions['nameStrokeWidth']; ?>",
        nameStrokeOpacity : "<?php echo $doptions['nameStrokeOpacity']; ?>",
        map_data        : <?php echo $doptions['map_data']; ?>,
        ignoreLinks     : true,
        points          : <?php echo (isset($options[$map_id]['points']) AND $options[$map_id]['points']) ? json_encode($options[$map_id]['points']) : '{}' ?>
        };
<?php
    if (file_exists($params_file = dirname(__FILE__).'/static/paths.json')) {
        echo "map_cfg.map_params = ".file_get_contents($params_file).";\n";
    }
?>
        var map = new FlaShopRussiaMap(map_cfg);
        var activePoint = null;
        var editorSettings = <?php echo json_encode($editorSettings); ?>;
        jQuery(function($){
            var btnAdd = {
                'text': '<?php _e("Add", "russia-html5-map"); ?>',
                'icons': {
                    'primary': 'ui-icon-plus'
                },
                'click' : function() {
                    var x = parseFloat(pX.val());
                    var y = parseFloat(pY.val());
                    if (isNaN(x)) x = 0;
                    if (isNaN(y)) y = 0;
                    var p = map.addPoint(x, y, pN.val(), null, pT.val());
                    var link, isnw, info = null;
                    var act = $('input[name="clickaction"]:checked').val();
                    if (act == 'url') {
                        link = pU.val();
                        isnw = pUNW.attr('checked') ? true : false;
                    } else if (act == 'info') {
                        link = '#info';
                        info = editorGet();
                    } else if (act == 'popup') {
                        link = '#popup';
                    } else {
                        link = null;
                    }
                    var attrs = {
                        shortname:          pSN.val().replace(/\\n/g, "\n"),
                        comment:            tooltipEditorGet(),
                        image:              pImg.val(),
                        'class':            pCl.val(),
                        textPos:            editorSettings.textPosition = pTP.val(),
                        radius:             editorSettings.radius = pR.val() < 1 ? 1 : (pR.val() > <?php echo $maxRadius ?> ? <?php echo $maxRadius ?> : pR.val()),
                        color:              editorSettings.color = uDC.attr('checked') ? null : pC.val(),
                        colorOver:          editorSettings.colorOver = uDCO.attr('checked') ? null : pCO.val(),
                        borderColor:        editorSettings.borderColor = uBDC.attr('checked') ? null : pBC.val(),
                        borderColorOver:    editorSettings.borderColorOver = uBDCO.attr('checked') ? null : pBCO.val(),
                        nameColor:          editorSettings.nameColor = uNDC.attr('checked') ? null : pNC.val(),
                        nameColorOver:      editorSettings.nameColorOver = uNDCO.attr('checked') ? null : pNCO.val(),
                        nameStrokeColor:    editorSettings.nameStrokeColor = uNSDC.attr('checked') ? null : pNSC.val(),
                        nameStrokeColorOver: editorSettings.nameStrokeColorOver = uNSDCO.attr('checked') ? null : pNSCO.val(),
                        link: link,
                        info: info,
                        isNewWindow: isnw,
                        popup_id: $('select[name="popup-id"]').val()
                    };
                    if (uDFS.attr('checked')) {
                        attrs.nameFontSize = null;
                        editorSettings.nameFontSize = null;
                    } else {
                        var fs = parseInt(pFS.val());
                        fs = fs < 3 ? 3 : (fs > 20 ? 20 : fs);
                        attrs.nameFontSize = fs+'px';
                        editorSettings.nameFontSize = fs;
                    }
                    map.setPointAttr(p, attrs);
                    $(this).dialog('close');
                }
            };
            var btnSave = {
                'text': '<?php _e("Apply", "russia-html5-map"); ?>',
                'icons': {
                    'primary': 'ui-icon-save'
                },
                'click' : function() {
                    var x = parseFloat(pX.val());
                    var y = parseFloat(pY.val());
                    if (isNaN(x)) x = 0;
                    if (isNaN(y)) y = 0;
                    var link, isnw, info = null;
                    var act = $('input[name="clickaction"]:checked').val();
                    if (act == 'url') {
                        link = pU.val();
                        isnw = pUNW.attr('checked') ? true : false;
                    } else if (act == 'info') {
                        link = '#info';
                        info = editorGet();
                    } else if (act == 'popup') {
                        link = '#popup';
                    } else {
                        link = null;
                    }

                    var attrs = {
                        x: x,
                        y: y,
                        pointType:          pT.val(),
                        radius:             editorSettings.radius = pR.val() < 1 ? 1 : (pR.val() > <?php echo $maxRadius ?> ? <?php echo $maxRadius ?> : pR.val()),
                        name:               pN.val(),
                        shortname:          pSN.val().replace(/\\n/g, "\n"),
                        comment:            tooltipEditorGet(),
                        image:              pImg.val(),
                        'class':            pCl.val(),
                        textPos:            editorSettings.textPosition = pTP.val(),
                        color:              editorSettings.color = uDC.attr('checked') ? null : pC.val(),
                        colorOver:          editorSettings.colorOver = uDCO.attr('checked') ? null : pCO.val(),
                        borderColor:        editorSettings.borderColor = uBDC.attr('checked') ? null : pBC.val(),
                        borderColorOver:    editorSettings.borderColorOver = uBDCO.attr('checked') ? null : pBCO.val(),
                        nameColor:          editorSettings.nameColor = uNDC.attr('checked') ? null : pNC.val(),
                        nameColorOver:      editorSettings.nameColorOver = uNDCO.attr('checked') ? null : pNCO.val(),
                        nameStrokeColor:    editorSettings.nameStrokeColor = uNSDC.attr('checked') ? null : pNSC.val(),
                        nameStrokeColorOver: editorSettings.nameStrokeColorOver = uNSDCO.attr('checked') ? null : pNSCO.val(),
                        link: link,
                        info: info,
                        isNewWindow: isnw,
                        popup_id: $('select[name="popup-id"]').val()
                    };
                    if (uDFS.attr('checked')) {
                        attrs.nameFontSize = null;
                        editorSettings.nameFontSize = null;
                    } else {
                        var fs = parseInt(pFS.val());
                        fs = fs < 3 ? 3 : (fs > 20 ? 20 : fs);
                        attrs.nameFontSize = fs+'px';
                        editorSettings.nameFontSize = fs;
                    }
                    map.setPointAttr(activePoint, attrs);
                    $(this).dialog('close');
                    activePoint = null;
                }
            };
            var btnDelete = {
                'text': '<?php _e("Delete", "russia-html5-map"); ?>',
                'icons': {
                    'primary': 'ui-icon-delete'
                },
                'click' : function() {
                    var name = map.fetchPointAttr(activePoint, 'name');
                    if (confirm('<?php _e("Are you sure you want to delete point", "russia-html5-map") ?> '+name)) {
                        map.deletePoint(activePoint);
                        activePoint = null;
                        $(this).dialog('close');
                    }
                }
            };
            var btnClose = {
                'text': '<?php _e("Cancel", "russia-html5-map"); ?>',
                'icons': {
                    'primary': 'ui-icon-close'
                },
                'click' : function() {
                    $(this).dialog('close');
                }
            };
            map.draw('map_container');
            map.on('dblclick', function(ev, sid, map){
                dlg.dialog('open');
                if (sid && map.mapConfig.points[sid]) {
                    var p = map.mapConfig.points[sid];
                    pX.val(p.x);
                    pY.val(p.y);
                    pN.val(p.name);
                    pSN.val(typeof(p.shortname) === 'string' ? p.shortname.replace(/\n/g, '\\n') : '');
                    pR.val(p.radius);
                    pU.val('http://');
                    pT.val(p.pointType);
                    var act = 'nothing';
                    editorSet('');
                    if ((p.link && /^javascript:russiahtml5map_set_state_text/.test(p.link)) || p.link == '#info')
                    {
                        act = 'info';
                        editorSet(p.info?p.info:'');
                    }
                    else if (p.link=='#popup') {
                        act = 'popup';
                        $('select[name="popup-id"]').val(p.popup_id);
                    }
                    else if (p.link)
                        act = 'url';
                    $('#ca-'+act).attr('checked', 'checked').click();
                    pU.val(act == 'url' && p.link ? p.link : 'http://');
                    pTP.val(p.textPos ? p.textPos : 'right-middle');
                    pUNW.attr('checked', p.isNewWindow ? 'checked' : false);
                    dlg.dialog('option', {
                        'title': '<?php _e("Edit point: %s (id: %s)", "russia-html5-map") ?>'.replace('%s', p.name).replace('%s', sid),
                        'buttons': [btnSave, btnDelete, btnClose]
                    });
                    tooltipEditorSet(p.comment);
                    pImg.val(p.image ? p.image : '');
                    pCl.val(p.class ? p.class : '');
                    show_comment(!!p.comment);

                    if (p.color) {
                        uDC.attr('checked', false);
                        pC.val(p.color).css('backgroundColor', p.color).attr('disabled', false);
                    } else {
                        uDC.attr('checked', 'checked');
                        pC.val(dpC.val()).css('backgroundColor', dpC.val()).attr('disabled', 'disabled');
                    }
                    if (p.colorOver) {
                        uDCO.attr('checked', false);
                        pCO.val(p.colorOver).css('backgroundColor', p.colorOver).attr('disabled', false);
                    } else {
                        uDCO.attr('checked', 'checked');
                        pCO.val(dpCO.val()).css('backgroundColor', dpCO.val()).attr('disabled', 'disabled');
                    }

                    var smc = false;
                    if (p.borderColor) {
                        smc = true;
                        uBDC.attr('checked', false);
                        pBC.val(p.borderColor).css('backgroundColor', p.borderColor).attr('disabled', false);
                    } else {
                        uBDC.attr('checked', 'checked');
                        pBC.val(dpBC.val()).css('backgroundColor', dpBC.val()).attr('disabled', 'disabled');
                    }
                    if (p.borderColorOver) {
                        smc = true;
                        uBDCO.attr('checked', false);
                        pBCO.val(p.borderColorOver).css('backgroundColor', p.borderColorOver).attr('disabled', false);
                    } else {
                        uBDCO.attr('checked', 'checked');
                        pBCO.val(dpBCO.val()).css('backgroundColor', dpBCO.val()).attr('disabled', 'disabled');
                    }

                    if (p.nameColor) {
                        smc = true;
                        uNDC.attr('checked', false);
                        pNC.val(p.nameColor).css('backgroundColor', p.nameColor).attr('disabled', false);
                    } else {
                        uNDC.attr('checked', 'checked');
                        pNC.val(dpNC.val()).css('backgroundColor', dpNC.val()).attr('disabled', 'disabled');
                    }
                    if (p.nameColorOver) {
                        smc = true;
                        uNDCO.attr('checked', false);
                        pNCO.val(p.nameColorOver).css('backgroundColor', p.nameColorOver).attr('disabled', false);
                    } else {
                        uNDCO.attr('checked', 'checked');
                        pNCO.val(dpNCO.val()).css('backgroundColor', dpNCO.val()).attr('disabled', 'disabled');
                    }

                    if (p.nameStrokeColor) {
                        smc = true;
                        uNSDC.attr('checked', false);
                        pNSC.val(p.nameStrokeColor).css('backgroundColor', p.nameStrokeColor).attr('disabled', false);
                    } else {
                        uNSDC.attr('checked', 'checked');
                        pNSC.val(dpNSC.val()).css('backgroundColor', dpNSC.val()).attr('disabled', 'disabled');
                    }
                    if (p.nameStrokeColorOver) {
                        smc = true;
                        uNSDCO.attr('checked', false);
                        pNSCO.val(p.nameStrokeColorOver).css('backgroundColor', p.nameStrokeColorOver).attr('disabled', false);
                    } else {
                        uNSDCO.attr('checked', 'checked');
                        pNSCO.val(dpNSCO.val()).css('backgroundColor', dpNSCO.val()).attr('disabled', 'disabled');
                    }

                    if (p.nameFontSize) {
                        pFS.val(parseInt(p.nameFontSize)).attr('disabled', false);
                        uDFS.attr('checked', false);
                    } else {
                        pFS.val(parseInt(map.mapConfig.pointNameFontSize)).attr('disabled', 'disabled');
                        uDFS.attr('checked', 'checked');
                    }
                    /*show_more_colors(smc);*/
                    show_more_colors(false);

                    activePoint = sid;
                } else {
                    var useSavedState = iES.attr('checked') ? false : true;
                    var color;
                    pX.val(ev.onMapX);
                    pY.val(ev.onMapY);
                    pN.val('');
                    pSN.val('');
                    pU.val('http://');
                    pT.val('');
                    pR.val((useSavedState && editorSettings.radius) ? editorSettings.radius : pDR.val());
                    pTP.val(useSavedState ? editorSettings.textPosition : 'right-middle');
                    tooltipEditorSet('');
                    pImg.val('');
                    pCl.val('');
                    show_comment(false);
                    pUNW.attr('checked', false);
                    editorSet('');
                    $('#ca-nothing').attr('checked', 'checked').click();

                    pC.val(color = (useSavedState && editorSettings.color) ? editorSettings.color : dpC.val()).css('backgroundColor', color).attr('disabled', (useSavedState && editorSettings.color) ? false : 'disabled');
                    uDC.attr('checked', (useSavedState && editorSettings.color) ? false : 'checked');
                    pCO.val(color = (useSavedState && editorSettings.colorOver) ? editorSettings.colorOver : dpCO.val()).css('backgroundColor', color).attr('disabled', (useSavedState && editorSettings.colorOver) ? false : 'disabled');
                    uDCO.attr('checked', (useSavedState && editorSettings.colorOver) ? false : 'checked');

                    pBC.val(color = (useSavedState && editorSettings.borderColor) ? editorSettings.borderColor : dpBC.val()).css('backgroundColor', color).attr('disabled', (useSavedState && editorSettings.borderColor) ? false : 'disabled');
                    uBDC.attr('checked', (useSavedState && editorSettings.borderColor) ? false : 'checked');
                    pBCO.val(color = (useSavedState && editorSettings.borderColorOver) ? editorSettings.borderColorOver : dpBCO.val()).css('backgroundColor', color).attr('disabled', (useSavedState && editorSettings.borderColorOver) ? false : 'disabled');
                    uBDCO.attr('checked', (useSavedState && editorSettings.borderColorOver) ? false : 'checked');

                    pNC.val(color = (useSavedState && editorSettings.nameColor) ? editorSettings.nameColor : dpNC.val()).css('backgroundColor', color).attr('disabled', (useSavedState && editorSettings.nameColor) ? false : 'disabled');
                    uNDC.attr('checked', (useSavedState && editorSettings.nameColor) ? false : 'checked');
                    pNCO.val(color = (useSavedState && editorSettings.nameColorOver) ? editorSettings.nameColorOver : dpNCO.val()).css('backgroundColor', color).attr('disabled', (useSavedState && editorSettings.nameColorOver) ? false : 'disabled');
                    uNDCO.attr('checked', (useSavedState && editorSettings.nameColorOver) ? false : 'checked');

                    pNSC.val(color = (useSavedState && editorSettings.nameStrokeColor) ? editorSettings.nameStrokeColor : dpNSC.val()).css('backgroundColor', color).attr('disabled', (useSavedState && editorSettings.nameStrokeColor) ? false : 'disabled');
                    uNSDC.attr('checked', (useSavedState && editorSettings.nameStrokeColor) ? false : 'checked');
                    pNSCO.val(color = (useSavedState && editorSettings.nameStrokeColorOver) ? editorSettings.nameStrokeColorOver : dpNSCO.val()).css('backgroundColor', color).attr('disabled', (useSavedState && editorSettings.nameStrokeColorOver) ? false : 'disabled');
                    uNSDCO.attr('checked', (useSavedState && editorSettings.nameStrokeColorOver) ? false : 'checked');

                    if (useSavedState) {
                        /*show_more_colors(
                            editorSettings.color||editorSettings.colorOver||editorSettings.borderColor||editorSettings.borderColorOver||
                            editorSettings.nameColor||editorSettings.nameColorOver||editorSettings.nameStrokeColor||editorSettings.nameStrokeColorOver
                        );*/
                        show_more_colors(false);
                    } else {
                        show_more_colors(false);
                    }

                    pFS.val((useSavedState && editorSettings.nameFontSize) ? editorSettings.nameFontSize : parseInt(Math.min(20, Math.max(3, dpFS.val())))).attr('disabled', (useSavedState && editorSettings.nameFontSize) ? false : 'disabled');
                    uDFS.attr('checked', (useSavedState && editorSettings.nameFontSize) ? false : 'checked');
                    dlg.dialog('option', {
                        'title': '<?php _e("Add new point", "russia-html5-map") ?>',
                        'buttons': [btnAdd, btnClose]
                    });
                    activePoint = null;
                }
            });
            var lastX = 0, lastY = 0, is_moving = false;
            map.on('mousedown', function(ev, sid, map) { if (sid && map.mapConfig.points[sid]) {
                lastX = ev.onMapX;
                lastY = ev.onMapY;
                is_moving = sid;
                ev.stopPropagation();
            } });
            map.on('mouseup', function(ev, sid, map) {
                lastX = 0;
                lastY = 0;
                is_moving = false;
                });
            var round = function (x) {
                return Math.round(x * 1000) / 1000;
            };
            map.on('mousemove', function(ev, sid, map) {
                if (is_moving) {
                    var dx = ev.onMapX - lastX,
                        dy = ev.onMapY - lastY;
                    map.setPointAttr(is_moving, {
                        x: round(map.fetchPointAttr(is_moving, 'x')+dx),
                        y: round(map.fetchPointAttr(is_moving, 'y')+dy)
                    });
                    lastX = ev.onMapX;
                    lastY = ev.onMapY;
                    ev.stopPropagation();
                }
            });
            var pX = $('#pointX');
            var pY = $('#pointY');
            var pT = $('#pointType');
            var pN = $('#pointName');
            var pSN = $('#pointShortname');

            var pC  = $('#pointColor');
            var pCO = $('#pointColorOver');
            var dpC  = $('#dPointColor');
            var dpCO = $('#dPointColorOver');
            var uDC  = $('#colorDef').change(function() {
                var checked = $(this).attr('checked'), ac = null;
                pC.attr('disabled', checked ? 'disabled' : null);
                if (checked)
                    pC.val(dpC.val()).css('backgroundColor', dpC.val());
                else if (activePoint && (ac = map.fetchPointAttr(activePoint, 'color')))
                    pC.val(ac).css('backgroundColor', ac);
            });
            var uDCO = $('#colorOverDef').change(function() {
                var checked = $(this).attr('checked'), ac = null;
                pCO.attr('disabled', checked ? 'disabled' : null);
                if (checked)
                    pCO.val(dpCO.val()).css('backgroundColor', dpCO.val());
                else if (activePoint && (ac = map.fetchPointAttr(activePoint, 'colorOver')))
                    pCO.val(ac).css('backgroundColor', ac);
            });

            var pBC  = $('#borderColor');
            var pBCO = $('#borderColorOver');
            var dpBC  = $('#dPointBorderColor');
            var dpBCO = $('#dPointBorderColorOver');
            var uBDC  = $('#borderColorDef').change(function() {
                var checked = $(this).attr('checked'), ac = null;
                pBC.attr('disabled', checked ? 'disabled' : null);
                if (checked)
                    pBC.val(dpBC.val()).css('backgroundColor', dpBC.val());
                else if (activePoint && (ac = map.fetchPointAttr(activePoint, 'borderColor')))
                    pBC.val(ac).css('backgroundColor', ac);
            });
            var uBDCO = $('#borderColorOverDef').change(function() {
                var checked = $(this).attr('checked'), ac = null;
                pBCO.attr('disabled', checked ? 'disabled' : null);
                if (checked)
                    pBCO.val(dpBCO.val()).css('backgroundColor', dpBCO.val());
                else if (activePoint && (ac = map.fetchPointAttr(activePoint, 'borderColorOver')))
                    pBCO.val(ac).css('backgroundColor', ac);
            });

            var pNC  = $('#nameColor');
            var pNCO = $('#nameColorOver');
            var dpNC  = $('#dPointNameColor');
            var dpNCO = $('#dPointNameColorOver');
            var uNDC  = $('#nameColorDef').change(function() {
                var checked = $(this).attr('checked'), ac = null;
                pNC.attr('disabled', checked ? 'disabled' : null);
                if (checked)
                    pNC.val(dpNC.val()).css('backgroundColor', dpNC.val());
                else if (activePoint && (ac = map.fetchPointAttr(activePoint, 'nameColor')))
                    pNC.val(ac).css('backgroundColor', ac);
            });
            var uNDCO = $('#nameColorOverDef').change(function() {
                var checked = $(this).attr('checked'), ac = null;
                pNCO.attr('disabled', checked ? 'disabled' : null);
                if (checked)
                    pNCO.val(dpNCO.val()).css('backgroundColor', dpNCO.val());
                else if (activePoint && (ac = map.fetchPointAttr(activePoint, 'nameColorOver')))
                    pNCO.val(ac).css('backgroundColor', ac);
            });

            var pNSC  = $('#nameStrokeColor');
            var pNSCO = $('#nameStrokeColorOver');
            var dpNSC  = $('#dPointNameStrokeColor');
            var dpNSCO = $('#dPointNameStrokeColorOver');
            var uNSDC  = $('#nameStrokeColorDef').change(function() {
                var checked = $(this).attr('checked'), ac = null;
                pNSC.attr('disabled', checked ? 'disabled' : null);
                if (checked)
                    pNSC.val(dpNSC.val()).css('backgroundColor', dpNSC.val());
                else if (activePoint && (ac = map.fetchPointAttr(activePoint, 'nameStrokeColor')))
                    pNSC.val(ac).css('backgroundColor', ac);
            });
            var uNSDCO = $('#nameStrokeColorOverDef').change(function() {
                var checked = $(this).attr('checked'), ac = null;
                pNSCO.attr('disabled', checked ? 'disabled' : null);
                if (checked)
                    pNSCO.val(dpNSCO.val()).css('backgroundColor', dpNSCO.val());
                else if (activePoint && (ac = map.fetchPointAttr(activePoint, 'nameStrokeColorOver')))
                    pNSCO.val(ac).css('backgroundColor', ac);
            });

            var pR   = $('#pointRadius');
            var pDR  = $('#dPointRadius');
            var pTP  = $('#pointTextPos');
                pCmt = $('#pointComment');
            var pImg = $('#pointImage');
            var pCl  = $('#pointClass');
                pAI  = $('#pointAddInfo');
            var pFS  = $('#pointFS');
            var dpFS = $('#dPointFontSize');
            var uDFS = $('#pointFSDef').change(function() {
                pFS.attr('disabled', $(this).attr('checked') ? 'disabled' : null);
            });
            var pU = $('#pointURL');
            var pUNW = $('#pointURLNW');
            var dlg = $('#point_cfg').dialog({
                'minWidth': 600,
                'width': '80%',
                'autoOpen': false,
                'dialogClass': 'russia-html5-map',
                'buttons': []
            });
            var iES = $('#ignoreES');
            $('input[name="clickaction"]').click(function(){
                $('#action-url, #action-info, #action-popup').hide();
                $('#action-'+$('input[name="clickaction"]:checked').val()).show();
            });
            try{
            if (typeof tinyMCE !== 'undefined') tinyMCE.execCommand('mceAddControl', true, 'pointAddInfo');
            } catch (e) { console.log(e) }
        });
        var pCmt;
        var pAI;
        function show_comment(show) {
            if (typeof show == 'undefined')
                show = true;
            var w = jQuery('#pointCommentWrapp');
            var p = w.prev().find('span');
            p.html(show ? '-' : '+');
            show ? w.show() : w.hide();
        }
        function show_more_colors(show) {
            if (typeof show == 'undefined')
                show = true;
            var w = jQuery('#pointColorsWrapp');
            var p = w.prev().find('span');
            p.html(show ? '-' : '+');
            show ? w.show() : w.hide();
        }
        function show_default_options(show) {
            if (typeof show == 'undefined')
                show = true;
            var w = jQuery('#pointDefOptionsWrapp');
            var p = w.prev().find('span');
            p.html(show ? '-' : '+');
            show ? w.show() : w.hide();
        }
        function editorSet(txt) {
            if (jQuery('#wp-pointAddInfo-wrap').hasClass('tmce-active') && tinyMCE.get('pointAddInfo')) {
                tinyMCE.get('pointAddInfo').setContent(txt);
            } else {
                pAI.val(txt);
            }
        }
        function editorGet() {
            if (jQuery('#wp-pointAddInfo-wrap').hasClass('tmce-active') && tinyMCE.get('pointAddInfo')) {
                return tinyMCE.get('pointAddInfo').getContent();
            } else {
                return pAI.val();
            }
        }
        function tooltipEditorSet(txt) {
            if (jQuery('#wp-pointComment-wrap').hasClass('tmce-active') && tinyMCE.get('pointComment')) {
                tinyMCE.get('pointComment').setContent(txt);
            } else {
                pCmt.val(txt);
            }
        }
        function tooltipEditorGet() {
            if (jQuery('#wp-pointComment-wrap').hasClass('tmce-active') && tinyMCE.get('pointComment')) {
                return tinyMCE.get('pointComment').getContent();
            } else {
                return pCmt.val();
            }
        }
    </script>
    <input type="hidden" name="act_type" value="russia-html5-map-points-save" />
    <input type="hidden" name="map_points"  id="map_points"  />
    <input type="hidden" name="points_info" id="points_info" />
    <input type="hidden" name="editor_settings" id="editor_settings" />
    <p class="submit">
        <input type="submit" value="<?php esc_attr_e('Save Changes', 'russia-html5-map'); ?>" class="button-primary" id="submit" name="submit" disabled>
        &nbsp;&nbsp;&nbsp;
        <label style="display: none"><input type="checkbox" name="clear_saved_state" /> <?php _e('Clear editor saved state'); ?></label>
    </p>
</form>
</div>
