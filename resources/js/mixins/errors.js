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
        handleError(error) {
            this.saving = false
            if (error.response.status == 422) {
                this.validationErrors = error.response.data.errors;
            } else {
                alert(error.response.data.message)
            }
        },

    },
    computed: {
        hasErrors() {
            return _.isEmpty(this.validationErrors)
        }
    },
}
