$(window).on('load', function () {
    const DEFMESSAGE = $('.accordion .accordion-item').first().clone();
    const DEFNAME = 'situation[messages]';

    $('.type-text.d-none, .type-template.d-none').find('input, textarea, select').prop('disabled', true);

    $(document).on('focus', '.type-template textarea', function () {
        if ($(this).parents('.card').find('[type=file]').val().length != 0 || $(this).parents('.card').find('[data-name=title]').val().length) {
            $(this).attr('maxlength', 60);
        } else {
            $(this).attr('maxlength', 120);
        }
    });

    // メッセージタイプ切り替え
    $(document).on('click', '.message-type-switch [type=radio]', function () {
        let type = $(this).val();

        switch (type) {
            case 'carousel':
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

    // メッセージ追加
    $(document).on('click', '#addMessage', function (event) {
        let clonedMessage = DEFMESSAGE.clone();
        let messageIndex = $('.accordion .accordion-item').length;
        let messageCount = messageIndex + 1;

        renameMessage(clonedMessage, messageIndex);

        clonedMessage.find('.turn').val(messageCount);

        renameAccordion(clonedMessage, messageIndex, messageCount);

        renameCarousels(clonedMessage, messageIndex, 1);

        if (clonedMessage.find('.btn-remove').length == 0) {
            let removeButton = $('<button>', {
                type: 'button',
                class: 'col-4 btn btn-danger btn-remove'
            }).text('このメッセージを削除する');
            clonedMessage.find('.type-wrapper').after(removeButton);
        }

        clonedMessage.find('.btn-remove').prop('disabled', false);
        $('.btn-remove').prop('disabled', false);

        clonedMessage.find('.type-template.d-none').find('input, textarea, select').prop('disabled', true);
        if ($('#text').prop('checked')) {
            clonedMessage.find('[data-name=send_type]').eq(0).prop('disabled', true);
            clonedMessage.find('[data-name=send_type]').eq(1).prop('checked', true).prop('disabled', false);
            clonedMessage.find('[data-name=keyword]').prop('disabled', false);
        } else {
            clonedMessage.find('[data-name=send_type]').eq(0).prop('checked', true);
            clonedMessage.find('[data-name=send_type]').eq(1).prop('disabled', true);
            clonedMessage.find('[data-name=keyword]').prop('disabled', true);
        }

        $('#accordionMessages').append(clonedMessage);
    });

    // 削除ボタン
    $(document).on('click', '.btn-remove', function () {
        $(this).parents('.accordion-item').remove();

        $('.accordion .accordion-item').each(function (index, element) {
            let messageCount = index + 1;
            let messageElement = $(element);

            renameMessage(messageElement, index);

            messageElement.find('.turn').val(messageCount);

            renameAccordion(messageElement, index, messageCount);

            renameCarousels(messageElement, index);
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

    $(document).on('click', '#follow, #text, #question', function (clickedEvent) {
        $('.accordion-item').each(function (index, accordion) {
            if ($(clickedEvent.target).val() == 2) {
                $(accordion).find(`#send_type_reply_${index}, [data-name=keyword]`).prop('disabled', false);
                $(accordion).find(`#send_type_push_${index}`).prop('disabled', true);
                $(accordion).find(`#send_type_reply_${index}`).prop('checked', true);
                $('#addMessage').prop('disabled', true);
                $('.accordion-item:not(:first-of-type)').addClass('d-none').find('[data-name=disabled]').val(1);
            } else {
                $(accordion).find(`#send_type_reply_${index}, [data-name=keyword]`).prop('disabled', true);
                $(accordion).find(`#send_type_push_${index}`).prop('checked', true).prop('disabled', false);
                $('#addMessage').prop('disabled', false);
                $('.accordion-item:not(:first-of-type)').removeClass('d-none').find('[data-name=disabled]').val(0);
            }
        });
    });

    $(document).on('click', '.img-remove', function (clickedEvent) {
        $(clickedEvent.target).siblings('[type=file]').val('');
        $(clickedEvent.target).siblings('.preview-img').addClass('d-none').children('img').attr('src', '');
        $(clickedEvent.target).siblings('.sample-img').removeClass('d-none');
        $(clickedEvent.target).siblings('.file-path').prop('disabled', true);
        $(clickedEvent.target).addClass('d-none');
    });

    $(document).on('change', '.card-img-top[type=file]', function (clickedEvent) {
        $(clickedEvent.target).siblings('.img-remove').removeClass('d-none');
        $(clickedEvent.target).siblings('.file-path').prop('disabled', true);
        let preview = $(clickedEvent.target).siblings('.preview-img');

        let reader = new FileReader();
        reader.onload = function (clickedEvent) {
            preview.children('img').attr('src', clickedEvent.target.result);
        }
        reader.readAsDataURL(clickedEvent.target.files[0]);

        preview.removeClass('d-none');
        preview.siblings('.sample-img').addClass('d-none');
    });

    if ($('#text').prop('checked')) {
        $('[data-name=send_type]:even').prop('disabled', true);
    }

    if ($('#follow').prop('checked') || $('#unfollow').prop('checked')) {
        $('[data-name=send_type]:odd').prop('disabled', true);
    }

    let situation = window.situationOld;
    if (situation != null) {
        console.log(situation.messages);
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
                case 'carousel':
                    target.find('[data-name=message_type][value=carousel]').trigger('click');

                    $.each(message, function (name, value) {
                        if (value == null) return;
                        switch (name) {
                            case 'text':
                                target.find(`[data-name=${name}]:not(:disabled)`).text(value);
                                break;
                            case 'carousels':
                                $.each(value, function (carouselIndex, carousel) {
                                    let carouselElement = target.find('.card').eq(carouselIndex);

                                    $.each(carousel, function (key, item) {
                                        switch (key) {
                                            case 'actions':
                                                $.each(item, function (j, action) {
                                                    carouselElement.find('[data-name=actions]').eq(j).val(action.action);
                                                });
                                                break;
                                            default:
                                                carouselElement.find(`[data-name=${key}]`).val(item);
                                                break;
                                        }
                                    });
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
        messageElement.find('input, textarea').not('.carousel-group input, .carousel-group textarea').each(function (index, element) {
            let elementName = $(element).data('name');
            let elementValue = $(element).val();

            if ($(element).attr('type') === 'radio') {
                $(element).attr('id', `${elementName}_${elementValue}_${messageIndex}`);
                $(element).next().attr('for', `${elementName}_${elementValue}_${messageIndex}`);
            }

            $(element).attr('name', `${DEFNAME}[${messageIndex}][${elementName}]`);
        });
    }

    function renameCarousels(clonedMessage, messageIndex, isCreate = 0)
    {
        clonedMessage.find('.carousel-group .card').each(function (cardIndex, element) {
            let card = $(element);

            renameImage(card, cardIndex, messageIndex, isCreate);
            renameTitle(card, cardIndex, messageIndex, isCreate);
            renameText(card, cardIndex, messageIndex, isCreate);
            renameButtons(card, cardIndex, messageIndex, isCreate);
        });
    }

    function renameAccordion(messageElement, messageIndex, messageCount)
    {
        messageElement.find('.accordion-button').attr('data-bs-target', `#message_${messageIndex}`).text(`メッセージ${messageCount}`);
        messageElement.find('.accordion-collapse').attr('id', `message_${messageIndex}`);
    }

    function renameImage(card, cardIndex, messageIndex, isCreate)
    {
        const CARD_ID = `thumbnail-image-${messageIndex}-${cardIndex}`;
        card.find('[type=file]')
            .attr('id', CARD_ID)
            .attr('name', `${DEFNAME}[${messageIndex}][carousels][${cardIndex}][thumbnail_image_url]`);

        card.find('.preview-img').attr('for', CARD_ID);
        card.find('.sample-img').attr('for', CARD_ID);

        if (isCreate == 1) {
            card.find('[type=file]').val('').addClass('d-none');
            card.find('.file-path').val('');
            card.find('.preview-img').addClass('d-none').children('img').attr('src', '');
            card.find('.sample-img').removeClass('d-none');
            card.find('.img-remove').addClass('d-none');
        }
    }

    function renameTitle(card, cardIndex, messageIndex, isCreate)
    {
        card.find('[data-name=title]').attr('name', `${DEFNAME}[${messageIndex}][carousels][${cardIndex}][title]`);

        if (isCreate == 1) {
            card.find('[data-name=title]').val('');
        }
    }

    function renameText(card, cardIndex, messageIndex, isCreate)
    {
        card.find('[data-name=text]').attr('name', `${DEFNAME}[${messageIndex}][carousels][${cardIndex}][text]`);

        if (isCreate == 1) {
            card.find('[data-name=text]').val('');
        }
    }

    function renameButtons(card, cardIndex, messageIndex, isCreate)
    {
        card.find('[data-name=actions]').each(function (buttonIndex, button) {
            $(button).attr('name', `${DEFNAME}[${messageIndex}][carousels][${cardIndex}][actions][${buttonIndex}][action]`);

            if (isCreate == 1) {
                $(button).val('');
            }
        });
    }
});
