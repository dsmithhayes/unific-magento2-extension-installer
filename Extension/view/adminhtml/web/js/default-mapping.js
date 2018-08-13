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
        var attributeDefaults = {
            table: $("default-" + config.prefix + '-mappings-table'),
            eavAttributes: config.eavAttributes,
            itemCount: 0,
            totalItems: 0,
            rendered: 0,
            prefix: config.prefix,
            template: mageTemplate('#row-template-default-' + config.prefix + '-mappings'),
            isReadOnly: config.isReadOnly,
            add: function (data, render) {
                console.log(data);

                var element = this.template({
                    data: data
                });

                this.itemCount++;
                this.totalItems++;
                this.elements += element;

                if (render) {
                    this.render();
                    this.updateItemsCountField();
                }

                this.fillTypes('#' + config.prefix + '_mapping_internal_type_' + data.id, data);
            },
            copyInternalValue: function (data) {
                jQuery('#' + config.prefix + '_mapping_internal_' + data.id).val(jQuery('#' + config.prefix + '_mapping_internal_' + data.id + '_select option[selected=selected]').val());
            },
            fillTypes: function (element, data) {
                var selected = false;
                jQuery.each(config.eavAttributes, function (entityType, attributeData) {
                    var newElement = jQuery("<option></option>").attr('value', entityType).text(entityType);

                    if (data['internaltype'] == entityType) {
                        selected = true;
                        newElement.attr('selected', 'selected');
                    }

                    jQuery(element).append(newElement);
                });

                if (selected == true) {
                    this.fillMappings('#' + config.prefix + '_mapping_internal_type_' + data.id, '#' + config.prefix + '_mapping_internal_' + data.id + '_select', data);
                }
            },

            fillMappings: function (element, target, data) {
                if (jQuery(element).val() != '') {
                    jQuery(target).empty();
                    var selected = false;

                    if (jQuery(element).val() == 'other') {
                        jQuery('#' + config.prefix + '_mapping_internal_' + data.id).val()
                        jQuery('#' + config.prefix + '_mapping_internal_' + data.id + '_select').hide();
                        jQuery('#' + config.prefix + '_mapping_internal_' + data.id).show();
                    } else {
                        jQuery('#' + config.prefix + '_mapping_internal_' + data.id + '_select').show();
                        jQuery('#' + config.prefix + '_mapping_internal_' + data.id).hide();
                    }

                    jQuery.each(config.eavAttributes[jQuery(element).val()], function (elementValue, elementText) {
                        var newElement = jQuery("<option></option>").attr('value', jQuery(element).val() + '.' + elementText).text(elementText);

                        if (data['internal'] == elementText) {
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
            },
            bindRemoveButtons: function () {
                jQuery('#swatch-visual-options-panel').on('click', '.delete-option', this.remove.bind(this));
            },
            render: function () {
                Element.insert($$('[data-role=default-' + this.prefix + '-mappings-options-container]')[0], this.elements);
                this.elements = '';
            },

            renderDefaults: function () {
                jQuery('#default-' + config.prefix + '-mappings-container').empty();

                var mappingKey = 'order';

                switch(jQuery('#' + config.prefix + '_event').val())
                {
                    case 'Magento\\Customer\\Model\\Session::setCustomerAsLoggedIn':
                    case 'Magento\\Customer\\Api\\CustomerManagementInterface::save':
                    case 'Magento\\Customer\\Model\\Session::logout':
                        mappingKey = 'customer';
                        break;
                    case 'Magento\\Order\\Model\\Order\\Invoice::capture':
                        mappingKey = 'invoice';
                        break;
                    case 'Magento\\Order\\Model\\Order\\Creditmemo::save':
                        mappingKey = 'creditmemo';
                        break;
                    case 'Magento\\Quote\\Api\\CartManagementInterface::save':
                        mappingKey = 'cart';
                        break;
                    case 'Magento\\Shipment\\Model\\Shipment::save':
                        mappingKey = 'shipment';
                        break;
                    case 'Magento\\Backend\\Model\\Auth\\Session::processLogin':
                    case 'Magento\\Backend\\Model\\Auth\\Session::processLogout':
                    case 'Magento\\User\\Model\\User::save':
                        mappingKey = 'admin_user';
                        break;
                    default:
                        mappingKey = 'order';
                }

                jQuery.each(config.attributesData.mappings['header'], function(key, values)
                {
                    values = (values == null) ? {} : values;
                    values.internal = (values.internal == null) ? key : values.internal;
                    values.external = (values.external == null) ? values.internal : values.external;
                    values.location = (values.location == null) ? 'header' : values.location;
                    attributeDefaults.add(values, true);
                });

                if(typeof(config.attributesData.mappings[mappingKey]) != 'undefined')
                {
                    jQuery.each(config.attributesData.mappings[mappingKey], function(key, values)
                    {
                        values = (values == null) ? {} : values;
                        values.internal = (values.internal == null) ? key : values.internal;
                        values.external = (values.external == null) ? values.internal : values.external;
                        values.location = (values.location == null) ? 'body' : values.location;
                        attributeDefaults.add(values, true);
                    });
                }

                return true;
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

        window.attributeDefaults = attributeDefaults;

        rg.set('default-' + config.prefix + '-mappings-panel', attributeDefaults);

        jQuery('#' + config.prefix + '_event').change(attributeDefaults.renderDefaults);
        jQuery('#' + config.prefix + '_event').trigger('change');
    };
});
