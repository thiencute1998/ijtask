// Apps
// ========================== Accounting - Kế toán ============================================
const AccountingActgvouctransLists = () => import('@/views/accounting/actgvouctrans/Lists');
const AccountingActgvouctransEdit = () => import('@/views/accounting/actgvouctrans/Edit');
const AccountingActgvouctransCreate = () => import('@/views/accounting/actgvouctrans/Create');
const AccountingActgvouctransView = () => import('@/views/accounting/actgvouctrans/View');
const AccountingActgvouctransReport = () => import('@/views/accounting/actgvouctrans/Report');
const AccountingActgvouctransReportDetail = () => import('@/views/accounting/actgvouctrans/ReportDetail');
const AccountingActgvouctransReportAccounting = () => import('@/views/accounting/actgvouctrans/ReportAccounting');

// ========================== Accounting InTransType - Loại nghiệp vụ kế toán ============================================
const AccountingInTransTypeLists = () => import('@/views/accounting/actintranstype/Lists');
const AccountingInTransTypeEdit = () => import('@/views/accounting/actintranstype/Edit');
const AccountingInTransTypeCreate = () => import('@/views/accounting/actintranstype/Create');
const AccountingInTransTypeView = () => import('@/views/accounting/actintranstype/View');
const AccountingInTransTypeReport = () => import('@/views/accounting/actintranstype/Report');
const AccountingInTransTypeReportDetail = () => import('@/views/accounting/actintranstype/ReportDetail');
const AccountingInTransTypeReportAccounting = () => import('@/views/accounting/actintranstype/ReportAccounting');

// ========================== Accounting Autoact - Nghiệp vụ hạch toán tự động ============================================
const AccountingAutoactLists = () => import('@/views/accounting/actautoact/Lists');
const AccountingAutoactEdit = () => import('@/views/accounting/actautoact/Edit');
const AccountingAutoactCreate = () => import('@/views/accounting/actautoact/Create');
const AccountingAutoactView = () => import('@/views/accounting/actautoact/View');
const AccountingAutoactReport = () => import('@/views/accounting/actautoact/Report');
const AccountingAutoactReportDetail = () => import('@/views/accounting/actautoact/ReportDetail');
const AccountingAutoactReportAccounting = () => import('@/views/accounting/actautoact/ReportAccounting');

// ========================== Accounting CFAccount - Tài khoản kết chuyển ============================================
const AccountingCFAccountLists = () => import('@/views/accounting/actcfaccount/Lists');
const AccountingCFAccountEdit = () => import('@/views/accounting/actcfaccount/Edit');
const AccountingCFAccountCreate = () => import('@/views/accounting/actcfaccount/Create');
const AccountingCFAccountView = () => import('@/views/accounting/actcfaccount/View');
const AccountingCFAccountReport = () => import('@/views/accounting/actcfaccount/Report');
const AccountingCFAccountReportDetail = () => import('@/views/accounting/actcfaccount/ReportDetail');
const AccountingCFAccountReportAccounting = () => import('@/views/accounting/actcfaccount/ReportAccounting');

// ========================== Accounting CCAccount - Tài khoản đồng thời ============================================
const AccountingCCAccountLists = () => import('@/views/accounting/actccaccount/Lists');
const AccountingCCAccountEdit = () => import('@/views/accounting/actccaccount/Edit');
const AccountingCCAccountCreate = () => import('@/views/accounting/actccaccount/Create');
const AccountingCCAccountView = () => import('@/views/accounting/actccaccount/View');
const AccountingCCAccountReport = () => import('@/views/accounting/actccaccount/Report');
const AccountingCCAccountReportDetail = () => import('@/views/accounting/actccaccount/ReportDetail');
const AccountingCCAccountReportAccounting = () => import('@/views/accounting/actccaccount/ReportAccounting');

let router = [
    {
        path: 'accounting',
        name: 'Accounting',
        component: {
            render(c) {
                return c('router-view')
            }
        },
        children: [
         // ===================================== Accounting -  ===========================================
          {
            path: 'actgvouctrans',
            name: 'accounting-actgvouctrans',
            component: AccountingActgvouctransLists,
          },
          {
            path: 'actgvouctrans/view/:id',
            name: 'accounting-actgvouctrans-view',
            component: AccountingActgvouctransView,
          },
          {
            path: 'actgvouctrans/edit/:id',
            name: 'accounting-actgvouctrans-edit',
            component: AccountingActgvouctransEdit,
          },
          {
            path: 'actgvouctrans/create',
            name: 'accounting-actgvouctrans-create',
            component: AccountingActgvouctransCreate,
          },
          {
            path: 'actgvouctrans/report',
            name: 'accounting-actgvouctrans-report',
            component: AccountingActgvouctransReport,
          },
          {
            path: 'actgvouctrans/report-detail',
            name: 'accounting-actgvouctrans-report-detail',
            component: AccountingActgvouctransReportDetail,
          },
          {
            path: 'actgvouctrans/report-accounting',
            name: 'accounting-actgvouctrans-report-accounting',
            component: AccountingActgvouctransReportAccounting,
          },

          // ===================================== Accounting InTransType - Loại nghiệp vụ kế toán ===========================================
          {
            path: 'actintranstype',
            name: 'accounting-actintranstype',
            component: AccountingInTransTypeLists,
          },
          {
            path: 'actintranstype/view/:id',
            name: 'accounting-actintranstype-view',
            component: AccountingInTransTypeView,
          },
          {
            path: 'actintranstype/edit/:id',
            name: 'accounting-actintranstype-edit',
            component: AccountingInTransTypeEdit,
          },
          {
            path: 'actintranstype/create',
            name: 'accounting-actintranstype-create',
            component: AccountingInTransTypeCreate,
          },
          {
            path: 'actintranstype/report',
            name: 'accounting-actintranstype-report',
            component: AccountingInTransTypeReport,
          },
          {
            path: 'actintranstype/report-detail',
            name: 'accounting-actintranstype-report-detail',
            component: AccountingInTransTypeReportDetail,
          },
          {
            path: 'actintranstype/report-accounting',
            name: 'accounting-actintranstype-report-accounting',
            component: AccountingInTransTypeReportAccounting,
          },

          // ===================================== Accounting Autoact - Nghiệp vụ hạch toán tự động ===========================================
          {
            path: 'actautoact',
            name: 'accounting-actautoact',
            component: AccountingAutoactLists,
          },
          {
            path: 'actautoact/view/:id',
            name: 'accounting-actautoact-view',
            component: AccountingAutoactView,
          },
          {
            path: 'actautoact/edit/:id',
            name: 'accounting-actautoact-edit',
            component: AccountingAutoactEdit,
          },
          {
            path: 'actautoact/create',
            name: 'accounting-actautoact-create',
            component: AccountingAutoactCreate,
          },
          {
            path: 'actautoact/report',
            name: 'accounting-actautoact-report',
            component: AccountingAutoactReport,
          },
          {
            path: 'actautoact/report-detail',
            name: 'accounting-actautoact-report-detail',
            component: AccountingAutoactReportDetail,
          },
          {
            path: 'actautoact/report-accounting',
            name: 'accounting-actautoact-report-accounting',
            component: AccountingAutoactReportAccounting,
          },

          // ===================================== Accounting CFAccount - Tài khoản kết chuyển ===========================================
          {
            path: 'actcfaccount',
            name: 'accounting-actcfaccount',
            component: AccountingCFAccountLists,
          },
          {
            path: 'actcfaccount/view/:id',
            name: 'accounting-actcfaccount-view',
            component: AccountingCFAccountView,
          },
          {
            path: 'actcfaccount/edit/:id',
            name: 'accounting-actcfaccount-edit',
            component: AccountingCFAccountEdit,
          },
          {
            path: 'actcfaccount/create',
            name: 'accounting-actcfaccount-create',
            component: AccountingCFAccountCreate,
          },
          {
            path: 'actcfaccount/report',
            name: 'accounting-actcfaccount-report',
            component: AccountingCFAccountReport,
          },
          {
            path: 'actcfaccount/report-detail',
            name: 'accounting-actcfaccount-report-detail',
            component: AccountingCFAccountReportDetail,
          },
          {
            path: 'actcfaccount/report-accounting',
            name: 'accounting-actcfaccount-report-accounting',
            component: AccountingCFAccountReportAccounting,
          },

          // ===================================== Accounting CCAccount - Tài khoản đồng thời ===========================================
          {
            path: 'actccaccount',
            name: 'accounting-actccaccount',
            component: AccountingCCAccountLists,
          },
          {
            path: 'actccaccount/view/:id',
            name: 'accounting-actccaccount-view',
            component: AccountingCCAccountView,
          },
          {
            path: 'actccaccount/edit/:id',
            name: 'accounting-actccaccount-edit',
            component: AccountingCCAccountEdit,
          },
          {
            path: 'actccaccount/create',
            name: 'accounting-actccaccount-create',
            component: AccountingCCAccountCreate,
          },
          {
            path: 'actccaccount/report',
            name: 'accounting-actccaccount-report',
            component: AccountingCCAccountReport,
          },
          {
            path: 'actccaccount/report-detail',
            name: 'accounting-actccaccount-report-detail',
            component: AccountingCCAccountReportDetail,
          },
          {
            path: 'actccaccount/report-accounting',
            name: 'accounting-actccaccount-report-accounting',
            component: AccountingCCAccountReportAccounting,
          },
        ]
    },
];

export default router;
