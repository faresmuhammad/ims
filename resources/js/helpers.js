export function formatTimeSince(datetime) {

    const date = new Date(datetime);
    const diff = new Date() - date
    const minutes = Math.floor(diff / (1000 * 60))
    const hours = Math.floor(diff / (1000 * 60 * 60))
    const days = Math.floor(diff / (1000 * 60 * 60 * 24))
    const weeks = Math.floor(diff / (1000 * 60 * 60 * 24 * 7))
    const months = Math.floor(diff / (1000 * 60 * 60 * 24 * 30))
    const years = Math.floor(diff / (1000 * 60 * 60 * 24 * 365))
    const formatted = years < 1 ? months < 1 ? weeks < 1 ? days == 1 ? 'Yesterday' : days < 1 ? hours < 1 ? minutes < 1 ? 'Just Now' : `${minutes} minutes ago` : `${hours} hours ago` : `${days} days ago` : `${weeks} weeks ago` : `${months} months ago` : `${years} years ago`;
    return formatted;
}

export function formatDateTime(datetime) {
    const date = new Date(datetime)
    return date.toLocaleString()
}
