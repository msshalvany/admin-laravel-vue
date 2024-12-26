import {
    useAlertStateError,
    useAlertStateSuccess, useAlertStateWarning,
    useAlertText,
    useLoader
} from "~/composables/states.js";

export const loaderfun = () => {
    if (useLoader().value === true) {
        setTimeout(function () {
            useLoader().value = false
        },500)
        return false
    }
    if (useLoader().value === false) {
        useLoader().value = true
        return true
    }
}
export const AlertSuccess= (text) => {
    useAlertText().value = text
    useAlertStateSuccess().value = true
    setTimeout(function () {
        useAlertStateSuccess().value = false
    } ,5000)
}
export const AlertError = (text) => {
    useAlertText().value = text
    useAlertStateError().value = true
    setTimeout(function () {
        useAlertStateError().value = false
    } ,5000)
}
export const AlertWarning= (text) => {
    useAlertText().value = text
    useAlertStateWarning().value = true
    setTimeout(function () {
        useAlertStateWarning().value = false
    } ,5000)
}

