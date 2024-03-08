(function ($) {
    'use strict';
    function initializeForm(dataAssigner, dataPermission) {
        const policyForm = $('#tdmrep-policy-form');

        function addField(addButtonId, fieldId, fieldClass, legendTextKey, fields, divId, data) {
            const addButton = $(`#${addButtonId}`);
        
            addButton.click(function (event) {
                event.preventDefault();
        
                addButton.hide();
        
                const table = $('<table/>', {
                    class: 'form-table'
                });
        
                const tbody = $('<tbody/>');
        
                const title = $('<h3/>', {
                    text: tdmrep_object.i18n[legendTextKey]
                });
        
                const deleteButton = $('<button/>', {
                    html: tdmrep_object.i18n['delete'], // Ajout de l'icône
                    role: 'button',
                    class: 'button button-delete',
                    click: function () {
                        table.remove();
                        title.remove();
                        $(this).remove();
        
                        addButton.prop('disabled', false);
                        addButton.show();
                    }
                });
        
                let currentSection = '';
                fields.forEach(field => {
                    if (field.section !== currentSection) {
                        currentSection = field.section;
                        const sectionTitle = $('<h4/>', {
                            text: tdmrep_object.i18n[currentSection]
                        });
                        tbody.append($('<tr/>').append($('<td/>').attr('colspan', 2).append(sectionTitle)));
                    }
                    let inputField;
                    let tr = $('<tr/>');
                    tr.attr(`data-${fieldId}-field`, field.name);
                    let th = $('<th/>', {
                        scope: 'row'
                    });
                    let td = $('<td/>');
                    let label = $('<label/>', {
                        for: `${fieldId}-${field.name}`,
                        text: tdmrep_object.i18n[field.name],
                        class: field.required ? 'required' : ''
                    });
        
                    if (field.type === 'select') {
                        inputField = $('<select/>', {
                            name: `${fieldId}[${field.name}]`,
                            id: `${fieldId}-${field.name}`,
                        });
                        field.options.forEach(option => {
                            const optionElement = $('<option/>', {
                                value: option.value,
                                text: tdmrep_object.i18n[option.textKey],
                                selected: data && data[field.name] === option.value
                            });
                            inputField.append(optionElement);
                        });
                    } else {
                        inputField = $('<input/>', {
                            type: field.type,
                            name: `${fieldId}[${field.name}]`,
                            id: `${fieldId}-${field.name}`,
                            value: data ? data[field.name] : '',
                        });
                    }
                    
                    if (field.required) {
                        inputField.prop('required', true);
                    }
        
                    th.append(label);
                    td.append(inputField);
                    tr.append(th, td);
        
                    tbody.append(tr);
                });
        
                table.append(tbody);
        
                title.append(deleteButton);
        
                $(`#${divId}`).append(title, table);
        
                addButton.prop('disabled', true);
            });

            if ((typeof data === 'object' && data !== null && Object.keys(data).length > 0)) {
                addButton.trigger('click');
            }
        }

        addField('assigner-add', 'assigner', 'assigner-field', 'addAssigner', [
            { name: 'fn', type: 'text', placeholderKey: 'fullName', section: 'Identity', required: true },
            { name: 'nickname', type: 'text', placeholderKey: 'nickname', section: 'Identity' },
            { name: 'has_email', type: 'email', placeholderKey: 'email', section: 'Contact Info' },
            { name: 'has_telephone', type: 'tel', placeholderKey: 'telephone', section: 'Contact Info' },
            { name: 'has_url', type: 'url', placeholderKey: 'url', section: 'Contact Info' },
            { name: 'has_address_street', type: 'text', placeholderKey: 'streetAddress', section: 'Address' },
            { name: 'has_address_postal-code', type: 'text', placeholderKey: 'postalCode', section: 'Address' },
            { name: 'has_address_locality', type: 'text', placeholderKey: 'locality', section: 'Address' },
            { name: 'has_address_country-name', type: 'text', placeholderKey: 'countryName', section: 'Address' }
        ], 'assigner-fields', dataAssigner || {});

        addField('permission-add', 'permission', 'permission-field', 'addPermission', [
            { name: 'target', type: 'text', placeholderKey: 'target' },
            {
                name: 'action', type: 'select', placeholderKey: 'action', options: [
                    { value: 'tdm:mine', textKey: 'textDataMine' }
                ], required: true
            },
            {
                name: 'duties', type: 'select', placeholderKey: 'duties', options: [
                    { value: '', textKey: 'noDuty' },
                    { value: 'obtainConsent', textKey: 'dutyToObtainConsent' },
                    { value: 'compensate', textKey: 'dutyToCompensate' }
                ]
            },
            {
                name: 'constraints', type: 'select', placeholderKey: 'constraints', options: [
                    { value: '', textKey: 'noConstraint' },
                    { value: 'research', textKey: 'research' },
                    { value: 'non-research', textKey: 'nonResearch' }]
            }
        ], 'permission-fields', dataPermission || {});

        $('#policy-location').change(function () {
            let url = $(this).val();
            if (url.startsWith('http://') || url.startsWith('https://')) {
                let urlObj = new URL(url);
                let wpPath = tdmrep_object.wpPath;
                let newPath = urlObj.pathname.replace(wpPath, '/') + urlObj.search + urlObj.hash;
                $(this).val(newPath);
            }
        });

        $('#submit').click(function (event) {
            $(policyForm).find(':input').removeClass('invalid');

            let isValid = true;
            $(policyForm).find(':input').each(function () {
                if (!this.checkValidity()) {
                    isValid = false;
                    $(this).addClass('invalid');
                }
            });

            if (!isValid) {
                event.preventDefault();
                return false;
            }

            policyForm.submit();
        });

    };

    $(document).ready(function ($) {
        $('.edit-policy-button, .add-policy-button').on('click', handlePolicyButtonClick);
    });

    function handlePolicyButtonClick() {
        let policyUid = $(this).data('policy-uid');

        $.ajax({
            url: tdmrep_object.ajaxUrl,
            type: 'POST',
            data: {
                action: 'get_policy_form',
                policy_uid: policyUid,
                nonce: tdmrep_object.nonce
            },
            success: (response) => handlePolicyFormResponse(response, policyUid)
        });
    }

    function handlePolicyFormResponse(response, policyUid) {
        const data = response.data;
        $('#tdmrep-popup-policy').html(data.html);

        const policyModalTitle = policyUid ? tdmrep_object.i18n.editPolicy : tdmrep_object.i18n.addPolicy;

        tb_show(policyModalTitle, '#TB_inline?width=600&height=550&inlineId=tdmrep-popup-policy');

        initializeForm(data.assigner, data.permission);
    }
})(jQuery);