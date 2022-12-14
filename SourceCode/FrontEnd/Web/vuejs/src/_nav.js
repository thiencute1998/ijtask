import {PermissionService} from "./services/permission.service";

let permission = PermissionService.getPermission(), itemsMenu = [];
_.forEach(permission.menuLeftArr, function (item, key) {
    let tmpObj = {};
    tmpObj.name = item.MenuName;
    tmpObj.menuID = item.MenuID;
    tmpObj.parentID = item.ParentID;
    tmpObj.module = item.Module;
    if (!item.Allow && item.FeatureKey !== 'SYSWORKFLOW') {
      // tmpObj.class = 'app-disable';
      tmpObj.attributes = {};
      // TODO: remove comment
      // tmpObj.attributes.disabled = false;
    }

    if (item.RouterFrontEnd) {
        tmpObj.url = '/' + item.RouterFrontEnd;
    }else {
        tmpObj.url = '/pages/404';
    }
    tmpObj.icon = item.Icon;


    if (item.Level == 0) {
        if (item.Module === "SYSADMIN") {
            tmpObj.badge = {};
            tmpObj.badge.variant = 'primary';
            tmpObj.badge.text = 'NEW';
            tmpObj.url = '/dashboard';
        }

        let _itemExist = _.find(itemsMenu, ['menuID', item.MenuID]);
        if (!_itemExist) {
            itemsMenu.push(tmpObj);
        } else {
            let _index = _.findIndex(itemsMenu, _itemExist);
            itemsMenu[_index] = tmpObj;
        }
    } else {
        let _itemExist = _.find(itemsMenu, ['menuID', item.ParentID]);
      let parent = _.find(permission.menuLeftArr, ['MenuID', item.ParentID]);
      if (parent && !parent.Allow && item.FeatureKey !== 'SYSWORKFLOW') {
        tmpObj.attributes = {};
        // TODO: remove comment
        // tmpObj.attributes.disabled = true;
      }
        if (_itemExist) {
            let _index = _.findIndex(itemsMenu, _itemExist);
            if (!_.isArray(itemsMenu[_index].children)) {
                itemsMenu[_index].children = [];
                itemsMenu[_index].children.push(tmpObj);
            } else {
                itemsMenu[_index].children.push(tmpObj);
            }
        }
    }
});


export default {
  // items: [
  //   {
  //     name: 'Dashboard',
  //     url: '/dashboard',
  //     icon: 'icon-speedometer',
  //     moduleNav: 'NavModuleDefault',
  //     badge: {
  //       variant: 'primary',
  //       text: 'NEW'
  //     }
  //   },
  //   {
  //     name: 'V??n b???n',
  //     url: '/dashboard',
  //     icon: 'fa fa-file-text-o',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //   {
  //     name: 'Danh m???c',
  //     url: '/listing',
  //     icon: 'fa fa-file-text-o',
  //     moduleNav: 'NavModuleDefault',
  //     children: [
  //       {
  //         name: '????n v???',
  //         url: '/listing/company',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'Nh??n vi??n',
  //         url: '/listing/employee',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'Nh?? cung c???p',
  //         url: '/listing/vendor',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: '?????i t?????ng kh??c',
  //         url: '/listing/object',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'D??? ??n',
  //         url: '/listing/project',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'H??ng h??a d???ch v???',
  //         url: '/listing/items',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'Kho???n chi',
  //         url: '/listing/expense',
  //         icon: 'fa fa-file-text-o'
  //       },
  //
  //       {
  //         name: '????n v??? t??nh',
  //         url: '/listing/uom',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'Lo???i c??ng vi???c',
  //         url: '/listing/tcatelist',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'Lo???i kh??ch h??ng',
  //         url: '/listing/ccatelist',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'Lo???i h???p ?????ng',
  //         url: '/listing/ctcatelist',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'Lo???i t??i li???u',
  //         url: '/listing/dcatelist',
  //         icon: 'fa fa-file-text-o'
  //       },
  //
  //     ]
  //   },
  //   {
  //     name: 'C??ng vi???c',
  //     url: '/dashboard',
  //     icon: 'fa fa-tasks',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //   {
  //     name: '?????u th???u',
  //     url: '/dashboard',
  //     icon: 'fa fa-drivers-license-o',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //
  //   {
  //     name: 'Ti???n ????? d??? ??n',
  //     url: '/dashboard',
  //     icon: 'fa fa-clock-o',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //
  //   {
  //     name: 'Ch???t l?????ng c??ng tr??nh',
  //     url: '/dashboard',
  //     icon: 'fa fa-industry',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //
  //   {
  //     name: 'Gi??m s??t d??? ??n',
  //     url: '/dashboard',
  //     icon: 'fa fa-video-camera',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //
  //   {
  //     name: 'K??? ho???ch v???n',
  //     url: '/dashboard',
  //     icon: 'fa fa-newspaper-o',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //
  //   {
  //     name: 'K??? to??n',
  //     url: '/dashboard',
  //     icon: 'fa fa-book',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //
  //   {
  //     name: 'Quy???t to??n',
  //     url: '/dashboard',
  //     icon: 'fa fa-bullseye',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //
  //   {
  //     name: 'H??? th???ng',
  //     url: '/sysadmin',
  //     // icon: 'cui-settings',
  //     icon: 'fa fa-cog',
  //     moduleNav: 'NavModuleSysadmin',
  //     children: [
  //       {
  //         name: 'Ng?????i d??ng',
  //         url: '/sysadmin/users',
  //         icon: 'fa fa-user'
  //       },
  //
  //       {
  //         name: 'Nh??m ng?????i d??ng',
  //         url: '/sysadmin/group-user',
  //         icon: 'fa fa-users'
  //       },
  //       // {
  //       //   name: 'T??y ch???n h??? th???ng',
  //       //   url: '/sysadmin/sys-setup',
  //       //   icon: 'fa fa-wrench'
  //       // },
  //       // {
  //       //   name: 'S??? t??? ?????ng t??ng',
  //       //   url: '/sysadmin/auto-number',
  //       //   icon: 'fa fa-rocket'
  //       // },
  //       {
  //         name: 'Thi???t l???p',
  //         url: '/sysadmin/settings',
  //         icon: 'fa fa-rocket'
  //       },
  //
  //     ]
  //   }
  // ]
    items: itemsMenu
}
