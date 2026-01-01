import { useRoute } from "vue-router";

export const useIsEqual = (a, b) => {
    if (a == b) return true;
    if (a && b && typeof a == 'object' && typeof b == 'object') {
        if (a.constructor !== b.constructor) return false;

        var length, i, keys;
        if (Array.isArray(a)) {
            length = a.length;
            if (length != b.length) return false;
            for (i = length; i-- !== 0;)
                if (!useIsEqual(a[i], b[i])) return false;
            return true;
        }


        if ((a instanceof Map) && (b instanceof Map)) {
            if (a.size !== b.size) return false;
            for (i of a.entries())
                if (!b.has(i[0])) return false;
            for (i of a.entries())
                if (!useIsEqual(i[1], b.get(i[0]))) return false;
            return true;
        }

        if ((a instanceof Set) && (b instanceof Set)) {
            if (a.size !== b.size) return false;
            for (i of a.entries())
                if (!b.has(i[0])) return false;
            return true;
        }

        if (ArrayBuffer.isView(a) && ArrayBuffer.isView(b)) {
            length = a.length;
            if (length != b.length) return false;
            for (i = length; i-- !== 0;)
                if (a[i] !== b[i]) return false;
            return true;
        }


        if (a.constructor === RegExp) return a.source === b.source && a.flags === b.flags;
        if (a.valueOf !== Object.prototype.valueOf) return a.valueOf() === b.valueOf();
        if (a.toString !== Object.prototype.toString) return a.toString() === b.toString();

        keys = Object.keys(a);
        length = keys.length;
        if (length !== Object.keys(b).length) return false;

        for (i = length; i-- !== 0;)
            if (!Object.prototype.hasOwnProperty.call(b, keys[i])) return false;

        for (i = length; i-- !== 0;) {
            var key = keys[i];

            if (!useIsEqual(a[key], b[key])) return false;
        }

        return true;
    }

    // true if both NaN, false otherwise
    return a !== a && b !== b;
}

export const debounceReturn = (callback, timeout) => {
    let timer;
    const resolvers = [];
    return async (...args) => {
        clearTimeout(timer);
        return new Promise((resolve) => {
            resolvers.push(resolve);
            timer = setTimeout(async () => {
                const result = await callback.apply(this, args);
                resolvers.map((resolver) => resolver(result));
            }, timeout);
        });
    };
};

export const checkEmailExists = async (value, except_email = null) => {
    if (value == '' || value == null) return true

    const res = await axios.get(route(`admin.check-email-exists`, { email: value, except_email: except_email }),)
    return !res.data.status
}

export const checkCompanyInvitationEmailExists = async (value, except_email = null) => {
    if (value == '' || value == null) return true

    const res = await axios.get(route(`admin.company-manager-invite.check-email-exists`, { email: value, except_email: except_email }),)
    return !res.data.status
}

export const checkTitleExists = async (value, except_title = null) => {
    if (value == '' || value == null) return true

    const res = await axios.get(route(`background.track.title-exists`, { title: value, except_title: except_title }),)
    return !res.data.status
}

export const checkWorkingTimeNameExists = async (value, except_id = null) => {
    if (value == '' || value == null) return true

    const res = await axios.get(route(`check-name-working-timeset-exists`, { name: value, except_id: except_id }),)
    return !res.data.status
}

export const getQuery = (column) => {
    return useRoute().query.hasOwnProperty(column) ? useRoute().query[column]?.split(',') : [];
}

export const capitalizeFirstLetter = (str) => {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

export const checkEmployeeIdExists = async (value, except_employee_id = null) => {
    if (value == '' || value == null) return true

    const res = await axios.get(route(`user.employees.check-id`, { employee_id: value, except_employee_id: except_employee_id }),)
    return !res.data.status
}

export const isValidWeek = (weekNumber, year) => {
    if (weekNumber < 1) return false;

    // Get the date of Dec 28, which always falls in the last ISO week of the year
    const dec28 = new Date(year, 11, 28); // Month is 0-based

    // Get the ISO week number of Dec 28
    const lastISOWeek = getISOWeek(dec28);

    return weekNumber <= lastISOWeek;
}

function getISOWeek(date) {
    const target = new Date(date.valueOf());

    // Set to Thursday of the current week: ISO 8601 defines week as the one containing Thursday
    target.setDate(target.getDate() + 3 - ((target.getDay() + 6) % 7));

    // First Thursday of the year
    const firstThursday = new Date(target.getFullYear(), 0, 4);

    // Calculate week number
    const weekNumber = 1 + Math.round(
        ((target - firstThursday) / 86400000 - 3 + ((firstThursday.getDay() + 6) % 7)) / 7
    );

    return weekNumber;
}
