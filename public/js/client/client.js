"use strict";
var KTCreateAccount = function() {
    var e, t, i, o, s, r, a = [];
    return {
        init: function() {
            (e = document.querySelector("#kt_modal_create_account")) && new bootstrap.Modal(e), t = document.querySelector("#kt_create_account_stepper"), i = t.querySelector("#create_client_form"), o = t.querySelector('[data-kt-stepper-action="submit"]'), s = t.querySelector('[data-kt-stepper-action="next"]'), (r = new KTStepper(t)).on("kt.stepper.changed", (function(e) {
                4 === r.getCurrentStepIndex() ? (o.classList.remove("d-none"), o.classList.add("d-inline-block"), s.classList.add("d-none")) : 5 === r.getCurrentStepIndex() ? (o.classList.add("d-none"), s.classList.add("d-none")) : (o.classList.remove("d-inline-block"), o.classList.remove("d-none"), s.classList.remove("d-none"))
            })), r.on("kt.stepper.next", (function(e) {

                var section = e.getCurrentStepIndex() - 1;
                var t = a[e.getCurrentStepIndex() - 1];

                $('.error').html('');
                $.ajax({
                    type        : 'post',
                    url         : 'check-client-validation/'+section,
                    data        : new FormData($("#create_client_form")[0]),
                    contentType : false,
                    processData : false,
                    cache       : false,
                    success: function(response) {
                        console.log(response);
                        if(response.code === 200) {
                            (e.goNext(), KTUtil.scrollTop())
                        } else if (response.code === 500) {
                            toastr.error(response.message);
                            KTUtil.scrollTop()
                        } else {
                            KTUtil.scrollTop()
                            for(let i = 0; i < Object.keys(response.data).length; i++) {
                                $("#"+Object.keys(response.data)[i]+"_error").empty().append(response.data[Object.keys(response.data)[i]][0]);
                            }
                        }
                    },
                    error: function () {
                        toastr.error('Validation - Technical error, please try again.');
                    }
                })

                /*t ? t.validate().then((function(t) {
                    "Valid" === t ? (e.goNext(), KTUtil.scrollTop()) : Swal.fire({
                        text                : "Sorry, looks like there are some errors detected, please try again.",
                        icon                : "error",
                        buttonsStyling      : !1,
                        confirmButtonText   : "Ok, got it!",
                        customClass         : {
                            confirmButton   : "btn btn-light"
                        }
                    }).then((function() {
                        KTUtil.scrollTop()
                    }))
                })) : (e.goNext(), KTUtil.scrollTop())*/

            })), r.on("kt.stepper.previous", (function(e) {
                console.log("stepper.previous"), e.goPrevious(), KTUtil.scrollTop()
            })), a.push(FormValidation.formValidation(i, {
                fields: {},
                plugins: {
                    trigger: new FormValidation.plugins.Trigger,
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: ""
                    })
                }
            })), a.push(FormValidation.formValidation(i, {
                fields: {},
                plugins: {
                    trigger: new FormValidation.plugins.Trigger,
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: ""
                    })
                }
            })), a.push(FormValidation.formValidation(i, {
                fields: {},
                plugins: {
                    trigger: new FormValidation.plugins.Trigger,
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: ""
                    })
                }
            })), a.push(FormValidation.formValidation(i, {
                fields: {},
                plugins: {
                    trigger: new FormValidation.plugins.Trigger,
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: ""
                    })
                }
            })), o.addEventListener("click", (function(e) {
                a[3].validate().then((function(t) {
                    "Valid" === t ? (e.preventDefault(), o.disabled = !0, o.setAttribute("data-kt-indicator", "on"),
                        $.ajax({
                            type        : 'post',
                            url         : 'store-client',
                            data        : new FormData($("#create_client_form")[0]),
                            contentType : false,
                            processData : false,
                            cache       : false,
                            success     : function (response) {
                                decodeResponse(response);
                                if(response.code === 200) {
                                    setTimeout(function() { window.location.href='client-management'; }, 1500);
                                }
                                o.removeAttribute("data-kt-indicator"), o.disabled = !1, r.goNext()
                            },
                            error   : function (response) {
                                toastr.error(response.statusText);
                            }
                        }), setTimeout((function() {
                    }), 2e3)) : Swal.fire({
                        text: "Sorry, looks like there are some errors detected, please try again.",
                        icon: "error",
                        buttonsStyling: !1,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-light"
                        }
                    }).then((function() {
                        KTUtil.scrollTop()
                    }))
                }))
            }))/*, $(i.querySelector('[name="card_expiry_month"]')).on("change", (function() {
                a[3].revalidateField("card_expiry_month")
            })), $(i.querySelector('[name="card_expiry_year"]')).on("change", (function() {
                a[3].revalidateField("card_expiry_year")
            })), $(i.querySelector('[name="business_type"]')).on("change", (function() {
                a[2].revalidateField("business_type")
            }))*/
        }
    }
}();
KTUtil.onDOMContentLoaded((function() {
    KTCreateAccount.init()
}));
