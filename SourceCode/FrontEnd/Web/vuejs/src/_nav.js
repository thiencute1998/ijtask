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
  //     name: 'Văn bản',
  //     url: '/dashboard',
  //     icon: 'fa fa-file-text-o',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //   {
  //     name: 'Danh mục',
  //     url: '/listing',
  //     icon: 'fa fa-file-text-o',
  //     moduleNav: 'NavModuleDefault',
  //     children: [
  //       {
  //         name: 'Đơn vị',
  //         url: '/listing/company',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'Nhân viên',
  //         url: '/listing/employee',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'Nhà cung cấp',
  //         url: '/listing/vendor',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'Đối tượng khác',
  //         url: '/listing/object',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'Dự án',
  //         url: '/listing/project',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'Hàng hóa dịch vụ',
  //         url: '/listing/items',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'Khoản chi',
  //         url: '/listing/expense',
  //         icon: 'fa fa-file-text-o'
  //       },
  //
  //       {
  //         name: 'Đơn vị tính',
  //         url: '/listing/uom',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'Loại công việc',
  //         url: '/listing/tcatelist',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'Loại khách hàng',
  //         url: '/listing/ccatelist',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'Loại hợp đồng',
  //         url: '/listing/ctcatelist',
  //         icon: 'fa fa-file-text-o'
  //       },
  //       {
  //         name: 'Loại tài liệu',
  //         url: '/listing/dcatelist',
  //         icon: 'fa fa-file-text-o'
  //       },
  //
  //     ]
  //   },
  //   {
  //     name: 'Công việc',
  //     url: '/dashboard',
  //     icon: 'fa fa-tasks',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //   {
  //     name: 'Đấu thầu',
  //     url: '/dashboard',
  //     icon: 'fa fa-drivers-license-o',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //
  //   {
  //     name: 'Tiến độ dự án',
  //     url: '/dashboard',
  //     icon: 'fa fa-clock-o',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //
  //   {
  //     name: 'Chất lượng công trình',
  //     url: '/dashboard',
  //     icon: 'fa fa-industry',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //
  //   {
  //     name: 'Giám sát dự án',
  //     url: '/dashboard',
  //     icon: 'fa fa-video-camera',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //
  //   {
  //     name: 'Kế hoạch vốn',
  //     url: '/dashboard',
  //     icon: 'fa fa-newspaper-o',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //
  //   {
  //     name: 'Kế toán',
  //     url: '/dashboard',
  //     icon: 'fa fa-book',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //
  //   {
  //     name: 'Quyết toán',
  //     url: '/dashboard',
  //     icon: 'fa fa-bullseye',
  //     moduleNav: 'NavModuleDefault',
  //   },
  //
  //   {
  //     name: 'Hệ thống',
  //     url: '/sysadmin',
  //     // icon: 'cui-settings',
  //     icon: 'fa fa-cog',
  //     moduleNav: 'NavModuleSysadmin',
  //     children: [
  //       {
  //         name: 'Người dùng',
  //         url: '/sysadmin/users',
  //         icon: 'fa fa-user'
  //       },
  //
  //       {
  //         name: 'Nhóm người dùng',
  //         url: '/sysadmin/group-user',
  //         icon: 'fa fa-users'
  //       },
  //       // {
  //       //   name: 'Tùy chọn hệ thống',
  //       //   url: '/sysadmin/sys-setup',
  //       //   icon: 'fa fa-wrench'
  //       // },
  //       // {
  //       //   name: 'Số tự động tăng',
  //       //   url: '/sysadmin/auto-number',
  //       //   icon: 'fa fa-rocket'
  //       // },
  //       {
  //         name: 'Thiết lập',
  //         url: '/sysadmin/settings',
  //         icon: 'fa fa-rocket'
  //       },
  //
  //     ]
  //   }
  // ]
    items: itemsMenu
}
