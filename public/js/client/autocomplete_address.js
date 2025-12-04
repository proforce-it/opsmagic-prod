let autocomplete;
let address1Field;
let address2Field;
let postalField;

let site_autocomplete;
let site_address1Field;
let site_address2Field;
let site_postalField;

function initAutocomplete() {
    address1Field = document.querySelector("#address_line_one");
    address2Field = document.querySelector("#address_line_two");
    postalField = document.querySelector("#postcode");
    autocomplete = new google.maps.places.Autocomplete(address1Field, {
        fields: ["address_components", "geometry"],
        types: ["address"],
    });
    autocomplete.addListener("place_changed", fillInAddress);

    site_address1Field = document.querySelector("#site_address_line_one");
    site_address2Field = document.querySelector("#site_address_line_two");
    site_postalField = document.querySelector("#site_postcode");
    site_autocomplete = new google.maps.places.Autocomplete(site_address1Field, {
        fields: ["address_components", "geometry"],
        types: ["address"],
    });
    site_autocomplete.addListener("place_changed", site_fillInAddress);
}

function fillInAddress() {
    const place = autocomplete.getPlace();
    let address1 = "";
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
            case "postal_code": {
                postcode = `${component.long_name}${postcode}`;
                break;
            }
            case "postal_code_suffix": {
                postcode = `${postcode}-${component.long_name}`;
                break;
            }
            case "postal_town": {
                document.querySelector("#city").value = component.long_name;
                break;
            }
            /*case "administrative_area_level_1": {
                document.querySelector("#current_state").value = component.long_name;
                break;
            }*/
            case "country": {
                document.querySelector("#country").value = component.long_name;
                break;
            }
        }
    }

    address1Field.value = address1;
    postalField.value = postcode;
    address2Field.focus();
}

function site_fillInAddress() {
    const site_place = site_autocomplete.getPlace();
    let site_address1 = "";
    let site_postcode = "";

    for (const site_component of site_place.address_components) {
        const site_componentType = site_component.types[0];

        switch (site_componentType) {
            case "street_number": {
                site_address1 = `${site_component.long_name} ${site_address1}`;
                break;
            }
            case "route": {
                site_address1 += site_component.long_name;
                break;
            }
            case "sublocality_level_1": {
                site_address1 += ", "+site_component.long_name;
                break;
            }
            case "sublocality_level_2": {
                site_address1 += ", "+site_component.long_name;
                break;
            }
            case "postal_code": {
                site_postcode = `${site_component.long_name}${site_postcode}`;
                break;
            }
            case "postal_code_suffix": {
                site_postcode = `${site_postcode}-${site_component.long_name}`;
                break;
            }
            case "postal_town": {
                document.querySelector("#site_city").value = site_component.long_name;
                break;
            }
            /*case "administrative_area_level_1": {
                document.querySelector("#permanent_state").value = site_component.long_name;
                break;
            }*/
            case "country": {
                document.querySelector("#site_country").value = site_component.long_name;
                break;
            }
        }
    }

    if (site_place.geometry && site_place.geometry.location) {
        const site_lat = site_place.geometry.location.lat();
        const site_lng = site_place.geometry.location.lng();

        document.querySelector("#site_address_latitude").value = site_lat;
        document.querySelector("#site_address_longitude").value = site_lng;
    }

    site_address1Field.value = site_address1;
    site_postalField.value = site_postcode;
    site_address2Field.focus();
}