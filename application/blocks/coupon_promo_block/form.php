<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div id="form-container-<?php echo $uniqueID; ?>">

    <?php
    echo $app->make('helper/concrete/ui')->tabs([
        ['entries-tab-'.$uniqueID, t('Entries'), true],
        ['settings-tab-'.$uniqueID, t('Settings')],
    ]);
    ?>

    <div class="tab-content mt-4">

        <div class="js-tab-pane tab-pane show active" id="entries-tab-<?php echo $uniqueID; ?>">

            <div class="mb-3 entries-actions">
                <button type="button" class="btn btn-primary entries-action-button js-add-entry" data-action="prepend"><?php echo t('Add at the top'); ?></button>
                <button type="button" class="btn btn-primary entries-action-button js-add-entry" data-action="append"><?php echo t('Add at the bottom'); ?></button>
                <button type="button" class="btn btn-primary entries-action-button js-copy-last-entry" data-action="append"><?php echo t('Copy last entry'); ?></button>
                <span class="entries-actions-links">
                    <a href="#" class="entries-action-link js-expand-all"><i class="far fa-plus-square"></i> <?php echo t('Expand all'); ?></a>
                    <a href="#" class="entries-action-link js-collapse-all"><i class="far fa-minus-square"></i> <?php echo t('Collapse all'); ?></a>
                </span>
                <a href="#" class="entries-action-link entries-action-link-remove-all js-remove-all" data-confirm-text="<?php echo t('Are you sure?'); ?>"  title="<?php echo t('Remove all'); ?>"><i class="fas fa-times-circle"></i></a>
            </div>

            <div class="entries" id="entries-<?php echo $uniqueID; ?>" data-entries="<?php echo htmlspecialchars(json_encode($entries)); ?>" data-column-names="<?php echo h(json_encode($entryColumnNames)); ?>"></div>

            <div class="mb-3 entries-actions">
                <button type="button" class="btn btn-primary entries-action-button js-add-entry" data-action="prepend"><?php echo t('Add at the top'); ?></button>
                <button type="button" class="btn btn-primary entries-action-button js-add-entry" data-action="append"><?php echo t('Add at the bottom'); ?></button>
                <button type="button" class="btn btn-primary entries-action-button js-copy-last-entry" data-action="append"><?php echo t('Copy last entry'); ?></button>
                <span class="entries-actions-links">
                    <a href="#" class="entries-action-link js-expand-all"><i class="far fa-plus-square"></i> <?php echo t('Expand all'); ?></a>
                    <a href="#" class="entries-action-link js-collapse-all"><i class="far fa-minus-square"></i> <?php echo t('Collapse all'); ?></a>
                </span>
                <a href="#" class="entries-action-link entries-action-link-remove-all js-remove-all" data-confirm-text="<?php echo t('Are you sure?'); ?>"  title="<?php echo t('Remove all'); ?>"><i class="fas fa-times-circle"></i></a>
            </div>

            <div class="mb-3">
                <div class="form-check-inline">
                    <input type="checkbox"
                        name="<?php echo $view->field('disableSmoothScroll'); ?>"
                        class="form-check-input js-disable-smooth-scroll"
                        value="1"
                        id="<?php echo $view->field('disableSmoothScroll'); ?>"
                    />
                    <label for="<?php echo $view->field('disableSmoothScroll'); ?>" class="form-check-label"><?php echo t('Disable smooth scroll'); ?></label>
                </div>
                <div class="form-check-inline">
                    <input type="checkbox"
                        name="<?php echo $view->field('keepAddedEntryCollapsed'); ?>"
                        class="form-check-input js-keep-added-entry-collapsed"
                        value="1"
                        id="<?php echo $view->field('keepAddedEntryCollapsed'); ?>"
                    />
                    <label for="<?php echo $view->field('keepAddedEntryCollapsed'); ?>" class="form-check-label"><?php echo t('Keep added/copied entry collapsed'); ?></label>
                </div>
            </div>

            <script type="text/template" class="js-entry-template">

                <div class="well entry js-entry" data-position="<%=_.escape(position)%>">

                    <div class="entry-header">
                        <button type="button" class="entry-header-action entry-header-duplicate-entry-and-add-at-the-end js-duplicate-entry-and-add-at-the-end" title="<?php echo t('Duplicate entry and add at the end'); ?>"><i class="far fa-clone"></i></button>
                        <button type="button" class="entry-header-action entry-header-duplicate-entry js-duplicate-entry" title="<?php echo t('Duplicate entry'); ?>"><i class="fas fa-clone"></i></button>
                        <div class="entry-header-action entry-header-remove-entry js-remove-entry" data-confirm-text="<?php echo t('Are you sure?'); ?>"  title="<?php echo t('Remove entry'); ?>"><i class="fas fa-times"></i></div>
                        <div class="entry-header-action entry-header-move-entry js-move-entry"><i class="fas fa-arrows-alt"></i></div>
                        <div class="entry-header-action entry-header-toggle-entry js-toggle-entry" data-action="collapse"><i class="far fa-minus-square"></i></div>
                        <div class="entry-header-title">
                            <span class="js-entry-title">
                                #<%=_.escape(position)%>
                            </span>
                        </div>
                    </div>

                    <div class="entry-content js-entry-content" <% if (keepAddedEntryCollapsed) { %>style="display: none;"<% } %>>

                        <div class="mb-4">
                            <label for="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][titleBadge]" class="form-label"><?php echo t('Title Badge'); ?></label>
                            <input type="text" id="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][titleBadge]" name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][titleBadge]" value="<%=_.escape(titleBadge)%>" class="form-control" maxlength="255" />
                        </div>

                        <div class="mb-4 js-custom-editor-height-<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][mainText]-<?php echo $uniqueID; ?>">
                            <label for="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][mainText]" class="form-label"><?php echo t('Main Text'); ?></label>
                            <textarea data-activation-script="mainText" style="display: none;" class="js-editor-content" name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][mainText]" id="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][mainText]"><%=_.escape(mainText)%></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][buttonText]" class="form-label"><?php echo t('Button Text'); ?></label>
                            <input type="text" id="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][buttonText]" name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][buttonText]" value="<%=_.escape(buttonText)%>" class="form-control" maxlength="255" />
                        </div>

                        <div class="field-group">

                            <div class="mb-4">
                                <label for="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][buttonURL]" class="form-label"><?php echo t('Button URL'); ?></label>
                                <div class="row">
                                    <div class="col-12 col-lg-3 margin-bottom-on-mobile">
                                        <select id="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][buttonURL_protocol]" name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][buttonURL_protocol]" class="form-select js-external-link-protocol">
                                            <?php foreach ($externalLinkProtocols as $k => $v): ?>
                                                <option value="<?php echo $k; ?>" <% if (buttonURL_protocol=='<?php echo $k; ?>') { %>selected="selected"<% } %> ><?php echo h($v); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        </div>
                                        <div class="col-12 col-lg-9">
                                        <input type="text" id="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][buttonURL]" name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][buttonURL]" value="<%=_.escape(buttonURL)%>" class="form-control js-external-link-url" maxlength="255" />
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="field-group field-group-highlight mb-4 js-image-wrapper">

                            <div class="row margin-bottom">
                                <div class="col-12">
                                    <label for="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image]" class="form-label"><?php echo t('Image'); ?></label>
                                </div>
                                <div class="col-12 col-lg-6 margin-bottom-on-mobile">
                                    <div data-concrete-file-input="js-file-selector">
                                        <concrete-file-input :file-id="<%= image ? _.escape(image) : '0' %>"
                                            choose-text="<?php echo t('Choose File'); ?>"
                                            input-name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image]"
                                        ></concrete-file-input>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <span class="toggle-additional-image-fields <% if (parseInt(image_show_additional_fields)) { %>toggle-additional-image-fields-active<% } %> btn btn-secondary js-toggle-additional-image-fields"
                                        data-show-text="<?php echo t('Show additional fields'); ?>"
                                        data-hide-text="<?php echo t('Hide additional fields'); ?>"
                                    ><i class="fas fa-caret-right"></i> <span class="js-toggle-additional-image-fields-text"><% if (parseInt(image_show_additional_fields)) { %><?php echo t('Hide additional fields'); ?><% } else { %><?php echo t('Show additional fields'); ?><% } %></span></span>
                                    <input type="hidden" class="js-toggle-additional-image-fields-value" id="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_show_additional_fields]" name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_show_additional_fields]" value="<%=_.escape(image_show_additional_fields)%>" maxlength="255" />
                                </div>
                            </div>

                            <div class="js-additional-image-fields-wrapper" <% if (!parseInt(image_show_additional_fields)) { %>style="display: none;"<% } %>>

                                <div class="mb-4">
                                    <label for="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_alt]" class="form-label"><?php echo t('Alt text'); ?></label>
                                    <input type="text" id="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_alt]" name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_alt]" value="<%=_.escape(image_alt)%>" class="form-control" maxlength="255" />
                                </div>

                                <div class="row margin-bottom">
                                    <div class="col-12">
                                        <label for="" class="form-label"><?php echo t('Override Thumbnail dimensions'); ?></label>
                                        <div class="form-check">
                                            <input type="checkbox" id="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_override_dimensions]" name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_override_dimensions]" class="js-toggle-override-image-dimensions form-check-input" value="<%=parseInt(image_override_dimensions)%>" <% if (parseInt(image_override_dimensions)) { %>checked<% } %> />
                                            <label for="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_override_dimensions]" class="form-check-label"><?php echo t('Yes'); ?></label>
                                        </div>
                                    </div>
                                    <div class="row mt-3 js-override-image-dimensions-wrapper" <% if (!parseInt(image_override_dimensions)) { %>style="display: none;"<% } %>>
                                        <div class="col-12 col-lg-4 margin-bottom-on-mobile">
                                            <label for="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_width]" class="form-label"><?php echo t('Width'); ?></label>
                                            <div class="input-group">
                                                <input type="number" id="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_width]" name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_width]" value="<% if (parseInt(image_custom_width)) { %><%=_.escape(image_custom_width)%><% } else { %><% } %>" class="form-control ccm-input-number" />
                                                <span class="input-group-text"><?php echo t('px'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-4 margin-bottom-on-mobile">
                                            <label for="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_height]" class="form-label"><?php echo t('Height'); ?></label>
                                            <div class="input-group">
                                                <input type="number" id="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_height]" name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_height]" value="<% if (parseInt(image_custom_height)) { %><%=_.escape(image_custom_height)%><% } else { %><% } %>" class="form-control ccm-input-number" />
                                                <span class="input-group-text"><?php echo t('px'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <label for="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_crop]" class="form-label"><?php echo t('Crop'); ?></label>
                                            <select id="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_crop]" name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_crop]" class="form-select">
                                                <option value="0" <% if (!parseInt(image_custom_crop)) { %>selected="selected"<% } %>><?php echo t('No'); ?></option>
                                                <option value="1" <% if (parseInt(image_custom_crop)==1) { %>selected="selected"<% } %>><?php echo t('Yes'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row margin-bottom">
                                    <div class="col-12">
                                        <label for="" class="form-label"><?php echo t('Override Fullscreen Image dimensions'); ?></label>
                                        <div class="form-check">
                                            <input type="checkbox" id="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_override_fullscreen_dimensions]" name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_override_fullscreen_dimensions]" class="form-check-input js-toggle-override-fullscreen-image-dimensions" value="<%=parseInt(image_override_fullscreen_dimensions)%>" <% if (parseInt(image_override_fullscreen_dimensions)) { %>checked<% } %> />
                                            <label for="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_override_fullscreen_dimensions]" class="form-check-label"><?php echo t('Yes'); ?></label>
                                        </div>
                                    </div>
                                    <div class="row mt-3 js-override-fullscreen-image-dimensions-wrapper" <% if (!parseInt(image_override_fullscreen_dimensions)) { %>style="display: none;"<% } %>>
                                        <div class="col-12 col-lg-4 margin-bottom-on-mobile">
                                            <label for="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_fullscreen_width]" class="form-label"><?php echo t('Width'); ?></label>
                                            <div class="input-group">
                                                <input type="number" id="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_fullscreen_width]" name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_fullscreen_width]" value="<% if (parseInt(image_custom_fullscreen_width)) { %><%=_.escape(image_custom_fullscreen_width)%><% } else { %><% } %>" class="form-control ccm-input-number" />
                                                <span class="input-group-text"><?php echo t('px'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-4 margin-bottom-on-mobile">
                                            <label for="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_fullscreen_height]" class="form-label"><?php echo t('Height'); ?></label>
                                            <div class="input-group">
                                                <input type="number" id="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_fullscreen_height]" name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_fullscreen_height]" value="<% if (parseInt(image_custom_fullscreen_height)) { %><%=_.escape(image_custom_fullscreen_height)%><% } else { %><% } %>" class="form-control ccm-input-number" />
                                                <span class="input-group-text"><?php echo t('px'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <label for="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_fullscreen_crop]" class="form-label"><?php echo t('Crop'); ?></label>
                                            <select id="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_fullscreen_crop]" name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][image_custom_fullscreen_crop]" class="form-select">
                                                <option value="0" <% if (!parseInt(image_custom_fullscreen_crop)) { %>selected="selected"<% } %>><?php echo t('No'); ?></option>
                                                <option value="1" <% if (parseInt(image_custom_fullscreen_crop)==1) { %>selected="selected"<% } %>><?php echo t('Yes'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div><?php // .js-image-wrapper ?>

                        <div class="mb-4">
                            <label for="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][backgroundColor]" class="form-label"><?php echo t('Background Color'); ?></label>
                            <div>
                                <input type="text"
                                       id="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][backgroundColor]"
                                       name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][backgroundColor]"
                                       value="<%=_.escape(backgroundColor)%>"
                                       class="js-color-picker"
                                       data-cancel-text="<?php echo t('Cancel'); ?>"
                                       data-choose-text="<?php echo t('Choose'); ?>"
                                       data-toggle-palette-more-text="<?php echo t('more'); ?>"
                                       data-toggle-palette-less-text="<?php echo t('less'); ?>"
                                       data-no-color-selected-text="<?php echo t('No Color Selected'); ?>"
                                       data-clear-text="<?php echo t('Clear Color Selection'); ?>"
                                />
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][isImageRight]" class="form-label"><?php echo t('IsImageRight'); ?></label>
                            <select id="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][isImageRight]" name="<?php echo $view->field('entry'); ?>[<%=_.escape(position)%>][isImageRight]" class="form-select">
                                <?php foreach ($entry_isImageRight_options as $k => $v): ?>
                                    <option value="<?php echo $k; ?>" <% if (isImageRight=='<?php echo $k; ?>') { %>selected="selected"<% } %> ><?php echo h($v); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>

                </div>

            </script>

            <script type="text/template" class="js-template-no-entries">

                <div class="alert alert-info js-alert"><?php echo t('No entries found.'); ?></div>

            </script>

            <script>

                var CCM_EDITOR_SECURITY_TOKEN = '<?php echo $app->make('helper/validation/token')->generate('editor'); ?>';
                var activateEditor = {
                    titleBadge: <?php echo $app->make('editor')->getEditorInitJSFunction(json_decode('{}', true)); ?>,
                    mainText: <?php echo $app->make('editor')->getEditorInitJSFunction(json_decode('{}', true)); ?>,
                    buttonText: <?php echo $app->make('editor')->getEditorInitJSFunction(json_decode('{}', true)); ?>,
                    buttonURL: <?php echo $app->make('editor')->getEditorInitJSFunction(json_decode('{}', true)); ?>,
                    image: <?php echo $app->make('editor')->getEditorInitJSFunction(json_decode('{}', true)); ?>,
                    backgroundColor: <?php echo $app->make('editor')->getEditorInitJSFunction(json_decode('{}', true)); ?>,
                    isImageRight: <?php echo $app->make('editor')->getEditorInitJSFunction(json_decode('{}', true)); ?>,
                }

                $(function () {

                    Concrete.event.publish('open.block.coupon-promo-block', {
                        'uniqueID' : '<?php echo $uniqueID; ?>'
                    });

                });

            </script>

        </div>

        <div class="js-tab-pane tab-pane" id="settings-tab-<?php echo $uniqueID; ?>">

            <div class="mb-4 js-image-settings-wrapper">
                <div class="row margin-bottom">
                    <div class="col-12">
                        <?php echo $form->label('', 'Image' . ' - ' . t('Override Thumbnail dimensions')); ?>
                        <div class="form-check">
                            <?php echo $form->checkbox($view->field('settings[image_override_dimensions]'), '1', $settings['image_override_dimensions'] ?? null, ['class' => 'form-check-input js-toggle-override-all-dimensions']); ?>
                            <label for="<?php echo $view->field('settings[image_override_dimensions]'); ?>" class="form-check-label"><?php echo t('Yes'); ?></label>
                        </div>
                    </div>
                    <div class="row mt-3 js-override-all-dimensions-wrapper" <?php if (empty($settings['image_override_dimensions'])): ?>style="display: none;"<?php endif; ?>>
                        <div class="col-12 col-lg-4 margin-bottom-on-mobile">
                            <?php echo $form->label($view->field('settings[image_custom_width]'), t('Width')); ?>
                            <div class="input-group">
                                <?php echo $form->number($view->field('settings[image_custom_width]'), !empty($settings['image_custom_width']) ? $settings['image_custom_width'] : ''); ?>
                                <span class="input-group-text"><?php echo t('px'); ?></span>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 margin-bottom-on-mobile">
                            <?php echo $form->label($view->field('settings[image_custom_height]'), t('Height')); ?>
                            <div class="input-group">
                                <?php echo $form->number($view->field('settings[image_custom_height]'), !empty($settings['image_custom_height']) ? $settings['image_custom_height'] : ''); ?>
                                <span class="input-group-text"><?php echo t('px'); ?></span>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <?php echo $form->label($view->field('settings[image_custom_crop]'), t('Crop')); ?>
                            <?php echo $form->select($view->field('settings[image_custom_crop]'), ['0'=>t('No'), '1'=>t('Yes')], $settings['image_custom_crop'] ?? null); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4 js-fullscreen-image-settings-wrapper">
                <div class="row margin-bottom">
                    <div class="col-12">
                        <?php echo $form->label('', 'Image' . ' - ' . t('Override Fullscreen Image dimensions')); ?>
                        <div class="form-check">
                            <?php echo $form->checkbox($view->field('settings[image_override_fullscreen_dimensions]'), '1', $settings['image_override_fullscreen_dimensions'] ?? null, ['class' => 'form-check-input js-toggle-override-all-fullscreen-dimensions']); ?>
                            <label for="<?php echo $view->field('settings[image_override_fullscreen_dimensions]'); ?>" class="form-check-label"><?php echo t('Yes'); ?></label>
                        </div>
                    </div>
                    <div class="row mt-3 js-override-all-fullscreen-dimensions-wrapper" <?php if (empty($settings['image_override_fullscreen_dimensions'])): ?>style="display: none;"<?php endif; ?>>
                        <div class="col-12 col-lg-4 margin-bottom-on-mobile">
                            <?php echo $form->label($view->field('settings[image_custom_fullscreen_width]'), t('Width')); ?>
                            <div class="input-group">
                                <?php echo $form->number($view->field('settings[image_custom_fullscreen_width]'), !empty($settings['image_custom_fullscreen_width']) ? $settings['image_custom_fullscreen_width'] : ''); ?>
                                <span class="input-group-text"><?php echo t('px'); ?></span>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 margin-bottom-on-mobile">
                            <?php echo $form->label($view->field('settings[image_custom_fullscreen_height]'), t('Height')); ?>
                            <div class="input-group">
                                <?php echo $form->number($view->field('settings[image_custom_fullscreen_height]'), !empty($settings['image_custom_fullscreen_height']) ? $settings['image_custom_fullscreen_height'] : ''); ?>
                                <span class="input-group-text"><?php echo t('px'); ?></span>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <?php echo $form->label($view->field('settings[image_custom_fullscreen_crop]'), t('Crop')); ?>
                            <?php echo $form->select($view->field('settings[image_custom_fullscreen_crop]'), ['0'=>t('No'), '1'=>t('Yes')], $settings['image_custom_fullscreen_crop'] ?? null); ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>