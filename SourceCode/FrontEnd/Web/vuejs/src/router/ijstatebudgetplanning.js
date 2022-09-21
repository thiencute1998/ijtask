// Apps
// ========================== StateBudgetPlanning - DỰ TOÁN NSNN ============================================
//make
const SbpmakeplanLists = () => import('@/views/statebudgetplanning/sbpmakeplan/Lists');
const SbpmakeplanEdit = () => import('@/views/statebudgetplanning/sbpmakeplan/Edit');
const SbpmakeplanCreate = () => import('@/views/statebudgetplanning/sbpmakeplan/Create');
const SbpmakeplanView = () => import('@/views/statebudgetplanning/sbpmakeplan/View');
const SbpmakeplanReport = () => import('@/views/statebudgetplanning/sbpmakeplan/Report');
const SbpmakeplanReportDetail = () => import('@/views/statebudgetplanning/sbpmakeplan/ReportDetail');
const SbpmakeplanReportSbpmakeplan = () => import('@/views/statebudgetplanning/sbpmakeplan/ReportSbpmakeplan');
//review
const SbpreviewplanLists = () => import('@/views/statebudgetplanning/sbpreviewplan/Lists');
const SbpreviewplanEdit = () => import('@/views/statebudgetplanning/sbpreviewplan/Edit');
const SbpreviewplanCreate = () => import('@/views/statebudgetplanning/sbpreviewplan/Create');
const SbpreviewplanView = () => import('@/views/statebudgetplanning/sbpreviewplan/View');
const SbpreviewplanReport = () => import('@/views/statebudgetplanning/sbpreviewplan/Report');
const SbpreviewplanReportDetail = () => import('@/views/statebudgetplanning/sbpreviewplan/ReportDetail');
const SbpreviewplanReportSbpreviewplan = () => import('@/views/statebudgetplanning/sbpreviewplan/ReportSbpreviewplan');
//approval
const SbpapprovalplanLists = () => import('@/views/statebudgetplanning/sbpapprovalplan/Lists');
const SbpapprovalplanEdit = () => import('@/views/statebudgetplanning/sbpapprovalplan/Edit');
const SbpapprovalplanCreate = () => import('@/views/statebudgetplanning/sbpapprovalplan/Create');
const SbpapprovalplanView = () => import('@/views/statebudgetplanning/sbpapprovalplan/View');
const SbpapprovalplanReport = () => import('@/views/statebudgetplanning/sbpapprovalplan/Report');
const SbpapprovalplanReportDetail = () => import('@/views/statebudgetplanning/sbpapprovalplan/ReportDetail');
const SbpapprovalplanReportSbpapprovalplan = () => import('@/views/statebudgetplanning/sbpapprovalplan/ReportSbpapprovalplan');
//assign
const SbpassignplanLists = () => import('@/views/statebudgetplanning/sbpassignplan/Lists');
const SbpassignplanEdit = () => import('@/views/statebudgetplanning/sbpassignplan/Edit');
const SbpassignplanCreate = () => import('@/views/statebudgetplanning/sbpassignplan/Create');
const SbpassignplanView = () => import('@/views/statebudgetplanning/sbpassignplan/View');
const SbpassignplanReport = () => import('@/views/statebudgetplanning/sbpassignplan/Report');
const SbpassignplanReportDetail = () => import('@/views/statebudgetplanning/sbpassignplan/ReportDetail');
const SbpassignplanReportSbpassignplan = () => import('@/views/statebudgetplanning/sbpassignplan/ReportSbpassignplan');
//give
const SbpgiveplanLists = () => import('@/views/statebudgetplanning/sbpgiveplan/Lists');
const SbpgiveplanEdit = () => import('@/views/statebudgetplanning/sbpgiveplan/Edit');
const SbpgiveplanCreate = () => import('@/views/statebudgetplanning/sbpgiveplan/Create');
const SbpgiveplanView = () => import('@/views/statebudgetplanning/sbpgiveplan/View');
const SbpgiveplanReport = () => import('@/views/statebudgetplanning/sbpgiveplan/Report');
const SbpgiveplanReportDetail = () => import('@/views/statebudgetplanning/sbpgiveplan/ReportDetail');
const SbpgiveplanReportSbpgiveplan = () => import('@/views/statebudgetplanning/sbpgiveplan/ReportSbpgiveplan');
//estimate
const SbpestimateplanLists = () => import('@/views/statebudgetplanning/sbpestimateplan/Lists');
const SbpestimateplanEdit = () => import('@/views/statebudgetplanning/sbpestimateplan/Edit');
const SbpestimateplanCreate = () => import('@/views/statebudgetplanning/sbpestimateplan/Create');
const SbpestimateplanView = () => import('@/views/statebudgetplanning/sbpestimateplan/View');
const SbpestimateplanReport = () => import('@/views/statebudgetplanning/sbpestimateplan/Report');
const SbpestimateplanReportDetail = () => import('@/views/statebudgetplanning/sbpestimateplan/ReportDetail');
const SbpestimateplanReportSbpestimateplan = () => import('@/views/statebudgetplanning/sbpestimateplan/ReportSbpestimateplan');
//regu
const SbpreguplanLists = () => import('@/views/statebudgetplanning/sbpreguplan/Index');

let router = [
    {
        path: 'statebudgetplanning',
        name: 'StateBudgetPlanning',
        component: {
            render(c) {
                return c('router-view')
            }
        },
        children: [
         // ===================================== StateBudgetPlanning -  ===========================================
          //make
          {
            path: 'sbpmakeplan',
            name: 'statebudgetplanning-sbpmakeplan',
            component: SbpmakeplanLists,
          },
          {
            path: 'sbpmakeplan/view/:id',
            name: 'statebudgetplanning-sbpmakeplan-view',
            component: SbpmakeplanView,
          },
          {
            path: 'sbpmakeplan/edit/:id',
            name: 'statebudgetplanning-sbpmakeplan-edit',
            component: SbpmakeplanEdit,
          },
          {
            path: 'sbpmakeplan/create',
            name: 'statebudgetplanning-sbpmakeplan-create',
            component: SbpmakeplanCreate,
          },
          {
            path: 'sbpmakeplan/report',
            name: 'statebudgetplanning-sbpmakeplan-report',
            component: SbpmakeplanReport,
          },
          {
            path: 'sbpmakeplan/report-detail',
            name: 'statebudgetplanning-sbpmakeplan-report-detail',
            component: SbpmakeplanReportDetail,
          },
          {
            path: 'sbpmakeplan/report-sbpmakeplan',
            name: 'statebudgetplanning-sbpmakeplan-report-sbpmakeplan',
            component: SbpmakeplanReportSbpmakeplan,
          },
          //review
          {
            path: 'sbpreviewplan',
            name: 'statebudgetplanning-sbpreviewplan',
            component: SbpreviewplanLists,
          },
          {
            path: 'sbpreviewplan/view/:id',
            name: 'statebudgetplanning-sbpreviewplan-view',
            component: SbpreviewplanView,
          },
          {
            path: 'sbpreviewplan/edit/:id',
            name: 'statebudgetplanning-sbpreviewplan-edit',
            component: SbpreviewplanEdit,
          },
          {
            path: 'sbpreviewplan/create',
            name: 'statebudgetplanning-sbpreviewplan-create',
            component: SbpreviewplanCreate,
          },
          {
            path: 'sbpreviewplan/report',
            name: 'statebudgetplanning-sbpreviewplan-report',
            component: SbpreviewplanReport,
          },
          {
            path: 'sbpreviewplan/report-detail',
            name: 'statebudgetplanning-sbpreviewplan-report-detail',
            component: SbpreviewplanReportDetail,
          },
          {
            path: 'sbpreviewplan/report-sbpmakeplan',
            name: 'statebudgetplanning-sbpreviewplan-report-sbpreviewplan',
            component: SbpreviewplanReportSbpreviewplan,
          },
          //approval
          {
            path: 'sbpapprovalplan',
            name: 'statebudgetplanning-sbpapprovalplan',
            component: SbpapprovalplanLists,
          },
          {
            path: 'sbpapprovalplan/view/:id',
            name: 'statebudgetplanning-sbpapprovalplan-view',
            component: SbpapprovalplanView,
          },
          {
            path: 'sbpapprovalplan/edit/:id',
            name: 'statebudgetplanning-sbpapprovalplan-edit',
            component: SbpapprovalplanEdit,
          },
          {
            path: 'sbpapprovalplan/create',
            name: 'statebudgetplanning-sbpapprovalplan-create',
            component: SbpapprovalplanCreate,
          },
          {
            path: 'sbpapprovalplan/report',
            name: 'statebudgetplanning-sbpapprovalplan-report',
            component: SbpapprovalplanReport,
          },
          {
            path: 'sbpapprovalplan/report-detail',
            name: 'statebudgetplanning-sbpapprovalplan-report-detail',
            component: SbpapprovalplanReportDetail,
          },
          {
            path: 'sbpapprovalplan/report-sbpapprovalplan',
            name: 'statebudgetplanning-sbpapprovalplan-report-sbpapprovalplan',
            component: SbpapprovalplanReportSbpapprovalplan,
          },
          //assign
          {
            path: 'sbpassignplan',
            name: 'statebudgetplanning-sbpassignplan',
            component: SbpassignplanLists,
          },
          {
            path: 'sbpassignplan/view/:id',
            name: 'statebudgetplanning-sbpassignplan-view',
            component: SbpassignplanView,
          },
          {
            path: 'sbpassignplan/edit/:id',
            name: 'statebudgetplanning-sbpassignplan-edit',
            component: SbpassignplanEdit,
          },
          {
            path: 'sbpassignplan/create',
            name: 'statebudgetplanning-sbpassignplan-create',
            component: SbpassignplanCreate,
          },
          {
            path: 'sbpassignplan/report',
            name: 'statebudgetplanning-sbpassignplan-report',
            component: SbpassignplanReport,
          },
          {
            path: 'sbpassignplan/report-detail',
            name: 'statebudgetplanning-sbpassignplan-report-detail',
            component: SbpassignplanReportDetail,
          },
          {
            path: 'sbpassignplan/report-sbpassignplan',
            name: 'statebudgetplanning-sbpassignplan-report-sbpassignplan',
            component: SbpassignplanReportSbpassignplan,
          },
          //give
          {
            path: 'sbpgiveplan',
            name: 'statebudgetplanning-sbpgiveplan',
            component: SbpgiveplanLists,
          },
          {
            path: 'sbpgiveplan/view/:id',
            name: 'statebudgetplanning-sbpgiveplan-view',
            component: SbpgiveplanView,
          },
          {
            path: 'sbpgiveplan/edit/:id',
            name: 'statebudgetplanning-sbpgiveplan-edit',
            component: SbpgiveplanEdit,
          },
          {
            path: 'sbpgiveplan/create',
            name: 'statebudgetplanning-sbpgiveplan-create',
            component: SbpgiveplanCreate,
          },
          {
            path: 'sbpgiveplan/report',
            name: 'statebudgetplanning-sbpgiveplan-report',
            component: SbpgiveplanReport,
          },
          {
            path: 'sbpgiveplan/report-detail',
            name: 'statebudgetplanning-sbpgiveplan-report-detail',
            component: SbpgiveplanReportDetail,
          },
          {
            path: 'sbpgiveplan/report-sbpgiveplan',
            name: 'statebudgetplanning-sbpgiveplan-report-sbpgiveplan',
            component: SbpgiveplanReportSbpgiveplan,
          },
          //estimate
          {
            path: 'sbpestimateplan',
            name: 'statebudgetplanning-sbpestimateplan',
            component: SbpestimateplanLists,
          },
          {
            path: 'sbpestimateplan/view/:id',
            name: 'statebudgetplanning-sbpestimateplan-view',
            component: SbpestimateplanView,
          },
          {
            path: 'sbpestimateplan/edit/:id',
            name: 'statebudgetplanning-sbpestimateplan-edit',
            component: SbpestimateplanEdit,
          },
          {
            path: 'sbpestimateplan/create',
            name: 'statebudgetplanning-sbpestimateplan-create',
            component: SbpestimateplanCreate,
          },
          {
            path: 'sbpestimateplan/report',
            name: 'statebudgetplanning-sbpestimateplan-report',
            component: SbpestimateplanReport,
          },
          {
            path: 'sbpestimateplan/report-detail',
            name: 'statebudgetplanning-sbpestimateplan-report-detail',
            component: SbpestimateplanReportDetail,
          },
          {
            path: 'sbpestimateplan/report-sbpestimateplan',
            name: 'statebudgetplanning-sbpestimateplan-report-sbpestimateplan',
            component: SbpestimateplanReportSbpestimateplan,
          },
          //regu
          {
            path: 'sbpreguplan',
            name: 'statebudgetplanning-sbpreguplan',
            component: SbpreguplanLists,
          },

        ]
    },
];

export default router;
