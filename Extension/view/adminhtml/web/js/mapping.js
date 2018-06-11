/* eslint-disable no-undef */
// jscs:disable jsDoc

define([
    'jquery',
    'mage/template',
    'uiRegistry',
    'jquery/ui',
    'prototype',
    'form',
    'validation'
], function (jQuery, mageTemplate, rg) {
    'use strict';

    return function (config) {
        var attributeOption = {
            table: $(config.prefix + '-mappings-table'),
            eavAttributes: config.eavAttributes,
            itemCount: 0,
            totalItems: 0,
            rendered: 0,
            prefix: config.prefix,
            template: mageTemplate('#row-template-' + config.prefix + '-mappings'),
            isReadOnly: config.isReadOnly,
            add: function (data, render) {
                var isNewOption = false,
                    element;

                if (typeof data.id == 'undefined') {
                    data = {
                        'id': 'option_' + this.itemCount,
                        'sort_order': this.itemCount + 1
                    };
                    isNewOption = true;
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

                if(isNewOption == false) {
                    jQuery('#' + config.prefix + '_mapping_internal_location_' + data.id + ' option').each(function() {
                       if(jQuery(this).val() == data.location) {
                           jQuery(this).attr('selected', 'selected');
                       }
                    });
                }

                this.fillTypes('#' + config.prefix + '_mapping_internal_type_' + data.id, data);

                Event.observe(config.prefix + '_mapping_internal_type_' + data.id, 'change', this.fillMappings.bind(this, '#' + config.prefix + '_mapping_internal_type_' + data.id, '#' + config.prefix + '_mapping_internal_' + data.id + '_select', data));
                Event.observe(config.prefix + '_mapping_internal_' + data.id + '_select', 'change', this.copyInternalValue.bind(this, data));
            },
            copyInternalValue: function(data) {
                jQuery('#' + config.prefix + '_mapping_internal_' + data.id).val(jQuery('#' + config.prefix + '_mapping_internal_' + data.id + '_select option[selected=selected]').val());
            },
            fillTypes: function(element, data) {
                var selected=false;
                jQuery.each(config.eavAttributes, function(entityType, attributeData) {
                    var newElement = jQuery("<option></option>").attr('value', entityType).text(entityType);

                    if(data['internaltype'] == entityType) {
                        selected = true;
                        newElement.attr('selected', 'selected');
                    }

                    jQuery(element).append(newElement);
                });

                if(selected == true) {
                    this.fillMappings('#' + config.prefix + '_mapping_internal_type_' + data.id, '#' + config.prefix + '_mapping_internal_' + data.id + '_select', data);
                }
            },

            fillMappings: function(element, target, data) {
                if(jQuery(element).val() != '') {
                    jQuery(target).empty();
                    var selected = false;

                    if(jQuery(element).val() == 'other') {
                        jQuery('#' + config.prefix + '_mapping_internal_' + data.id).val()
                        jQuery('#' + config.prefix + '_mapping_internal_' + data.id + '_select').hide();
                        jQuery('#' + config.prefix + '_mapping_internal_' + data.id).show();
                    } else {
                        jQuery('#' + config.prefix + '_mapping_internal_' + data.id + '_select').show();
                        jQuery('#' + config.prefix + '_mapping_internal_' + data.id).hide();
                    }

                    jQuery.each(config.eavAttributes[jQuery(element).val()], function(elementValue, elementText) {
                        var newElement = jQuery("<option></option>").attr('value', jQuery(element).val() + '.' + elementText).text(elementText);

                        if(data['internal'] == elementText) {
                            selected = true;
                            newElement.attr('selected', 'selected');
                        }

                        jQuery(target).append(newElement);
                    });
                }
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
                $('option-count-check-' + this.prefix + '-mappings').value = this.totalItems > 0 ? '1' : '';
            },
            enableNewOptionDeleteButton: function (id) {
                $$('#delete_button_container_' + this.prefix + '_mapping_' + id + ' button').each(function (button) {
                    button.enable();
                    button.removeClassName('disabled');
                });
            },
            bindRemoveButtons: function () {
                jQuery('#swatch-visual-options-panel').on('click', '.delete-option', this.remove.bind(this));
            },
            render: function () {
                Element.insert($$('[data-role=' + this.prefix + '-mappings-options-container]')[0], this.elements);
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

        if ($('add_new_' + config.prefix + '_mapping_button')) {
            Event.observe('add_new_' + config.prefix + '_mapping_button', 'click', attributeOption.add.bind(attributeOption, {}, true));
        }
        $('manage-' + config.prefix + '-mappings-panel').on('click', '.delete-option', function (event) {
            attributeOption.remove(event);
        });

        if (config.attributesData != null) {
            jQuery('body').trigger('processStart');
            attributeOption.renderWithDelay(config.attributesData, 0, 100, 300);
            attributeOption.bindRemoveButtons();
        }

        if (config.isSortable) {
            jQuery(function ($) {
                $('[data-role=' + config.prefix + '-mappings-options-container]').sortable({
                    distance: 8,
                    tolerance: 'pointer',
                    cancel: 'input, button, select',
                    axis: 'y',
                    update: function () {
                        $('[data-role=' + config.prefix + '-mappings-options-container] [data-role=' + config.prefix + '-mappings-order]').each(function (index, element) {
                            $(element).val(index + 1);
                        });
                    }
                });
            });
        }

        window.attributeOption = attributeOption;
        window.optionDefaultInputType = attributeOption.getOptionInputType();

        rg.set('manage-' + config.prefix + '-mappings-panel', attributeOption);
    };
});
