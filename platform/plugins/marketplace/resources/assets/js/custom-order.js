$(() => {
    function submitFormAjax($form, $button) {
        $button.addClass('button-loading')

        $.ajax({
            type: 'POST',
            cache: false,
            url: $form.prop('action'),
            data: $form.serialize(),
            success: (res) => {
                if (!res.error) {
                    Botble.showNotice('success', res.message)
                    $button.closest('.modal').modal('hide')
                    if (window.LaravelDataTables) {
                        Object.keys(window.LaravelDataTables).map((x) => {
                            window.LaravelDataTables[x].draw()
                        })
                    }
                    if (res.data && res.data.balance) {
                        $('.vendor-balance').text(res.data.balance)
                    }
                } else {
                    Botble.showNotice('error', res.message)
                }
            },
            error: (res) => {
                Botble.handleError(res)
            },
            complete: () => {
                $button.removeClass('button-loading')
            },
        })
    }

    $(document).on('click', 'button[data-toggle="modal"][data-target="#create-custom-order-modal"]', (event) => {
        event.preventDefault()
        $('#create-custom-order-modal').modal('toggle')
    })

    $(document).on('click', '#create-custom-order-modal button[data-dismiss="modal"]', (event) => {
        event.preventDefault()
        $('#create-custom-order-modal').modal('hide')
    })

    $(document).on('click', 'button[data-toggle="modal"][data-target="#create-custom-subscription-modal"]', (event) => {
        event.preventDefault()
        $('#create-custom-subscription-modal').modal('toggle')
    })

    $(document).on('click', '#create-custom-subscription-modal button[data-dismiss="modal"]', (event) => {
        event.preventDefault()
        $('#create-custom-subscription-modal').modal('hide')
    })
})
