const PERMISSION = 'permission';

/**
 *
 **/

const PermissionService = {
    getPermission() {
        return JSON.parse(localStorage.getItem(PERMISSION) || '{}');
    },

    savePermission(permission) {
        localStorage.setItem(PERMISSION, JSON.stringify(permission));
    },

    removePermission() {
        localStorage.removeItem(PERMISSION);
    }
};

export {PermissionService};