import ApiService from './api.service';
import {TokenService} from './storage.service';
import {PermissionService} from "./permission.service";
import moment from "moment";
import Dexie from "dexie";
import __ from "../helpers";

class AuthenticationError extends Error {
    constructor(errorCode, message) {
        super(message);
        this.name = this.constructor.name;
        this.message = message;
        this.errorCode = errorCode;
    }
}

// login — prepare a request and obtain a token from the API via the API service
// logout — clear user stuff from the browser storage
// refresh token — obtain a refresh token from the API service

const UserService = {
    /**
     * Login the user and store the access token to TokenService.
     *
     * @returns access_token
     * @throws AuthenticationError
     **/
    login: async function (email, password) {
        const requestData = {
            method: 'post',
            url: "sysadmin/api/login",
            data: {
                grant_type: 'password',
                username: email,
                password: password
            },
            // TODO understand and config auth for axios
            auth: {
                username: process.env.VUE_APP_CLIENT_ID,
                password: process.env.VUE_APP_CLIENT_SECRET
            }

        };

        try {

            const response = await ApiService.customRequest(requestData);

            if (response.data.status === 1) {
                TokenService.saveToken(response.data.data.data.access_token);
                TokenService.saveRefreshToken(response.data.data.data.refresh_token);
                localStorage.setItem('Employee', JSON.stringify(response.data.data.Employee));
                localStorage.setItem('OptionSetting', JSON.stringify(response.data.data.OptionSetting));
                TokenService.setWorkdate(moment(new Date()).format('DD/MM/YYYY'));
                ApiService.setHeader();

                // TODO set permission
                let permission = {
                    menuLeftArr: [],
                    menuTopArr: []
                };

                if (response.data.data.MenuTopArr) {
                    permission.menuTopArr = response.data.data.MenuTopArr;
                }
                if (response.data.data.MenuLeftArr) {
                    permission.menuLeftArr = response.data.data.MenuLeftArr;
                }

                PermissionService.savePermission(permission);
            }


            // NOTE: We haven't covered this yet in our ApiService
            //       but don't worry about this just yet - I'll come back to it later
            ApiService.mount401Interceptor();

            return response.data;
            // return response.data.access_token;
        } catch (error) {
            throw new AuthenticationError(error.response.status, error.response.data.detail);
        }
    },

    /**
     * Refresh the access token.
     **/
    refreshToken: async function () {
        const refreshToken = TokenService.getRefreshToken()

        const requestData = {
            method: 'post',
            url: "/o/token/",
            data: {
                grant_type: 'refresh_token',
                refresh_token: refreshToken
            },
            auth: {
                username: process.env.VUE_APP_CLIENT_ID,
                password: process.env.VUE_APP_CLIENT_SECRET
            }
        };

        try {
            const response = await ApiService.customRequest(requestData);

            TokenService.saveToken(response.data.access_token);
            TokenService.saveRefreshToken(response.data.refresh_token);
            // Update the header in ApiService
            ApiService.setHeader();

            return response.data.access_token;
        } catch (error) {
            throw new AuthenticationError(error.response.status, error.response.data.detail);
        }

    },

    /**
     * Logout the current user by removing the token from storage.
     *
     * Will also remove `Authorization Bearer <token>` header from future requests.
     **/
    async logout() {
        // Remove the token and remove Authorization header from Api Service as well
        TokenService.removeToken();
        TokenService.removeRefreshToken();
        ApiService.removeHeader();

        PermissionService.removePermission();
        localStorage.removeItem('Employee');
        localStorage.removeItem('workdate');
        localStorage.removeItem('OptionSetting');

        await Dexie.getDatabaseNames(async function (names, cb) {
          console.log('database names: ', names);
          await names.forEach(async function (name) {
            let db = new Dexie(name);
            await db.delete().then(function() {
              console.log('Database successfully deleted: ', name);
            }).catch(function (err) {
              console.error('Could not delete database: ', name, err);
            }).finally(function() {
              console.log('Done. Now executing callback if passed.');
              if (typeof cb === 'function') {
                cb();
              }
            });
          });
        });

        // NOTE: Again, we'll cover the 401 Interceptor a bit later.
        ApiService.unmount401Interceptor();
    },

};

export default UserService;

export {UserService, AuthenticationError};
