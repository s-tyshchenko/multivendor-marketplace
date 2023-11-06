class StripeConnectVendor {
    init() {
        $(document).on('click', '#vendor-retrieve-stripe-connect-link', (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)

            _self.addClass('button-loading')

            $.ajax({
                type: 'GET',
                cache: false,
                url: _self.data('action'),
                success: (res) => {
                    if (!res.error) {
                        // Botble.showSuccess(res.message)
                        window.location.href = res.link
                    } else {
                        Botble.showError(res.message)
                    }
                    _self.removeClass('button-loading')
                },
                error: (res) => {
                    Botble.handleError(res)
                    _self.removeClass('button-loading')
                },
            })
        })
    }
}

$(document).ready(() => {
    new StripeConnectVendor().init()
})
