<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span><i class="icon-eye icon mr-2"></i> Ước thực hiện dự toán </span>
                        </div>
                    </b-col>
                    <b-col class="col-md-6"></b-col>
                </b-row>
                <b-row>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-actions">
                          <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onCreateClicked"><i class="fa fa-plus"></i> Thêm</b-button>
                          <b-button class="main-header-action mr-2" variant="primary" @click="actbook(model.TransID)" size="md" v-if="model.Posted==0">
                          <i class="fa fa-book"></i> Ghi sổ
                          </b-button>
                          <b-button class="main-header-action mr-2" variant="primary" @click="actbook(model.TransID)" size="md" v-if="model.Posted==1">
                            <i class="fa fa-book"></i> Bỏ ghi sổ
                          </b-button>
                          <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="onEditClicked"><i class="fa fa-edit"></i> Sửa</b-button>
                          <b-dropdown variant="primary" id="dropdown-actions" class="main-header-action mr-2" text="Thao tác">
                                <li class="dropdown b-dropdown dropright">
                                  <a class="dropdown-item dropdown-toggle d-lg-flex justify-content-between align-items-center border-0" data-toggle="dropdown" @click.stop="$_onToggleDropdownSubMenu($event)" href="#">In</a>
                                  <ul role="menu" tabindex="-1" class="dropdown-sub-menu dropdown-menu m-0">
                                    <li v-if="model.TransID == 108" role="presentation"><a role="menuitem" target="_self" href="/#/report/SBP_UTH_SOKHDT_02" class="dropdown-item" @click="handleExportPrint">Ước thực hiện dự toán</a></li>
                                    <li v-if="model.TransID == 131" role="presentation"><a role="menuitem" target="_self" href="/#/report/UTH" class="dropdown-item" @click="handleExportPrint">Ước thực hiện thu chi ngân sách nhà nước</a></li>
                                    <li v-if="model.TransID == 148" role="presentation"><a role="menuitem" target="_self" href="/#/report/UocThucHien_VPSoNN-UocTH" class="dropdown-item" @click="handleExportPrint">Ước thực hiện dự toán ngân sách nhà nước</a></li>
                                    <li v-if="model.TransID == 154" role="presentation"><a role="menuitem" target="_self" href="/#/report/Demo_modul_du_toan_ms00" class="dropdown-item" @click="handleExportPrint">Ước thực hiện dự toán ngân sách nhà nước</a></li>
                                    <li v-if="model.TransID == 160" role="presentation"><a role="menuitem" target="_self" href="/#/report/UocThucHien_VPSoNN-UocTH" class="dropdown-item" @click="handleExportPrint">Ước thực hiện dự toán ngân sách nhà nước</a></li>

                                  </ul>
                                </li>
                                <b-dropdown-item @click="handleCopyItem">Nhân bản</b-dropdown-item>
                                <b-dropdown-item>Chia sẻ</b-dropdown-item>
                                <b-dropdown-item>Chat</b-dropdown-item>
                                <b-dropdown-item>Zalo</b-dropdown-item>
                                <b-dropdown-item>Phân quyền</b-dropdown-item>
                                <b-dropdown-item @click="handleDeleteItem">Xóa</b-dropdown-item>
                            </b-dropdown>
                        </div>
                    </b-col>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-icons">
                            <div class="main-header-item-counter ml-auto mr-3" v-if="reqParams">
<!--                                <span>{{itemNo}} - {{itemTotalPerPage}}</span>-->
<!--                                /-->
<!--                                <span>{{itemTotal}}</span>-->
                              <span>{{itemNo}} - {{itemTotal}}</span>
                            </div>
                            <b-button-group id="main-header-views" class="main-header-views">
                                <b-button id="tooltip-view-prev" @click="onNavigationItem('prev')" v-if="reqParams" class="main-header-view"><i class="fa fa-angle-left"></i></b-button>
                                <b-button id="tooltip-view-next" @click="onNavigationItem('next')" v-if="reqParams" class="main-header-view"><i class="fa fa-angle-right"></i></b-button>
                                <b-button id="tooltip-view-back-to-list" @click="onBackToList" class="main-header-view"><i class="fa fa-list"></i></b-button>
                                <b-tooltip container="main-header-views" target="tooltip-view-back-to-list">Danh sách</b-tooltip>
                                <b-tooltip container="main-header-views" target="tooltip-view-prev">Trước</b-tooltip>
                                <b-tooltip container="main-header-views" target="tooltip-view-next">Sau</b-tooltip>
                            </b-button-group>
                            <div class="main-header-collapse" id="main-header-collapse" title="Co/giãn màn hình">
                                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen=true />
                            </div>

                        </div>
                    </b-col>
                </b-row>
            </div>

        </div>
        <div class="main-body main-body-view-action">
          <div class="container-fluid">
            <div class="card">
              <div class="form-group row" style="margin-bottom: 0px !important; margin-top: 10px; margin-right: 0px">
                <div class="col-md-13 col-lg-13">
                  <!--                    <b-card no-body>-->
                  <b-tabs card>
                    <b-tab title="Đơn vị lập" active>
                      <b-card-text>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-4 m-0">Tên:</label>
                          <div class="col-md-20 col-xl-20">
                            {{model.CompanyName}}
                          </div>
                        </div>
                        <!--                            <div class="form-group row align-items-center">-->
                        <!--                              <label class="col-md-3 m-0" >Mã số:</label>-->
                        <!--                              <div class="col-md-5">-->
                        <!--                                {{model.CompanyNo}}-->
                        <!--                              </div>-->

                        <!--                              <label class="col-md-3 m-0" >Mã ĐVQHNS: </label>-->
                        <!--                              <div class="col-md-5">-->
                        <!--                                {{model.CompanyMOFNo}}-->
                        <!--                              </div>-->

                        <!--                              <label class="col-md-3 m-0" >Mã địa bàn:</label>-->
                        <!--                              <div class="col-md-5">-->
                        <!--                                {{model.CompanyLocationNo}}-->
                        <!--                              </div>-->
                        <!--                            </div>-->
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-4 m-0">Địa chỉ:</label>
                          <div class="col-md-20 col-xl-20">
                            {{model.CompanyAddress}}
                          </div>
                        </div>
                        <!--                            <div class="form-group row align-items-center">-->
                        <!--                              <label class="col-md-3 m-0" >Tài khoản:</label>-->
                        <!--                              <div class="col-md-5">-->
                        <!--                                {{model.CompanyBankAccount}}-->
                        <!--                              </div>-->

                        <!--                              <label class="col-md-3 m-0" >Nơi mở: </label>-->
                        <!--                              <div class="col-md-5">-->
                        <!--                                {{model.CompanyBankName}}-->
                        <!--                              </div>-->
                        <!--                            </div>-->
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-4 m-0 text-nowrap" >Đại diện:</label>
                          <div class="col-md-20 col-xl-20">
                            {{model.CompanyContactName}}
                          </div>

                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-4 m-0 text-nowrap" >Chức vụ: </label>
                          <div class="col-md-20 col-xl-20 mt-2 mt-xl-0">
                            {{model.CompanyContactPosition}}
                          </div>
                        </div>

                      </b-card-text>
                    </b-tab>
                    <b-tab title="Đơn vị xét duyệt" >
                      <b-card-text>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-4 m-0">Tên:</label>
                          <div class="col-md-20 col-xl-20">
                            {{model.ParentCompanyName}}
                          </div>
                        </div>
                        <!--                            <div class="form-group row align-items-center">-->
                        <!--                              <label class="col-md-3 m-0" >Mã số</label>-->
                        <!--                              <div class="col-md-5">-->
                        <!--                                {{model.ParentCompanyNo}}-->
                        <!--                              </div>-->

                        <!--                              <label class="col-md-3 m-0" >Mã ĐVQHNS </label>-->
                        <!--                              <div class="col-md-5">-->
                        <!--                                {{model.ParentCompanyMOFNo}}-->
                        <!--                              </div>-->

                        <!--                              <label class="col-md-3 m-0" >Mã địa bàn</label>-->
                        <!--                              <div class="col-md-5">-->
                        <!--                                {{model.ParentCompanyLocationNo}}-->
                        <!--                              </div>-->
                        <!--                            </div>-->
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-4 m-0">Địa chỉ:</label>
                          <div class="col-md-20 col-xl-20">
                            {{model.ParentCompanyAddress}}
                          </div>
                        </div>
                        <!--                            <div class="form-group row align-items-center">-->
                        <!--                              <label class="col-md-3 m-0" >Tài khoản</label>-->
                        <!--                              <div class="col-md-5">-->
                        <!--                                {{model.ParentCompanyBankAccount}}-->
                        <!--                              </div>-->

                        <!--                              <label class="col-md-3 m-0" >Nơi mở </label>-->
                        <!--                              <div class="col-md-5">-->
                        <!--                                {{model.ParentCompanyBankName}}-->
                        <!--                              </div>-->
                        <!--                            </div>-->
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-4 m-0 text-nowrap" >Đại diện:</label>
                          <div class="col-md-20 col-xl-20">
                            {{model.ParentCompanyContactName}}
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-4 m-0 text-nowrap" >Chức vụ: </label>
                          <div class="col-md-20 col-xl-20">
                            {{model.ParentCompanyContactPosition}}
                          </div>
                        </div>

                      </b-card-text>
                    </b-tab>

                  </b-tabs>
                  <!--                    </b-card>-->
                  <div class="form-group row align-items-center" style="padding: 0 1rem; margin-bottom: 0px !important;">
                    <label class="col-md-4 col-xl-4 m-0">Chỉ thị:</label>
                    <div class="col-md-20 col-xl-20 mt-2 mt-xl-0" :title="model.DirectionName">
                      {{model.DirectionNo}}
                    </div>
                  </div>
                </div>
                <div class="col-md-1 col-lg-1"></div>
                <div class="col-md-10 col-lg-10" style="margin-top: 32px;">
                  <div class="form-group row align-items-center">
                    <label class="col-md-12 col-lg-12 col-xl-5 m-0" >Số CTG:</label>
                    <div class="col-md-12 col-lg-12 col-xl-7">
                      {{model.TransNo}}
                    </div>
                    <label class="col-md-12 col-lg-12 col-xl-5 m-0" >Ngày CTG:</label>
                    <div class="col-md-12 col-lg-12 col-xl-7">
                      {{model.TransDate | convertServerDateToClientDate}}
                    </div>
                  </div>
                  <div class="form-group row align-items-center">
                    <label class="col-md-12 col-lg-12 col-xl-5 m-0" >Số CTĐT:</label>
                    <div class="col-md-12 col-lg-12 col-xl-7">
                      {{model.eTransNo}}
                    </div>
                    <label class="col-md-12 col-lg-12 col-xl-5 m-0" >Ngày CTĐT:</label>
                    <div class="col-md-12 col-lg-12 col-xl-7">
                      {{model.eTransDate | convertServerDateToClientDate}}
                    </div>
                  </div>
                  <div class="form-group row align-items-center">
                    <label class="col-md-12 col-lg-12 col-xl-5 m-0" >Ngày HT:</label>
                    <div class="col-md-12 col-lg-12 col-xl-7">
                      {{model.PostDate | convertServerDateToClientDate}}
                    </div>
                    <label class="col-md-12 col-lg-12 col-xl-5 m-0" >Kỳ HT: </label>
                    <div class="col-md-12 col-lg-12 col-xl-7">
                      {{model.PeriodName}}
                    </div>

                  </div>
                  <div class="form-group row align-items-center">
                    <label class="col-md-12 col-lg-12 col-xl-5 m-0" >Loại HTTK:</label>
                    <div class="col-md-12 col-lg-12 col-xl-7">
                      {{model.CoaTypeName}}
                    </div>
                    <label class="col-md-12 col-lg-12 col-xl-5 m-0" >Trạng thái:</label>
                    <div class="col-md-12 col-lg-12 col-xl-7">
                      {{model.StatusDescription}}
                    </div>
                  </div>
                  <div class="form-group row justify-content-between" style="margin-bottom: 0.3rem !important;">
                    <div class="col-md-6 col-xl-6 m-0 ml-0 text-nowrap">
                      <b-form-checkbox type="check" v-model="model.isFinalTrans" class="form-control" style="padding-left: 22px !important;"></b-form-checkbox>
                      <label style="position: absolute; width: 102px; left: 32px; top: 3px;">Là CT cuối</label></div>
                    <div class="col-md-8 col-xl-8 m-0 ml-0 text-nowrap">
                      <b-form-checkbox type="check" v-model="model.isAdjustTrans" class="form-control" style="padding-left: 22px !important;"></b-form-checkbox>
                      <label style="position: absolute; width: 102px; left: 32px; top: 3px;">Là CT điều chỉnh</label>
                    </div>
                    <div class="col-md-7 col-xl-7 m-0 ml-0 text-nowrap">
                      <b-form-checkbox type="check" v-model="model.isDebtTrans" class="form-control" style="padding-left: 22px !important;"></b-form-checkbox>
                      <label style="position: absolute; width: 102px; left: 32px; top: 3px;">Là CT công nợ</label>
                    </div>
                  </div>
                </div>

              </div>
              <!-- End Master -->
              <div class="container-fluid" id="bar_detail">
                <div class="form-group justify-content-end row" style="width: 100%; text-align: right; margin-left: 10px; margin-bottom: 0.3rem !important;">
                  <div class="col-md-12 col-xl-12 m-0 ml-0">
                    <div class="form-group row justify-content-end">
                    <!--                      <div v-if="model.NormTableName" class="col-md-6 text-nowrap" style="line-height: 31px;">-->
                    <!--                        <span :title="model.NormTableName"><i class="fa fa-check" aria-hidden="true" style="color:#00a2e8;"></i> {{model.NormTableName}}</span>-->
                    <!--                      </div>-->
                    <!--                      <div class="col-md-2">-->
                    <!--                        <IjcoreModalMultiAct v-model="model" @changed="addLineHttk" :title="'HTTK'"-->
                    <!--                                             :api="'/listing/api/common/list'" :table="'coa_type'" :FieldID="'CoaTypeID'" :FieldName="'CoaTypeName'"></IjcoreModalMultiAct>-->
                    <!--                      </div>-->
                    <div class="col-md-5 col-xl-5 m-0 ml-0">
                      <b-form-checkbox type="check" v-model="model.item_tt" class="form-control">Tiền tệ</b-form-checkbox>
                    </div>
                    <div class="col-md-7 col-xl-7 m-0 ml-0">
                      <a @click="onToggleModalProperty()" class="new-row" style="width: 110px;">
                        <i v-if="model.item_ts" aria-hidden="true" class="fa fa-dot-circle-o ij-icon ij-icon-plus" style="font-size: 16px; color: #00a2e8"></i>
                        <i v-else aria-hidden="true" class="fa fa-circle-thin ij-icon ij-icon-plus" style="font-size: 16px;"></i> Tài sản/Vật tư
                      </a>
                    </div>
                    <!--                      <div class="col-md-3">-->
                    <!--                        <b-form-checkbox type="check" v-model="model.item_hd" class="form-control">Hóa đơn</b-form-checkbox>-->
                    <!--                      </div>-->
                    <div class="col-md-5 col-xl-5 m-0 ml-0 text-nowrap">
                      <b-form-checkbox type="check" v-model="model.item_da" class="form-control">Dự án</b-form-checkbox>
                    </div>
                    </div>
                  </div>
                  <div class="col-md-11 col-xl-11 m-0 ml-0">
                    <div class="form-group row justify-content-between text-left">
                    <div class="col-md-8 col-xl-8 m-0 ml-0 text-nowrap" style="padding-left: 43px;">
                      <a @click="onToggleModalObject()" class="new-row">
                        <i aria-hidden="true" class="fa fa-th-large ij-icon ij-icon-plus" style="font-size: 16px;"></i> Đối tượng
                      </a>
                    </div>
                    <div class="col-md-8 col-xl-8 m-0 ml-0 text-nowrap" style="padding-left: 28px;">
                      <b-form-checkbox type="check" v-model="model.item_nv" class="form-control">Nguồn vốn</b-form-checkbox>
                    </div>
                    <div class="col-md-7 col-xl-7 m-0 ml-0 text-nowrap" style="padding-left: 37px;">
                      <b-form-checkbox type="check" v-model="model.item_mlns" class="form-control">MLNS</b-form-checkbox>
                    </div>
                    </div>
                  </div>

                </div>
              </div>
              <div style="margin: 0 10px;">
                <div class="table-responsive b-table-sticky-header b-table-sticky-custom m-0" style="max-height: 300px;">
                  <table class="table b-table table-sm table-bordered table-tree table-view table-column-resizable border-0">
                    <thead>
                    <tr>
                      <th style="border-left: 1px solid #c8ced3; border-right: 0px; max-width: 1px !important; width: 1px !important; margin: 0px !important; padding: 0px !important; z-index: 999 !important;" class="bg-tree-tr b-table-sticky-column"></th>
                      <th scope="col" style="min-width: 200px" class="text-center">Bút toán</th>
                      <th v-if="model.item_tkhn" scope="col" style="min-width: 75px" class="text-center">Tài khoản</th>
                      <!--                      <th v-if="model.item_tkhn" scope="col" style="min-width: 75px" class="text-center" :title="'TKĐƯ'+ '('+model.CoaTypeName+')'">TKĐƯ</th>-->
                      <!-- Chỉ tiêu -->
                      <th scope="col" style="min-width: 100px; z-index: 11" class="text-center" title="Định mức dự toán chi tiết">ĐMDTCT</th>
                      <th scope="col" style="min-width: 100px; z-index: 11" class="text-center" title="Định mức phân bổ dự toán">ĐMPBDT</th>
                      <th scope="col" style="min-width: 100px; z-index: 11" class="text-center border-right-0" title="Chỉ tiêu dự toán">CTDT</th>
                      <!-- Nội dung -->
                      <th scope="col" style="min-width: 450px; z-index: 11 !important;" class="text-center b-table-sticky-column">Nội dung</th>
                      <!--Tiền tệ -->
                      <th v-if="model.item_tt" scope="col" style="min-width: 80px" class="text-center">Tiền tệ </th>
                      <th v-if="model.item_tt" scope="col" style="min-width: 80px" class="text-center">Tỷ giá </th>
                      <!-- Số dự toán -->
                      <th scope="col" style="min-width: 150px" class="text-center">Số dự toán <span v-if="model.item_tt">NT</span>  </th>
                      <th v-if="model.item_tt" scope="col" style="min-width: 150px" class="text-center">Số dự toán QĐ  </th>
                      <!-- Số đã TH -->
                      <th scope="col" style="min-width: 150px" class="text-center">Số đã TH <span v-if="model.item_tt">NT</span>  </th>
                      <th v-if="model.item_tt" scope="col" style="min-width: 150px" class="text-center">Số đã TH QĐ  </th>
                      <!--Tài sản/Vật tư -->
                      <th v-if="model.item_ts" scope="col" style="min-width: 120px" class="text-center">{{item_ts_name}} </th>
                      <th scope="col" style="min-width: 80px" class="text-center">ĐVT </th>
                      <th scope="col" style="min-width: 80px" class="text-center">Số lượng </th>
                      <th scope="col" style="min-width: 150px" class="text-center">Đơn giá</th>
                      <!-- Số tiền -->
                      <th scope="col" style="min-width: 150px" class="text-center">Số tiền <span v-if="model.item_tt">NT</span>  </th>
                      <th v-if="model.item_tt" scope="col" style="min-width: 150px" class="text-center">Số tiền QĐ  </th>
                      <!-- -->
                      <th scope="col" style="min-width: 120px" class="text-center">Nghiệp vụ</th>

                      <!--Hóa đơn -->
                      <th v-if="model.item_hd" scope="col" style="min-width: 120px" class="text-center">Tiền thuế </th>
                      <th v-if="model.item_hd" scope="col" style="min-width: 120px" class="text-center">Thuế suất </th>
                      <th v-if="model.item_hd" scope="col" style="min-width: 120px" class="text-center">Số hóa đơn </th>
                      <th v-if="model.item_hd" scope="col" style="min-width: 120px" class="text-center">Ngày hóa đơn </th>
                      <th v-if="model.item_hd" scope="col" style="min-width: 120px" class="text-center">Ký hiệu </th>
                      <th v-if="model.item_hd" scope="col" style="min-width: 120px" class="text-center">Mẫu số </th>
                      <th v-if="model.item_hd" scope="col" style="min-width: 120px" class="text-center">Mã bảo mật </th>
                      <th v-if="model.item_hd" scope="col" style="min-width: 160px" class="text-center">Địa chỉ tra cứu hóa đơn </th>
                      <!--Dự án -->
                      <th v-if="model.item_da" scope="col" style="min-width: 120px" class="text-center">Dự án </th>
                      <th v-if="model.item_da" scope="col" style="min-width: 139px" class="text-center">CTMT </th>
                      <th v-if="model.item_da" scope="col" style="min-width: 120px" class="text-center">Hợp đồng </th>
                      <!--Đối tượng -->
                      <th v-if="model.item_dt" scope="col" style="min-width: 120px" class="text-center">CQNN </th>
                      <th v-if="model.item_dt" scope="col" style="min-width: 120px" class="text-center">Nhân viên </th>
                      <th v-if="model.item_dt" scope="col" style="min-width: 120px" class="text-center">Nhân viên </th>
                      <th v-if="model.item_dt" scope="col" style="min-width: 120px" class="text-center">NCC </th>
                      <th v-if="model.item_dt" scope="col" style="min-width: 120px" class="text-center">Khách hàng </th>
                      <th v-if="model.item_dt" scope="col" style="min-width: 120px" class="text-center">Đối tác </th>

                      <!--                      <th scope="col" style="min-width: 80px" class="text-center">Quỹ </th>-->
                      <th scope="col" style="min-width: 95px" class="text-center">Khoản thu </th>
                      <!-- Lĩnh vực thu -->
                      <th scope="col" style="min-width: 95px" class="text-center">Lĩnh vực thu </th>
                      <th scope="col" style="min-width: 95px" class="text-center">Khoản chi </th>
                      <!-- Lĩnh vực chi -->
                      <th scope="col" style="min-width: 95px" class="text-center">Lĩnh vực chi </th>
                      <!--Nguồn vốn -->
                      <th v-if="model.item_nv" scope="col" style="min-width: 95px" class="text-center">Cấp NS </th>
                      <th v-if="model.item_nv" scope="col" style="min-width: 80px" class="text-center">Kỳ NS </th>
                      <th v-if="model.item_nv" scope="col" style="min-width: 80px" class="text-center">Năm NS </th>
                      <th v-if="model.item_nv" scope="col" style="min-width: 80px" class="text-center">Nguồn vốn </th>
                      <th v-if="model.item_nv" scope="col" style="min-width: 80px" class="text-center">Cấp phát </th>
                      <th v-if="model.item_nv" scope="col" style="min-width: 95px" class="text-center">Nhận bằng </th>

                      <!--MLNS -->
                      <th v-if="model.item_mlns" scope="col" style="min-width: 85px" class="text-center">Chương </th>
                      <th v-if="model.item_mlns" scope="col" style="min-width: 85px" class="text-center">Loại/khoản </th>
                      <th v-if="model.item_mlns" scope="col" style="min-width: 85px" class="text-center border-right-0">Mục/tm </th>
                      <th style="border-right: 1px solid #c8ced3; border-left: 0px; max-width: 1px !important; width: 1px !important; margin: 0px !important; padding: 0px !important; z-index: 999;" class="b-table-sticky-column-right"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="SbpmakeplanTrans[key].Show" v-for="(field, key) in SbpmakeplanTrans">
                      <td style="border-left: 1px solid #c8ced3; border-right: 0px; max-width: 1px !important; width: 1px !important; margin: 0px !important; padding: 0px !important;" class="bg-tree-tr b-table-sticky-column"></td>
                      <td class="has-padding field-task-name">
                        <span :id="'button-2' + key" class="text-left">{{getTitle(field.AutoactID)}}</span>
                        <b-tooltip :target="'button-2' + key" placement="left">
                          {{getTitle(field.AutoactID)}}
                        </b-tooltip>
                      </td>
                      <td v-if="model.item_tkhn">
                        <span class="text-left" :title="field.AccountName">{{field.AccountNo}}</span>
                      </td>
                      <!--                      <td v-if="model.item_tkhn">-->
                      <!--                        <span class="text-left">{{field.CoAccountNo}}</span>-->
                      <!--                      </td>-->
                      <!-- Định mức chỉ tiêu -->
                      <td>{{field.NormTableItemNo}}</td>
                      <td class="text-left">
                        <span :title="field.NormAllotLevelName">{{field.NormAllotLevelNo}}</span>
                      </td>
                      <td class="border-right-0">{{field.NormNo}}</td>
                      <!-- Nội dung -->
                      <td class="bg-tree-tr pl-0 b-table-sticky-column" style="background: #fff">
                        <span class="bg-tree-dot p-0" :style="{'left': (level * 12) + 'px', top: 0}" v-for="level in SbpmakeplanTrans[key].Level"></span>
                        <div class="bg-tree-content bg-tree-td"
                             :style="{'margin-left': (SbpmakeplanTrans[key].Level * 12 - 12) + 'px', width: 'calc(100% - ' + (SbpmakeplanTrans[key].Level * 12 - 12) + 'px)'}" style="position: absolute; top: 0; bottom: 0">
                          <span style="padding-left: 20px" :title="SbpmakeplanTrans[key].Description">{{SbpmakeplanTrans[key].Description}}</span>
                          <i class="fa fa-minus-square-o bg-tree-icon-action" v-if="!SbpmakeplanTrans[key].Detail" @click="onToggleChildNodes($event, SbpmakeplanTrans[key])"></i>
                        </div>

                      </td>
                      <!--Tiền tệ -->
                      <td v-if="model.item_tt">
                        <span class="text-center">{{field.CcyName}}</span>
                      </td>
                      <td v-if="model.item_tt">
                        <span class="text-center">{{field.ExchangeRate}}</span>
                      </td>
                      <!-- Số dự toán -->
                      <td>
                        <span class="text-right">{{field.FCPlanAmount | convertNumberToText}}</span>
                      </td>
                      <td v-if="model.item_tt">
                        <span class="text-right">{{field.LCPlanAmount | convertNumberToText}}</span>
                      </td>
                      <!-- Số đã TH -->
                      <td>
                        <span class="text-right">{{field.FCExecAmount | convertNumberToText}}</span>
                      </td>
                      <td v-if="model.item_tt">
                        <span class="text-right">{{field.LCExecAmount | convertNumberToText}}</span>
                      </td>
                      <!--Tài sản/Vật tư -->
                      <td v-if="model.item_ts">
                        <span v-if="check_ts == 1" class="text-center" :title="field.ItemName">{{field.ItemNo}}</span>
                        <span v-if="check_ts == 2" class="text-center" :title="field.FixedAssetName">{{field.FixedAssetNo}}</span>
                        <span v-if="check_ts == 3" class="text-center" :title="field.ToolName">{{field.ToolNo}}</span>
                        <span v-if="check_ts == 4" class="text-center" :title="field.InvestAssetName">{{field.InvestAssetNo}}</span>
                      </td>
                      <td>
                        <span class="text-left">{{field.UomName}}</span>
                      </td>
                      <td class="text-right">
                        <span class="text-right">{{field.Quantity}}</span>
                      </td>
                      <td>
                        <span class="text-right">{{field.FCUnitPrice | convertNumberToText}}</span>
                      </td>
                      <!-- Số tiền -->
                      <td>
                        <span class="text-right">{{field.FCDebitAmount | convertNumberToText}}</span>
                      </td>
                      <td v-if="model.item_tt">
                        <span class="text-right">{{field.LCDebitAmount | convertNumberToText}}</span>
                      </td>

                      <td>
                        <span :title="field.InTransTypeName">{{field.InTransTypeName}}</span>
                      </td>
                      <!--Hóa đơn -->
                      <td v-if="model.item_hd">
                        <span class="text-right">{{field.FCTaxAmount | convertNumberToText}}</span>
                      </td>
                      <td v-if="model.item_hd">
                        <span class="text-center">{{field.TaxRate}}</span>
                      </td>
                      <td v-if="model.item_hd">
                        <span class="text-center">{{field.InvoiceNo}}</span>
                      </td>
                      <td v-if="model.item_hd">
                        <span class="text-center">{{field.InvoiceDate | convertServerDateToClientDate}}</span>
                      </td>
                      <td v-if="model.item_hd">
                        <span class="text-center">{{field.InvoiceSerialNo}}</span>
                      </td>
                      <td v-if="model.item_hd">
                        <span class="text-center">{{field.InvoiceFormNo}}</span>
                      </td>
                      <td v-if="model.item_hd">
                        <span class="text-center">{{field.InvoiceSecurityCode}}</span>
                      </td>
                      <td v-if="model.item_hd">
                        {{field.InvoiceLookupAddress}}
                      </td>
                      <!--Dự án -->
                      <td v-if="model.item_da">
                        <span class="text-center" :title="field.ProjectName">{{field.ProjectNo}}</span>
                      </td>
                      <td v-if="model.item_da">
                        <span class="text-center" :title="field.ProgramName">{{field.ProgramNo}}</span>
                      </td>
                      <td v-if="model.item_da">
                        <span class="text-center" :title="field.ContractName">{{field.ContractNo}}</span>
                      </td>
                      <!--Đối tượng -->
                      <td v-if="model.item_dt">
                        <span class="text-left">{{field.InTransTypeName}}</span>
                      </td>
                      <td v-if="model.item_dt">
                        <span class="text-center">{{field.EmployeeNo}}</span>
                      </td>
                      <td v-if="model.item_dt">
                        <span class="text-center" :title="field.VendorName">{{field.VendorNo}}</span>
                      </td>
                      <td v-if="model.item_dt">
                        <span class="text-center" :title="field.CustomerName">{{field.CustomerNo}}</span>
                      </td>
                      <td v-if="model.item_dt">
                        <span class="text-left">{{field.FullName}}</span>
                      </td>

                      <!--                      <td>-->
                      <!--                        <span class="text-center">{{field.FundNo}}</span>-->
                      <!--                      </td>-->
                      <td>
                        <span class="text-left" :title="field.RevenueName">{{field.RevenueNo}}</span>
                      </td>
                      <!-- Lĩnh vực thu -->
                      <td>
                        <span class="text-left" :title="field.SbrSectorName">{{field.SbrSectorNo}}</span>
                      </td>
                      <td>
                        <span class="text-left" :title="field.ExpenseName">{{field.ExpenseNo}}</span>
                      </td>
                      <!-- Lĩnh vực chi -->
                      <td>
                        <span class="text-left" :title="field.SectorName">{{field.SectorNo}}</span>
                      </td>
                      <!--Nguồn vốn -->
                      <td v-if="model.item_nv">
                        <span class="text-left">{{BudgetLevel[field.BudgetLevel]}}</span>
                      </td>
                      <td v-if="model.item_nv">
                        <span class="text-left">{{FiscalPeriod[field.FiscalPeriod]}}</span>
                      </td>
                      <td v-if="model.item_nv">
                        <span class="text-center">{{field.FiscalYear}}</span>
                      </td>
                      <td v-if="model.item_nv">
                        <span class="text-left" :title="field.CapitalName">{{field.CapitalNo}}</span>
                      </td>
                      <td v-if="model.item_nv">
                        <span class="text-left" :title="BudgetAllocType[field.BudgetAllocTypeID]">{{BudgetAllocType[field.BudgetAllocTypeID]}}</span>
                      </td>
                      <td v-if="model.item_nv">
                        <span class="text-left" :title="ReceiveBy[field.ReceiveBy]">{{ReceiveBy[field.ReceiveBy]}}</span>
                      </td>
                      <!--MLNS -->
                      <td v-if="model.item_mlns">
                        <span class="text-left">{{field.SbiChapterNo}}</span>
                      </td>
                      <td v-if="model.item_mlns">
                        <span class="text-left">{{field.SbiCategoryNo}}</span>
                      </td>
                      <td v-if="model.item_mlns" class="border-right-0">
                        <span class="text-left">{{field.SbiItemNo}}</span>
                      </td>
                      <td style="border-right: 1px solid #c8ced3; border-left: 0px; max-width: 1px !important; margin: 0px !important; padding: 0px !important;" class="b-table-sticky-column-right"></td>
                    </tr>
                    </tbody>
                  </table>

                </div>
                <div class="form-group row" style="margin-top: 20px;">
                  <!--                    <label class="col-md-3" for="Comment">Ghi chú</label>-->
                  <div class="col-md-15" style="padding-right: 0px !important;">
                    {{model.Comment}}
                  </div>
                  <div  class="col-md-1">
                  </div>
                  <div class="col-md-8 row">
                    <label class="row" style="width: 100%"><span class="col-md-9">Tổng số tiền:</span> <span class="col-md-15 text-right" style="font-weight: bold;"> <span v-if="this.model.LCTotalAmount" id="totalFCDebit">{{model.LCTotalAmount | convertNumberToText}}</span> đ</span></label>
                    <label class="row" style="width: 100%"><span class="col-md-9">Tiền thuế:</span> <span class="col-md-15 text-right" style="font-weight: bold;"> <span v-if="this.model.LCTotalTaxAmount" id="totalFCCredit">{{model.LCTotalTaxAmount | convertNumberToText}}</span> đ</span></label>
                  </div>

                </div>
                <div class="row">
                  <div class="col-24">
                    <chat-category-comment :group-name="model.Comment" :category-key="{actgvouctrans: idParams}" category="actgvouctrans" :category-i-d="idParams"></chat-category-comment>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      <!-- Popup Đối tượng -->
      <b-modal ref="onToggleModalObject" id="modal-form-input-task-general" size="xs"
               title="Đối tượng">
        <div class="main-body pb-5 pt-10" style="height: 178px;" >
<!--          <div class="td-select2">-->
<!--            <Select2 v-model="model.arrObject" :settings="{multiple: true, placeholder: 'Chọn đối tượng', closeOnSelect: false, allowClear: true}" :options="optionsObject" >-->
<!--            </Select2>-->
<!--          </div>-->
          <b-form-checkbox type="check" v-model="model.item_dt_cqnn" class="form-control">Cơ quan Nhà nước</b-form-checkbox>
          <b-form-checkbox type="check" v-model="model.item_dt_nv" class="form-control">Nhân viên</b-form-checkbox>
          <b-form-checkbox type="check" v-model="model.item_dt_ncc" class="form-control">Nhà cung cấp</b-form-checkbox>
          <b-form-checkbox type="check" v-model="model.item_dt_kh" class="form-control">Khách hàng</b-form-checkbox>
          <b-form-checkbox type="check" v-model="model.item_dt_dt" class="form-control">Đối tác</b-form-checkbox>
        </div>
        <template v-slot:modal-footer>
          <div class="w-100 left">
            <b-button variant="primary" size="md" class="float-left mr-2" @click="onSaveObject()" >
              Chọn
            </b-button>
            <b-button variant="primary" size="md" class="float-left mr-2" @click="onSaveObject()" >
              Hủy
            </b-button>
          </div>
        </template>
      </b-modal>
      <!-- Popup tài sản/vật tư -->
      <b-modal ref="onToggleModalProperty" id="modal-form-input-task-general1" size="xs"
               title="Tài sản/Vật tư">
        <div class="main-body pb-5 pt-10" style="height: 115px; margin-left: 12px;">
          <div class="row">
            <b-form-radio-group
              id="member-radio"
              v-model="model.OptionsProperty"
              :plain="true"
              :options="[
              {text: 'Vật tư HHDV',value: 1},
              {text: 'Tài sản cố định',value: 2},
              {text: 'Công cụ dụng cụ',value: 3},
              {text: 'Tài sản đầu tư',value: 4}
            ]"
              :checked="3">
            </b-form-radio-group>
          </div>
        </div>
        <template v-slot:modal-footer>
          <div class="w-100 left">
            <b-button variant="primary" size="md" class="float-left mr-2" @click="onSaveProperty()" >
              Chọn
            </b-button>
            <b-button variant="primary" size="md" class="float-left mr-2" @click="onCancelProperty()" >
              Hủy
            </b-button>
          </div>
        </template>
      </b-modal>

    </div>
</template>

<script>
    import ApiService from '@/services/api.service';
    import Swal from 'sweetalert2';
    import 'sweetalert2/src/sweetalert2.scss';
    import IjcoreModalMultiAct from "../../../components/IjcoreModalMultiAct";
    import ChatCategoryComment from "../../apps/chat/partials/ChatCategoryComment";
    import ColumnResizer from "column-resizer";

    const ListRouter = 'statebudgetplanning-sbpestimateplan';
    const EditRouter = 'statebudgetplanning-sbpestimateplan-edit';
    const CreateRouter = 'statebudgetplanning-sbpestimateplan-create';
    const ViewApi = 'state-budget-planning/api/sbpestimateplan/view';
    const ListApi = 'state-budget-planning/api/sbpestimateplan';
    const DeleteApi = 'state-budget-planning/api/sbpestimateplan/delete';
    const CompanyType = {
        1: 'Đơn vị tổng hợp',
        2: 'Đơn vị trực thuộc',
        3: 'Đơn vị liên kết',
        4: 'Phòng ban',
    };
    const AutoactType = {
      1: 'Dự toán chi thường xuyên được cấp đầu năm',
      2: 'Rút tiền gửi ngân hàng về nhập quỹ tiền mặt',
      3: 'Rút tạm ứng dự toán về nhập quỹ- nguồn chi thường xuyên- giao tự chủ, giao khoán',
      4: 'Chi phí trực tiếp từ quỹ tiền mặt',
      5: 'Chi phí trực tiếp từ quỹ tiền mặt- đồng thời',
      6: 'Hoàn tạm ứng NSNN- chi thường xuyên',
      7: 'Thu phí, lệ phí bằng tiền mặt',
      8: 'Nhà tài trợ chuyển tiền về tài khoản tiền gửi tạm ứng (TK đặc biệt) do đơn vị làm chủ TK',
      9: 'Thanh toán cho nhà cung cấp bằng chuyển khoản nguồn viện trợ, vay nợ nước ngoài',
      10: 'Mua TSCĐ băng chuyển khoản về đưa ngay vào sử dụng, không phải qua lắp đặt, chạy thử',
      11: 'Nộp thuế GTGT cho nhà thầu',
      12: 'Quyết toán nguồn chi thường xuyên',
      13: 'Hủy nguồn chi thường xuyên năm nay',
      14: 'Chuyển nguồn',
    };
    const PeriodType = {
      1: 'Năm',
      2: 'Quý',
      3: 'Tháng',
      4: 'Tuần',
      5: 'Ngày',
      6: '6 tháng đầu',
      7: '6 tháng cuối',
      8: '3 năm',
      9: '5 năm',
      10: '10 năm',
      99: 'Tùy chọn',
    };

    export default {
        name: 'accounting-view-item',
        data () {
            return {
                StyleAction: 'width: 1800px;',
                idParams: this.$route.params.id,
                reqParams: this.$route.params.req,
                model: {
                    TransNo: '',
                    TransDate: '',
                    PostDate: '',
                    eTransNo: '',
                    eTransDate: '',
                    TransTypeID: '',
                    TransTypeName: '',
                    CoaTypeID: 4,
                    CoaTypeNo: '',
                    CoaTypeName: '',
                    Comment: '',
                    FCTotalDebitAmount: '',
                    LCTotalDebitAmount: '',
                    FCTotalCreditAmount: '',
                    LCTotalCreditAmount: '',
                    FCTotalTaxAmount: '',
                    LCTotalTaxAmount: '',
                    CompanyID: '',
                    CompanyNo: '',
                    CompanyMOFNo: '',
                    CompanyLocationNo: '',
                    CompanyName: '',
                    CompanyAddress: '',
                    CompanyBankAccount: '',
                    CompanyBankName: '',
                    CompanyContactName: '',
                    CompanyContactPosition: '',
                    ParentCompanyID: '',
                    ParentCompanyNo: '',
                    ParentCompanyMOFNo: '',
                    ParentCompanyLocationNo: '',
                    ParentCompanyName: '',
                    ParentCompanyAddress: '',
                    ParentCompanyBankAccount: '',
                    ParentCompanyBankName: '',
                    ParentCompanyContactName: '',
                    ParentCompanyContactPosition: '',
                    NormTableName: '',
                    PeriodID: '',
                    PeriodType: '',
                    PeriodName: '',
                    PeriodFromDate: '',
                    PeriodToDate: '',

                    AutoactName: '',
                    companyOption: [],
                    employeeOption: [],
                    ActgvoucTransItem: [],
                    AccoutingStatus: [],
                    Coatype: [],
                    ArrCoaChecked: [],
                    StatusValue: 3,
                    maxLineID: 0,
                    item_tt: false,
                    item_ts: false,
                    item_hd: false,
                    item_da: false,
                    item_dt: false,
                    item_nv: true,
                    item_mlns: true,
                    item_httk: false,
                    item_hcsn: false,
                    item_bqlda: false,
                    item_tkhn: true,
                    item_kbnn: false,
                    item_dt_cqnn: false,
                    item_dt_nv: false,
                    item_dt_ncc: false,
                    item_dt_kh: false,
                    item_dt_dt: false,
                    InputCompanyID: null
                },
                SbpmakeplanTrans: [],
                Actautoact: [],
                defaultModel: {},
                totalFCDebit: 0,
                totalFCCredit: 0,
                AutoactType: [],
                check_ts: 0,
                item_ts_name: 'Vật tư/Tài sản',
                stage: {
                  updatedData: false,
                  message: (this.$route.params.message) ? this.$route.params.message : ''
                },
              optionsObject: [
                { id: 1, text: 'Cơ quan Nhà nước' },
                { id: 2, text: 'Nhân viên' },
                { id: 3, text: 'Nhà cung cấp' },
                { id: 4, text: 'Khách hàng' },
                { id: 5, text: 'Đối tác' },
              ],
              FiscalPeriod: {
                1:  'Năm trước',
                2:  'Năm nay',
                3:  'Năm sau',
              },
              BudgetLevel: {
                0:  'Chưa xác định',
                1:  'Trung ương',
                2:  'Tỉnh',
                3:  'Huyện',
                4:  'Xã',
              },
              BudgetAllocType: {
                1:  'Dự toán',
                2:  'Lệnh chi tiền',
                3:  'Ghi thu, ghi chi',
                4:  'Ủy quyền',
                5:  'Gán thu bù chi',
                6:  'Hiện vật',
                7:  'Ngày công',
                9:  'Khác',
              },
              ReceiveBy: {
                1:  'Tiền mặt',
                2:  'Hiện vật',
                3:  'Ngày công',
                4:  'Khác',
              },
            }

        },

        components: {
          IjcoreModalMultiAct,
          ChatCategoryComment
        },
        beforeCreate() {

            this.$router.onReady(() => {
                if (!this.$route.params.id) {
                    this.$router.push({name: ListRouter});
                }
            });
        },
        created(){
        },
        beforeMount(){},
        mounted() {
            this.fetchData();
          // hiển thị thông báo
          if (this.stage.message && this.stage.message !== '') {
            this.$bvToast.toast(this.stage.message, {
              title: 'Thông báo',
              variant: 'success',
              solid: true
            });
          }
        },
        updated() {
            this.stage.updatedData = true;
        },

        computed: {
            itemNo(){
                if (!this.idParams) return;
                let index = 0;
                index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
                return index;
            },
            itemTotalPerPage(){
                if (!this.idParams) return;
                return this.reqParams.idsArray.length + this.reqParams.perPage * (this.reqParams.currentPage - 1);
            },
            itemTotal(){
                if (!this.idParams) return;
                return this.reqParams.total;

            },
        },
        methods: {
            fetchData() {
                if (document.querySelector('.table-column-resizable')) {
                  new ColumnResizer(
                    document.querySelector('.table-column-resizable'),{resizeMode: 'overflow'}
                  );
                }
                if (this.idParams == 0 || _.isUndefined(this.idParams)) {
                    return false;
                }
                let self = this;
                let urlApi = '';
                let requestData = {
                    method: 'get',
                };
                // Api edit user
                if(this.idParams){
                    urlApi = ViewApi + '/' + this.idParams;
                    let data = {
                        id: this.idParams
                    };
                    requestData.data = data;
                }
                requestData.url = urlApi;
                this.$store.commit('isLoading', true);
                ApiService.setHeader();
                ApiService.customRequest(requestData).then((responses) => {
                    let responsesData = responses.data; //console.log(responses.data);
                    self.defaultModel = responsesData;
                    if (responsesData.status === 1) {
                        self.model.TransID = responsesData.data.data.TransID;
                        self.model.TransNo = responsesData.data.data.TransNo;
                        self.model.TransDate = responsesData.data.data.TransDate;
                        self.model.PostDate = responsesData.data.data.PostDate;
                        self.model.eTransNo = responsesData.data.data.eTransNo;
                        self.model.eTransDate = responsesData.data.data.eTransDate;
                        self.model.TransTypeID = responsesData.data.data.TransTypeID;
                        self.model.CoaTypeID = responsesData.data.data.CoaTypeID;
                        self.model.CoaTypeNo = responsesData.data.data.CoaTypeNo;
                        self.model.CoaTypeName = responsesData.data.data.CoaTypeName;
                        self.model.StatusDescription = responsesData.data.data.StatusDescription;
                        self.model.CompanyID = responsesData.data.data.CompanyID;
                        self.model.CompanyName = responsesData.data.data.CompanyName;
                        self.model.CompanyNo = responsesData.data.data.CompanyNo;
                        self.model.CompanyAddress = responsesData.data.data.CompanyAddress;
                        self.model.CompanyBankAccount = responsesData.data.data.CompanyBankAccount;
                        self.model.CompanyBankName = responsesData.data.data.CompanyBankName;
                        self.model.CompanyContactName = responsesData.data.data.CompanyContactName;
                        self.model.CompanyContactPosition = responsesData.data.data.CompanyContactPosition;
                        self.model.ParentCompanyID = responsesData.data.data.ParentCompanyID;
                        self.model.ParentCompanyName = responsesData.data.data.ParentCompanyName;
                        self.model.ParentCompanyNo = responsesData.data.data.ParentCompanyNo;
                        self.model.ParentCompanyAddress = responsesData.data.data.ParentCompanyAddress;
                        self.model.ParentCompanyBankAccount = responsesData.data.data.ParentCompanyBankAccount;
                        self.model.ParentCompanyBankName = responsesData.data.data.ParentCompanyBankName;
                        self.model.ParentCompanyContactName = responsesData.data.data.ParentCompanyContactName;
                        self.model.ParentCompanyContactPosition = responsesData.data.data.ParentCompanyContactPosition;
                        self.model.NormTableName = responsesData.data.data.NormTableName;
                        self.model.PeriodID = responsesData.data.data.PeriodID;
                        self.model.PeriodType = responsesData.data.data.PeriodType;
                        self.model.PeriodName = responsesData.data.data.PeriodName;
                        //self.model.PeriodIDName = (responsesData.data.data.PeriodType && PeriodType[responsesData.data.data.PeriodType]) ? PeriodType[responsesData.data.data.PeriodType] : '';
                        self.model.PeriodFromDate = responsesData.data.data.PeriodFromDate;
                        self.model.PeriodToDate = responsesData.data.data.PeriodToDate;

                        self.model.FCTotalDebitAmount = responsesData.data.data.FCTotalDebitAmount;
                        self.model.Comment = responsesData.data.data.Comment;
                        self.model.FCTotalAmount = responsesData.data.data.FCTotalAmount;
                        self.model.LCTotalAmount = responsesData.data.data.LCTotalAmount;
                        self.model.FCTotalTaxAmount = responsesData.data.data.FCTotalTaxAmount;
                        self.model.LCTotalTaxAmount = responsesData.data.data.LCTotalTaxAmount;
                        self.model.Posted = responsesData.data.data.Posted;
                        self.model.DirectionID = responsesData.data.data.DirectionID;
                        self.model.DirectionNo = responsesData.data.data.DirectionNo;
                        self.model.DirectionName = responsesData.data.data.DirectionName;
                        self.model.isFinalTrans = (responsesData.data.data.isFinalTrans) ? true : false;
                        self.model.isAdjustTrans = (responsesData.data.data.isAdjustTrans) ? true : false;
                        self.model.isDebtTrans = (responsesData.data.data.isDebtTrans) ? true : false;
                        self.model.InputCompanyID = responsesData.data.data.InputCompanyID;
                        self.model.InputCompanyNo = responsesData.data.data.InputCompanyNo;
                        self.model.InputCompanyName = responsesData.data.data.InputCompanyName;

                        self.SbpmakeplanTrans = responsesData.data.SbpmakeplanTrans;
                      _.forEach(self.SbpmakeplanTrans, function (item, key) {
                        self.$set(self.SbpmakeplanTrans[key], 'Show', true);
                      });
                        self.Actautoact = responsesData.data.Actautoact;
                        //self.SbpmakeplanTrans.AutoactID = (responsesData.data.SbpmakeplanTrans.AutoactID && AutoactType[responsesData.data.SbpmakeplanTrans.AutoactID]) ? AutoactType[responsesData.data.SbpmakeplanTrans.AutoactID] : '';
                        self.model.ArrCoaChecked = responsesData.data.data.ArrCoaChecked;
                        _.forEach(JSON.parse(self.model.ArrCoaChecked),function(item,key){
                          //console.log(item)
                          if(item !== undefined){
                            switch(item){
                              case 1:
                                self.model.item_tkhn = true;
                                break;
                              case 2:
                                self.model.item_kbnn = true;
                                break;
                              case 3:
                                // Ngân hàng nhà nước
                                break;
                              case 4:
                                self.model.item_hcsn = true;
                                break;
                              case 5:
                                self.model.item_bqlda  = true;
                                break;
                              default:
                                console.log(123);
                                break;

                            }
                          }
                        });
                      //Check CoaTypeName
                      let check_ct = self.model.CoaTypeID;
                      if(check_ct){
                        switch(check_ct){
                          case 1:
                            this.model.CoaTypeName = 'TKHN';
                            break;
                          case 2:
                            this.model.CoaTypeName = 'TK Tabmis';
                            break;
                          case 3:
                            this.model.CoaTypeName = 'TKQG';
                            break;
                          case 4:
                            this.model.CoaTypeName = 'TK HCSN';
                            break;
                          case 5:
                            this.model.CoaTypeName = 'TK BQLDA';
                            break;
                          case 6:
                            this.model.CoaTypeName = 'TK Xã phường';
                            break;
                          default:
                            this.model.CoaTypeName = 'TKHN';
                            break;
                        }
                      }
                      //
                    }

                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                });
            },
            onNavigationItem(type) {
                let currentIndex = this.reqParams.idsArray.indexOf(this.idParams);
                let newIndex = (type === 'prev') ? currentIndex - 1 : currentIndex + 1;

                if (newIndex === (this.reqParams.idsArray.length) && (this.reqParams.currentPage != this.reqParams.lastPage)) {
                    this.reqParams.currentPage = this.reqParams.currentPage + 1;
                    this.getItemIds(type);
                } else if (newIndex < 0 && this.reqParams.currentPage > 1){
                    this.reqParams.currentPage = this.reqParams.currentPage - 1;
                    this.getItemIds(type);
                }
                else {
                    this.idParams = (this.reqParams.idsArray[newIndex]) ? this.reqParams.idsArray[newIndex] : this.idParams;
                }
            },
            onEditClicked(){
                this.$router.push({
                    name: EditRouter,
                    params: {id: this.idParams, req: this.reqParams}
                });
            },
            onCreateClicked(){
                this.$router.push({name: CreateRouter});
            },
            onBackToList(message = '') {
                if (_.isString(message)) {
                    this.$router.push({name: ListRouter, params: {message: message}});
                } else {
                    this.$router.push({name: ListRouter});
                }

            },
            handleCopyItem(){
                this.$router.push({name: CreateRouter, params: {itemCopy: this.defaultModel}});
            },
            handleDeleteItem() {
                let self = this;
                let title = 'Bạn có muốn xóa bản ghi?';
                Swal.fire({
                    title: title,
                    text: 'Bạn sẽ không thể khôi phục thông tin bản ghi!',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Hủy bỏ'
                }).then((result) => {
                    if (result.value) {
                        let requestData = {
                            method: 'post',
                            url: DeleteApi + '/' + self.idParams,
                            data: {
                                array_id: [self.idParams],
                            },
                        };

                        ApiService.setHeader();
                        ApiService.customRequest(requestData).then((response) => {
                            let responseData = response.data;
                            if (responseData.status === 1) {
                                self.onBackToList('Bản ghi đã được xóa');
                            } else {
                                Swal.fire(
                                    'Có lỗi',
                                    '',
                                    'error'
                                );
                                console.log(response);
                            }
                        }, (error) => {
                            console.log(error);

                        });

                    }
                });
            },
          addLineHttk(link) {
            // let self = this;
            // link.map(function (item, key) {
            //   self.HTTK.push({
            //     CoaTypeID: item.CoaTypeID,
            //     CoaTypeName: item.CoaTypeName,
            //     addnew: true,
            //   });
            // });

            let linkReset = link.filter(function (val) {
              return val
            });
            let self = this;
            let count_httk = 0;
            // console.log(typeof(link))
            // console.log(link)
            this.model.item_hcsn = false;
            this.model.item_bqlda  = false;
            this.model.item_tkhn  = false;
            this.model.item_kbnn = false;
            _.forEach(link,function(item,key){ count_httk = count_httk + 1;
              if(item !== undefined){
                switch(item.CoaTypeName){
                  case 'Hợp nhất':
                    self.model.item_tkhn = true;
                    break;
                  case 'KBNN':
                    self.model.item_kbnn = true;
                    break;
                  case 'Đơn vị HCSN':
                    self.model.item_hcsn = true;
                    break;
                  case 'Ban QLDA':
                    self.model.item_bqlda  = true;
                    break;
                  default:
                    console.log(123);
                    break;

                }
              }
            });

            _.forEach(linkReset, function (item, key) {
              // let indexExist = _.findIndex(self.HTTK, ['CoaTypeID', item.CoaTypeID]);
              // if (indexExist < 0) {
              //   self.HTTK.push({
              //     CoaTypeID: item.CoaTypeID,
              //     CoaTypeName: item.CoaTypeName,
              //     Ishttk: 1,
              //     addnew: true,
              //   });
              //   let indexNew = _.findIndex(self.HTTK, ['CoaTypeID', item.CoaTypeID]);
              //   if (indexNew > -1) {
              //     self.HTTK[indexNew].PersonAssign = ['1'];
              //     self.HTTK[indexNew].Description = 'HCSN';
              //   }
              // }
              if(item.CoaTypeID==2){
                this.model.item_httk = true;
              }
            });

            this.$forceUpdate();
          },
            onToggleModalProperty(){
              this.$refs['onToggleModalProperty'].show();
              this.$forceUpdate();
            },
            onToggleModalObject(){
              this.$forceUpdate();
              this.$refs['onToggleModalObject'].show();
            },
            handleExportPrint() {
              // Todo: handle export print
              let request = {};
              request.id = this.idParams;
              request.exportData = true;
              this.$router.push({
                name: 'statebudgetplanning-sbpmakeplan-report-detail',
                query: request
              });
            },
            handleExportReport(){
              let request = {};
              request.id = this.idParams;
              request.exportData = true;
              this.$router.push({
                name: 'statebudgetplanning-sbpmakeplan-report-accounting',
                query: request
              });
            },
            updateModel() {
                if (this.stage.updatedData) {
                    this.$forceUpdate();
                }
            },
          onSaveObject() {
            // this.$bvToast.toast('Đóng loại đối tượng\n', {
            //   title: 'Thông báo',
            //   variant: 'success',
            //   solid: true
            // });
            // _.forEach(this.model.arrObject, function (value, key) {
            //   //let personAssign = _.find(self.optionsObject, ['id', value]);
            //   alert(this.value);
            // });

            // this.model.item_dt_cqnn = false;
            // this.model.item_dt_nv = false;
            // this.model.item_dt_ncc = false;
            // this.model.item_dt_kh = false;
            // this.model.item_dt_dt = false;
            // let self = this;
            // _.forEach(this.model.arrObject, function(item, key){
            //   if(item){
            //     switch (item) {
            //       case '1':
            //         self.model.item_dt_cqnn = true;
            //         break;
            //       case '2':
            //         self.model.item_dt_nv = true;
            //         break;
            //       case '3':
            //         self.model.item_dt_ncc = true;
            //         break;
            //       case '4':
            //         self.model.item_dt_kh = true;
            //         break;
            //       default:
            //         self.model.item_dt_dt = true;
            //         break;
            //     }
            //   }
            // })

            this.$refs['onToggleModalObject'].hide();
          },
          onSaveProperty() {
            let check_ts = this.model.OptionsProperty;
            if(check_ts){
              switch(check_ts){
                case 1:
                  this.check_ts = 1;
                  this.item_ts_name = 'Vật tư HHDV';
                  break;
                case 2:
                  this.check_ts = 2;
                  this.item_ts_name = 'Tài sản cố định';
                  break;
                case 3:
                  this.check_ts = 3;
                  this.item_ts_name = 'Công cụ dụng cụ';
                  break;
                case 4:
                  this.check_ts = 4;
                  this.item_ts_name  = 'Tài sản đầu tư';
                  break;
                default:
                  this.check_ts = 0;
                  this.item_ts_name = 'Vật tư/Tài sản';
                  break;
              }
            }
            this.model.item_ts = true;
            this.model.OptionsProperty = this.model.OptionsProperty;
            this.$refs['onToggleModalProperty'].hide();
            this.$forceUpdate();
          },
          onCancelProperty(){
            this.model.item_ts = false;
            this.model.OptionsProperty ='';
            this.$refs['onToggleModalProperty'].hide();
            this.$forceUpdate();
          },
          getTitle(AutoactID) {
            // return _.find(AutoactType, function (item, key) {
            //   return Number(key) === value;
            // })
            let AutoactObj = _.find(this.Actautoact, ['AutoactID', AutoactID]);
            if (_.isObject(AutoactObj)) return AutoactObj.AutoactName;
            return '';
          },
          //Ghi sổ
          actbook(TransID) {
            this.$store.commit('isLoading', true);
            let self = this;
            const requestData = {
              method: 'post',
              url: 'state-budget-planning/api/sbpestimateplan/actbook',
              data: {
                TransID: this.idParams,
                Posted: this.model.Posted,
                PostType: 1,
                CoaTypeID: this.model.CoaTypeID,
                CoaTypeNo: this.model.CoaTypeNo,
                CoaTypeName: this.model.CoaTypeName,
                TransDate: this.model.TransDate,
                PostDate: this.model.PostDate,
                eTransDate: this.model.eTransDate,
                TransNo: this.model.TransNo,
                eTransNo: this.model.eTransNo,
                TransTypeID: this.model.TransTypeID,
                TransTypeName: this.model.TransTypeName,
                FCTotalAmount: this.model.FCTotalAmount,
                LCTotalAmount: this.model.LCTotalAmount,
                FCTotalTaxAmount: this.model.FCTotalTaxAmount,
                LCTotalTaxAmount: this.model.LCTotalTaxAmount,
                CompanyID: this.model.CompanyID,
                CompanyNo: this.model.CompanyNo,
                CompanyName: this.model.CompanyName,
                PeriodID: this.model.PeriodID,
                PeriodType: this.model.PeriodType,
                PeriodName: this.model.PeriodName,
                PeriodFromDate: this.model.PeriodFromDate,
                PeriodToDate: this.model.PeriodToDate,
                DirectionID: this.model.DirectionID,
                DirectionNo: this.model.DirectionNo,
                DirectionName: this.model.DirectionName,
                InputCompanyID: this.model.InputCompanyID,
                InputCompanyNo: this.model.InputCompanyNo,
                InputCompanyName: this.model.InputCompanyName,
                detail: this.SbpmakeplanTrans
              }
            };
            ApiService.setHeader();
            ApiService.customRequest(requestData).then((responses) => {
              let responsesData = responses.data;
              if (responsesData.status === 1) {
                if (!self.idParams && responsesData.data) self.idParams = responsesData.data.param;
                if (responsesData.Posted === 1) {
                  this.$bvToast.toast('Đã ghi sổ thành công!', {
                    title: 'Thông báo',
                    variant: 'success',
                    solid: true
                  });
                }
                  if (responsesData.Posted === 0) {
                    this.$bvToast.toast('Đã bỏ ghi sổ thành công!', {
                      title: 'Thông báo',
                      variant: 'success',
                      solid: true
                    });
                  }
                //this.value.splice(key, 1);
                self.$store.commit('isLoading', false);
                this.model.Posted = responsesData.Posted;
                this.$forceUpdate();
              } else {
                let htmlErrors = __.renderErrorApiHtml(responsesData.data);
                Swal.fire(
                  'Thông báo',
                  htmlErrors,
                  'error'
                )
                self.$store.commit('isLoading', false);
              }

            }, (error) => {
              console.log(error);
              Swal.fire(
                'Thông báo',
                'Không kết nối được với máy chủ',
                'error'
              )
              self.$store.commit('isLoading', false);
            });
          },
          onToggleChildNodes(e, itemParent) {
            let self = this;
            if (e && e.target.classList.contains('fa-minus-square-o')) {
              // close children
              e.target.classList.remove('fa-minus-square-o');
              e.target.classList.add('fa-plus-square-o');
              let allChildTableItem = this.getAllChildTableItem(itemParent, this.SbpmakeplanTrans);
              if (allChildTableItem && allChildTableItem.length) {
                _.forEach(allChildTableItem, function (childTableItem, key) {
                  let indexItem = _.findIndex(self.SbpmakeplanTrans, ['LineID', childTableItem.LineID]);
                  if (indexItem > -1) {
                    self.SbpmakeplanTrans[indexItem].Show = false;
                  }
                });
              }
            } else {
              // open children
              e.target.classList.remove('fa-plus-square-o');
              e.target.classList.add('fa-minus-square-o');
              let allChildren = _.filter(this.SbpmakeplanTrans, ['ParentID', itemParent.LineID]);
              if (allChildren.length) {
                _.forEach(allChildren, function (childTableItem, key) {
                  let indexItem = _.findIndex(self.SbpmakeplanTrans, ['LineID', childTableItem.LineID]);
                  if (indexItem > -1) {
                    self.SbpmakeplanTrans[indexItem].Show = true;
                    $('#table-item-' + self.SbpmakeplanTrans[indexItem].LineID + ' .bg-tree-icon-action').removeClass('fa-minus-square-o');
                    $('#table-item-' + self.SbpmakeplanTrans[indexItem].LineID + ' .bg-tree-icon-action').addClass('fa-plus-square-o');
                  }
                });
              }
            }
          },
          getAllChildTableItem(item, tableItemArr){
            let self = this, listChild = [];
            let allChild = _.filter(tableItemArr, ['ParentID', item.LineID]);
            if (allChild.length) {
              allChild = _.orderBy(allChild, ['LineID'], ['asc']);
              _.forEach(allChild, function (value, key) {
                listChild.push(value);
                if (_.filter(tableItemArr, ['ParentID', value.LineID]).length) {
                  let recursiveArr = self.getAllChildTableItem(value, tableItemArr);
                  recursiveArr = _.orderBy(recursiveArr, ['LineID', 'asc']);
                  _.forEach(recursiveArr, function (recursive, key) {
                    listChild.push(recursive);
                  });
                }

              });
            }
            return listChild;
          },
        },
        watch: {
            idParams() {
                this.fetchData();
            }
        },
        // beforeDestroy(){
        //     window.removeEventListener('unload', this.onReloadPage)
        // }
    }
</script>
<style lang="css">
  .form-group{ margin-bottom: 0.5rem !important;}
  .tab-content{ border-bottom: none !important; border-right: none !important; border-left: none !important;}
  .nav-tabs .nav-link{ border-top: none !important; }
  .div-scroll-table {
    width: 100%;
    overflow-x: scroll;
    margin-right: 5em;
    overflow-y: visible;
    padding: 0;
  }
  .td-action-fix-right-form:last-child{
    border-bottom: 1px solid #c8ced3 !important;
    height: 34px;
  }
  .top-detail i { color: #00a2e8}
  .top-detail span:hover{ cursor: pointer}
  #bar_detail .form-control{ padding-right: 2px !important; padding-left: 9px !important; border:  none !important;}
  .card-header{ padding-top: 5px !important; padding-bottom: 5px !important; background: none !important;}
  .card-header .nav-tabs .nav-link{ color: #0b0e0f !important; padding: 0.55rem 0.625rem;}
  .card-header .nav-tabs .nav-link:hover{ text-decoration: underline;}
  .card-header .nav-tabs .nav-link.active{ font-weight: bold; text-decoration:underline; }
  .tab-content{ border: none !important;}
  .card-header .nav-tabs{ border: none !important;}
  .nav-tabs .nav-link{ border: none !important;}
  .table-bordered thead th, .table-bordered thead td {
    border-bottom-width: 1px !important;
  }
  .comments{ }
  .mx-3{ margin-right: 0px !important;}
  #bar_detail .new-row{ margin-top: 3px; font-weight: normal; font-size: 14px;}
  .td-select2 {
    width: 99% !important;
    max-width: 650px;
    height: 30px;
    overflow-y: hidden;
  }
  .td-select2 .select2-container .select2-selection--multiple{
    width: 100% !important;
  }
  .select2-container{ width: 100% !important;}
  .table-view tr th { z-index: 10 !important;}
  .table-view tr td span{ display: block; padding-right: 3px; padding-left: 3px;}
  #member-radio .form-check-inline{ display: block; margin-bottom: 5px;}
  .tooltip.b-tooltip { opacity: 1;}
  .custom-checkbox{ border: none !important;}
  .table-tree th{ background: #ffffff;}
  @media only screen
  and (min-device-width : 768px)
  and (max-device-width : 1024px)
  and (orientation:landscape)
  {
    .main-body-view-action .form-group { margin-bottom: 0px !important;}
    /*#bar_detail div div{ max-width: 130px !important;}*/
  }
  @media only screen
  and (min-device-width : 768px)
  and (max-device-width : 1024px)
  and (orientation:portrait)
  {
    .main-body-view-action .form-group { margin-bottom: 0px !important;}
    /*#bar_detail div div{ max-width: 130px !important;}*/
  }
  .tab-content .tab-pane{ padding-bottom: 0px !important;  padding-top: 10px !important;}
  .table-view th, .table-view td {
    padding: 0.3rem !important;
  }
  .top-trans .ijcore-element-search{ padding-right: 0.5rem !important;}
  .bg-tree-tr{
    position: relative;
  }
  .bg-tree-content {
    display: flex;
    align-items: center;
    position: relative;
  }
  .bg-tree-content input {
    padding-left: 20px;
  }
  .bg-tree-td:before{
    display: inline-block;
    content: "";
    position: relative;
    top: 0;
    left: 13px;
    width: 16px;
    height: 0;
    border-top: 1px dotted #858585;
    z-index: 1;
  }
  .bg-tree-td:after{
    display: inline-block;
    content: "";
    position: absolute;
    top: 0;
    left: 12px;
    width: 1px;
    height: 100%;
    border-left: 1px dotted #858585;
    z-index: 1;
  }
  .bg-tree-dot {
    position: absolute;
    width: 1px;
    height: 100%;
    left: 12px;
  }
  .bg-tree-dot:before {
    display: inline-block;
    content: "";
    left: 0;
    width: 1px;
    height: 100%;
    border-left: 1px dotted #858585;
    z-index: 1;
  }
  .bg-tree-icon-action {
    position: absolute;
    left: 7px;
    background: #fff;
    z-index: 2;
    cursor: pointer;
  }
  .component-sbp-form .main-header-icon .dropdown-toggle {
    border-left: 1px solid #a5aeb7;
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
  }
  .table-pagination .pagination{
    margin: 0;
  }
</style>
