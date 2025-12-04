let autocomplete;
let address1Field;
let address2Field;
let postalField;

let pa_autocomplete;
let pa_address1Field;
let pa_address2Field;
let pa_postalField;

let nok_autocomplete;
let nok_address1Field;
let nok_address2Field;
let nok_postalField;

function initAutocomplete() {
    address1Field = document.querySelector("#current_address_line_one");
    address2Field = document.querySelector("#current_address_line_two");
    postalField = document.querySelector("#current_post_code");
    autocomplete = new google.maps.places.Autocomplete(address1Field, {
        fields: ["address_components", "geometry"],
        types: ["address"],
    });
    autocomplete.addListener("place_changed", fillInAddress);



    pa_address1Field = document.querySelector("#permanent_address_line_one");
    pa_address2Field = document.querySelector("#permanent_address_line_two");
    pa_postalField = document.querySelector("#permanent_post_code");
    pa_autocomplete = new google.maps.places.Autocomplete(pa_address1Field, {
        fields: ["address_components", "geometry"],
        types: ["address"],
    });
    pa_autocomplete.addListener("place_changed", pa_fillInAddress);



    nok_address1Field = document.querySelector("#next_of_kin_address_line_one");
    nok_address2Field = document.querySelector("#next_of_kin_address_line_two");
    nok_postalField = document.querySelector("#next_of_kin_post_code");
    nok_autocomplete = new google.maps.places.Autocomplete(nok_address1Field, {
        fields: ["address_components", "geometry"],
        types: ["address"],
    });
    nok_autocomplete.addListener("place_changed", nok_fillInAddress);
}

/*--- BEGIN CURRENT ADDRESS ---*/
function fillInAddress() {
    address2Field.value = '';
    postalField.value = '';
    document.querySelector("#current_city").value = '';
    document.querySelector("#current_state").value = '';
    document.querySelector("#current_country").value = '';

    const place = autocomplete.getPlace();
    let address1 = "";
    let address2 = "";
    let postcode = "";

    for (const component of place.address_components) {
        const componentType = component.types[0];
        switch (componentType) {
            case "street_number": {
                address1 = `${component.long_name} ${address1}`;
                break;
            }
            case "route": {
                address1 += component.long_name;
                break;
            }
            case "sublocality_level_1": {
                address1 += ", "+component.long_name;
                break;
            }
            case "sublocality_level_2": {
                address1 += ", "+component.long_name;
                break;
            }
            case "locality": {
                address2 += component.long_name;
                break;
            }
            case "postal_code": {
                postcode = `${component.long_name}${postcode}`;
                break;
            }
            case "postal_code_suffix": {
                postcode = `${postcode}-${component.long_name}`;
                break;
            }
            case "postal_town": {
                document.querySelector("#current_city").value = component.long_name;
                break;
            }
            case "administrative_area_level_1": {
                const current_state = component.long_name;
                document.querySelector("#current_state").value = current_state === "England" ? "" : current_state;
                break;
            }
            case "country": {
                if (component.long_name === 'New Zealand') {
                    document.querySelector("#current_city").value = address2;
                    address2 = '';
                }
                document.querySelector("#current_country").value = component.long_name;
                break;
            }
        }
    }

    address1Field.value = address1;
    address2Field.value = address2;
    postalField.value = postcode;
    address2Field.focus();
}
/*--- END CURRENT ADDRESS ---*/

/*--- BEGIN PERMANENT ADDRESS ---*/
function pa_fillInAddress() {
    pa_address2Field.value = '';
    pa_postalField.value = '';
    document.querySelector("#permanent_city").value = '';
    document.querySelector("#permanent_state").value = '';
    document.querySelector("#permanent_country").value = '';

    const pa_place = pa_autocomplete.getPlace();
    let pa_address1 = "";
    let pa_address2 = "";
    let pa_postcode = "";

    for (const pa_component of pa_place.address_components) {
        const pa_componentType = pa_component.types[0];

        switch (pa_componentType) {
            case "street_number": {
                pa_address1 = `${pa_component.long_name} ${pa_address1}`;
                break;
            }
            case "route": {
                pa_address1 += pa_component.long_name;
                break;
            }
            case "sublocality_level_1": {
                pa_address1 += ", "+pa_component.long_name;
                break;
            }
            case "sublocality_level_2": {
                pa_address1 += ", "+pa_component.long_name;
                break;
            }
            case "locality": {
                pa_address2 += pa_component.long_name;
                break;
            }
            case "postal_code": {
                pa_postcode = `${pa_component.long_name}${pa_postcode}`;
                break;
            }
            case "postal_code_suffix": {
                pa_postcode = `${pa_postcode}-${pa_component.long_name}`;
                break;
            }
            case "postal_town": {
                document.querySelector("#permanent_city").value = pa_component.long_name;
                break;
            }
            case "administrative_area_level_1": {
                const permanent_state = pa_component.long_name;
                document.querySelector("#permanent_state").value = permanent_state === "England" ? "" : permanent_state;
                break;
            }
            case "country": {
                if (pa_component.long_name === 'New Zealand') {
                    document.querySelector("#permanent_city").value = pa_address2;
                    pa_address2 = '';
                }
                document.querySelector("#permanent_country").value = pa_component.long_name;
                break;
            }
        }
    }

    pa_address1Field.value = pa_address1;
    pa_address2Field.value = pa_address2;
    pa_postalField.value = pa_postcode;
    pa_address2Field.focus();
}
/*--- END PERMANENT ADDRESS ---*/

/*--- BEGIN NEXT OF KIN ADDRESS ---*/
function nok_fillInAddress() {
    nok_address2Field.value = '';
    nok_postalField.value = '';
    document.querySelector("#next_of_kin_city").value = '';
    document.querySelector("#next_of_kin_state").value = '';
    document.querySelector("#next_of_kin_country").value = '';

    const nok_place = nok_autocomplete.getPlace();
    let nok_address1 = "";
    let nok_address2 = "";
    let nok_postcode = "";

    for (const nok_component of nok_place.address_components) {
        const nok_componentType = nok_component.types[0];

        switch (nok_componentType) {
            case "street_number": {
                nok_address1 = `${nok_component.long_name} ${nok_address1}`;
                break;
            }
            case "route": {
                nok_address1 += nok_component.long_name;
                break;
            }
            case "sublocality_level_1": {
                nok_address1 += ", "+nok_component.long_name;
                break;
            }
            case "sublocality_level_2": {
                nok_address1 += ", "+nok_component.long_name;
                break;
            }
            case "locality": {
                nok_address2 += nok_component.long_name;
                break;
            }
            case "postal_code": {
                nok_postcode = `${nok_component.long_name}${nok_postcode}`;
                break;
            }
            case "postal_code_suffix": {
                nok_postcode = `${nok_postcode}-${nok_component.long_name}`;
                break;
            }
            case "postal_town": {
                document.querySelector("#next_of_kin_city").value = nok_component.long_name;
                break;
            }
            case "administrative_area_level_1": {
                const next_of_kin_state = nok_component.long_name;
                document.querySelector("#next_of_kin_state").value = next_of_kin_state === "England" ? "" : next_of_kin_state;
                break;
            }
            case "country": {
                if (nok_component.long_name === 'New Zealand') {
                    document.querySelector("#next_of_kin_city").value = nok_address2;
                    nok_address2 = '';
                }
                document.querySelector("#next_of_kin_country").value = nok_component.long_name;
                break;
            }
        }
    }

    nok_address1Field.value = nok_address1;
    nok_address2Field.value = nok_address2;
    nok_postalField.value = nok_postcode;
    nok_address2Field.focus();
}
/*--- END NEXT OF KIN ADDRESS ---*/

window.initAutocomplete = initAutocomplete;
