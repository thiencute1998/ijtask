// Task
// ========================== task ============================================
const TaskLists = () => import('@/views/ijtask/task/Lists');
const TaskView = () => import('@/views/ijtask/task/View');
// const TaskEdit = () => import('@/views/ijtask/task/Edit');
const TaskCreate = () => import('@/views/ijtask/task/Create');

// ========================== dataflow ============================================
const Dataflow = () => import('@/views/ijtask/dataflow/Index');
const DataflowList = () => import('@/views/ijtask/dataflow/Lists');

// ========================== Indicator - Chỉ tiêu ============================================
const TaskIndicatorList = () => import('@/views/ijtask/indicator/Lists');
const TaskIndicatorEdit = () => import('@/views/ijtask/indicator/Edit');
const TaskIndicatorCreate = () => import('@/views/ijtask/indicator/Create');
const TaskIndicatorView = () => import('@/views/ijtask/indicator/View');

// ========================== Indicatorcatelist - Loại khách hàng ============================================
const TaskIndicatorCatelistList = () => import('@/views/ijtask/indicatorcatelist/Lists');
const TaskIndicatorCatelistEdit = () => import('@/views/ijtask/indicatorcatelist/Edit');
const TaskIndicatorCatelistCreate = () => import('@/views/ijtask/indicatorcatelist/Create');
const TaskIndicatorCatelistView = () => import('@/views/ijtask/indicatorcatelist/View');

// ========================== Indicatortemp - Mẫu bảng chỉ tiêu ĐGCV ============================================
const TaskIndicatorTempList = () => import('@/views/ijtask/indicatortemp/Lists');
const TaskIndicatorTempEdit = () => import('@/views/ijtask/indicatortemp/Edit');
const TaskIndicatorTempCreate = () => import('@/views/ijtask/indicatortemp/Create');
const TaskIndicatorTempView = () => import('@/views/ijtask/indicatortemp/View');

// ========================== Indicatortempitemkeyresult - Mẫu bảng chỉ tiêu ĐGCV(View) ============================================
const TaskIndicatorTempItemList = () => import('@/views/ijtask/indicatortempitem/Lists');
const TaskIndicatorTempItemEdit = () => import('@/views/ijtask/indicatortempitem/Edit');
const TaskIndicatorTempItemCreate = () => import('@/views/ijtask/indicatortempitem/Create');
const TaskIndicatorTempItemView = () => import('@/views/ijtask/indicatortempitem/View');

// ========================== Indicatortempitemkeyresult - Bảng mẫu chỉ tiêu ĐGCV – Kết quả then chốt (Sub view) ============================================
const TaskIndicatorTempItemKeyresultList = () => import('@/views/ijtask/indicatortempitemkeyresult/Lists');
const TaskIndicatorTempItemKeyresultEdit = () => import('@/views/ijtask/indicatortempitemkeyresult/Edit');
const TaskIndicatorTempItemKeyresultCreate = () => import('@/views/ijtask/indicatortempitemkeyresult/Create');
const TaskIndicatorTempItemKeyresultView = () => import('@/views/ijtask/indicatortempitemkeyresult/View');

// ========================== Indicatortable - Bảng chỉ tiêu ĐGCV ============================================
const TaskIndicatorTableList = () => import('@/views/ijtask/indicatortable/Lists');
const TaskIndicatorTableEdit = () => import('@/views/ijtask/indicatortable/Edit');
const TaskIndicatorTableCreate = () => import('@/views/ijtask/indicatortable/Create');
const TaskIndicatorTableView = () => import('@/views/ijtask/indicatortable/View');

// ===================================== evaluation1job - Đánh giá 1 công việc ================================
const TaskEvaluation1jobList = () => import('@/views/ijtask/evaluation1job/Lists');
const TaskEvaluation1jobEdit = () => import('@/views/ijtask/evaluation1job/Edit');
const TaskEvaluation1jobCreate = () => import('@/views/ijtask/evaluation1job/Create');
const TaskEvaluation1jobView = () => import('@/views/ijtask/evaluation1job/View');

// ========================== EvaluationTrans - Phiếu ĐGCV ============================================
const TaskEvaluationTransList = () => import('@/views/ijtask/evaluationtrans/Lists');
const TaskEvaluationTransEdit = () => import('@/views/ijtask/evaluationtrans/Edit');
const TaskEvaluationTransCreate = () => import('@/views/ijtask/evaluationtrans/Create');
const TaskEvaluationTransView = () => import('@/views/ijtask/evaluationtrans/View');

// ========================== ScaleRate - Bảng điểm ============================================
const TaskScaleRateList = () => import('@/views/ijtask/scalerate/Lists');
const TaskScaleRateEdit = () => import('@/views/ijtask/scalerate/Edit');
const TaskScaleRateCreate = () => import('@/views/ijtask/scalerate/Create');
const TaskScaleRateView = () => import('@/views/ijtask/scalerate/View');

// ========================== workflow ============================================
const SysadminWorkflowLists = () => import('@/views/ijsysadmin/workflow/Lists');
const SysadminWorkflowView = () => import('@/views/ijsysadmin/workflow/View');
const SysadminWorkflowrEdit = () => import('@/views/ijsysadmin/workflow/Edit');
const SysadminWorkflowCreate = () => import('@/views/ijsysadmin/workflow/Create');
const SysadminWorkflowIndex = () => import('@/views/ijsysadmin/workflow/Index');

let router = [
  {
    path: 'task',
    redirect: '/task/task',
    name: 'task',
    component: {
      render(c) {
        return c('router-view')
      }
    },
    children: [
    // task
      {
          path: 'task',
          name: 'task-task-list',
          component: TaskLists,
      },
      {
          path: 'task/create',
          name: 'task-task-create',
          component: TaskCreate
      },
      {
          path: 'task/view/:id',
          name: 'task-task-view',
          component: TaskView,
      },
      //
      // {
      //     path: 'task/edit/:id',
      //     name: 'task-edit',
      //     component: TaskEdit
      // },

      // workflow
      {
        path: 'workflow',
        name: 'sysadmin-workflow',
        component: SysadminWorkflowLists
      },

      {
        path: 'workflow/view/:id',
        name: 'sysadmin-workflow-view',
        component: SysadminWorkflowView,
      },

      {
        path: 'workflow/create',
        name: 'sysadmin-workflow-create',
        component: SysadminWorkflowCreate
      },

      {
        path: 'workflow/edit/:id',
        name: 'sysadmin-workflow-edit',
        component: SysadminWorkflowrEdit
      },

      // data flow
      {
        path: 'dataflow-list',
        name: 'task-dataflow-list',
        component: DataflowList
      },
      {
          path: 'dataflow',
          name: 'task-dataflow',
          component: Dataflow
      },
      {
        path: 'dataflow/:idWorkflow',
        name: 'task-dataflow',
        component: Dataflow
      },

      {
        path: 'dataflow/:idWorkflow/:idDataflow',
        name: 'task-dataflow',
        component: Dataflow
      },

      // ===================================== indicator - Chỉ tiêu ===========================================
      {
        path: 'indicator',
        name: 'task-indicator',
        component: TaskIndicatorList,
      },
      {
        path: 'indicator/view/:id',
        name: 'task-indicator-view',
        component: TaskIndicatorView,
      },
      {
        path: 'indicator/edit/:id',
        name: 'task-indicator-edit',
        component: TaskIndicatorEdit,
      },
      {
        path: 'indicator/create',
        name: 'task-indicator-create',
        component: TaskIndicatorCreate,
      },
      // ===================================== indicatorcatlist - Danh mục loại chỉ tiêu ĐGCV  ===========================================
      {
        path: 'indicatorcatelist',
        name: 'task-indicator-cate-list',
        component: TaskIndicatorCatelistList,
      },
      {
        path: 'indicatorcatelist/view/:id',
        name: 'task-indicator-cate-list-view',
        component: TaskIndicatorCatelistView,
      },
      {
        path: 'indicatorcatelist/edit/:id',
        name: 'task-indicator-cate-list-edit',
        component: TaskIndicatorCatelistEdit,
      },
      {
        path: 'indicatorcatelist/create',
        name: 'task-indicator-cate-list-create',
        component: TaskIndicatorCatelistCreate,
      },
      // ===================================== indicatortemp - Danh mục loại chỉ tiêu ĐGCV  ===========================================
      {
        path: 'indicatortemp',
        name: 'task-indicator-temp',
        component: TaskIndicatorTempList,
      },
      {
        path: 'indicatortemp/view/:id',
        name: 'task-indicator-temp-view',
        component: TaskIndicatorTempView,
      },
      {
        path: 'indicatortemp/edit/:id',
        name: 'task-indicator-temp-edit',
        component: TaskIndicatorTempEdit,
      },
      {
        path: 'indicatortemp/create',
        name: 'task-indicator-temp-create',
        component: TaskIndicatorTempCreate,
      },
      // ===================================== indicatortempitem - Mẫu bảng chỉ tiêu ĐGCV (view)  ===========================================
      {
        path: 'indicatortempitem',
        //path: 'indicatortempitem/:id',
        name: 'task-indicator-tempitem',
        component: TaskIndicatorTempItemList,
      },
      {
        path: 'indicatortempitem/view/:id',
        name: 'task-indicator-tempitem-view',
        component: TaskIndicatorTempItemView,
      },
      {
        path: 'indicatortempitem/edit/:id',
        name: 'task-indicator-tempitem-edit',
        component: TaskIndicatorTempItemEdit,
      },
      {
        path: 'indicatortempitem/create',
        name: 'task-indicator-tempitem-create',
        component: TaskIndicatorTempItemCreate,
      },
      // ===================================== indicatortempitemkeyresult - Bảng mẫu chỉ tiêu ĐGCV – Kết quả then chốt (Sub view)  ===========================================
      {
        path: 'indicatortempitemkeyresult',
        //path: 'indicatortempitemkeyresult/:id',
        name: 'task-indicator-tempitemkeyresult',
        component: TaskIndicatorTempItemKeyresultList,
      },
      {
        path: 'indicatortempitemkeyresult/view/:id',
        name: 'task-indicator-tempitemkeyresult-view',
        component: TaskIndicatorTempItemKeyresultView,
      },
      {
        path: 'indicatortempitemkeyresult/edit/:id',
        name: 'task-indicator-tempitemkeyresult-edit',
        component: TaskIndicatorTempItemKeyresultEdit,
      },
      {
        path: 'indicatortempitemkeyresult/create',
        name: 'task-indicator-tempitemkeyresult-create',
        component: TaskIndicatorTempItemKeyresultCreate,
      },

      // ===================================== indicatortable - Bảng chỉ tiêu ĐGCV  ===========================================
      {
        path: 'indicatortable',
        name: 'task-indicator-table',
        component: TaskIndicatorTableList,
      },
      {
        path: 'indicatortable/view/:id',
        name: 'task-indicator-table-view',
        component: TaskIndicatorTableView,
      },
      {
        path: 'indicatortable/edit/:id',
        name: 'task-indicator-table-edit',
        component: TaskIndicatorTableEdit,
      },
      {
        path: 'indicatortable/create',
        name: 'task-indicator-table-create',
        component: TaskIndicatorTableCreate,
      },

      // ===================================== evaluation1job - Đánh giá 1 công việc ===========================================
      {
        path: 'evaluation1job',
        name: 'task-evaluation-1job',
        component: TaskEvaluation1jobList,
      },
      {
        path: 'evaluation1job/view/:id',
        name: 'task-evaluation-1job-view',
        component: TaskEvaluation1jobView,
      },
      {
        path: 'evaluation1job/edit/:id',
        name: 'task-evaluation-1job-edit',
        component: TaskEvaluation1jobEdit,
      },
      {
        path: 'evaluation1job/create',
        name: 'task-evaluation-1job-create',
        component: TaskEvaluation1jobCreate,
      },

      // ===================================== evaluation trans: Phiếu ĐGCV  ===========================================
      {
        path: 'evaluationtrans',
        name: 'task-evaluation-trans',
        component: TaskEvaluationTransList,
      },
      {
        path: 'evaluationtrans/view/:id',
        name: 'task-evaluation-trans-view',
        component: TaskEvaluationTransView,
      },
      {
        path: 'evaluationtrans/edit/:id',
        name: 'task-evaluation-trans-edit',
        component: TaskEvaluationTransEdit,
      },
      {
        path: 'evaluationtrans/create',
        name: 'task-evaluation-trans-create',
        component: TaskEvaluationTransCreate,
      },
      // ===================================== ScaleRate - Bảng điểm  ===========================================
      {
        path: 'scalerate',
        name: 'task-scalerate',
        component: TaskScaleRateList,
      },
      {
        path: 'scalerate/view/:id',
        name: 'task-scalerate-view',
        component: TaskScaleRateView,
      },
      {
        path: 'scalerate/edit/:id',
        name: 'task-scalerate-edit',
        component: TaskScaleRateEdit,
      },
      {
        path: 'scalerate/create',
        name: 'task-scalerate-create',
        component: TaskScaleRateCreate,
      },

    ]
  },
];

export default router;
