import {usePage} from "@inertiajs/vue3";

export function usePermission() {
    const hasRole = (role) => usePage().props.user.roles.includes(role);
    const can = (permission) => usePage().props.user.permissions.includes(permission);

    return { hasRole, can };
}
