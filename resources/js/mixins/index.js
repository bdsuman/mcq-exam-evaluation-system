import { trans } from "laravel-vue-i18n";

import dayjs from "dayjs";
import customParseFormat from "dayjs/plugin/customParseFormat";
dayjs.extend(customParseFormat);

import duration from "dayjs/plugin/duration";
dayjs.extend(duration);

import utc from "dayjs/plugin/utc";
dayjs.extend(utc);

import objectSupport from "dayjs/plugin/objectSupport";
dayjs.extend(objectSupport);

export default {
    methods: {
        formatDateTime(dbTime) {
            return dayjs.utc(dbTime).local().format("DD.MM.YYYY hh:mm A");
        },
        formateDate(date, formate = "DD.MM.YYYY") {
            return dayjs(date).format(formate).toString();
        },
        formateTime(time, formate = "HH:mm") {
            // console.log(formate)
            return dayjs(time, "HH:mm:ss").format(formate).toString();
        },

        formatExactTime(date_time, format = "HH:mm:ss") {
            return dayjs(date_time, "YYYY-MM-DD HH:mm:ss").format(format);
        },
        userNameAbbreviations(first_name = "", last_name = "") {
            return `${first_name ? first_name[0]?.toUpperCase() : ""}${
                last_name ? last_name[0]?.toUpperCase() : ""
            }`;
        },
        frequencyView(data) {
            data = JSON.parse(data ?? "[]");
            if (!data || !data.length) return;
            data = data.map((item) => trans(item));
            return data.join(", ");
        },
        getMonthName(monthNumber) {
            if (isNaN(monthNumber)) {
                return trans("Invalid month");
            }

            return trans(dayjs().month(monthNumber).format("MMMM"));
        },
        getBlock(inputName) {
            const blocks = {
                attachment_style: "Block 1",
                big_five_ocean: "Block 2",
                communication_preference: "Block 3",
                time_orient: "Block 4",
                feedback_preference: "Block 5",
            }

            return trans(blocks[inputName] ?? "") || null
        }
    },
};
