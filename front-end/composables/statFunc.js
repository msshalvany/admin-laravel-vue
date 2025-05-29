import {
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

