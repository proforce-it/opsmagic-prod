let url = "http://localhost/vasim/metronic/index.php/"

activeMenu(window.location.pathname);

function activeMenu(endPoint) {
    $('a[href="' + window.location.origin + endPoint + '"]').addClass('active');
}

function sweetAlertRestore(text = "You would like to restore?") {
    return Swal.fire({
        title               : "Are you sure?",
        text                : text,
        icon                : "warning",
        showCancelButton    : true,
        buttonsStyling      : false,
        confirmButtonText   : "Yes, restore it!",
        cancelButtonText    : "No, cancel",
        customClass         : {
            confirmButton   : "btn btn-success",
            cancelButton    : "btn btn-active-light"
        }
    })
}

function sweetAlertConfirmDelete(text = "Are you sure you want to delete selected records?") {
    return Swal.fire({
        title               : "Are you sure?",
        text                : text,
        icon                : "warning",
        showCancelButton    : true,
        buttonsStyling      : false,
        confirmButtonText   : "Yes, delete!",
        cancelButtonText    : "No, cancel",
        customClass         : {
            confirmButton       : "btn fw-bold btn-danger",
            cancelButton        : "btn fw-bold btn-active-light-danger"
        }
    })
}

function sweetAlertArchived(text = "Are you sure you want to archived selected records?") {
    return Swal.fire({
        title               : "Are you sure?",
        text                : text,
        icon                : "warning",
        showCancelButton    : true,
        buttonsStyling      : false,
        confirmButtonText   : "Yes, archive!",
        cancelButtonText    : "No, cancel",
        customClass         : {
            confirmButton       : "btn fw-bold btn-danger",
            cancelButton        : "btn fw-bold btn-active-light-danger"
        }
    })
}

function sweetAlertUnarchived(text = "you want to un-archived selected records?") {
    return Swal.fire({
        title               : "Are you sure?",
        text                : text,
        icon                : "warning",
        showCancelButton    : true,
        buttonsStyling      : false,
        confirmButtonText   : "Yes, un-archive!",
        cancelButtonText    : "No, cancel",
        customClass         : {
            confirmButton       : "btn fw-bold btn-success",
            cancelButton        : "btn fw-bold btn-active-light-danger"
        }
    })
}

function sweetAlertUnlink(title= "Are you sure?", text = "you want to unlink selected records?") {
    return Swal.fire({
        title               : title,
        html                : text,
        icon                : "warning",
        showCancelButton    : true,
        buttonsStyling      : false,
        confirmButtonText   : "Yes, unlink!",
        cancelButtonText    : "No, cancel",
        customClass         : {
            confirmButton       : "btn fw-bold btn-success",
            cancelButton        : "btn fw-bold btn-active-light-danger"
        }
    })
}

function sweetAlertRelink(text = "you want to relink selected records?") {
    return Swal.fire({
        title               : "Are you sure?",
        text                : text,
        icon                : "warning",
        showCancelButton    : true,
        buttonsStyling      : false,
        confirmButtonText   : "Yes, relink!",
        cancelButtonText    : "No, cancel",
        customClass         : {
            confirmButton       : "btn fw-bold btn-success",
            cancelButton        : "btn fw-bold btn-active-light-danger"
        }
    })
}

function decodeResponse(response) {
    if(response.code === 200) {
        toastr.success(response.message);
    } else if(response.code === 500) {
        toastr.error(response.message);
    } else {
        const keys = Object.keys(response.data);
        keys.forEach((key, index) => {
            const inputId = key.replace(/\./g, '_');
            if (index === 0) {
                $("#" + inputId).focus();
            }
            $("#" + inputId + "_error").empty().append(response.data[key][0]);
        });
    }
}

function sweetAlertApproved(text = "Are you sure you want to approve selected records?") {
    return Swal.fire({
        title               : "Are you sure?",
        text                : text,
        icon                : "warning",
        showCancelButton    : true,
        buttonsStyling      : false,
        confirmButtonText   : "Yes, approve!",
        cancelButtonText    : "No, cancel",
        customClass         : {
            confirmButton       : "btn fw-bold btn-success",
            cancelButton        : "btn fw-bold btn-active-light-danger"
        }
    })
}

function sweetAlertUnapproved(text = "you want to un-approved selected records?") {
    return Swal.fire({
        title               : "Are you sure?",
        text                : text,
        icon                : "warning",
        showCancelButton    : true,
        buttonsStyling      : false,
        confirmButtonText   : "Yes, reject!",
        cancelButtonText    : "No, cancel",
        customClass         : {
            confirmButton       : "btn fw-bold btn-danger",
            cancelButton        : "btn fw-bold btn-active-light-danger"
        }
    })
}
