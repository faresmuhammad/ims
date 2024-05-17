import moment from "moment";

export function formatTimeSince(datetime) {
    return moment(datetime).fromNow()
}

export function formatDateTime(datetime) {
    const date = new Date(datetime)
    return date.toLocaleString()
}

export function formatExpireDate(date) {
    const formats = ['MM/YYYY', 'YYYY-MM-DD', 'YYYY-MM-DD hh:mm:ss']
    return moment(date, formats).format('MM/YYYY')
}
