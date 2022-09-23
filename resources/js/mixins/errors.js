import Swal from 'sweetalert2'
export const errorsMixin = {
    methods: {
        // errorClass(item, sm = false) {
        //     return { 'form-group': true, 'form-group-sm': sm, 'is-invalid': this.validationErrors.hasOwnProperty(item) }
        // },
        errorClass(item) {
            return { 'form-control': true, 'is-invalid': this.validationErrors.hasOwnProperty(item) }
        },
        buttonClass(def = 'btn-primary') {
            return ['btn', def, this.saving ? 'disabled' : '']
        },
        handleError(error, title = 'Oops...') {
            this.saving = false

            if (!error.hasOwnProperty('response')) {
                this.showMessage('error', 'Network Error', error)
                return
            }

            if (error.response.status == 422) {
                this.validationErrors = error.response.data.errors;
            } else {
                Swal.fire({
                    icon: 'error',
                    title,
                    text: error.response.data.message,
                })
            }
        },
        showMessage(icon, title, text) {
            Swal.fire({
                icon,
                title,
                text,
            });
        },
        showToast(title = 'Correcto', icon = 'success', timer = 3000) {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer,
                timerProgressBar: true,
            });

            Toast.fire({
                icon,
                title,
            });
        },
        startTransaction() {
            if (this.saving) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Transaccion en curso',
                    text: 'Por favor espere a que termine la transacion.',
                });
                throw new Error('Por favor espere a que termine la transacion.');
            }
            this.saving = true;
            this.validationErrors = [];
        },
        getError(item) {
            return (this.validationErrors && this.validationErrors.hasOwnProperty(item)) ? this.validationErrors[item][0] : null
        }

    },
    computed: {
        hasErrors() {
            return _.isEmpty(this.validationErrors)
        }
    },
}
