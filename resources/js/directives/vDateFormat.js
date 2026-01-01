import dayjs from "dayjs"
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'

dayjs.extend(utc)
dayjs.extend(timezone)

export default (el, binding) => {
    let format = binding.arg == 'datetime' ? 'DD.MM.YYYY HH:mm' : 'DD.MM.YYYY'
    let datetime = binding.value
    let utc_to_local = true;
    if (typeof binding.value == 'object' && binding.value) {
        format = binding.value.format
        datetime = binding.value.date
        utc_to_local = binding.value.utc_to_local
    }

    if (dayjs(datetime).isValid() && datetime) {
        el.textContent =  dayjs.utc(datetime).tz(dayjs.tz.guess()).format(format)
    } else {
        el.textContent = ''
    }
}
