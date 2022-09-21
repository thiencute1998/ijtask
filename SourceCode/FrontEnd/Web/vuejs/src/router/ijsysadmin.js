// Sysadmin
// ========================== user============================================
import * as path from "path";

const SysadminUserChangePassword = () => import('@/views/ijsysadmin/user/ChangePassword');
const SysadminUsersList = () => import('@/views/ijsysadmin/user/Lists');
const SysadminUserView = () => import('@/views/ijsysadmin/user/View');
const SysadminUserEdit = () => import('@/views/ijsysadmin/user/Edit');
const SysadminUserCreate = () => import('@/views/ijsysadmin/user/Create');
const SysadminUserIndex = () => import('@/views/ijsysadmin/user/Index');


// ========================== group user ============================================
const SysadminGroupUserLists = () => import('@/views/ijsysadmin/group-user/Lists');
const SysadminGroupUserView = () => import('@/views/ijsysadmin/group-user/View');
const SysadminGroupUserEdit = () => import('@/views/ijsysadmin/group-user/Edit');
const SysadminGroupUserCreate = () => import('@/views/ijsysadmin/group-user/Create');
const SysadminGroupUserIndex = () => import('@/views/ijsysadmin/group-user/Index');

// ========================== settings ============================================
const SysadminSettings = () => import('@/views/ijsysadmin/settings/Index');

// ========================== workflow ============================================
// const SysadminWorkflowLists = () => import('@/views/ijsysadmin/workflow/Lists');
// const SysadminWorkflowView = () => import('@/views/ijsysadmin/workflow/View');
// const SysadminWorkflowrEdit = () => import('@/views/ijsysadmin/workflow/Edit');
// const SysadminWorkflowCreate = () => import('@/views/ijsysadmin/workflow/Create');
// const SysadminWorkflowIndex = () => import('@/views/ijsysadmin/workflow/Index');

// ========================== fstatuslist - Trạng thái nút chức năng ============================================
const SysadminFstatuslistList = () => import('@/views/ijsysadmin/fstatuslist/Lists');
const SysadminFstatuslistEdit = () => import('@/views/ijsysadmin/fstatuslist/Edit');
const SysadminFstatuslistCreate = () => import('@/views/ijsysadmin/fstatuslist/Create');
const SysadminFstatuslistView = () => import('@/views/ijsysadmin/fstatuslist/View');

// ========================== status - Trạng thái nút chức năng ============================================
const SysadminSysStatustList = () => import('@/views/ijsysadmin/sys-status/Lists');
const SysadminSysStatusEdit = () => import('@/views/ijsysadmin/sys-status/Edit');
const SysadminSysStatusCreate = () => import('@/views/ijsysadmin/sys-status/Create');
const SysadminSysStatusView = () => import('@/views/ijsysadmin/sys-status/View');

// ========================== Binary Data - Kiểu dữ liệu nhị phân ============================================
const SysadminBinaryDataList = () => import('@/views/ijsysadmin/binarydata/Lists');
const SysadminBinaryDataEdit = () => import('@/views/ijsysadmin/binarydata/Edit');
const SysadminBinaryDataCreate = () => import('@/views/ijsysadmin/binarydata/Create');
const SysadminBinaryDataView = () => import('@/views/ijsysadmin/binarydata/View');

//====================================== Report ================================================================
const SysAdminReportList = () => import('@/views/ijsysadmin/report/Lists')

let router = [
    {
        path: 'sysadmin',
        redirect: '/sysadmin/users',
        name: 'SysAdmin',
        component: {
            render(c) {
                return c('router-view')
            }
        },
        children: [
            // user
            {
              path: 'user/flowchart',
              name: 'sysadmin-users-flowchart',
              component: SysadminUserIndex,
            },
            {
                path: 'users',
                name: 'sysadmin-user-list',
                component: SysadminUsersList,
            },

            {
                path: 'user/view/:id',
                name: 'sysadmin-user-view',
                component: SysadminUserView,
            },

            {
                path: 'user/change-password',
                name: 'sysadmin-user-password',
                component: SysadminUserChangePassword
            },
            {
                path: 'user/create',
                name: 'sysadmin-user-create',
                component: SysadminUserCreate
            },

            {
                path: 'user/edit/:id',
                name: 'sysadmin-user-edit',
                component: SysadminUserEdit
            },

            // group user
          {
            path: 'group-user/flowchart',
            name: 'sysadmin-group-user-flowchart',
            component: SysadminGroupUserIndex
          },
          {
              path: 'group-user',
              name: 'sysadmin-group-user',
              component: SysadminGroupUserLists
          },

          {
              path: 'group-user/view/:id',
              name: 'sysadmin-group-user-view',
              component: SysadminGroupUserView,
          },

          {
              path: 'group-user/create',
              name: 'sysadmin-group-user-create',
              component: SysadminGroupUserCreate
          },

          {
              path: 'group-user/edit/:id',
              name: 'sysadmin-group-user-edit',
              component: SysadminGroupUserEdit
          },

          // settings
          {
              path: 'settings',
              name: 'sysadmin-settings',
              component: SysadminSettings
          },


          // // workflow
          // {
          //   path: 'workflow',
          //   name: 'sysadmin-workflow',
          //   component: SysadminWorkflowLists
          // },
          //
          // {
          //   path: 'workflow/view/:id',
          //   name: 'sysadmin-workflow-view',
          //   component: SysadminWorkflowView,
          // },
          //
          // {
          //   path: 'workflow/create',
          //   name: 'sysadmin-workflow-create',
          //   component: SysadminWorkflowCreate
          // },
          //
          // {
          //   path: 'workflow/edit/:id',
          //   name: 'sysadmin-workflow-edit',
          //   component: SysadminWorkflowrEdit
          // },

          // ===================================== fstatuslist - trạng thái nút chức năng ===========================================
          {
            path: 'fstatuslist',
            name: 'sysadmin-fstatuslist',
            component: SysadminFstatuslistList,
          },
          {
            path: 'fstatuslist/view/:id',
            name: 'sysadmin-fstatuslist-view',
            component: SysadminFstatuslistView,
          },

          {
            path: 'fstatuslist/edit/:id',
            name: 'sysadmin-fstatuslist-edit',
            component: SysadminFstatuslistEdit,
          },

          {
            path: 'fstatuslist/create',
            name: 'sysadmin-fstatuslist-create',
            component: SysadminFstatuslistCreate,
          },

          // ===================================== status - trạng thái nút chức năng ===========================================
          {
              path: 'sys-status',
              name: 'sysadmin-sys-status',
              component: SysadminSysStatustList,
          },
          {
              path: 'sys-status/view/:id',
              name: 'sysadmin-sys-status-view',
              component: SysadminSysStatusView,
          },

          {
              path: 'sys-status/edit/:id',
              name: 'sysadmin-sys-status-edit',
              component: SysadminSysStatusEdit,
          },

          {
              path: 'sys-status/create',
              name: 'sysadmin-sys-status-create',
              component: SysadminSysStatusCreate,
          },

        // ===================================== Binary Data - Kiểu dữ liệu nhị phân ===========================================
          {
            path: 'binarydata',
            name: 'sysadmin-binarydata',
            component: SysadminBinaryDataList,
          },
          {
            path: 'binarydata/view/:id',
            name: 'sysadmin-binarydata-view',
            component: SysadminBinaryDataView,
          },

          {
            path: 'binarydata/edit/:id',
            name: 'sysadmin-binarydata-edit',
            component: SysadminBinaryDataEdit,
          },

          {
            path: 'binarydata/create',
            name: 'sysadmin-binarydata-create',
            component: SysadminBinaryDataCreate,
          },
        //=================================== Report =====================================================
          {
            path: 'report',
            name: 'sysadmin-report',
            component: SysAdminReportList,
          },
        ]
    },
];

export default router;
