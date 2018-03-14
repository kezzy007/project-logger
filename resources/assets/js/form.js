/**
 * Created by Ed on 02/01/2017.
 */

var Errors  = require('./errors');
var axios   = require('axios');
var _       = require('lodash');
var $       = require('jquery');

module.exports = class {

    constructor(data) {

        this.$ready         = false;
        this.clearOnSubmit  = false;

        this.resetStages();

        this.originalData   = data;

        for (let field in this.originalData) {

            this[field] = data[field];
        }

        this.errors     = new Errors();

        this.http       = axios.create({
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content"),
            }
        });
    }

    data() {

        let data   = {};

        for(let property in this.originalData) {

            data[property]  = this[property];

        }

        return data;
    }

    ready() {
        this.$ready = true;
    }

    reset() {

        for (let field in this.originalData) {

            this[field] = '';
        }

        this.errors.clear();
    }

    clearOnSubmit()
    {
        this.clearOnSubmit  = true;
    }

    resetStages() {
        this.submitting     = false;
        this.submitted      = false;
        this.succeeded      = false;
        this.failed         = false;
    }

    post(url, permanentData) {
        return this.submit('post', url, permanentData);
    }

    put(url, permanentData) {
        return this.submit('put', url, permanentData);
    }

    delete(url, permanentData) {
        return this.submit('delete', url, permanentData);
    }

    submit(method, url, permanentData) {
        this.resetStages();

        this.submitting = true;

        return new Promise((resolve, reject) => {

            var data    = _.extend(this.data(), permanentData);

            this.http[method](url, data)
                .then(response => {
                    this.onSuccess(response.data);
                    resolve(response.data);

                    this.succeeded      = true;
                    this.submitted      = true;
                    this.submitting     = false;
                })
                .catch(error => {
                    this.onError(error.response.data);
                    reject(error.response.data);

                    if (error.response.status != 422) {
                        this.failed     = true;
                    }

                    this.submitted      = true;
                    this.submitting     = false;
                });
        });
    }

    onSuccess(data) {

        if (this.clearOnSubmit)
        {
            this.reset();
        }
    }

    onError(errors) {
        this.errors.record(errors);
    }
}