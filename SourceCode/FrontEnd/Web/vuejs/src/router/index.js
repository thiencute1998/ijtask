import Vue from 'vue';
import Router from 'vue-router';
import {TokenService} from '../services/storage.service';
import {PermissionService} from "../services/permission.service";
import qs from 'qs';

// Containers
const DefaultContainer = () => import('@/containers/DefaultContainer');

// Views - Dashboard
const Dashboard = () => import('@/views/Dashboard');

// Pages
const Page404 = () => import('@/views/pages/Page404');
const Page500 = () => import('@/views/pages/Page500');
const Login = () => import('@/views/pages/Login');
const Register = () => import('@/views/pages/Register');
const Test = () => import('@/views/pages/Test');
const ReportDesigner = () => import('@/views/pages/ReportDesigner');

Vue.use(Router);

const loggedIn = !!TokenService.getToken();
if (loggedIn) {
  let permission = PermissionService.getPermission();
}


const ifAuthenticated = (to, from, next) => {
  const loggedIn = !!TokenService.getToken();
  if (loggedIn) {
    // continue to route
    next();
    return
  }
  next('/pages/login');
};

const hadAuthenticated = (to, from, next) => {
  const loggeIn = !!TokenService.getToken();
  if (loggeIn) {
    next('/');
    return;
  }
  next();
};

// TODO: check permission router
import SysadminRouter from './ijsysadmin';
import ListingRouter from './ijlisting';
import TaskRouter from './ijtask';
import AppsRouter from './ijapps';
import CustomerRouter from './ijcustomer';
import ContractRouter from './ijcontract';
import DocRouter from './ijdoc';
import Report from './ijreport';
import Accounting from './ijaccounting';
import Calendar from './ijcalendar';
import StateBudgetExpenditures from './ijstatebudgetexpenditures';
import StateBudgetPlanning from './ijstatebudgetplanning';
import StateBudgetRevenues from './ijstatebudgetrevenues';
import StateBudgetSettlement from './ijstatebudgetsettlement';


let mainRouter = [
  {
    path: 'bang-tin',
    name: 'Dashboard',
    component: Dashboard
  },
];

mainRouter = mainRouter.concat(SysadminRouter);
mainRouter = mainRouter.concat(ListingRouter);
mainRouter = mainRouter.concat(TaskRouter);
mainRouter = mainRouter.concat(AppsRouter);
mainRouter = mainRouter.concat(CustomerRouter);
mainRouter = mainRouter.concat(DocRouter);
mainRouter = mainRouter.concat(Report);
mainRouter = mainRouter.concat(Accounting);
mainRouter = mainRouter.concat(Calendar);
mainRouter = mainRouter.concat(StateBudgetExpenditures);
mainRouter = mainRouter.concat(StateBudgetSettlement);
mainRouter = mainRouter.concat(StateBudgetPlanning);
mainRouter = mainRouter.concat(StateBudgetRevenues);

export default new Router({
  mode: 'hash', // https://router.vuejs.org/api/#mode
  // mode: 'history', // https://router.vuejs.org/api/#mode
  linkActiveClass: 'open active',
  scrollBehavior: () => ({y: 0}),
  routes: [
    {
      path: '/',
      redirect: '/bang-tin',
      name: 'Home',
      component: DefaultContainer,
      // children: ModuleRouter,
      children: mainRouter,
      beforeEnter: ifAuthenticated,
    },
    {
      path: '/pages',
      redirect: '/pages/404',
      name: 'Pages',
      component: {
        render(c) {
          return c('router-view')
        }
      },
      children: [
        {
          path: '404',
          name: 'Page404',
          component: Page404
        },
        {
          path: '500',
          name: 'Page500',
          component: Page500
        },
        {
          path: 'login',
          name: 'Login',
          component: Login,
          beforeEnter: hadAuthenticated
        },
        {
          path: 'register',
          name: 'Register',
          component: Register
        },

        {
          path: 'test',
          name: 'Test',
          component: Test
        },
        {
          path: 'report-designer',
          name: 'page-report-designer',
          component: ReportDesigner
        }
      ]
    }
  ],
  parseQuery(query) {
    return qs.parse(query);
  },
  stringifyQuery(query) {
    let result = qs.stringify(query);

    return result ? ('?' + result) : '';
  }
});
