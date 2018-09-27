/* eslint-disable no-undef */
// jscs:disable jsDoc

define([
    'jquery',
    'mage/template',
    'uiRegistry',
    'validation',
    'jquery/ui',
    'prototype',
    'form'
], function (jQuery, mageTemplate, rg, Validation) {
    'use strict';

    return function (config) {
        var attributeOption = {
            table: $(config.prefix + '-conditions-table'),
            itemCount: 0,
            totalItems: 0,
            rendered: 0,
            prefix: config.prefix,
            template: mageTemplate('#row-template-' + config.prefix + '-conditions'),
            isReadOnly: config.isReadOnly,
            add: function (data, render) {
                var isNewOption = false,
                    element;

                if (typeof data.id == 'undefined') {
                    data = {
                        'id': 'option_' + this.itemCount,
                        'sort_order': this.itemCount + 1,
                        'action_params': {}
                    };
                    isNewOption = true;
                } else {
                    data.sort_order = data.id;

                    if(data.action_params == null)
                    {
                        data.action_params = {};
                    }

                    if(data.condition_action_params == null)
                    {
                        data.condition_action_params = {};
                    }
                }

                if (!data.intype) {
                    data.intype = this.getOptionInputType();
                }

                element = this.template({
                    data: data
                });

                if (isNewOption && !this.isReadOnly) {
                    this.enableNewOptionDeleteButton(data.id);
                }

                this.itemCount++;
                this.totalItems++;
                this.elements += element;

                if (render) {
                    this.render();
                    this.updateItemsCountField();
                }

                this.addSelectChange(this.prefix + '_condition_type_' + data.id);

                if (isNewOption == false) {
                    jQuery('#' + config.prefix + '_condition_type_' + data.id + ' option').each(function () {
                        if (jQuery(this).val() == data.condition) {
                            jQuery(this).attr('selected', 'selected');
                        }
                    });

                    this.addSelectChange(this.prefix + '_condition_type_' + data.id);

                    jQuery('#' + config.prefix + '_condition_comparison_' + data.id + ' option').each(function () {
                        if (jQuery(this).val() == data.condition_comparison) {
                            jQuery(this).attr('selected', 'selected');
                        }
                    });

                    jQuery('#' + config.prefix + '_condition_value_' + data.id + '_select option').each(function () {
                        if (jQuery(this).val() == data.condition_value) {
                            jQuery(this).attr('selected', 'selected');
                        }
                    });

                    jQuery('#' + config.prefix + '_condition_action_' + data.id + ' option').each(function () {
                        if (jQuery(this).val() == data.condition_action) {
                            jQuery(this).attr('selected', 'selected');
                        }
                    });

                    if(typeof data.condition_action_params == "string")
                    {
                        data.condition_action_params = JSON.parse(data.condition_action_params);

                        jQuery('#action-' + config.prefix + '-' + data.id + ' option').each(function () {
                            if (jQuery(this).val() == data.condition_action_params.protocol) {
                                jQuery(this).attr('selected', 'selected');
                            }
                        });

                        jQuery('#action-' + config.prefix + '-method-' + data.id + ' option').each(function () {
                            if (jQuery(this).val() == data.condition_action_params.method) {
                                jQuery(this).attr('selected', 'selected');
                            }
                        });

                        jQuery('#action-' + config.prefix + '-webhook-' + data.id + ' option').each(function () {
                            if (jQuery(this).val() == data.condition_action_params.webhook) {
                                jQuery(this).attr('selected', 'selected');
                            }
                        });
                    }
              }

                this.addActionChange(this.prefix + '_condition_action_' + data.id);
                this.addRequestChange('action-request-' + data.id);

                Event.observe(this.prefix + '_condition_type_' + data.id, 'change', this.addSelectChange.bind(this, this.prefix + '_condition_type_' + data.id));
                Event.observe(this.prefix + '_condition_action_' + data.id, 'change', this.addActionChange.bind(this, this.prefix + '_condition_action_' + data.id));
                Event.observe('action-' +  this.prefix + '-' + data.id, 'change', this.addRequestChange.bind(this, 'action-' +  this.prefix + '-' + data.id));

                jQuery('#' + this.prefix + '_condition_type_' + data.id).trigger('change');

            },
            addActionChange: function (elementId) {
                jQuery('.' + elementId + '-table').hide();

                var selectedOption = jQuery('#' + elementId).find('option:selected');
                jQuery('#' + elementId + '-table-' + jQuery(selectedOption).val()).show();
            },
            addRequestChange: function (elementId) {
                jQuery('.' + elementId).hide();

                var selectedOption = jQuery('#' + elementId).find('option:selected');
                jQuery('.' + elementId + '-' + jQuery(selectedOption).val()).show();
            },
            addSelectChange: function (elementId) {
                var selectedOption = jQuery('#' + elementId).find('option:selected');
                var dataType = jQuery(selectedOption).attr('data-type');
                var dataSource = jQuery(selectedOption).attr('data-source');
                var dataTarget = jQuery(selectedOption).attr('data-target');
                var dataHasValues = jQuery(selectedOption).attr('data-has-values');
                var dataInputs = jQuery(selectedOption).parent().attr('data-inputs');

                jQuery(dataTarget).empty();

                if (dataType == 'json') {
                    var options = eval(dataSource);

                    jQuery(options).each(function () {
                        jQuery(dataTarget).append(jQuery("<option></option>").attr('value', this.id).text(this.name));
                    });
                }

                if (dataType == 'input') {
                    jQuery(dataSource).each(function () {
                        jQuery(dataTarget).append(jQuery("<option></option>").attr('value', this.value).text(this.value));
                    });
                }


                if (dataHasValues == 'true') {
                    jQuery('#' + elementId + '_value').show();
                } else {
                    jQuery('#' + elementId + '_value').hide();
                }

                jQuery('#' + dataInputs).children().hide();
                jQuery('#' + dataInputs + ' ' + jQuery(selectedOption).attr('data-input-type')).show();
            },
            remove: function (event) {
                var element = $(Event.findElement(event, 'tr')),
                    elementFlags; // !!! Button already have table parent in safari

                // Safari workaround
                element.ancestors().each(function (parentItem) {
                    if (parentItem.hasClassName('option-row')) {
                        element = parentItem;
                        throw $break;
                    } else if (parentItem.hasClassName('box')) {
                        throw $break;
                    }
                });

                if (element) {
                    elementFlags = element.getElementsByClassName('delete-flag');

                    if (elementFlags[0]) {
                        elementFlags[0].value = 1;
                    }

                    element.addClassName('no-display');
                    element.addClassName('template');
                    element.hide();
                    this.totalItems--;
                    this.updateItemsCountField();
                }
            },
            updateItemsCountField: function () {
                $('option-count-check-' + this.prefix + '-conditions').value = this.totalItems > 0 ? '1' : '';
            },
            enableNewOptionDeleteButton: function (id) {
                $$('#delete_button_container_' + this.prefix + '_condition_' + id + ' button').each(function (button) {
                    button.enable();
                    button.removeClassName('disabled');
                });
            },

            bindRemoveButtons: function () {
                jQuery('#swatch-visual-options-panel').on('click', '.delete-option', this.remove.bind(this));
            },
            render: function () {
                Element.insert($$('[data-role=' + this.prefix + '-conditions-options-container]')[0], this.elements);
                this.elements = '';
            },
            renderWithDelay: function (data, from, step, delay) {
                var arrayLength = data.length,
                    len;

                for (len = from + step; from < len && from < arrayLength; from++) {
                    this.add(data[from], true);
                }

                if (from === arrayLength) {
                    this.updateItemsCountField();
                    this.rendered = 1;
                    jQuery('body').trigger('processStop');

                    return true;
                }
                setTimeout(this.renderWithDelay.bind(this, data, from, step, delay), delay);
            },
            ignoreValidate: function () {
                var ignore = '.ignore-validate input, ' +
                    '.ignore-validate select, ' +
                    '.ignore-validate textarea';

                jQuery('#edit_form').data('validator').settings.forceIgnore = ignore;
            },
            getOptionInputType: function () {
                var optionDefaultInputType = 'radio';

                if ($('frontend_input') && $('frontend_input').value === 'multiselect') {
                    optionDefaultInputType = 'checkbox';
                }

                return optionDefaultInputType;
            }
        };

        if ($('add_new_' + config.prefix + '_condition_button')) {
            Event.observe('add_new_' + config.prefix + '_condition_button', 'click', attributeOption.add.bind(attributeOption, {}, true));
        }
        $('manage-' + config.prefix + '-conditions-panel').on('click', '.delete-option', function (event) {
            attributeOption.remove(event);
        });

        if (config.attributesData != null) {
            jQuery('body').trigger('processStart');
            attributeOption.renderWithDelay(config.attributesData, 0, 100, 300);
            attributeOption.bindRemoveButtons();
        }

        if (config.isSortable) {
            jQuery(function ($) {
                $('[data-role=' + config.prefix + '-conditions-options-container]').sortable({
                    distance: 8,
                    tolerance: 'pointer',
                    cancel: 'input, button, select',
                    axis: 'y',
                    update: function () {
                        $('[data-role=' + config.prefix + '-conditions-options-container] [data-role=' + config.prefix + '-conditions-order]').each(function (index, element) {
                            $(element).val(index + 1);
                        });
                    }
                });
            });
        }

        window.attributeOption = attributeOption;
        window.optionDefaultInputType = attributeOption.getOptionInputType();

        rg.set('manage-' + config.prefix + '-conditions-panel', attributeOption);
    };
});
