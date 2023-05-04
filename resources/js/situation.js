$(window).on('load', function () {
    const DEFMESSAGE = $('.accordion .accordion-item').first().clone();
    const DEFNAME = 'situation[messages]';

    $('.type-text.d-none, .type-template.d-none').find('input, textarea, select').prop('disabled', true);

    $(document).on('focus', '.type-template textarea', function () {
        if ($('.type-template [type=file]').val().length != 0 || $('.type-template [data-name=title]').val().length) {
            $(this).attr('maxlength', 60);
        } else {
            $(this).attr('maxlength', 120);
        }
    });

    // メッセージ追加
    $(document).on('click', '#addMessage', function (event) {
        let clonedMessage = DEFMESSAGE.clone();
        let messageIndex = $('.accordion .accordion-item').length;
        let messageCount = messageIndex + 1;

        renameMessage(clonedMessage, messageIndex);

        clonedMessage.find('.turn').val(messageCount);

        renameAccordion(clonedMessage, messageIndex, messageCount);

        if (clonedMessage.find('.btn-remove').length == 0) {
            let removeButton = $('<button>', {
                type: 'button',
                class: 'col-4 btn btn-danger btn-remove'
            }).text('このメッセージを削除する');
            clonedMessage.find('.type-wrapper').after(removeButton);
        }

        clonedMessage.find('.btn-remove').prop('disabled', false);
        $('.btn-remove').prop('disabled', false);

        renameImage(clonedMessage);

        clonedMessage.find('.type-template.d-none').find('input, textarea, select').prop('disabled', true);
        if ($('#text').prop('checked')) {
            clonedMessage.find('[data-name=send_type]').eq(1).prop('disabled', false);
            clonedMessage.find('[data-name=keyword]').prop('disabled', false);
        } else {
            clonedMessage.find('[data-name=send_type]').eq(1).prop('disabled', true);
            clonedMessage.find('[data-name=keyword]').prop('disabled', true);
        }

        $('#accordionMessages').append(clonedMessage);
    });

    // メッセージタイプ切り替え
    $(document).on('click', '.message-type-switch [type=radio]', function () {
        let type = $(this).val();

        switch (type) {
            case 'buttons':
                $(this).parents('.accordion-collapse').find('.type-text').addClass('d-none');
                $(this).parents('.accordion-collapse').find('.type-template').removeClass('d-none');

                $(this).parents('.accordion-collapse').find('.type-text').find('input, textarea, select').prop('disabled', true);
                $(this).parents('.accordion-collapse').find('.type-template').find('input, textarea, select').prop('disabled', false);
                break;
            case 'text':
                $(this).parents('.accordion-collapse').find('.type-template').addClass('d-none');
                $(this).parents('.accordion-collapse').find('.type-text').removeClass('d-none');

                $(this).parents('.accordion-collapse').find('.type-text').find('input, textarea, select').prop('disabled', false);
                $(this).parents('.accordion-collapse').find('.type-template').find('input, textarea, select').prop('disabled', true);
                break;
        }
    });

    $(document).on('click', '.change-img', function () {
        if ($(this).prop('checked')) {
            $(this).parent().find('[type=file]').prop('disabled', false);
            $(this).prev().prop('disabled', false);

            $(this).parents('.accordion-collapse').find('.img-thumbnail').addClass('d-none');
        } else {
            $(this).parent().find('[type=file]').prop('disabled', true);
            $(this).prev().prop('disabled', true);

            $(this).parents('.accordion-collapse').find('.img-thumbnail').removeClass('d-none');
        }
    });

    // 削除ボタン
    $(document).on('click', '.btn-remove', function () {
        $(this).parents('.accordion-item').remove();

        $('.accordion .accordion-item').each(function (index, element) {
            let messageCount = index + 1;

            renameMessage($(element), index);

            $(element).find('.turn').val(messageCount);

            renameAccordion($(element), index, messageCount);
        });

        if ($('.accordion-item').length == 1) {
            $('.btn-remove').prop('disabled', true);
        }
    });

    $(document).on('click', '[type=submit]', function (event) {
        $('.ol-labels [data-name=actions-label], .ol-labels [data-name=actions-trigger]').each(function (index, element) {
            if ($(element).val().length == 0) {
                $(element).prop('disabled', true);
            }
        })
    });

    $(document).on('click', '#follow, #text, #unfollow', function (clickedEvent) {
        $('.accordion-item').each(function (index, accordion) {
            if ($(clickedEvent.target).val() == 2) {
                $(accordion).find(`#send_type_reply_${index}, [data-name=keyword]`).prop('disabled', false);
                $(accordion).find(`#send_type_push_${index}`).prop('disabled', true);
                $(accordion).find(`#send_type_reply_${index}`).prop('checked', true);
            } else {
                $(accordion).find(`#send_type_reply_${index}, [data-name=keyword]`).prop('disabled', true);
                $(accordion).find(`#send_type_push_${index}`).prop('checked', true).prop('disabled', false);
            }
        });
    });

    if ($('#text').prop('checked')) {
        $('[data-name=send_type]:even').prop('disabled', true);
    }

    if ($('#follow').prop('checked') || $('#unfollow').prop('checked')) {
        $('[data-name=send_type]:odd').prop('disabled', true);
    }

    let situation = window.situationOld;
    if (situation != null) {
        $.each(situation.messages, function (index, message) {
            if (index == 0) return;
            $('#addMessage').trigger('click');

            let target = $('.accordion-item').eq(index);
            switch (message.message_type) {
                case 'text':
                    target.find('[data-name=message_type][value=text]').trigger('click');

                    $.each(message, function (name, value) {
                        if (value == null) return;

                        if (name == 'keyword') {
                            target.find(`[data-name=${name}]:not(:disabled)`).val(value);
                        }
                        if (name == 'text') {
                            target.find(`[data-name=${name}]:not(:disabled)`).text(value);
                        }
                        if (name == 'send_type') {
                            target.find(`[data-name=${name}]:not(:disabled)[value=${value}]`).prop('checked', true);
                        }
                    });
                    break;
                case 'buttons':
                    target.find('[data-name=message_type][value=buttons]').trigger('click');

                    $.each(message, function (name, value) {
                        if (value == null) return;
                        switch (name) {
                            case 'text':
                                target.find(`[data-name=${name}]:not(:disabled)`).text(value);
                                break;
                            case 'labels':
                                let labelCount = value.length;
                                target.find('.buttons-select').val(labelCount).trigger('change');

                                $.each(value, function (j, label) {
                                    target.find('[data-name=labels]').eq(j).val(label);
                                });
                                break;
                            case 'send_type':
                                target.find(`[data-name=${name}]:not(:disabled)[value=${value}]`).prop('checked', true);
                                break;
                            default:
                                target.find(`[data-name=${name}]:not(:disabled)`).val(value);
                                break;
                        }
                    });
                    break;
            }
        });
    }

    function renameMessage(messageElement, messageIndex)
    {
        messageElement.find('input, textarea').not('[data-name=actions]').each(function (index, element) {
            let elementName = $(element).data('name');
            let elementValue = $(element).val();

            if ($(element).attr('type') === 'radio') {
                $(element).attr('id', `${elementName}_${elementValue}_${messageIndex}`);
                $(element).next().attr('for', `${elementName}_${elementValue}_${messageIndex}`);
            }

            $(element).attr('name', `${DEFNAME}[${messageIndex}][${elementName}]`);
        });

        messageElement.find('.ol-labels li').each(function (i, element) {
            $(element).find('[type=radio]').each(function (j, radio) {
                let rand = Math.random().toString(8).substring(2);
                let elementName = $(radio).data('name');
                let elementValue = $(radio).val();
                $(radio).attr('id', `${elementName}_${elementValue}_${rand}`);
                $(radio).next().attr('for', `${elementName}_${elementValue}_${rand}`);
                $(radio).attr('name', `${DEFNAME}[${messageIndex}][${elementName}][${i}][type]`);
            });

            $(element).find('[type=text]').each(function (j, text) {
                let actionName = $(text).data('action');
                let elementName = $(text).data('name');
                $(text).attr('name', `${DEFNAME}[${messageIndex}][${elementName}][${i}][${actionName}]`);
            });
        });
    }

    function renameAccordion(messageElement, messageIndex, messageCount)
    {
        messageElement.find('.accordion-button').attr('data-bs-target', `#message_${messageIndex}`).text(`メッセージ${messageCount}`);
        messageElement.find('.accordion-collapse').attr('id', `message_${messageIndex}`);
    }

    function renameImage(messageElement)
    {
        messageElement.find('.img-thumbnail').attr('src', '').addClass('d-none');
        messageElement.find('[data-name=thumbnail_image_url], [data-name="delete_turns"]').prop('disabled', false);
        messageElement.find('.change-img, .change-img + label').addClass('d-none');
    }
});
