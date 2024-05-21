import moment from "moment";

export function formatTimeSince(datetime) {
    return moment(datetime).fromNow()
}

export function formatDateTime(datetime, format) {
    return moment(datetime).format(format)
}

export function formatExpireDate(date) {
    const formats = ['MM/YYYY', 'YYYY-MM-DD', 'YYYY-MM-DD hh:mm:ss']
    return moment(date, formats).format('MM/YYYY')
}
