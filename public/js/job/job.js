"use strict";
var KTCreateAccount = function() {
    var e, t, i, o, s, r, a = [];
    return {
        init: function() {
            (e = document.querySelector("#kt_modal_create_account")) && new bootstrap.Modal(e), t = document.querySelector("#kt_create_account_stepper"), i = t.querySelector("#create_job_form"), o = t.querySelector('[data-kt-stepper-action="submit"]'), s = t.querySelector('[data-kt-stepper-action="next"]'), (r = new KTStepper(t)).on("kt.stepper.changed", (function(e) {
                4 === r.getCurrentStepIndex() ? (o.classList.remove("d-none"), o.classList.add("d-inline-block"), s.classList.add("d-none")) : 5 === r.getCurrentStepIndex() ? (o.classList.add("d-none"), s.classList.add("d-none")) : (o.classList.remove("d-inline-block"), o.classList.remove("d-none"), s.classList.remove("d-none"))
            })), r.on("kt.stepper.next", (function(e) {
                var t = a[e.getCurrentStepIndex() - 1];
                t ? t.validate().then((function(t) {
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
                })) : (e.goNext(), KTUtil.scrollTop())
            })), r.on("kt.stepper.previous", (function(e) {
                console.log("stepper.previous"), e.goPrevious(), KTUtil.scrollTop()
            })), a.push(FormValidation.formValidation(i, {
                fields: {
                    job_title        : {
                        validators      : {
                            notEmpty    : {
                                message : "Job title field is required"
                            }
                        }
                    },
                    client      : {
                        validators      : {
                            notEmpty    : {
                                message : "Client field is required"
                            }
                        }
                    },
                    category          : {
                        validators      : {
                            notEmpty    : {
                                message : "Category field is required"
                            }
                        }
                    },
                    cost    : {
                        validators      : {
                            notEmpty    : {
                                message : "Cost field is required"
                            }
                        }
                    },
                    job_timeline           : {
                        validators      : {
                            notEmpty    : {
                                message : "Job timeline field is required"
                            }
                        }
                    },
                    job_start              : {
                        validators      : {
                            notEmpty    : {
                                message : "Job start field is required"
                            }
                        }
                    },
                    job_end            : {
                        validators      : {
                            notEmpty    : {
                                message : "Job end field is required"
                            }
                        }
                    },
                    number_of_workers       : {
                        validators      : {
                            notEmpty    : {
                                message : "Number of workers field is required"
                            }
                        }
                    },
                    worker_cost: {
                        validators      : {
                            notEmpty    : {
                                message : "Worker cost field is required"
                            }
                        }
                    },
                    details    : {
                        validators      : {
                            notEmpty    : {
                                message : "Details field is required"
                            }
                        }
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger,
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: ""
                    })
                }
            })), a.push(FormValidation.formValidation(i, {
                fields: {
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger,
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: ""
                    })
                }
            })), a.push(FormValidation.formValidation(i, {
                fields: {
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger,
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: ""
                    })
                }
            })), a.push(FormValidation.formValidation(i, {
                fields: {
                },
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
                            url         : $("#form_url").val(),
                            data        : new FormData($("#create_job_form")[0]),
                            contentType : false,
                            processData : false,
                            cache       : false,
                            success     : function (response) {
                                decodeResponse(response);
                                if(response.code === 200) {
                                    setTimeout(function() { window.location.href=$("#redirect_url").val(); }, 1500);
                                }
                            },
                            error   : function (response) {
                                toastr.error(response.statusText);
                            }
                        }), setTimeout((function() {
                        o.removeAttribute("data-kt-indicator"), o.disabled = !1, r.goNext()
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
