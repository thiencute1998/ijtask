<template>
  <div class="animated fadeIn">
    <b-navbar-nav class="d-xs-down-none">
      <b-nav-text class="px-3 nav-module-dropdown" v-if="$store.state.moduleNavTop === 'DASHBOARD'">
        <AppHeaderDropdown left>
          <template slot="header">
            <span class="mr-1" v-if="$store.state.menuTop.dashboard.tabNo === 1">Điều hành</span>
            <span class="mr-1" v-if="$store.state.menuTop.dashboard.tabNo === 16">Dự toán NSNN</span>
            <span class="mr-1" v-if="$store.state.menuTop.dashboard.tabNo === 2">Chấp hành NSNN</span>
            <span class="mr-1" v-if="$store.state.menuTop.dashboard.tabNo === 17">Quyết toán NSNN</span>
            <span class="mr-1" v-if="$store.state.menuTop.dashboard.tabNo === 3">Ngân quỹ</span>
            <span class="mr-1" v-if="$store.state.menuTop.dashboard.tabNo === 4">Kế toán</span>
            <span class="mr-1" v-if="$store.state.menuTop.dashboard.tabNo === 5">Tiền lương</span>
            <span class="mr-1" v-if="$store.state.menuTop.dashboard.tabNo === 6">Tài sản công</span>
            <span class="mr-1" v-if="$store.state.menuTop.dashboard.tabNo === 7">Giá</span>
            <span class="mr-1" v-if="$store.state.menuTop.dashboard.tabNo === 8">Đầu tư công</span>
            <span class="mr-1" v-if="$store.state.menuTop.dashboard.tabNo === 9">Nợ công</span>
            <span class="mr-1" v-if="$store.state.menuTop.dashboard.tabNo === 10">Viện trợ</span>
            <span class="mr-1" v-if="$store.state.menuTop.dashboard.tabNo === 11">Tài chính doanh nghiệp</span>
            <span class="mr-1" v-if="$store.state.menuTop.dashboard.tabNo === 12">Thanh tra</span>
            <span class="mr-1" v-if="$store.state.menuTop.dashboard.tabNo === 13">Thuế</span>
            <span class="mr-1" v-if="$store.state.menuTop.dashboard.tabNo === 14">Hải quan</span>
            <span class="mr-1" v-if="$store.state.menuTop.dashboard.tabNo === 15">Dự trữ</span>
          </template>
          <template slot="dropdown">
            <b-dropdown-item @click="setDashboardTab(1)" style="cursor: pointer">Điều hành</b-dropdown-item>
            <b-dropdown-item @click="setDashboardTab(16)" style="cursor: pointer">Dự toán NSNN</b-dropdown-item>
            <b-dropdown-item @click="setDashboardTab(2)" style="cursor: pointer">Chấp hành NSNN</b-dropdown-item>
            <b-dropdown-item @click="setDashboardTab(17)" style="cursor: pointer">Quyết toán NSNN</b-dropdown-item>
            <b-dropdown-item @click="setDashboardTab(3)" style="cursor: pointer">Ngân quỹ</b-dropdown-item>
            <b-dropdown-item @click="setDashboardTab(4)" style="cursor: pointer">Kế toán</b-dropdown-item>
            <b-dropdown-item @click="setDashboardTab(5)" style="cursor: pointer">Tiền lương</b-dropdown-item>
            <b-dropdown-item @click="setDashboardTab(6)" style="cursor: pointer">Tài sản công</b-dropdown-item>
            <b-dropdown-item @click="setDashboardTab(7)" style="cursor: pointer">Giá</b-dropdown-item>
            <b-dropdown-item @click="setDashboardTab(8)" style="cursor: pointer">Đầu tư công</b-dropdown-item>
            <b-dropdown-item @click="setDashboardTab(9)" style="cursor: pointer">Nợ công</b-dropdown-item>
            <b-dropdown-item @click="setDashboardTab(10)" style="cursor: pointer">Viện trợ</b-dropdown-item>
            <b-dropdown-item @click="setDashboardTab(11)" style="cursor: pointer">Tài chính doanh nghiệp</b-dropdown-item>
            <b-dropdown-item @click="setDashboardTab(12)" style="cursor: pointer">Thanh tra</b-dropdown-item>
            <b-dropdown-item @click="setDashboardTab(13)" style="cursor: pointer">Thuế</b-dropdown-item>
            <b-dropdown-item @click="setDashboardTab(14)" style="cursor: pointer">Hải quan</b-dropdown-item>
            <b-dropdown-item @click="setDashboardTab(15)" style="cursor: pointer">Dự trữ</b-dropdown-item>
          </template>
        </AppHeaderDropdown>
      </b-nav-text>
      <b-nav-text class="px-3 nav-module-dropdown" v-if="$store.state.moduleNavTop === 'REPORT'">
        <AppHeaderDropdown left>
          <template slot="header">
            <span class="mr-1">Báo cáo</span>
          </template>
          <template slot="dropdown">
            <li class="dropdown b-dropdown dropright" v-for="(report, key) in reports">
              <a :class="(report.items && report.items.length) ? 'dropdown-toggle' : ''" class="dropdown-item d-lg-flex justify-content-between align-items-center border-0" :data-toggle="(report.items && report.items.length) ? 'dropdown' : ''" @click.stop="$_onToggleDropdownSubMenu($event)" href="#">{{report.name}}</a>
              <ul role="menu" tabindex="-1" class="dropdown-sub-menu dropdown-menu m-0" v-if="report.items && report.items.length">
                <li role="presentation" :class="[(item.items && item.items.length) ? 'dropdown b-dropdown dropright' : '']" v-for="(item, key) in report.items">
                  <a v-if="!item.items" role="menuitem" target="_self" href="#" @click="onClickReport($event, item)" class="dropdown-item">{{item.name}}</a>
                  <a v-else class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0" data-toggle="dropdown" @click.stop="$_onToggleDropdownSubMenu($event)" href="#">{{item.name}}</a>
                  <ul v-if="item.items && item.items.length" role="menu" tabindex="-1" class="dropdown-sub-menu dropdown-menu m-0">
                    <li role="presentation" v-for="(itemChild, key) in item.items">
                      <a v-if="!itemChild.items" role="menuitem" target="_self" href="#" @click="onClickReport($event, itemChild)" class="dropdown-item">{{itemChild.name}}</a>
                      <a v-else class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0" data-toggle="dropdown" @click.stop="$_onToggleDropdownSubMenu($event)" href="#">{{itemChild.name}}</a>
                      <ul v-if="itemChild.items && itemChild.items.length" role="menu" tabindex="-1" class="dropdown-sub-menu dropdown-sub-menu-report dropdown-menu m-0">
                        <li role="presentation" v-for="(itemChildL4, key) in itemChild.items">
                          <a role="menuitem" target="_self" href="#" @click="onClickReport($event, itemChildL4)" class="dropdown-item">{{itemChildL4.name}}</a>
                        </li>
                      </ul>
                    </li>
                  </ul>
                </li>
              </ul>
            </li>
          </template>
        </AppHeaderDropdown>
      </b-nav-text>
    </b-navbar-nav>
  </div>
</template>

<script>
import {HeaderDropdown as AppHeaderDropdown} from '@coreui/vue';
import {PermissionService} from "../services/permission.service";
export default {
  data (){
    return {
      moduleName: '',
      listing: [],
      operation: [],
      reports: [
        {
          name: 'Báo cáo điều hành',
          items: [
            {
              name: 'Quốc hội',
              items: [],
            },
            {
              name: ' Chính phủ',
              items: [],
            },
            {
              name: 'Bộ tài chính',
              items: [],
            },
            {
              name: 'Ủy ban nhân dân tỉnh/TP',
              items: [],
            },
            {
              name: 'Sở Tài chính',
              items: [],
            },
            {
              name: 'Hội đồng nhân dân huyện',
              items: [],
            },
            {
              name: ' Ủy ban nhân dân huyện',
              items: []
            },
            {
              name: 'Phòng Tài chính Kế hoạch',
              items: [],















            },
            {
              name: ' Hội đồng nhân dân xã',
              items: [],
            },
            {
              name: 'Ủy ban nhân dân xã',
              items: [],
            },
            {
              name: 'Đơn vị dự toán cấp II',
              items: [],
            },
            {
              name: 'Đơn vị dự toán cấp III',
              items: [],
            },

          ]
        },
        {
          name: 'Báo cáo Dự toán NSNN',
          items: [
            {
              name: 'Báo cáo điều hành dự toán NSNN',
              items:[
                {
                  name: ' Đánh giá tình hình thực hiện nhiệm vụ ngân sách nhà nước của Chi cục trồng trọt và bảo vệ thực vật',
                  router: '/report/ThuyetMinh_DuToan-2021_CC-TTbVTV'
                },
                {
                  name: 'Đánh giá tình hình thực hiện nhiệm vụ ngân sách nhà nước của Sở Nông nghiệp và Phát triển nông thôn ',
                  router: '/report/ThuyetMinh_DuToan2021_BanHanh30-9'
                }
              ]
            },

            {
              name: 'Báo cáo theo Văn bản QPPL',
              items: [
                {
                  name:'Nghị định 31/2020/NĐ-CP',
                  items:[
                    //phần đầu là của thanh
                    {
                      name: 'Dự báo một số chỉ tiêu kinh tế - Xã hội chủ yếu giai đoạn...',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-01'
                    },
                    {
                      name: 'Kế hoạch tài chính - Ngân sách giai đoạn 05',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-02'
                    },
                    {
                      name: 'Dự kiến phương án phân bổ kế hoạch đầu tư công trung hạn vốn NSNN giai đoạn 05',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-03'
                    },
                    {
                      name: 'Tổng hợp dự kiến kế hoạch đầu tư công trung hạn vốn NSNN của các cơ quan, đơn vị và địa phương giai đoạn 05',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-04'
                    },
                    {
                      name: ' Dự kiến thu ngân sách nhà nước theo lĩnh vực giai đoạn 03',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-08'
                    },
                    {
                      name: ' Dự kiến cân đối nguồn thu, chi ngân sách cấp tỉnh và ngân sách huyện giai đoạn 03',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-09'
                    },
                    {
                      name: 'Dự kiến chi ngân sách cấp tỉnh theo cơ cấu chi giai đoạn 03',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-10'
                    },
                    //đây là phần của dũng
                    {
                      name: 'Đánh giá thực hiện thu ngân sách nhà nước trên địa bàn từng huyện (xã) theo lĩnh vực ',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-21'
                    },
                    {
                      name: 'Đánh giá thực hiện chi ngân sách địa phương, chi ngân sách cấp Tỉnh(Huyện) và chia ngân sách Huyện(Xã) theo cơ cấu chi',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-22'
                    },
                    {
                      name: 'Đánh giả thực hiện chi ngân sách cấp Tỉnh(Huyện, Xã) Theo lĩnh vực',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-23'
                    },
                    {
                      name: 'Đánh giá thực hiện chi ngân sách cấp Tỉnh (Huyện, Xã) từng cơ quan, tổ chức theo lĩnh vực',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-24'
                    },
                    {
                      name: 'Đánh giá thực hiện chi đầu tư và phát triển của ngân sách cấp Tỉnh (Huyện, Xã) co từng cơ quan, tổ chức theo lĩnh vực',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-25'
                    },
                    {
                      name: 'Đánh giá thực hiện chi thường xuyên của ngân sách cấp Tỉnh (Huyện, Xã) co từng cơ quan, tổ chức theo lĩnh vực',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-26'
                    },
                    {
                      name: 'Đánh giá thực hiện chi cân đối ngân sách từng huyện (Xã)',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-27'
                    },
                    {
                      name: 'Tình hình thực hiện kế hoạch tài chính các quỹ tìa chính ngoài ngân sách do địa phương quản lý',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-28'
                    },
                    {
                      name: 'Đánh giá thực hiện thu dịch vụ của đơn vị sự nghiệp công',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-29'
                    }
                  ]
                },
                {
                  name: 'Thông tư số 69/2017/TT-BTC',
                  items: []
                },
                {
                  name: 'Thông tư số 38/2019/TT-BTC',
                  items: []
                },
                {
                  name: 'Thông tư số 342/2016/TT-BTC',
                  items: [
                    {
                      name: 'Tổng hợp dự toán thu ngân sách nhà nước ',
                      router: '/report/SBP_TT-342-2016-TT-BTC_MB-01'
                    },
                    {
                      name: 'Tổng hợp dự toán thu ngân sách nhà nước theo sắc thuế ',
                      router: '/report/SBP_TT-342-2016-TT-BTC_MB-02'
                    },

                    {
                      name: 'Dự kiến số thuế giá trị gia tăng phải hoàn',
                      router: '/report/SBP_TT-342-2016-TT-BTC_MB-03'
                    },
                    {
                      name: 'Tổng hợp dự toán thu từ hoạt động XNK',
                      router: '/report/SBP_TT-342-2016-TT-BTC_MB-04'
                    },
                    {
                      name: 'Dự toán thu, chi ngân sách nhà nước',
                      router: '/report/Demo_05-TH'
                    },
                    {
                      name: 'Dự toán chi đầu tư nguồn NSNN (vốn trong nước)',
                      router: '/report/SBP_TT-342-2016-TT-BTC'
                    },
                    {
                      name: 'Dự toán thu, chi, nộp ngân sách nhà nước từ các khoản phí và lệ phí',
                      router: '/report/SBP_TT-342-2016-TT-BTC_MB-07'
                    },
                    {
                      name: 'Cơ sở tính chi hoạt động của các cơ quan quản lý Nhà nước, Đảng, Đoàn thể',
                      router: '/report/Demo_14-QLNN'
                    },
                    {
                      name: 'Cơ sở tính chi các hoạt động kinh tế',
                      router: '/report/Demo_13-8-cshdkt'
                    },
                    // Dự toán 2021 ngày 30.09
                    {
                      name: 'Dự toán thu chi, ngân sách các đơn vị QLNN',
                      router: '/report/du_toan_3009_01'
                    },
                    {
                      name: 'Dự toán thu, chi, nộp ngân sách nhà nước từ các khoản phí và lệ phí',
                      router: '/report/du_toan_3009_02'
                    },
                    {
                      name: 'Dự toán thu, chi ngân sách sự nghiệp',
                      router: '/report/du_toan_3009_03'
                    },
                    {
                      name: 'Dự toán chi các chương trình mục tiêu quốc gia, chương trình mục tiêu',
                      router: '/report/du_toan_3009_04'
                    }
                  ]
                },
                {
                  name: 'Thông tư số 343/2016/TT-BTC',
                  items: []
                },
                {
                  name: 'Thông tư số 344/2016/TT-BTC',
                  items: [
                    {
                      name: 'Biểu cân đối quyết toán ngân sách xã',
                      router: '/report/SBS_TT-344-2016-TT-BTC_MBS-07'
                    },
                    {
                      name: 'Tổng hợp quyết toán thu ngân sách xã',
                      router: '/report/SBS_TT-344-2016-TT-BTC_MBS-08'
                    },
                    {
                      name: 'Tổng hợp quyết toán chi ngân sách xã',
                      router: '/report/SBS_TT-344-2016-TT-BTC_MBS-09'
                    },
                    {
                      name: 'Quyết toán thu ngân sách xã theo mục lục NSNN',
                      router: '/report/SBS_TT-344-2016-TT-BTC_MBS-10'
                    }
                  ]
                },
              ]
            },
            {
              name: 'Bảng định mức thu chi',
              items: [
                {
                  name: 'Loại chỉ tiêu định mức thu, chi',
                  items: []
                },
                {
                  name: 'Danh sách chỉ tiêu định mức thu, chi',
                  items: []
                },
                {
                  name: 'Bảng chỉ tiêu định mức thu, chi',
                  items: []
                },
              ]
            },
            {
              name: 'Danh sách',
              items: [
                {
                  name: 'Danh sách CQNN',
                },
                {
                  name: 'Danh sách loại CQNN',
                },
                {
                  name: 'Danh sách khoản thu',
                },
                {
                  name: 'Danh sách loại khoản thu',
                },
                {
                  name: 'Danh sách khoản chi',
                },
                {
                  name: 'Danh sách loại khoản chi',
                },
              ]
            },

          ]
        },
        {
          name: 'Báo cáo Thu&Chi NSNN',
        },
        {
          name: 'Báo cáo Quyết toán NSNN',
          items:[
            {
              name:'Báo cáo điều hành Quyết toán',
            },
            {
              name:'Báo cáo theo Văn bản',
              items:[
                {
                  name:'Nghị định 31/2020/NĐ-CP',
                  items:[
                    //phần đầu là của thanh
                    {
                      name: 'Dự báo một số chỉ tiêu kinh tế - Xã hội chủ yếu giai đoạn...',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-01'
                    },
                    {
                      name: 'Kế hoạch tài chính - Ngân sách giai đoạn 05',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-02'
                    },
                    {
                      name: 'Dự kiến phương án phân bổ kế hoạch đầu tư công trung hạn vốn NSNN giai đoạn 05',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-03'
                    },
                    {
                        name: 'Tổng hợp dự kiến kế hoạch đầu tư công trung hạn vốn NSNN của các cơ quan, đơn vị và địa phương giai đoạn 05',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-04'
                    },
                    {
                      name: ' Dự kiến thu ngân sách nhà nước theo lĩnh vực giai đoạn 03',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-08'
                    },
                    {
                      name: ' Dự kiến cân đối nguồn thu, chi ngân sách cấp tỉnh và ngân sách huyện giai đoạn 03',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-09'
                    },
                    {
                      name: 'Dự kiến chi ngân sách cấp tỉnh theo cơ cấu chi giai đoạn 03',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-10'
                    },
                    //đây là phần của dũng
                    {
                      name: 'Đánh giá thực hiện thu ngân sách nhà nước trên địa bàn từng huyện (xã) theo lĩnh vực ',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-21'
                    },
                    {
                      name: 'Đánh giá thực hiện chi ngân sách địa phương, chi ngân sách cấp Tỉnh(Huyện) và chia ngân sách Huyện(Xã) theo cơ cấu chi',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-22'
                    },
                    {
                      name: 'Đánh giả thực hiện chi ngân sách cấp Tỉnh(Huyện, Xã) Theo lĩnh vực',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-23'
                    },
                    {
                      name: 'Đánh giá thực hiện chi ngân sách cấp Tỉnh (Huyện, Xã) từng cơ quan, tổ chức theo lĩnh vực',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-24'
                    },
                    {
                      name: 'Đánh giá thực hiện chi đầu tư và phát triển của ngân sách cấp Tỉnh (Huyện, Xã) co từng cơ quan, tổ chức theo lĩnh vực',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-25'
                    },
                    {
                      name: 'Đánh giá thực hiện chi thường xuyên của ngân sách cấp Tỉnh (Huyện, Xã) co từng cơ quan, tổ chức theo lĩnh vực',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-26'
                    },
                    {
                      name: 'Đánh giá thực hiện chi cân đối ngân sách từng huyện (Xã)',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-27'
                    },
                    {
                      name: 'Tình hình thực hiện kế hoạch tài chính các quỹ tìa chính ngoài ngân sách do địa phương quản lý',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-28'
                    },
                    {
                      name: 'Đánh giá thực hiện thu dịch vụ của đơn vị sự nghiệp công',
                      router: '/report/SBP_ND-31-2017-ND-CP_BM-29'
                    }
                  ]
                },
                {
                  name: 'Thông tư 99/2018/TT-BTC',
                  items:[
                    {
                      name: 'Mẫu số B01/BCTC-TH',
                      router: '/report/SBS_TT-99-2018-TT-BTC_B01-BCTC-TH'
                    },
                    {
                      name: 'Mẫu số B01/BSTT',
                      router: '/report/SBS_TT-99-2018-TT-BTC_B01-BSTT'
                    },
                    {
                      name: 'Mẫu số B02/BCTC-TH',
                      router: '/report/SBS_TT-99-2018-TT-BTC_B02-BCTC-TH'
                    },
                    {
                      name: 'Mẫu số B03/BCTC-TH',
                      router: '/report/SBS_TT-99-2018-TT-BTC_B03-BCTC-TH'
                    },
                    {
                      name: 'Thuyết minh báo cáo tài chính tổng hợp',
                      router: '/report/SBS_TT-99-2018-TT-BTC_B04-BCTC-TH'
                    },
                    {
                      name: 'Bảng tổng hợp bổ sung thông tin tài chính',
                      router: '/report/SBS_TT-99-2018-TT-BTC_S01-BTH'
                    },
                    {
                      name: 'Bảng tổng hợp các chỉ tiêu báo cáo tài chính',
                      router: '/report/SBS_TT-99-2018-TT-BTC_S02-BTH'
                    }
                  ]
                },
                {
                  name:'Thông tư số 137/2017/TT-BTC',
                  items:[
                    {
                      name: 'Biên bản xét duyệt/Thẩm định quyết toán ngân sách',
                      router: '/report/SBS_TT-137-2017-TT-BTC_PL-01'
                    },
                    {
                      name: 'Thông báo xét duyệt/Thẩm định quyết toán ngân sách',
                      router: '/report/SBS_TT-137-2017-TT-BTC_PL-02'
                    },
                    {
                      name: 'Thẩm định quyết toán ngân sách huyện (quận, thị xã, thành phố trực thuộc tỉnh), xã (phường, thị trấn)',
                      router: '/report/SBS_TT-137-2017-TT-BTC_PL-03'
                    },
                    {
                      name: 'Báo cáo quyết toán các quỹ tài chính nhà nước ngoài ngân sách',
                      router: '/report/SBS_TT-137-2017-TT-BTC_MB-01'
                    },
                    {
                      name: 'Số liệu xét duyệt (hoặc thẩm định) thu phí, lệ phí',
                      router: '/report/SBS_TT-137-2017-TT-BTC_MB-1a'
                    },
                    {
                      name: 'Đối chiếu số liệu kết quả hoạt động',
                      router: '/report/SBS_TT-137-2017-TT-BTC_MB-1b'
                    },
                    {
                      name: 'Số liệu xét duyệt (hoặc thẩm định) quyết toán chi ngân sách',
                      router: '/report/SBS_TT-137-2017-TT-BTC_MB-1c'
                    },
                    {
                      name: 'Tổng hợp số thu dịch vụ của đơn vị sự nghiệp công (không bao gồm nguồn ngân sách nhà nước',
                      router: '/report/SBS_TT-137-2017-TT-BTC_MB-02'
                    },
                    {
                      name: 'Số liệu xét duyệt (hoặc thẩm định) thu phí, lệ phí năm',
                      router: '/report/SBS_TT-137-2017-TT-BTC_MB-2a'
                    },
                    {
                      name: 'Đối chiếu số liệu kết quả hoạt động',
                      router: '/report/SBS_TT-137-2017-TT-BTC_MB-2b'
                    },
                    {
                      name: 'Số liệu xét duyệt (hoặc thẩm định) quyết toán chi ngân sách',
                      router: '/report/SBS_TT-137-2017-TT-BTC_MB-2c'
                    },
                    {
                      name: 'Báo cáo thuyết minh quyết toán năm so với dự toán',
                      router: '/report/SBS_TT-137-2017-TT-BTC_MB03'
                    },
                    {
                      name: 'Đối chiếu số liệu thu, chi hoạt động sự nghiệp và hoạt động sản xuất kinh doanh',
                      router: '/report/SBS_TT-137-2017-TT-BTC_MB-3a'
                    },
                    {
                      name: 'Số liệu xét duyệt hoặc thẩm định chi ngân sách.đơn vị:',
                      router: '/report/SBS_TT-137-2017-TT-BTC_MB-3b'
                    },
                    {
                      name: 'Số liệu đối chiếu thu, chi hoạt động sự nghiệp và hoạt động sản xuất kinh doanh',
                      router: '/report/SBS_TT-137-2017-TT-BTC_MB-4a'
                    },
                    {
                      name: 'Số liệu đối chiếu thu, chi hoạt động sự nghiệp và hoạt động sản xuất kinh doanh năm đơn vị',
                      router: '/report/SBS_TT-137-2017-TT-BTC_MB-4b'
                    }
                  ]
                },
                {
                  name:'Thông tư 342',
                  items:[
                    {
                      name: 'Tổng hợp dự toán thu từ hoạt động XNK',
                      router: '/report/Demo_test_04'
                    },
                    {
                      name: 'Dự toán thu, chi ngân sách nhà nước',
                      router: '/report/Demo_05-TH'
                    },
                    {
                      name: 'Dự toán thu, chi, nộp ngân sách nhà nước từ các khoản phí và lệ phí',
                      router: '/report/SBP_TT-342-2016-TT-BTC_MB-07'
                    },
                    {
                      name: 'Cơ sở tính chi hoạt động của các cơ quan quản lý Nhà nước, Đảng, Đoàn thể',
                      router: '/report/Demo_14-QLNN'
                    },
                    {
                      name: 'Cơ sở tính chi các hoạt động kinh tế',
                      router: '/report/Demo_13-8-cshdkt'
                    },
                    {
                      name: 'Dự toán chi đầu tư nguồn NSNN (VỐN TRONG NƯỚC)',
                      router: '/report/SBP_TT-342-2016-TT-BTC'
                    },
                  ]
                },
                {
                  name:'Thông tư 343',
                },
                {
                  name:'Thông tư 344/2016/TT-BTC',
                  items:[
                    {
                      name: 'Biểu cân đối quyết toán ngân sách xã',
                      router: '/report/SBS_TT-344-2016-TT-BTC_MBS-07'
                    },
                    {
                      name: 'Tổng hợp quyết toán thu ngân sách xã',
                      router: '/report/SBS_TT-344-2016-TT-BTC_MBS-08'
                    },
                    {
                      name: 'Tổng hợp quyết toán chi ngân sách xã',
                      router: '/report/SBS_TT-344-2016-TT-BTC_MBS-09'
                    },
                    {
                      name: 'Quyết toán thu ngân sách xã theo mục lục NSNN',
                      router: '/report/SBS_TT-344-2016-TT-BTC_MBS-10'
                    }
                  ]
                }
              ]
            }
          ]
        },
        {
          name: 'Báo cáo Ngân quỹ',
        },
        {
          name: 'Báo cáo Kế toán TCNN',
          items:[
            {
              name: 'Báo cáo tình hình tài chính nhà nước',
              router: '/report/RCE_TT-25-2017-BCTC_CDKT'
            },
            {
              name: 'Báo cáo kết quả hoạt động tài chính nhà nước',
              router: '/report/RCE_TT-25-2017-BCTC'
            },
            {
              name: 'Báo cáo lưu chuyển tiền tệ',
              router: '/report/RCE_TT-25-2017-BCTC_LCTT'
            },
            {
              name: 'Sổ cái',
              router: '/report/RCE_TT-25-2017-BCTC_Socai'
            },
            {
              name: 'Bảng cân đối phát sinh tài khoản',
              router: '/report/RCE_TT-25-2017-BCTC_BangCanDoi'
            },

            // {
            //   name: 'Ủy nhiệm chi',
            //   router: '/report/RCE_TT-25-2017-BCTC_Uynhiemchi'
            // },
          ]
        },
        {
          name:'Báo cáo kế toán Tabmis'
        },
        {
          name:'Báo cáo kế toán HCSN',
          items:[
            {
              name:'Thông tư 107/2017/TT-BTC',
              items: [
                {
                  name: 'Báo cáo tình hình tài chính',
                  router: '/report/RCE_TT-107_B01-BCTC',
                },
                {
                  name: 'Báo cáo kết quả hoạt động',
                  router: '/report/RCE_TT-107_B02-BCTC'
                },
                {
                  name: 'Báo cáo lưu chuyển tiền tệ( theo phương pháp trực tiếp)',
                  router: '/report/RCE_TT-107_B03a-BCTC'
                },
                {
                  name: 'Báo cáo lưu chuyển tiền tệ( theo phương pháp gián tiếp)',
                  router: '/report/RCE_TT-107_B03b-BCTC'
                },
                {
                  name: 'Thuyết minh báo cáo tài chính',
                  router: '/report/RCE_TT-107_B04BCTC'
                }
              ]
            },
            {
              name: 'Thông tư 99/2018/TT-BTC',
              items:[
                {
                  name: 'Mẫu số B01/BCTC-TH',
                  router: '/report/SBS_TT-99-2018-TT-BTC_B01-BCTC-TH'
                },
                {
                  name: 'Mẫu số B01/BSTT',
                  router: '/report/SBS_TT-99-2018-TT-BTC_B01-BSTT'
                },
                {
                  name: 'Mẫu số B02/BCTC-TH',
                  router: '/report/SBS_TT-99-2018-TT-BTC_B02-BCTC-TH'
                },
                {
                  name: 'Mẫu số B03/BCTC-TH',
                  router: '/report/SBS_TT-99-2018-TT-BTC_B03-BCTC-TH'
                },
                {
                  name: 'Thuyết minh báo cáo tài chính tổng hợp',
                  router: '/report/SBS_TT-99-2018-TT-BTC_B04-BCTC-TH'
                },
                {
                  name: 'Bảng tổng hợp bổ sung thông tin tài chính',
                  router: '/report/SBS_TT-99-2018-TT-BTC_S01-BTH'
                },
                {
                  name: 'Bảng tổng hợp các chỉ tiêu báo cáo tài chính',
                  router: '/report/SBS_TT-99-2018-TT-BTC_S02-BTH'
                }
              ]
            }
          ]
        },
        {
          name:'báo cáo dư',
          items:[
            {
              name:'bản pdf của sếp',
              items:[
                {
                  name:'link 1',
                  items:[
                    {
                      name: 'Báo cáo ước thực hiện thu - chi ngân sách',
                      router: '/report/demo_Dung_bc01'
                    },
                    {
                      name: 'Cân đối ngân sách địa phương',
                      router: '/report/demo_Dung_bc02'
                    },
                    {
                      name: 'Dự kiến dự toán thu ngân sách',
                      router: '/report/demo_Dung_bc04'
                    },
                    {
                      name: 'Dự toán thu ngân sách',
                      router: '/report/demo_Dung_bc03'
                    },
                    {
                      name: 'Dự toán chi ngân sách địa phương',
                      router: '/report/demo_Dung_bc05'
                    },
                    {
                      name: 'Dự toán thu chi, ngân sách các đơn vị QLNN',
                      router: '/report/demo_Dung_bc06'
                    },

                    {
                      name: 'Dự toán chi quản lý hành chính khối tỉnh',
                      router: '/report/Report_Tuan_demo-6'
                    },
                    {
                      name: 'Dự toán chi sự nghiệp văn xã khối tỉnh',
                      router: '/report/Report_Tuan_demo-7'
                    },
                    {
                      name: 'Dự toán chi sự nghiệp kinh tế và môi trường khối tỉnh',
                      router: '/report/Report_Tuan_demo-8'
                    },
                    {
                      name: 'Dự toán chi khác khối tỉnh',
                      router: '/report/Report_Tuan_demo-9'
                    },
                    {
                      name: 'Dự toán chi quốc phòng - an ninh khối tỉnh',
                      router: '/report/Report_Tuan_demo-10'
                    },
                    {
                      name: 'Dự toán chi vốn đối ứng khối tỉnh',
                      router: '/report/Report_Tuan_demo-11'
                    },
                    {
                      name: 'Dự toán chi hỗ trợ doanh nghiệp',
                      router: '/report/Report_Tuan_demo-12'
                    },
                    {
                      name: 'Dự kiến giao thu ngân sách cho các huyện, thành phố, thị xã',
                      router: '/report/Report_Tuan_demo-13'
                    },
                    //Thanh

                    {
                      name: 'Dự toán chi NSNN khối huyện, thị xã, thành phố',
                      router: '/report/Demo-bang4'
                    },
                    {
                      name: 'Dự toán các chi ngân sách xã, phường, thị trấn',
                      router: '/report/Demo-bang5'
                    },
                    {
                      name: 'Sổ bổ sung từ ngân sách cấp tỉnh cho ngân sách các huyện, thành phố, thị xã',
                      router: '/report/Demo-bang6'
                    }
                  ]
                },
                {
                  name:'link 2',
                  items:[
                    {
                      name:'Quyết định Về công bố công khai số liệu dự toán ngân sách',
                      router: '/report/test_55_UBND_Q3'
                    },
                    {
                      name: 'Cân đối dự toán ngân sách Quận - Phường',
                      router: '/report/test_55_UBND_Q3_MS21'
                    },
                    {
                      name: 'Cân đối và dự toán ngân sách Quận - ngân sách Phường',
                      router: '/report/test_55_UBND_Q3_MS22'
                    },
                    {
                      name: 'Dự toán thu ngân sách nhà nước trên địa bàn quận thuộc thành phố',
                      router: '/report/test_55_UBND_Q3_MS23'
                    },
                    {
                      name: 'Dự toán chi ngân sách Quận - Phường thuộc thành phố',
                      router: '/report/test_55_UBND_Q3_MS24'
                    },
                    {
                      name: 'Dự toán chi ngân sách quận thành phố',
                      router: '/report/test_55_UBND_Q3_MS25'
                    },
                    {
                      name: 'Dự toán chi ngân sách cấp quận cho từng cơ quan, đơn vị thuộc tỉnh ',
                      router: '/report/test_55_UBND_Q3_MS26'
                    },
                    {
                      name: 'Tỷ lệ phần trăm (%) phân chia các khoản thu giữa ngân sách cấp thành phố,quận,phường',
                      router: '/report/test_55_UBND_Q3_MS29'
                    },
                    {
                      name: 'Tỷ lệ phần trăm (%) phân chia các khoản thu cho ngân sách từng phường',
                      router: '/report/test_55_UBND_Q3_MS30'
                    },
                    {
                      name: 'Dự toán thu chi ngân sách 14 phường',
                      router: '/report/test_55_UBND_Q3_MS31'
                    }
                  ]
                },
                {
                  name:'link 3',
                  items:[
                    {
                      name: 'Cân đối ngân sách huyện',
                      router: '/report/SBP_TT-343-2016-TT-BTC_BS69'
                    },
                    {
                      name: 'Cân đối nguồn thu, chi dự toán ngân sách cấp huyện và ngân sách xã',
                      router: '/report/SBP_TT-343-2016-TT-BTC_BS70'
                    },
                    {
                      name: 'Dự toán thu ngân sách nhà nước',
                      router: '/report/SBP_TT-343-2016-TT-BTC_BS71'
                    },
                    {
                      name: 'Dự toán chi ngân sách huyện, chi ngân sách cấp huyện và chi ngân sách xã theo cơ cấu chi',
                      router: '/report/SBP_TT-343-2016-TT-BTC_BS72'
                    },
                    {
                      name: 'Dự toán chi ngân sách cấp huyện theo từng lĩnh vực',
                      router: '/report/SBP_TT-343-2016-TT-BTC_BS73'
                    },
                    {
                      name: 'Dự toán chi ngân sách cấp huyện cho từng cơ quan, tổ chức',
                      router: '/report/SBP_TT-343-2016-TT-BTC_BS74'
                    },
                    {
                      name: 'Dự toán chi đầu tư phát triển của ngân sách cấp huyện cho từng cơ quan, tổ chức theo lĩnh vực',
                      router: '/report/SBP_TT-343-2016-TT-BTC_BS75'
                    },
                    {
                      name: 'Dự toán chi thường xuyên của ngân sách huyện cho từng cơ quan, tổ chức theo lĩnh vực',
                      router: '/report/SBP_TT-343-2016-TT-BTC_BS76'
                    },
                    //trên là của liên, dưới là của thanh
                    {
                      name: 'Dự toán thu, số bổ sung và dự toán chi cân đối ngân sách từng xã',
                      router: '/report/Demo-bang1'
                    },
                    {
                      name: 'Dự toán chi bổ sung có mục tiêu từ ngân sách cấp huyện cho ngân sách từng xã',
                      router: '/report/Demo-bang2'
                    },
                    {
                      name: 'Danh mục các chương trình, dự án sử dụng vốn ngân sách nhà nước nguồn vốn ngân sách huyện',
                      router: '/report/Demo-bang3'
                    }
                  ]
                }
              ]
            },
            {

              name:'Ước thực hiện',
              items: [
                {
                  name:'Demo modul du toan-CCTTBVTV',
                  items: [
                    {
                      name: 'Ước thực hiện dự toán thu, chi ngân sách nhà nước',
                      router: '/report/SBP_UTH_Demo_modul_du_toan_00',
                    },
                    {
                      name: 'Lập dự toán thu, cho ngân sách nhà nước',
                      router: '/report/SBP_UTH_Demo_modul_du_toan_01',
                    },
                    {
                      name: 'Xem xét dự toán thu, cho ngân sách nhà nước',
                      router: '/report/SBP_UTH_Demo_modul_du_toan_02',
                    },
                    {
                      name: 'Phê duyệt dự toán thu, cho ngân sách nhà nước',
                      router: '/report/SBP_UTH_Demo_modul_du_toan_03',
                    },
                    {
                      name: 'Giao dự toán thu, cho ngân sách nhà nước',
                      router: '/report/SBP_UTH_Demo_modul_du_toan_04',
                    },
                    {
                      name: 'Cấp dự toán thu, cho ngân sách nhà nước',
                      router: '/report/SBP_UTH_Demo_modul_du_toan_05',
                    },
                  ],
                },
                {
                  name:'DMCS- DMDV',
                  items:[
                    {
                      name: 'Bảng định mức cơ sở',
                      router: '/report/SBP_UTH_DMCS-DMDV_01'
                    },
                    {
                      name: 'Bảng định mức đơn vị',
                      router: '/report/SBP_UTH_DMCS-DMDV_02'
                    },
                  ]
                },
                {
                  name:'Module Dự toán- Sở KHĐT',
                  items:[
                    {
                      name: 'Ước thực hiện dự toán thu, chi ngân sách nhà nước',
                      router: '/report/SBP_UTH_SOKHDT_02'
                    },
                    {
                      name: 'Bảng định mức đơn vị',
                      router: '/report/SBP_UTH_SOKHDT_01'
                    },
                    {
                      name: 'Lập dự toán thu, chi ngân sách nhà nước',
                      router: '/report/SBP_UTH_SOKHDT_03'
                    },
                    {
                      name: 'Xem xét dự toán thu, chi ngân sách nhà nước',
                      router: '/report/SBP_UTH_SOKHDT_04'
                    },
                    {
                      name: 'Phê duyệt dự toán thu, chi ngân sách nhà nước',
                      router: '/report/SBP_UTH_SOKHDT_05'
                    },
                    {
                      name: 'Giao dự toán thu, chi ngân sách nhà nước',
                      router: '/report/SBP_UTH_SOKHDT_06'
                    },
                    {
                      name: 'Cấp dự toán thu, chi ngân sách nhà nước',
                      router: '/report/SBP_UTH_SOKHDT_07'
                    },
                  ]
                },
                {
                  name:'Modul dự toán- VPSo NN',
                  items:[
                    {
                      name: 'Ước thực hiện dự toán thu, chi ngân sách nhà nước',
                      router: '/report/SBP_UTH_VPSoNN_01'
                    },
                    {
                      name: 'Lập dự toán thu, chi ngân sách nhà nước',
                      router: '/report/SBP_UTH_VPSoNN_02'
                    },
                    {
                      name: 'Xem xét dự toán thu, chi ngân sách nhà nước',
                      router: '/report/SBP_UTH_VPSoNN_03'
                    },
                    {
                      name: 'Phê duyệt dự toán thu, chi ngân sách nhà nước',
                      router: '/report/SBP_UTH_VPSoNN_04'
                    },
                    {
                      name: 'Giao dự toán thu, chi ngân sách nhà nước',
                      router: '/report/SBP_UTH_VPSoNN_05'
                    },
                    {
                      name: 'Cấp dự toán thu, chi ngân sách nhà nước',
                      router: '/report/SBP_UTH_VPSoNN_06'
                    },
                  ]
                },
              ]

            },
            {
              name:'Lập dự toán',
              items:[
                {
                  name: 'Sở kế hoạch đầu tư gửi sở tài chính',
                  items:[
                    {
                      name: 'CV xây dựng kế hoạch - sở KHĐT',
                      router: '/report/SoKHDTguiSTC_01'
                    },
                    {
                      name: 'Thuyết minh dự toán - sở KHĐT',
                      router: '/report/SoKHDTguiSTC_02'
                    },
                    {
                      name: 'Dự đoán chi đầu tư nguồn NSNN (vốn trong nước)',
                      router: '/report/SoKHDTguiSTC_03'
                    },
                    {
                      name: 'Dự đoán thu, chi ngân sách nhà nước',
                      router: '/report/SoKHDTguiSTC_04'
                    },
                    {
                      name: 'Dự đoán thu, chi, nộp ngân sách nhà nước từ các khoản phí lệ phí',
                      router: '/report/SoKHDTguiSTC_05'
                    },
                    {
                      name: 'Cơ sở tính chi hoạt động của các cơ quan quản lý nhà nước, đảng, đoàn thể',
                      router: '/report/SoKHDTguiSTC_06'
                    },
                    {
                      name: 'Cơ sở tính chi các hoạt động kinh tế',
                      router: '/report/SoKHDTguiSTC_07'
                    },
                    {
                      name: 'Thuyết Minh dự toán ban hành 30.9',
                      router: '/report/ThuyetMinh'
                    }
                  ]
                },
                {
                  name: 'Văn phòng Sở gửi Sở Nông nghiệp',
                  items:[
                    {
                      name: ' CV Xay dung Ke hoach - VP Sở NN',
                      router: '/report/VPSguiSNN_03'
                    },
                    {
                      name: 'Thuyet minh du toan - VP So',
                      router: '/report/VPSguiSNN_04'
                    },
                    {
                      name: 'Cơ sở tính chi hoạt động của các cơ quan quản lý nhà nước, đảng, đoàn thể',
                      router: '/report/VPSguiSNN_01'
                    },  {
                      name: 'Dự đoán thu, chi, nộp ngân sách nhà nước từ các khoản phí lệ phí',
                      router: '/report/VPSguiSNN_02'
                    },

                  ]
                },
                {
                  name:'Chi cục bảo vệ thực vật gửi sở Nông Nghiệp',
                  items: [
                    {
                      name: 'Dự toán thu, chi ngân sách nhà nước',
                      router: '/report/Demo_05-TH'
                    },
                    {
                      name: 'Dự toán thu, chi, nộp ngân sách nhà nước từ các khoản phí và lệ phí',
                      router: '/report/Demo_07-thu'
                    },
                    {
                      name: 'Cơ sở tính chi các hoạt động kinh tế',
                      router: '/report/Demo_13-8-cshdkt'
                    },
                    {
                      name: 'Cơ sở tính chi hoạt động của các cơ quan quản lý nhà nước, đảng, đoàn thể',
                      router: '/report/Demo_14-QLNN'
                    },
                    {
                      name: 'Số: 26/2020/CV-CCBVTV',
                      router: '/report/Demo_26-2020-CV-CCBVTV'
                    },

                  ]
                },
                {
                  name:'Sở Nông Nghiệp gửi Sở Tài Chính',
                  items:[
                    {
                      name: 'Dự toán thu chi, ngân sách các đơn vị QLNN',
                      router: '/report/du_toan_3009_01'
                    },
                    {
                      name: 'Dự toán thu, chi, nộp ngân sách nhà nước từ các khoản phí và lệ phí',
                      router: '/report/du_toan_3009_02'
                    },
                    {
                      name: 'Dự toán thu, chi ngân sách sự nghiệp',
                      router: '/report/du_toan_3009_03'
                    },
                    {
                      name: 'Dự toán chi các chương trình mục tiêu quốc gia, chương trình mục tiêu',
                      router: '/report/du_toan_3009_04'
                    }
                  ]
                }
              ]

            },
            {
              name:'xem xét',
              items:[
                {
                  name:'Tờ trình',
                  items:[
                    {
                      name: 'Tờ trình Về việc Đề nghị phê duyệt dự toán thu ngân sách nhà nước trên địa bàn, chi ngân sách địa phương bản 1',
                      router: '/report/ToTrinh_01'
                    },
                    {
                      name: 'Tờ trình Về việc Đề nghị phê duyệt dự toán thu ngân sách nhà nước trên địa bàn, chi ngân sách địa phương bản 2',
                      router: '/report/ToTrinh_02'
                    }
                  ]
                },
                {
                  name: 'Công khai nội dung trình HDND tỉnh quyết định dự toán Đồng Nai',
                  router: '/report/DongNai'
                }
              ]
            },
            {
              name:'phê duyệt',
              items:[
                {
                  name: 'Dự toán chi đầu tư phát triển của ngân sách cấp tỉnh cho từng cơ quan, tổ chức theo lĩnh vực',
                  router: '/report/SBP_PD_QDUBCboDTNS_01'
                },
                {
                  name: 'Dự toán chi thường xuyên của ngân sách cấp tỉnh cho từng cơ quan, tổ chức theo lĩnh vực',
                  router: '/report/SBP_PD_QDUBCboDTNS_02'
                },
                {
                  name: 'Tỷ lệ phần trăm (%) các khoản thu phân chia giữa ngân sách các cấp chính quyền địa phương',
                  router: '/report/SBP_PD_QDUBCboDTNS_03'
                },
                {
                  name: 'Số bổ sung từ ngân sách cấp tỉnh cho ngân sách các huyện, thành phố tỉnh Nam Định',
                  router: '/report/SBP_PD_QDUBCboDTNS_04'
                },
                {
                  name: 'Dự toán chi bổ sung có mục tiêu từ ngân sách cấp tỉnh cho ngân sách từng huyện',
                  router: '/report/SBP_PD_QDUBCboDTNS_05'
                }
              ]
            },
            {
              name:'phân bổ',
              items: [
                {
                  name: 'Về việc công bố công khai dự toán thu, chi ngân sách nhà nước năm 2021 của Sở Kế hoạch và Đầu tư',
                  router: '/report/SBP_PB_01',
                },
                {
                  name: 'Về việc giao dự toán thu, chi ngân sách nhà nước cho các đơn vị sử dụng ngân sách trực thuộc Sở Kế hoạch và Đầu tư',
                  router: '/report/SBP_PB_02',
                },
              ],
            },
            {
              name:'cấp dự toán',
              items:[
                {
                  name:'quyết định',
                  items:[
                    {
                      name: 'Quyết định Về việc bổ sung dự toán ngân sách Nhà nước',
                      router: '/report/QD_GiaoBoSungDuToan_NguonSuNghiep'
                    },
                    {
                      name: 'Quyết định Về việc giao dự toán ngân sách Nhà nước, Giám đốc sở nông nghiệp và phát triển nông thôn',
                      router: '/report/QD_GiaoDuToan_NguonSuNghiep'
                    },
                    {
                      name: 'Quyết đinh về việc giao dự toán ngân sách Nhà nước, Ủy ban nhân dân tỉnh ',
                      router: '/report/QD_GiaoDuToanNguon_QLNN'
                    },
                    {
                      name: 'Quyết định về việc giao dự toán ngân sách Nhà nước, Chánh văn phòng ủy ban nhân dân tỉnh ',
                      router: '/report/QD_PhanBoDuToan_VPUBNDTinh'
                    }
                  ]
                }
              ]
            },
            {
              name:'huyện',
              items:[
                {
                  name:'dữ liệu demo huyện Vĩnh Thạnh',
                  items: [
                    {
                      name: 'Lập dự toán thu chi',
                      router: '/report/LapDT'
                    },
                    {
                      name: 'Phê duyệt dự toán thu chi',
                      router: '/report/DULIEUDEMOVINHTHANH_PHEDUYET'
                    },
                    {
                      name: 'Xem xét dự toán thu chi',
                      router: '/report/xemxet'
                    },
                    {
                      name: 'Cân đối dự toán ngân sách quận - phường',
                      router: '/report/DULIEUDEMOVINHTHANH_UocTH'
                    },
                    {
                      name: 'Giao dự toán thu chi',
                      router: '/report/DULIEUDEMOVINHTHANH_Giao'
                    },
                    {
                      name: 'Ước thực hiện thu chi',
                      router: '/report/UTH'
                    },

                  ]

                }
              ]
            },
            {
              name:'CV-STC gửi đơn vị xem xét dự toán',
              items:[
                {
                  name:'CV-STC gửi đơn vị xem xét dự toán',
                  router:'/report/CV-STC-gui-cac-don-vi-xem-du-toan'
                },
                {
                  name:'CV-STC gửi đơn vị xem xét dự toán vòng 2',
                  router:'/report/CV-STC-gui-cac-don-vi-xem-du-toan-vong-2'

                }
              ]
            }
          ]
        },
        {
          name:'Báo cáo kế toán chủ đầu tư'
        },
        {
          name:'Báo cáo kế toán xã, phường'
        },
        {
          name:'Báo cáo kế toán doanh nghiệp'
        },
        {
          name: 'Báo cáo Tiền lương',
        },
        {
          name: 'Báo cáo Tài sản công',
        },
        {
          name: 'Báo cáo Đầu tư công',
        },
        {
          name: 'Báo cáo Nợ công',
        },
        {
          name: 'Báo cáo Viện trợ công',
        },
        {
          name: 'Báo cáo Tài chính doanh nghiệp ',
        },
        {
          name: 'Báo cáo Giá',
        },
        {
          name: 'Báo cáo Thanh tra tài chính',
        },
        {
          name: 'Báo cáo Danh mục',
        },
        {
          name: 'Báo cáo Quản trị hệ thống',
        },
      ],
      settings: []
    };
  },
  components:{
    AppHeaderDropdown
  },
  props: {
  },
  computed: {
  },
  mounted() {
    let self = this;
    // self.handleSetMenu(self.$store.state.moduleNavTop);
    // this.$store.watch(
    //     function (state) {
    //         return state.moduleNavTop;
    //     },
    //     function () {
    //         //do something on data change
    //         // alert(self.$store.state.moduleNavTop);
    //         self.handleSetMenu(self.$store.state.moduleNavTop);
    //
    //     },
    //     {
    //         deep: true //add this if u need to watch object properties change etc.
    //     }
    // );
  },
  methods: {
    handleSetMenu(module){
      let self = this;
      let permission = PermissionService.getPermission();
      self.listing = [];
      self.operation = [];
      self.reports = [];
      self.settings = [];
      let itemsMenu = _.filter(permission.menuTopArr, ['Module', module]);

      self.moduleName = (itemsMenu[0]) ? itemsMenu[0].ModuleName : '';
      _.forEach(itemsMenu, function (item, key) {
        if (item.Position === 1) {
          self.listing.push(item);
        }else if (item.Position === 2) {
          self.operation.push(item);
        }else if (item.Position === 3) {
          self.reports.push(item);
        }else if (item.Position === 4) {
          self.settings.push(item);
        }
      });
    },
    setDashboardTab(tabNo){
      this.$store.commit('menuTop', {dashboard: {tabNo: tabNo}});
    },
    onClickReport(e, report) {
      this.$router.push({
        path: report.router
      });
    }
  }
}
</script>

<style lang="css" scoped>
  .dropright .dropdown-menu {
    top: 0 !important;
  }
  .dropdown-sub-menu-report {
    max-height: 250px;
    overflow-y: auto;
  }
</style>
