<template>
    <div class="main-entry component-sbp-form">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Chứng từ chung<span v-if="model.companyName">:</span> {{model.companyName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Chứng từ chung<span v-if="model.companyName">:</span> {{model.companyName}}</span>
                        </div>
                    </b-col>
                    <b-col class="col-md-6"></b-col>
                </b-row>
                <b-row>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-actions">
                            <b-button type="submit" variant="primary" class="main-header-action mr-2" @click="handleSubmitForm"><i class="fa fa-check-square-o"></i> Lưu</b-button>
                            <b-button type="reset" variant="primary" class="main-header-action mr-2" @click="onBackToList"><i class="fa fa-ban"></i> Hủy</b-button>
                        </div>
                    </b-col>
                    <b-col class="col-md-12">
                        <div class="main-header-item main-header-icons">
                            <b-dropdown id="dropdown-per-page" title="Số bản ghi/trang" menu-class="p-0" :text="perPage" class="app-dropdown-center main-header-icon">
                            <b-dropdown-item @click="changePerPage(10)">10</b-dropdown-item>
                            <b-dropdown-item @click="changePerPage(15)">15</b-dropdown-item>
                            <b-dropdown-item @click="changePerPage(20)">20</b-dropdown-item>
                            <b-dropdown-item @click="changePerPage(30)">30</b-dropdown-item>
                            <b-dropdown-item @click="changePerPage(40)">40</b-dropdown-item>
                            <b-dropdown-item @click="changePerPage(50)">50</b-dropdown-item>
                          </b-dropdown>
                            <div class="main-header-collapse" id="main-header-collapse">
                                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen=true />
                            </div>

                        </div>
                    </b-col>
                </b-row>
            </div>

        </div>
        <div class="main-body main-body-view-action" id="main-body">
          <div class="container-fluid">
            <div class="card">
              <div class="form-group row" style="margin-bottom: 0px !important; margin-top: 10px; margin-right: 0px;">
                <div class="col-md-16 col-lg-16">
                  <!--                      <b-card no-body>-->
                  <b-tabs card>
                    <b-tab title="Cơ quan chính quyền" active>
                      <b-card-text>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0">Tên</label>
                          <div class="col-md-20 col-xl-21">
                            <IjcoreModalListing
                              v-model="model" :title="'cơ quan chính quyền'" :api="'/listing/api/common/list'"
                              :table="'company'" :FieldID="'CompanyID'" :FieldName="'CompanyName'"
                              :FieldUpdate="['Address', 'ContactName']"
                              @changed="handleGetChangedCompany"
                              :FieldNo="'CompanyNo'">
                            </IjcoreModalListing>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0" for="CompanyNo">Mã số</label>
                          <div class="col-md-20 col-xl-5">
                            <input type="text" v-model="model.CompanyNo" id="CompanyNo" class="form-control" placeholder="Mã số"/>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0 text-nowrap" for="CompanyMOFNo">Mã ĐV </label>
                          <div class="col-md-20 col-xl-5 mt-2 mt-xl-0">
                            <input type="text" v-model="model.CompanyMOFNo" id="CompanyMOFNo" class="form-control" placeholder="Mã ĐVQHNS"/>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0 text-nowrap" for="CompanyLocationNo">Mã địa bàn</label>
                          <div class="col-md-20 col-xl-5 mt-2 mt-xl-0">
                            <input type="text" v-model="model.CompanyLocationNo" id="CompanyLocationNo" class="form-control" placeholder="Mã địa bàn"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0">Địa chỉ</label>
                          <div class="col-md-20 col-xl-21">
                            <input v-model="model.CompanyAddress" class="form-control" rows="3" placeholder="Địa chỉ"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0 text-nowrap" for="CompanyBankAccount">Tài khoản</label>
                          <div class="col-md-20 col-xl-9">
                            <input type="text" v-model="model.CompanyBankAccount" id="CompanyBankAccount" class="form-control" placeholder="Tài khoản"/>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0 mt-2 mt-xl-0 text-nowrap" for="CompanyBankName">Nơi mở </label>
                          <div class="col-md-20 col-xl-9 mt-2 mt-xl-0">
                            <input type="text" v-model="model.CompanyBankName" id="CompanyBankName" class="form-control" placeholder="Nơi mở"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0 text-nowrap" for="CompanyContactName">Đại diện</label>
                          <div class="col-md-20 col-xl-9">
                            <input type="text" v-model="model.CompanyContactName" id="CompanyContactName" class="form-control" placeholder="Đại diện"/>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0 mt-2 mt-xl-0 text-nowrap" for="CompanyContactPosition">Chức vụ </label>
                          <div class="col-md-20 col-xl-9 mt-2 mt-xl-0">
                            <input type="text" v-model="model.CompanyContactPosition" id="CompanyContactPosition" class="form-control" placeholder="Chức vụ"/>
                          </div>
                        </div>

                      </b-card-text>
                    </b-tab>
                    <b-tab title="Nhân viên">
                      <b-card-text>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0">Họ và tên</label>
                          <div class="col-md-14 col-xl-15">
                            <IjcoreModalListing
                              v-model="model" :title="'nhân viên'" :api="'/listing/api/common/list'"
                              :table="'employee'" :FieldID="'EmployeeID'" :FieldName="'EmployeeName'"
                              :FieldUpdate="['CitizenIdNo', 'CitizenIdDate', 'CitizenIdAt', 'DepartmentName', 'PositionName']"
                              @changed="handleGetChangedEmployee"
                              :FieldNo="'EmployeeNo'">
                            </IjcoreModalListing>
                          </div>

                          <label class="col-md-3 col-xl-3 m-0">Mã số </label>
                          <div class="col-md-3 col-xl-3">
                            <input type="text" v-model="model.EmployeeNo" class="form-control" placeholder="Mã số"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0 text-nowrap">CMT/CCCD</label>
                          <div class="col-md-20 col-xl-5">
                            <input type="text" v-model="model.EmployeeCitizenIdNo"class="form-control" placeholder="Số CMT/CCCD"/>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0 text-nowrap" >Ngày cấp </label>
                          <div class="col-md-20 col-xl-5 mt-2 mt-xl-0">
                            <!--                                  <input type="text" v-model="model.EmployeeCitizenIdDate" class="form-control" placeholder="Ngày cấp"/>-->
                            <IjcoreDatePicker v-model="model.EmployeeCitizenIdDate" style="width: 100%;"></IjcoreDatePicker>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0" >Nơi cấp</label>
                          <div class="col-md-20 col-xl-5 mt-2 mt-xl-0">
                            <input type="text" v-model="model.EmployeeCitizenIdAt" class="form-control" placeholder="Nơi cấp"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0">Bộ phận</label>
                          <div class="col-md-20 col-xl-9">
                            <input type="text" v-model="model.EmployeeDepartmentName" class="form-control" placeholder="Bộ phận"/>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0" >Chức vụ </label>
                          <div class="col-md-20 col-xl-9 mt-2 mt-xl-0">
                            <input type="text" v-model="model.EmployeePosition" class="form-control" placeholder="Chức vụ"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0">Tài khoản</label>
                          <div class="col-md-20 col-xl-9">
                            <input type="text" v-model="model.EmployeeBankAccount" class="form-control" placeholder="Tài khoản"/>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0" >Nơi mở </label>
                          <div class="col-md-20 col-xl-9 mt-2 mt-xl-0">
                            <input type="text" v-model="model.EmployeeBankName" class="form-control" placeholder="Nơi mở"/>
                          </div>
                        </div>

                      </b-card-text>
                    </b-tab>
                    <b-tab title="Nhà cung cấp">
                      <b-card-text>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0">Tên</label>
                          <div class="col-md-14 col-xl-15">
                            <IjcoreModalListing
                              v-model="model" :title="'nhà cung cấp'" :api="'/listing/api/common/list'"
                              :table="'vendor'" :FieldID="'VendorID'" :FieldName="'VendorName'"
                              :FieldUpdate="['TaxCode', 'Address', 'CitizenIdAt', 'DepartmentName', 'PositionName']"
                              @changed="handleGetChangedVendor"
                              :FieldNo="'VendorNo'">
                            </IjcoreModalListing>
                          </div>

                          <label class="col-md-3 col-xl-3 m-0">Mã số </label>
                          <div class="col-md-3 col-xl-3">
                            <input type="text" v-model="model.VendorNo" class="form-control" placeholder="Mã số"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0 text-nowrap">Mã số thuế</label>
                          <div class="col-md-20 col-xl-9">
                            <input type="text" v-model="model.VendorTaxcode" class="form-control" placeholder="Mã số thuế"/>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0" >Mã địa bàn </label>
                          <div class="col-md-20 col-xl-9 mt-2 mt-xl-0">
                            <input type="text" v-model="model.VendorLocationNo" class="form-control" placeholder="Mã địa bàn"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0">Địa chỉ</label>
                          <div class="col-md-20 col-xl-21">
                            <input v-model="model.VendorAddress" class="form-control" rows="3" placeholder="Địa chỉ"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0">Tài khoản</label>
                          <div class="col-md-20 col-xl-9">
                            <input type="text" v-model="model.VendorBankAccount" class="form-control" placeholder="Tài khoản"/>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0" >Nơi mở </label>
                          <div class="col-md-20 col-xl-9 mt-2 mt-xl-0">
                            <input type="text" v-model="model.VendorBankName" class="form-control" placeholder="Nơi mở"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0">Đại diện</label>
                          <div class="col-md-20 col-xl-9">
                            <input type="text" v-model="model.VendorContactName" class="form-control" placeholder="Đại diện"/>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0" >Chức vụ </label>
                          <div class="col-md-20 col-xl-9 mt-2 mt-xl-0">
                            <input type="text" v-model="model.VendorContactPosition" class="form-control" placeholder="Chức vụ"/>
                          </div>
                        </div>

                      </b-card-text>
                    </b-tab>
                    <b-tab title="Khách hàng">
                      <b-card-text>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0">Tên</label>
                          <div class="col-md-14 col-xl-15">
                            <IjcoreModalListing
                              v-model="model" :title="'khách hàng'" :api="'/listing/api/common/list'"
                              :table="'customer'" :FieldID="'CustomerID'" :FieldName="'CustomerName'"
                              :FieldUpdate="['TaxCode', 'Address', 'BankAccount', 'BankName', 'BillTo']"
                              @changed="handleGetChangedCustomer"
                              :FieldNo="'CustomerNo'">
                            </IjcoreModalListing>
                          </div>

                          <label class="col-md-3 col-xl-3 m-0">Mã số </label>
                          <div class="col-md-3 col-xl-3">
                            <input type="text" v-model="model.CustomerNo" class="form-control" placeholder="Mã số"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0 text-nowrap">Mã số thuế</label>
                          <div class="col-md-20 col-xl-9">
                            <input type="text" v-model="model.CustomerTaxcode" class="form-control" placeholder="Mã số thuế"/>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0" >Mã địa bàn </label>
                          <div class="col-md-20 col-xl-9 mt-2 mt-xl-0">
                            <input type="text" v-model="model.CustomerLocationNo" class="form-control" placeholder="Mã địa bàn"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0">Địa chỉ</label>
                          <div class="col-md-20 col-xl-21">
                            <input v-model="model.CustomerAddress" class="form-control" rows="3" placeholder="Địa chỉ"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0">Tài khoản</label>
                          <div class="col-md-20 col-xl-9">
                            <input type="text" v-model="model.CustomerBankAccount" class="form-control" placeholder="Tài khoản"/>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0" >Nơi mở </label>
                          <div class="col-md-20 col-xl-9 mt-2 mt-xl-0">
                            <input type="text" v-model="model.CustomerBankName" class="form-control" placeholder="Nơi mở"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0">Đại diện</label>
                          <div class="col-md-20 col-xl-9">
                            <input type="text" v-model="model.CustomerContactName" class="form-control" placeholder="Đại diện"/>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0" >Chức vụ </label>
                          <div class="col-md-20 col-xl-9 mt-2 mt-xl-0">
                            <input type="text" v-model="model.CustomerContactPosition" class="form-control" placeholder="Chức vụ"/>
                          </div>
                        </div>

                      </b-card-text>
                    </b-tab>
                    <b-tab title="Đối tác">
                      <b-card-text>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0">Tên</label>
                          <div class="col-md-14 col-xl-15">
                            <IjcoreModalListing
                              v-model="model" :title="'đối tác'" :api="'/listing/api/common/list'"
                              :table="'partner'" :FieldID="'PartnerID'" :FieldName="'PartnerName'"
                              :FieldUpdate="['PartnerAddress', 'CitizenIdNo', 'CitizenIdDate', 'CitizenIdAt', 'NativeCountry']"
                              @changed="handleGetChangedPartner"
                              :FieldNo="'PartnerNo'">
                            </IjcoreModalListing>
                          </div>

                          <label class="col-md-3 col-xl-3 m-0">Mã số </label>
                          <div class="col-md-3 col-xl-3">
                            <input type="text" v-model="model.PartnerNo" class="form-control" placeholder="Mã số"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0 text-nowrap">Mã số thuế</label>
                          <div class="col-md-20 col-xl-9">
                            <input type="text" v-model="model.PartnerTaxcode" class="form-control" placeholder="Mã số thuế"/>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0" >Mã địa bàn </label>
                          <div class="col-md-20 col-xl-9 mt-2 mt-xl-0">
                            <input type="text" v-model="model.PartnerLocationNo" class="form-control" placeholder="Mã địa bàn"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0">Địa chỉ</label>
                          <div class="col-md-20 col-xl-21">
                            <input v-model="model.PartnerAddress" class="form-control" rows="3" placeholder="Địa chỉ"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0">Tài khoản</label>
                          <div class="col-md-20 col-xl-9">
                            <input type="text" v-model="model.PartnerBankAccount" class="form-control" placeholder="Tài khoản"/>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0" >Nơi mở </label>
                          <div class="col-md-20 col-xl-9 mt-2 mt-xl-0">
                            <input type="text" v-model="model.PartnerBankName" class="form-control" placeholder="Nơi mở"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0">Đại diện</label>
                          <div class="col-md-20 col-xl-9">
                            <input type="text" v-model="model.PartnerContactName" class="form-control" placeholder="Đại diện"/>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0" >Chức vụ </label>
                          <div class="col-md-20 col-xl-9 mt-2 mt-xl-0">
                            <input type="text" v-model="model.PartnerContactPosition" class="form-control" placeholder="Chức vụ"/>
                          </div>
                        </div>
                        <div class="form-group row align-items-center">
                          <label class="col-md-4 col-xl-3 m-0 text-nowrap">CMT/CCCD</label>
                          <div class="col-md-20 col-xl-5">
                            <input type="text" v-model="model.PartnerCitizenIdNo"class="form-control" placeholder="Số CMT/CCCD"/>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0" >Ngày cấp </label>
                          <div class="col-md-20 col-xl-5 mt-2 mt-xl-0">
                            <!--                                  <input type="text" v-model="model.PartnerCitizenIdDate" class="form-control" placeholder="Ngày cấp"/>-->
                            <IjcoreDatePicker v-model="model.PartnerCitizenIdDate" style="width: 100%;"></IjcoreDatePicker>
                          </div>

                          <label class="col-md-4 col-xl-3 m-0" >Nơi cấp</label>
                          <div class="col-md-20 col-xl-5 mt-2 mt-xl-0">
                            <input type="text" v-model="model.PartnerCitizenIdAt" class="form-control" placeholder="Nơi cấp"/>
                          </div>
                        </div>

                      </b-card-text>
                    </b-tab>
                  </b-tabs>
                  <!--                      </b-card>-->
                </div>
                <div class="col-md-8 col-lg-8 w-form-control" style="margin-top: 32px;">
                  <div class="form-group row align-items-center mb-0">
                    <label class="col-md-12 col-lg-12 col-xl-5 m-0 p-1" >Số CTG</label>
                    <div class="col-md-12 col-lg-12 col-xl-7 p-1">
                      <input type="text" v-model="model.TransNo" class="form-control" placeholder="Số CTG"/>
                    </div>
                    <label class="col-md-12 col-lg-12 col-xl-5 m-0 p-1" >Ngày CTG</label>
                    <div class="col-md-12 col-lg-12 col-xl-7 p-1">
                      <IjcoreDatePicker v-model="model.TransDate" style="width: 100% !important;"></IjcoreDatePicker>
                    </div>
                  </div>
                  <div class="form-group row align-items-center" style="margin-top: -8px;">
                    <label class="col-md-12 col-lg-12 col-xl-5 m-0 p-1" >Số CTĐT</label>
                    <div class="col-md-12 col-lg-12 col-xl-7 p-1">
                      <input type="text" v-model="model.eTransNo" class="form-control" placeholder="Số CTĐT"/>
                    </div>
                    <label class="col-md-12 col-lg-12 col-xl-5 m-0 p-1" >Ngày CTĐT</label>
                    <div class="col-md-12 col-lg-12 col-xl-7 p-1">
                      <IjcoreDatePicker v-model="model.eTransDate" style="width: 100% !important;"></IjcoreDatePicker>
                    </div>
                  </div>
                  <div class="form-group row align-items-center" style="margin-top: -8px;">
                    <label class="col-md-12 col-lg-12 col-xl-5 m-0 p-1" >Ngày HT</label>
                    <div class="col-md-12 col-lg-12 col-xl-7 p-1">
                      <IjcoreDatePicker v-model="model.PostDate" style="width: 100% !important;"></IjcoreDatePicker>
                    </div>
                    <label class="col-md-12 col-lg-12 col-xl-5 m-0 p-1" >Loại HTTK</label>
                    <div class="col-md-12 col-lg-12 col-xl-7 p-1">
                      <b-form-select v-model="model.CoaTypeID" :options="model.Coatype" @change="changeCoatype"></b-form-select>
                    </div>
                  </div>
                  <div class="form-group row align-items-center" style="margin-top: -8px;">
                    <label class="col-md-12 col-lg-12 col-xl-5 m-0 p-1" >Kỳ HT</label>
                    <div class="col-md-12 col-lg-12 col-xl-7 p-1">
                      <b-form-select class="select-control"
                                     v-model="model.PeriodType" @change="changePeriodType"
                                     :options="[
                                        {value: null, text: 'Chọn kỳ HT'},
                                        {value: 1, text: 'Năm'},
                                        {value: 2, text: 'Quý'},
                                        {value: 3, text: 'Tháng'},
                                        {value: 4, text: 'Tuần'},
                                        {value: 5, text: 'Ngày'},
                                        {value: 6, text: '6 tháng'},
                                        {value: 7, text: '9 tháng'},
                                        {value: 8, text: '3 năm'},
                                        {value: 9, text: '5 năm'},
                                        {value: 10, text: '10 năm'},
                                        {value: 99, text: 'Tùy chọn'},]">
                      </b-form-select>
                    </div>
                    <!--                        <label class="col-md-5 m-0 p-1" ></label>-->
                    <div v-if="model.item_DF599" class="col-md-12 col-lg-12 col-xl-12 p-1 ml-lg-auto">
                      <b-form-select
                        v-model="model.PeriodID"
                        :options="model.ArrPeriodOption" @change="changePeriodName">
                      </b-form-select>
                    </div>
                    <div v-if="model.item_599" class="col-md-12 p-1">
                      <IjcoreDatePicker v-model="model.PeriodFromDate" style="width: 100%;"></IjcoreDatePicker>
                    </div>
                  </div>
                  <div class="form-group row align-items-center" style="margin-top: -8px;">
                    <label class="col-md-12 col-lg-12 col-xl-5 m-0 p-1" >Trạng thái</label>
                    <div class="col-md-12 col-lg-12 col-xl-7 p-1">
                      <b-form-select v-model="model.StatusValue" :options="model.AccoutingStatus" @change="changeStatusValue"></b-form-select>
                    </div>
                    <div class="col-md-12 col-lg-12 col-xl-12 mt-3 mt-xl-0">
                      <label style="position: absolute; margin-left: 13px; top: -15px;"><b-form-checkbox type="check" v-model="model.isAdjustTrans" class="form-control" ></b-form-checkbox></label>
                      <label style="position: absolute; left: 25px; top: -12px; right: -35px;">Là CT điều chỉnh</label>
                    </div>
                  </div>
                  <!--                      <div class="form-group row align-items-center" style="margin-top: 0px;">-->
                  <!--                        <label class="col-md-12 col-lg-12 col-xl-5 m-0 p-1" ></label>-->
                  <!--                        <div class="col-md-12 col-lg-12 col-xl-7 p-1">-->

                  <!--                        </div>-->
                  <!--                        <div class="col-md-12 col-lg-12 col-xl-12 mt-3 mt-xl-0">-->
                  <!--                          <label style="position: absolute; margin-left: 13px; top: -15px;"><b-form-checkbox type="check" v-model="model.isDebtTrans" class="form-control" ></b-form-checkbox></label>-->
                  <!--                          <label style="position: absolute; left: 25px; top: -11px; right: -35px;">Nợ ĐTXDCB</label>-->
                  <!--                        </div>-->
                  <!--                      </div>-->

                </div>
              </div>
              <!-- Detail -->
              <div class="container-fluid" id="bar_detail">
                <div class="form-group justify-content-end row" style="width: 100%;">
                  <div class="col-md-2 col-xl-3" style="padding-left: 5px; max-width: 11%;">
                    <norm-table-form v-model="model" @changed="onAddLine"></norm-table-form>
                  </div>
<!--                  <div class="col-md-2 col-xl-2" style="max-width: 8%;">-->
<!--                    <IjcoreModalMultiAct v-model="model" @changed="addLineHttk" :title="'HTTK'"-->
<!--                                             :api="'/listing/api/common/list'" :table="'coa_type'" :FieldID="'CoaTypeID'" :FieldNo="'CoaTypeNo'" :FieldName="'CoaTypeName'"></IjcoreModalMultiAct>-->
<!--                  </div>-->
                  <div class="col-md-4 col-xl-3" style="padding-left: 32px; max-width: 11%;">
                    <b-form-checkbox type="check" v-model="model.item_tt" class="form-control">Tiền tệ</b-form-checkbox>
                  </div>
                  <div class="col-md-5 col-xl-4" style="max-width: 13%;">
                    <!--                        <b-form-radio @change="onToggleModalProperty" type="radio" v-model="model.item_ts" class="form-control" >Vật tư/Tài sản</b-form-radio>-->
                    <a @click="onToggleModalProperty()" class="new-row" style="width: 110px;">
                      <i v-if="model.item_ts" aria-hidden="true" class="fa fa-dot-circle-o ij-icon ij-icon-plus" style="font-size: 16px; color: #00a2e8"></i>
                      <i v-else aria-hidden="true" class="fa fa-circle-thin ij-icon ij-icon-plus" style="font-size: 16px;"></i> Tài sản/Vật tư
                    </a>
                  </div>
                  <div class="col-md-4 col-xl-3" style="max-width: 10%;">
                    <b-form-checkbox type="check" v-model="model.item_hd" class="form-control">Hóa đơn</b-form-checkbox>
                  </div>
                  <div class="col-md-3 col-xl-2" style="max-width: 8%;">
                    <b-form-checkbox type="check" v-model="model.item_da" class="form-control">Dự án</b-form-checkbox>
                  </div>
                  <div class="col-md-4 col-xl-3" style="max-width: 11%;">
                    <!--                        <b-form-checkbox type="check" v-model="model.item_dt" class="form-control">Đối tượng</b-form-checkbox>-->
                    <a @click="onToggleModalObject()" class="new-row">
                      <i aria-hidden="true" class="fa fa-th-large ij-icon ij-icon-plus" style="font-size: 16px;"></i> Đối tượng
                    </a>
                  </div>
                  <div class="col-md-4 col-xl-3" style="max-width: 12%;">
                    <b-form-checkbox type="check" v-model="model.item_nv" class="form-control">Nguồn vốn</b-form-checkbox>
                  </div>
                  <div class="col-md-4 col-xl-3" style="max-width: 5%;">
                    <b-form-checkbox type="check" v-model="model.item_mlns" class="form-control">MLNS</b-form-checkbox>
                  </div>

                </div>
              </div>

              <div style="margin: 0 10px;">
                <div class="table-responsive b-table-sticky-header b-table-sticky-custom m-0" id="scrollHeight" style="max-height: 350px;">
                  <table class="table b-table table-sm table-bordered table-editable table-tree table-column-resizable" style="border-left: none !important;">
                    <thead>
                    <tr>
                      <!--                      <th scope="col" class="td-action-fix-left-form b-table-sticky-column" style="height: 29px; border-top: none; border-bottom: none !important; border-left: none; width: 0px !important; display: inherit;"></th>-->
                      <!--                      <th scope="col" class="td-action-fix-left-form b-table-sticky-column" style="width: 11px; height: 34px; border-left: 1px solid #c8ced3; border-top: none; z-index: 999; display: inherit"></th>-->
                      <th style="width: 5%; min-width: 50px; background: #fff; z-index: 11 !important;" class="b-table-sticky-column"></th>
                      <th scope="col" style="min-width: 200px; border-left: none;" class="text-center">Bút toán</th>
                      <th v-if="model.item_tkhn" scope="col" style="min-width: 100px" class="text-center">Tài khoản</th>
                      <th v-if="model.item_tkhn" scope="col" style="min-width: 100px" class="text-center" :title="'TKĐƯ'+ '('+model.CoaTypeName+')'">TKĐƯ</th>
                      <th scope="col" v-if="NormTable.NormTableID" style="min-width: 100px; z-index: 10" class="text-center" title="Định mức dự toán chi tiết">ĐMDTCT</th>
                      <!-- Phân bổ -->
                      <th scope="col" style="min-width: 100px; z-index: 10" class="text-center" title="Định mức phân bổ dự toán">ĐMPBDT</th>
                      <!-- Chỉ tiêu -->
                      <th scope="col" style="min-width: 100px; z-index: 10" class="text-center border-right-0" title="Chỉ tiêu dự toán">CTDT</th>
                      <!-- Nội dung -->
                      <th scope="col" style="min-width: 300px; z-index: 11 !important;" class="text-center b-table-sticky-column">Nội dung </th>
                      <!--Tiền tệ -->
                      <th v-if="model.item_tt" scope="col" style="min-width: 95px" class="text-center">Tiền tệ </th>
                      <th v-if="model.item_tt" scope="col" style="min-width: 80px" class="text-center">Tỷ giá </th>
                      <!--Tài sản/Vật tư -->
                      <th v-if="model.item_ts" scope="col" style="min-width: 120px" class="text-center">{{item_ts_name}} </th>
                      <th scope="col" style="min-width: 138px" class="text-center">ĐVT </th>
                      <th scope="col" style="min-width: 80px" class="text-center">Số lượng </th>
                      <th scope="col" style="min-width: 150px" class="text-center">Đơn giá <i v-if="model.item_tt" style="font-style: normal !important;">(NT)</i> </th>
                      <th v-if="model.item_tt" scope="col" style="min-width: 150px" class="text-center">Đơn giá (QĐ) </th>
                      <!-- -->
                      <th v-if="model.item_tt" scope="col" style="min-width: 150px" class="text-center">Nợ nguyên tệ</th>
                      <th v-else scope="col" style="min-width: 150px" class="text-center">Nợ </th>
                      <th v-if="model.item_tt" scope="col" style="min-width: 150px" class="text-center">Có nguyên tệ</th>
                      <th v-else scope="col" style="min-width: 150px" class="text-center">Có </th>
                      <th v-if="model.item_tt" scope="col" style="min-width: 150px" class="text-center">Nợ quy đổi</th>
                      <th v-if="model.item_tt" scope="col" style="min-width: 150px" class="text-center">Có quy đổi</th>
                      <!-- -->
                      <th scope="col" style="min-width: 180px" class="text-center">Nghiệp vụ </th>

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
                      <th v-if="model.item_da" scope="col" style="min-width: 139px" class="text-center">Dự án </th>
                      <th v-if="model.item_da" scope="col" style="min-width: 139px" class="text-center">CTMT </th>
                      <th v-if="model.item_da" scope="col" style="min-width: 115px" class="text-center">Hợp đồng </th>
                      <!--Đối tượng -->
                      <th v-if="model.item_dt_cqnn" scope="col" style="min-width: 120px" class="text-center">CQNN </th>
                      <th v-if="model.item_dt_nv" scope="col" style="min-width: 120px" class="text-center">Nhân viên </th>
                      <th v-if="model.item_dt_ncc" scope="col" style="min-width: 120px" class="text-center">NCC </th>
                      <th v-if="model.item_dt_kh" scope="col" style="min-width: 120px" class="text-center">Khách hàng </th>
                      <th v-if="model.item_dt_dt" scope="col" style="min-width: 120px" class="text-center">Đối tác </th>

                      <th scope="col" style="min-width: 95px" class="text-center">Quỹ </th>
                      <th scope="col" style="min-width: 95px" class="text-center">Khoản thu </th>
                      <!-- Lĩnh vực thu -->
                      <th scope="col" style="min-width: 95px" class="text-center">Lĩnh vực thu </th>
                      <th scope="col" style="min-width: 95px" class="text-center">Khoản chi </th>
                      <!-- Lĩnh vực chi -->
                      <th scope="col" style="min-width: 95px" class="text-center">Lĩnh vực chi </th>
                      <!-- Chỉ tiêu -->
                      <th scope="col" style="min-width: 100px" class="text-center">ĐMDTCT </th>
                      <!--Nguồn vốn -->
                      <th v-if="model.item_nv" scope="col" style="min-width: 95px" class="text-center">Cấp NS </th>
                      <th v-if="model.item_nv" scope="col" style="min-width: 95px" class="text-center">Kỳ NS </th>
                      <th v-if="model.item_nv" scope="col" style="min-width: 95px" class="text-center">Năm NS </th>
                      <th v-if="model.item_nv" scope="col" style="min-width: 95px" class="text-center">Nguồn vốn </th>
                      <th v-if="model.item_nv" scope="col" style="min-width: 95px" class="text-center">Cấp phát </th>
                      <th v-if="model.item_nv" scope="col" style="min-width: 95px" class="text-center">Nhận bằng </th>
                      <!--MLNS -->
                      <th v-if="model.item_mlns" scope="col" style="min-width: 110px" class="text-center">Chương </th>
                      <th v-if="model.item_mlns" scope="col" style="min-width: 110px" class="text-center">Loại/khoản </th>
                      <th v-if="model.item_mlns" scope="col" style="min-width: 110px" class="text-center">Mục/tm </th>
                      <th style="width: 3%; min-width: 45px; background: #fff; z-index: 12 !important;" class="b-table-sticky-column-right"></th>
                    </tr>
                    </thead>
                    <draggable v-model="model.ActgvoucTransItem" tag="tbody" draggable=".draggable" handle=".my-handle">
                      <tr class="draggable" v-if="field.Show && field.ShowPagination" :id="'table-item-' + field.LineID" v-for="(field, key) in model.ActgvoucTransItem">
                        <!--                      <th scope="col" class="td-action-fix-left-form" style="width: 11px; height: 34px; border-left: none; border-top: none; z-index: 999;"></th>-->
                        <td class="text-center my-handle b-table-sticky-column" style="overflow: unset; cursor: move; background: #fff">
                          <div class="d-flex align-items-center justify-content-around">
                            <i title="Thêm con" @click="onAddFieldChildrenOnTable(key)" class="fa fa-plus-circle" style="cursor: pointer; font-size: 18px; margin-top: 2px; color: #a79f9f;"></i>
                            <i title="Nhân bản" @click="onCloneFieldChildrenOnTable(key)" class="fa fa-clone" style="cursor: pointer; font-size: 16px; margin-top: 2px; color: #a79f9f;"></i>
                          </div>
                        </td>
                        <td style="border-left: none;">
                          <IjcoreModalAutoact
                            v-model="model.ActgvoucTransItem[key]" :title="'bút toán'" :api="'/listing/api/common/list'"
                            :table="'act_autoact'" :FieldID="'AutoactID'" :FieldName="'AutoactName'" :FieldNo="'AutoactName'"
                            :FieldWhere="{AutoactType : model.ActgvoucTransItem[key].NormType}"
                            :FieldType="1" @selected="changeAutoact($event,key)">
                          </IjcoreModalAutoact>
<!--                          <b-form-select v-model="model.ActgvoucTransItem[key].AutoactID" :options="model.Autoact"  @change="changeAutoact1($event,key)"></b-form-select>-->
                        </td>
                        <td v-if="model.item_tkhn">
                          <!--                        <IjcoreModalAccounting-->
                          <!--                          v-model="model.ActgvoucTransItem[key]" :title="'TKHN'" :api="'/listing/api/common/list'" :FieldWhere="{Detail : 1}"-->
                          <!--                          :table="'coa_con'" :FieldID="'AccountID'" :FieldName="'AccountName'" :FieldNo="'AccountNo'" :FieldType="1" >-->
                          <!--                        </IjcoreModalAccounting>-->

                          <ijcore-select2-server2
                            v-model="model.ActgvoucTransItem[key]"
                            :url="$store.state.appRootApi + '/listing/api/common/list2'"
                            :table="Table_CoaType"
                            field-name="AccountName"
                            field-i-d="AccountID"
                            field-no="AccountNo"
                            :field-type="2"
                            :delay="200"
                            :get-all="false"
                            :dropdown-with="400"
                            :client-d-b="true"
                            :placeholder="'Chọn '+ model.CoaTypeName"
                            :allowClear="false"
                            :settings="{maximumSelectionSize: 1}"
                            :multiple="true">
                          </ijcore-select2-server2>

                        </td>
                        <td v-if="model.item_tkhn">
                          <!--                        <IjcoreModalAccounting-->
                          <!--                          v-model="model.ActgvoucTransItem[key]" :title="'TKĐƯ TKHN'" :api="'/listing/api/common/list'" :FieldWhere="{Detail : 1}"-->
                          <!--                          :table="'coa_con'" :FieldID="'CoAccountID'" :FieldName="'CoAccountName'" :FieldNo="'CoAccountNo'" :FieldType="2" :columnID="'AccountID'" :columnNo="'AccountNo'" :columnName="'AccountName'">-->
                          <!--                        </IjcoreModalAccounting>-->
                          <ijcore-select2-server2
                            v-model="model.ActgvoucTransItem[key]"
                            :url="$store.state.appRootApi + '/listing/api/common/list2'"
                            :table="Table_CoaType"
                            field-name="AccountName"
                            field-i-d="AccountID"
                            field-no="AccountNo"
                            co-field-i-d="CoAccountID"
                            co-field-name="CoAccountName"
                            co-field-no="CoAccountNo"
                            :field-type="2"
                            :delay="200"
                            :get-all="false"
                            :dropdown-with="400"
                            :client-d-b="true"
                            :placeholder="'TKĐƯ '+ model.CoaTypeName"
                            :allowClear="false"
                            :settings="{maximumSelectionSize: 1}"
                            :multiple="true">
                          </ijcore-select2-server2>
                        </td>

                        <td v-if="NormTable.NormTableID">
                          <ijcore-modal-listing
                            v-model="model.ActgvoucTransItem[key]"
                            title="Định mức dự toán"
                            table="norm_table_item"
                            field-i-d="NormTableItemID"
                            field-name="NormTableItemName"
                            field-no="NormTableItemNo"
                            :field-type="2"
                            :field-where="{NormTableID: NormTable.NormTableID}"
                            :field-update="['NormAllotLevelID', 'NormAllotLevelName', 'NormAllotLevelNo', 'NormID', 'NormNo', 'NormName']"
                            @changed="changeNormTableItemNo($event, key)"
                            api="/listing/api/common/list">
                          </ijcore-modal-listing>
                        </td>
                        <!-- Chỉ tiêu -->
                        <td>
                          <ijcore-modal-listing
                            v-model="model.ActgvoucTransItem[key]"
                            title="Định mức phân bổ dự toán"
                            table="norm_allot_level"
                            field-i-d="NormAllotLevelID"
                            field-name="NormAllotLevelName"
                            field-no="NormAllotLevelNo"
                            :field-type="2"
                            :field-update="['NormID', 'NormName', 'NormNo']"
                            api="/listing/api/common/list">
                          </ijcore-modal-listing>
                        </td>
                        <td class="border-right-0">
                          <ijcore-modal-listing
                            v-model="model.ActgvoucTransItem[key]"
                            title="Chỉ tiêu dự toán"
                            table="norm"
                            field-i-d="NormID"
                            field-name="NormName"
                            field-no="NormNo"
                            :field-type="2"
                            api="/listing/api/common/list">
                          </ijcore-modal-listing>
                        </td>
                        <!-- Nội dung -->
                        <td class="bg-tree-tr b-table-sticky-column" style="background: #fff;" :title="model.ActgvoucTransItem[key].Description">
                          <span class="bg-tree-dot" :style="{'left': (level * 12) + 'px', top: 0}" v-for="level in model.ActgvoucTransItem[key].Level"></span>
                          <div class="bg-tree-content bg-tree-td"
                               :style="{'margin-left': (model.ActgvoucTransItem[key].Level * 12 - 12) + 'px', width: 'calc(100% - ' + (model.ActgvoucTransItem[key].Level * 12 - 12) + 'px)'}" style="position: absolute; top: 0">
                            <b-form-input v-model="model.ActgvoucTransItem[key].Description"></b-form-input>
                            <i class="fa fa-minus-square-o bg-tree-icon-action" v-if="!model.ActgvoucTransItem[key].Detail" @click="onToggleChildNodes($event, model.ActgvoucTransItem[key])"></i>
                          </div>
                        </td>
                        <!--Tiền tệ -->
                        <td v-if="model.item_tt">
                          <IjcoreModalListing
                            v-model="model.ActgvoucTransItem[key]" :title="'tiền tệ'" :api="'/listing/api/common/list'"
                            :table="'ccy'" :FieldID="'CcyID'" :FieldName="'CcyName'"
                            :FieldNo="'CcyNo'" :FieldType="1">
                          </IjcoreModalListing>
                        </td>
                        <td v-if="model.item_tt">
                          <b-form-input
                            type="text"
                            v-model="model.ActgvoucTransItem[key].ExchangeRate"
                            class="text-center">
                          </b-form-input>
                        </td>
                        <!--Tài sản/Vật tư -->
                        <td v-if="model.item_ts">
                          <!--                        <b-form-select  v-model="model.ActgvoucTransItem[key].ItemID"-->
                          <!--                                        :options="[{value: 1, text: 'Vật tư HHDV'},-->
                          <!--                                                  {value: 2, text: 'Tài sản cố định'},-->
                          <!--                                                  {value: 3, text: 'Công cụ dụng cụ'},-->
                          <!--                                                  {value: 4, text: 'Tài sản đầu tư '}-->
                          <!--                                       ]"-->
                          <!--                                        disabled="disabled"-->
                          <!--                        ></b-form-select>-->
                          <IjcoreModalAccounting v-if="check_ts == 1" :key="check_ts"
                                                 v-model="model.ActgvoucTransItem[key]" :title="'vật tư HHDV'" :api="'/listing/api/common/list'"
                                                 :table="'item'" :FieldID="'ItemID'" :FieldName="'ItemName'"
                                                 :FieldNo="'ItemNo'" :FieldType="1">
                          </IjcoreModalAccounting>
                          <IjcoreModalAccounting v-if="check_ts == 2" :key="check_ts"
                                                 v-model="model.ActgvoucTransItem[key]" :title="'tài sản cố định'" :api="'/listing/api/common/list'"
                                                 :table="'fixed_asset'" :FieldID="'FixedAssetID'" :FieldName="'FixedAssetName'"
                                                 :FieldNo="'FixedAssetNo'" :FieldType="1">
                          </IjcoreModalAccounting>
                          <IjcoreModalAccounting v-if="check_ts == 3" :key="check_ts"
                                                 v-model="model.ActgvoucTransItem[key]" :title="'công cụ dụng cụ'" :api="'/listing/api/common/list'"
                                                 :table="'tool'" :FieldID="'ToolID'" :FieldName="'ToolName'"
                                                 :FieldNo="'ToolNo'" :FieldType="1">
                          </IjcoreModalAccounting>
                          <IjcoreModalAccounting v-if="check_ts == 4" :key="check_ts"
                                                 v-model="model.ActgvoucTransItem[key]" :title="'tài sản đầu tư'" :api="'/listing/api/common/list'"
                                                 :table="'invest_asset'" :FieldID="'InvestAssetID'" :FieldName="'InvestAssetName'"
                                                 :FieldNo="'InvestAssetNo'" :FieldType="1">
                          </IjcoreModalAccounting>
                        </td>
                        <td>
                          <IjcoreModalListing
                            v-model="model.ActgvoucTransItem[key]" :title="'đơn vị tính'" :api="'/listing/api/common/list'"
                            :table="'uom'" :FieldID="'UomID'" :FieldName="'UomName'" :FieldNo="'UomNo'">
                          </IjcoreModalListing>
                        </td>
                        <td>
<!--                          <b-form-input-->
<!--                            type="text"-->
<!--                            v-model="model.ActgvoucTransItem[key].Quantity"-->
<!--                            class="text-right">-->
<!--                          </b-form-input>-->
                          <ijcore-number v-model="model.ActgvoucTransItem[key].Quantity"  @input="addtoFCUnitPrice(model.ActgvoucTransItem[key].FCUnitPrice, key)"></ijcore-number>
                        </td>
                        <td>
                          <ijcore-number v-model="model.ActgvoucTransItem[key].FCUnitPrice" @input="addtoFCUnitPrice(model.ActgvoucTransItem[key].FCUnitPrice, key)"></ijcore-number>
                        </td>
                        <td v-if="model.item_tt">
                          <ijcore-number v-model="model.ActgvoucTransItem[key].LCUnitPrice" @input="addtoLCUnitPrice(model.ActgvoucTransItem[key].LCUnitPrice, key)"></ijcore-number>
                        </td>
                        <!-- -->
                        <td>
                          <ijcore-number v-model="model.ActgvoucTransItem[key].FCDebitAmount" @input="addtoLCDebitAmount(model.ActgvoucTransItem[key].FCDebitAmount, key)"></ijcore-number>
                        </td>
                        <td>
                          <ijcore-number v-model="model.ActgvoucTransItem[key].FCCreditAmount" @input="addtoLCCreditAmount(model.ActgvoucTransItem[key].FCCreditAmount, key)"></ijcore-number>
                        </td>
                        <!-- Tiền tệ -->
                        <td v-if="model.item_tt">
                          <ijcore-number v-model="model.ActgvoucTransItem[key].LCDebitAmount" ></ijcore-number>
                        </td>
                        <td v-if="model.item_tt">
                          <ijcore-number v-model="model.ActgvoucTransItem[key].LCCreditAmount" ></ijcore-number>
                        </td>
                        <td>
                          <IjcoreModalAutoact
                            v-model="model.ActgvoucTransItem[key]" :title="'nghiệp vụ kế toán'" :api="'/listing/api/common/list'"
                            :table="'act_intranstype'" :FieldID="'InTransTypeID'" :FieldName="'InTransTypeName'" :FieldNo="'InTransTypeName'">
                          </IjcoreModalAutoact>
                        </td>
                        <!--Hóa đơn -->
                        <td v-if="model.item_hd">
                          <ijcore-number v-model="model.ActgvoucTransItem[key].FCTaxAmount"></ijcore-number>
                        </td>
                        <td v-if="model.item_hd">
                          <b-form-input
                            type="text"
                            v-model="model.ActgvoucTransItem[key].TaxRate">
                          </b-form-input>
                        </td>
                        <td v-if="model.item_hd">
                          <b-form-input
                            type="text"
                            v-model="model.ActgvoucTransItem[key].InvoiceNo">
                          </b-form-input>
                        </td>
                        <td v-if="model.item_hd">
                          <IjcoreDatePicker v-model="model.ActgvoucTransItem[key].InvoiceDate" style="width: 100%; height: 28px !important;"></IjcoreDatePicker>
                        </td>
                        <td v-if="model.item_hd">
                          <b-form-input
                            type="text"
                            v-model="model.ActgvoucTransItem[key].InvoiceSerialNo">
                          </b-form-input>
                        </td>
                        <td v-if="model.item_hd">
                          <b-form-input
                            type="text"
                            v-model="model.ActgvoucTransItem[key].InvoiceFormNo">
                          </b-form-input>
                        </td>
                        <td v-if="model.item_hd">
                          <b-form-input
                            type="text"
                            v-model="model.ActgvoucTransItem[key].InvoiceSecurityCode">
                          </b-form-input>
                        </td>
                        <td v-if="model.item_hd">
                          <b-form-input
                            type="text"
                            v-model="model.ActgvoucTransItem[key].InvoiceLookupAddress">
                          </b-form-input>
                        </td>
                        <!--Dự án -->
                        <td v-if="model.item_da">
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'dự án'" :api="'/listing/api/common/list'"
                            :table="'project'" :FieldID="'ProjectID'" :FieldName="'ProjectName'"
                            :FieldNo="'ProjectNo'" :FieldUpdate="['TabmisNo']" :FieldType="1">
                          </IjcoreModalAccounting>
                        </td>
                        <td v-if="model.item_da">
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'chương trình mục tiêu'" :api="'/listing/api/common/list'"
                            :table="'program'" :FieldID="'ProgramID'" :FieldName="'ProgramName'"
                            :FieldNo="'ProgramNo'" :FieldUpdate="['ProgramType']" :FieldType="1">
                          </IjcoreModalAccounting>
                        </td>
                        <td v-if="model.item_da">
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'hợp đồng'" :api="'/listing/api/common/list'"
                            :table="'contract'" :FieldID="'ContractID'" :FieldName="'ContractName'"
                            :FieldNo="'ContractNo'" :FieldType="1">
                          </IjcoreModalAccounting>
                        </td>
                        <!--Đối tượng -->
                        <td v-if="model.item_dt_cqnn">
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'cơ quan nhà nước'" :api="'/listing/api/common/list'"
                            :table="'company'" :FieldID="'CompanyID'" :FieldName="'CompanyName'"
                            :FieldNo="'CompanyNo'" :FieldType="1">
                          </IjcoreModalAccounting>
                        </td>
                        <td v-if="model.item_dt_nv">
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'nhân viên'" :api="'/listing/api/common/list'"
                            :table="'employee'" :FieldID="'EmployeeID'" :FieldName="'EmployeeName'"
                            :FieldNo="'EmployeeNo'" :FieldType="1">
                          </IjcoreModalAccounting>
                        </td>
                        <td v-if="model.item_dt_ncc">
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'nhà cung cấp'" :api="'/listing/api/common/list'"
                            :table="'vendor'" :FieldID="'VendorID'" :FieldName="'VendorName'"
                            :FieldNo="'VendorNo'" :FieldType="1">
                          </IjcoreModalAccounting>
                        </td>
                        <td v-if="model.item_dt_kh">
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'khách hàng'" :api="'/listing/api/common/list'"
                            :table="'customer'" :FieldID="'CustomerID'" :FieldName="'CustomerName'"
                            :FieldNo="'CustomerNo'" :FieldType="1">
                          </IjcoreModalAccounting>
                        </td>
                        <td v-if="model.item_dt_dt">
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'đối tác'" :api="'/listing/api/common/list'"
                            :table="'partner'" :FieldID="'PartnerID'" :FieldName="'PartnerName'"
                            :FieldNo="'PartnerNo'" :FieldType="1">
                          </IjcoreModalAccounting>
                        </td>

                        <td>
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'quỹ'" :api="'/listing/api/common/list'"
                            :table="'fund'" :FieldID="'FundID'" :FieldName="'FundName'"
                            :FieldNo="'FundNo'" :FieldType="1">
                          </IjcoreModalAccounting>
                        </td>
                        <td>
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'khoản thu'" :api="'/listing/api/common/list'"
                            :table="'revenue'" :FieldID="'RevenueID'" :FieldName="'RevenueName'"
                            :FieldNo="'RevenueNo'" :FieldType="1" :FieldDetail="'Detail'">
                          </IjcoreModalAccounting>
                        </td>
                        <!-- Lĩnh vực thu -->
                        <td>
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'lĩnh vực thu'" :api="'/listing/api/common/list'"
                            :table="'sbr_sector'" :FieldID="'SbrSectorID'" :FieldName="'SbrSectorName'" :FieldNo="'SbrSectorNo'" :FieldType="1" :FieldDetail="'Detail'"
                            :FieldUpdate="['SbiChapterID', 'SbiChapterNo', 'SbiChapterName']">
                          </IjcoreModalAccounting>
                        </td>
                        <td>
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'khoản chi'" :api="'/listing/api/common/list'"
                            :table="'expense'" :FieldID="'ExpenseID'" :FieldName="'ExpenseName'"
                            :FieldNo="'ExpenseNo'" :FieldType="1" :FieldDetail="'Detail'"
                            :FieldUpdate="['SectorID', 'SectorNo', 'SectorName']">
                          </IjcoreModalAccounting>
                        </td>
                        <!-- Lĩnh vực chi -->
                        <td>
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'lĩnh vực chi'" :api="'/listing/api/common/list'"
                            :table="'sector'" :FieldID="'SectorID'" :FieldName="'SectorName'" :FieldNo="'SectorNo'" :FieldType="1" :FieldDetail="'Detail'"
                            :FieldUpdate="['SbiCategoryID', 'SbiCategoryNo', 'SbiCategoryName']">
                          </IjcoreModalAccounting>
                        </td>
                        <!-- Chỉ tiêu -->
                        <td>
                          <!--                        <IjcoreModalAccounting-->
                          <!--                          v-model="model.ActgvoucTransItem[key]" :title="'bảng chỉ tiêu định mức dự toán'" :api="'/listing/api/common/list'"-->
                          <!--                          :table="'norm_table_item'" :FieldID="'NormTableItemID'" :FieldName="'NormTableItemName'"-->
                          <!--                          :FieldUpdate="['NormID','NormNo','NormName']"-->
                          <!--                          :FieldWhere="{NormTableID : model.NormTableID, Detail : 1}" :FieldNo="'NormTableItemNo'" :FieldType="1" >-->
                          <!--                        </IjcoreModalAccounting>-->
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'chỉ tiêu định mức dự toán'" :api="'/listing/api/common/list'"
                            :table="'norm'" :FieldID="'NormID'" :FieldName="'NormName'" :FieldNo="'NormNo'" :FieldType="1" >
                          </IjcoreModalAccounting>
                        </td>
                        <!--Nguồn vốn -->
                        <td v-if="model.item_nv">
                          <b-form-select v-model="model.ActgvoucTransItem[key].BudgetLevel" :options="BudgetLevel">
                          </b-form-select>
                        </td>
                        <td v-if="model.item_nv">
                          <b-form-select v-model="model.ActgvoucTransItem[key].FiscalPeriod" :options="FiscalPeriod">
                          </b-form-select>
                        </td>
                        <td v-if="model.item_nv">
                          <b-form-input
                            type="text"
                            v-model="model.ActgvoucTransItem[key].FiscalYear"
                            class="text-center">
                          </b-form-input>
                        </td>
                        <td v-if="model.item_nv">
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'nguồn vốn'" :api="'/listing/api/common/list'"
                            :table="'capital'" :FieldID="'CapitalID'" :FieldName="'CapitalName'"
                            :FieldNo="'CapitalNo'" :FieldType="1" :FieldDetail="'Detail'">
                          </IjcoreModalAccounting>
                        </td>
                        <td v-if="model.item_nv">
                          <b-form-select v-model="model.ActgvoucTransItem[key].BudgetAllocTypeID" :options="BudgetAllocType">
                          </b-form-select>
                        </td>
                        <td v-if="model.item_nv">
                          <b-form-select v-model="model.ActgvoucTransItem[key].ReceiveBy" :options="ReceiveBy">
                          </b-form-select>
                        </td>

                        <!--MLNS -->
                        <td v-if="model.item_mlns">
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'chương'" :api="'/listing/api/common/list'"
                            :table="'sbi_chapter'" :FieldID="'SbiChapterID'" :FieldName="'SbiChapterName'" :FieldNo="'SbiChapterNo'" :FieldType="1" >
                          </IjcoreModalAccounting>
                        </td>
                        <td v-if="model.item_mlns">
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'loại - khoản'" :api="'/listing/api/common/list'"
                            :table="'sbi_category'" :FieldID="'SbiCategoryID'" :FieldName="'SbiCategoryName'" :FieldNo="'SbiCategoryNo'" :FieldType="1" >
                          </IjcoreModalAccounting>
                        </td>
                        <td v-if="model.item_mlns">
                          <IjcoreModalAccounting
                            v-model="model.ActgvoucTransItem[key]" :title="'mục - tiểu mục'" :api="'/listing/api/common/list'"
                            :table="'sbi_item'" :FieldID="'SbiItemID'" :FieldName="'SbiItemName'" :FieldNo="'SbiItemNo'" :FieldType="1" >
                          </IjcoreModalAccounting>
                        </td>
                        <td class="b-table-sticky-column-right" style="background: #fff; text-align: center;">
                          <i @click="$_tableTree_onDeleteFieldOnTable(key, 'ActgvoucTransItem', 'LineID')" class="fa fa-trash-o" title="Xóa"
                             style="font-size: 18px; cursor: pointer"></i>
                        </td>
                      </tr>
                    </draggable>
                  </table>
                </div>
                <div class="d-flex justify-content-between mt-2">
                  <div class="d-flex align-items-center">
                    <a @click="onAddFieldOnTable()" class="new-row mr-3">
                      <i aria-hidden="true" class="fa fa-plus-square-o ij-icon ij-icon-plus"></i> Thêm dòng
                    </a>
                  </div>
                  <div class="d-flex align-items-center">
                    <div class="table-pagination mr-3" v-if="model.ActgvoucTransItem.length > Number(perPage)">
                      <b-pagination
                        v-model="currentPage"
                        :total-rows="totalRows"
                        :per-page="perPage"
                        aria-controls="my-table"
                        size="md">
                      </b-pagination>
                    </div>
                  </div>
                </div>
                <div class="form-group row " style="margin-top: 20px;">
                  <!--                    <label class="col-md-3" for="Comment">Ghi chú</label>-->
                  <div class="col-md-15" style="padding-right: 0px !important;">
                    <textarea v-model="model.Comment" class="form-control" id="Comment" rows="3" placeholder="Ghi chú"></textarea>
                  </div>
                  <div  class="col-md-1">
                  </div>
                  <div class="col-md-8 row">
                    <label class="row" style="width: 100%"><span class="col-md-9">Tổng số tiền:</span> <span class="col-md-15 text-right" style="font-weight: bold;"> <span v-if="this.totalAmount" id="totalFCDebit">{{totalAmount | convertNumberToText}}</span> đ</span></label>
                    <label class="row" style="width: 100%"><span class="col-md-9">Tiền thuế:</span> <span class="col-md-15 text-right" style="font-weight: bold;"> <span v-if="this.totalTaxAmount" id="totalFCCredit">{{totalTaxAmount | convertNumberToText}}</span> đ</span></label>
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
<!--              <option>Chọn đối tượng</option>-->
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
    import Multiselect from "vue-multiselect";
    import Select2 from 'v-select2-component'
    import IjcoreModalListing from "../../../../components/IjcoreModalListing";
    import IjcoreModalAccounting from "../../../../components/IjcoreModalAccounting";
    import IjcoreModalAutoact from "../../../../components/IjcoreModalAutoact";
    import IjcoreNumber from "../../../../components/IjcoreNumber";
    import IjcoreModalMultiListing from "../../../../components/IjcoreModalMultiListing";
    import IjcoreModalMultiAct from "../../../../components/IjcoreModalMultiAct";
    import IjcoreDatePicker from "../../../../components/IjcoreDatePicker";
    import moment from 'moment';
    import ColumnResizer from "column-resizer";
    import NormTableForm from "../../../statebudgetplanning/sbpreviewplan/partials/NormTableForm";
    import IjcoreSelect2Server2 from "../../../../components/IjcoreSelect2Server2";
    import draggable from 'vuedraggable';
    moment.locale('vi');
	import mixinTablePagination from "@/mixins/tablePagination";
    import tableTree from "../../../../mixins/tableTree";

    const ListRouter = 'accounting-actgvouctrans';
    const EditRouter = 'accounting-actgvouctrans-edit';
    const ViewRouter = 'accounting-actgvouctrans-view';
    const CreateRouter = 'accounting-actgvouctrans-create';
    const ViewApi = 'accounting/api/actgvouctrans/view';
    const EditApi = 'accounting/api/actgvouctrans/edit';
    const CreateApi = 'accounting/api/actgvouctrans/create';
    const StoreApi = 'accounting/api/actgvouctrans/store';
    const UpdateApi = 'accounting/api/actgvouctrans/update';
    const ListApi = 'accounting/api/actgvouctrans';

    export default {
        name: 'accounting-view-item',
        mixins: [mixinTablePagination, tableTree],
        data () {
            return {
                StyleAction: 'width: 1800px;',
                idParams: this.idParamsProps,
                reqParams: this.reqParamsProps,
                model: {
                    TransDate: moment().format('L'),
                    PostDate: moment().format('L'),
                    eTransDate: moment().format('L'),
                    TransNo: '',
                    eTransNo: '',
                    TransTypeID: '1',
                    TransTypeName: '',
                    CoaTypeID: 1,
                    CoaTypeNo: '01',
                    CoaTypeName: 'TKHN',
                    Comment: '',
                    FCTotalAmount: '',
                    LCTotalAmount: '',
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
                    EmployeeID: '',
                    EmployeeNo: '',
                    EmployeeName: '',
                    EmployeeCitizenIdNo: '',
                    EmployeeCitizenIdDate: moment().format('L'),
                    EmployeeCitizenIdAt: '',
                    EmployeeDeparttmentName: '',
                    EmployeePosition: '',
                    EmployeeBankAccount: '',
                    EmployeeBankName: '',
                    VendorID: '',
                    VendorNo: '',
                    VendorName: '',
                    VendorTaxcode: '',
                    VendorLocationNo: '',
                    VendorAddress: '',
                    VendorBankAccount: '',
                    VendorBankName: '',
                    VendorContactName: '',
                    VendorContactPosition: '',
                    CustomerID: '',
                    CustomerNo: '',
                    CustomerName: '',
                    CustomerTaxcode: '',
                    CustomerLocationNo: '',
                    CustomerAddress: '',
                    CustomerBankAccount: '',
                    CustomerBankName: '',
                    CustomerContactName: '',
                    CustomerContactPosition: '',
                    PartnerID: '',
                    PartnerName: '',
                    PartnerTaxcode: '',
                    PartnerLocationNo: '',
                    PartnerAddress: '',
                    PartnerBankAccount: '',
                    PartnerBankName: '',
                    PartnerContactName: '',
                    PartnerContactPosition: '',
                    PartnerCitizenIdNo: '',
                    PartnerCitizenIdDate: moment().format('L'),
                    PartnerCitizenIdAt: '',
                    Locked: '',
                    LockedDate: '',
                    LockedUserID: '',
                    StatusID: 1,
                    StatusValue: 3,
                    StatusDescription: '',
                    inactive: false,
                    companyOption: [],
                    employeeOption: [],
                    ActgvoucTransItem: [],
                    AccoutingStatus: [],
                    Coatype: [],
                    Autoact: [],
                    ArrCoaChecked: [1,4],
                    maxLineID: 0,
                    ActPeriod: [],
                    ActPeriodOption: [],
                    PeriodType: 1,
                    PeriodID: 24,
                    PeriodName: new Date().getFullYear(),
                    PeriodFromDate:  '01/01/'+new Date().getFullYear(),
                    PeriodToDate:  '31/12/'+new Date().getFullYear(),
                    isAdjustTrans: false,
                    NormTableID: '',
                    NormTableNo: '',
                    NormTableName: '',
                    item_tt: false,
                    item_ts: false,
                    item_hd: false,
                    item_da: false,
                    item_dt: false,
                    item_nv: true,
                    item_mlns: true,
                    item_httk: false,
                    item_hcsn: true,
                    item_bqlda: false,
                    item_tkhn: true,
                    item_kbnn: false,
                    item_dt_cqnn: false,
                    item_dt_nv: false,
                    item_dt_ncc: false,
                    item_dt_kh: false,
                    item_dt_dt: false,
                    item_DF599: true,
                    item_599: false,
                },
                NormTable: {
                  NormTableID: null,
                  NormTableName: '',
                  NormTableNo: ''
                },
                FCDebitAmount: 0,
                totalFCDebit: 0,
                totalFCCredit: 0,
                check_ts: 0,
                RowItem: 1,
                scrollHeight: 0,
                item_ts_name: 'Vật tư/Tài sản',
                Table_CoaType: 'coa_con',
                HTTK: {},
                stage: {
                    updatedData: false
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
                arrObject: [],
            }

        },
        filters:{
          filterAutoact(value, NormType){
            if (NormType) {
              let options = _.filter(value, ['AutoactType', NormType]);
              if (options && options.length) {
                return options;
              }
            }
            return value;
          }
        },
        props: {
            idParamsProps: {
                type: Number,
                default: 0
            },
            reqParamsProps: {
                type: Object,
                default: function () {
                    return {}
                }
            },
            itemCopy: {
                type: Object,
                default: function () {
                    return {}
                }
            }
        },
        components: {
          IjcoreModalListing,
          IjcoreModalAccounting,
          IjcoreNumber,
          IjcoreModalMultiListing,
          IjcoreModalMultiAct,
          Multiselect,
          IjcoreDatePicker,
          IjcoreModalAutoact,
          NormTableForm,
          Select2,
          IjcoreSelect2Server2,
          draggable
        },
        beforeCreate() {},
        created() {
          if (!this.idParams && _.isEmpty(this.itemCopy)) {
                       // let fieldObj = {};
          // fieldObj.LineID = 1;
          // fieldObj.entry = 1;
          // fieldObj.CoAccountID = '';
          // fieldObj.CoAccountNo = '';
          // fieldObj.FCDebitAmount = this.model.FCDebitAmount;
          // fieldObj.InTransTypeID = 1;
          // fieldObj.CcyID = 33;
          // fieldObj.CcyName = 'VND';
          // fieldObj.ExchangeRate = 1;
          // fieldObj.Quantity = 1;
          // fieldObj.ItemID = this.model.OptionsProperty;
          // fieldObj.FiscalPeriod = 2;
          // fieldObj.ReceiveBy = 1;
          // fieldObj.BudgetLevel = 0;
          // fieldObj.BudgetAllocTypeID = 1;
          // fieldObj.UomID = 7;
          // fieldObj.UomNo = 401;
          // fieldObj.UomName = 'Đồng';
          // fieldObj.FiscalYear = new Date().getFullYear();
          // fieldObj.InvoiceDate = moment().format('L');
          // fieldObj.Description = '';
          // this.model.ActgvoucTransItem.push(fieldObj);
          	this.onAddFieldOnTable();
          }
        },
        mounted() {
            this.fetchData();
        },
        updated() {
            this.stage.updatedData = true;
        },
        computed: {
            itemNo(){
                let index = 0;
                index = this.reqParams.idsArray.indexOf(this.idParams) + 1 + this.reqParams.perPage * (this.reqParams.currentPage - 1);
                return index;
            },
            totalAmount(){
              let Amount =0;
              let sum = _.reduce(this.model.ActgvoucTransItem, function(sum, n) {
                if(n['Detail']==1) {
                  Amount = n['FCDebitAmount'];
                }
                if(n['FCDebitAmount'] === undefined){
                  n['FCDebitAmount'] = 0;
                }
                sum = (sum) ? sum : 0;
                Amount = (Amount) ? Amount : 0;
                return sum + Amount;
              }, 0);
              this.model.LCTotalAmount = sum;
              return sum;
            },
            totalTaxAmount(){
              let sum = _.reduce(this.model.ActgvoucTransItem, function(sum, n) {
                if(n['FCTaxAmount'] === undefined){
                  n['FCTaxAmount'] = 0;
                }
                return sum + n['FCTaxAmount'];
              }, 0);
              this.model.LCTotalTaxAmount = sum;
              return sum;
            }
        },
        methods: {
            fetchData() {
                if (document.querySelector('.table-column-resizable')) {
                  new ColumnResizer(
                    document.querySelector('.table-column-resizable'),{resizeMode: 'overflow'}
                  );
                }
                let self = this;
                let urlApi = CreateApi;
                let requestData = {
                    method: 'get',
                    data: {}
                };
                // Api edit user
                if(this.idParams){
                    urlApi = EditApi + '/' + this.idParams;
                    requestData.data.id = this.idParams;
                }
                requestData.url = urlApi;
                this.$store.commit('isLoading', true);

                ApiService.setHeader();
                ApiService.customRequest(requestData).then((responses) => {
                    let responsesData = responses.data;

                    // copy item
                    if (!self.idParams && !_.isEmpty(self.itemCopy)) {
                        responsesData.data.data = self.itemCopy.data.data;
                    }

                    if (responsesData.status === 1) {

                        if (self.idParams || !_.isEmpty(self.itemCopy)) {
                            if (_.isObject(responsesData.data.data)) {
                                self.model.TransDate = __.convertDateToString(responsesData.data.data.TransDate);
                                self.model.PostDate = __.convertDateToString(responsesData.data.data.PostDate);
                                self.model.eTransDate = __.convertDateToString(responsesData.data.data.eTransDate);
                                self.model.TransNo = responsesData.data.data.TransNo;
                                self.model.eTransNo = responsesData.data.data.eTransNo;
                                self.model.TransTypeID = responsesData.data.data.TransTypeID;
                                self.model.TransTypeName = responsesData.data.data.TransTypeName;
                                self.model.CoaTypeID = responsesData.data.data.CoaTypeID;
                                self.model.CoaTypeNo = responsesData.data.data.CoaTypeNo;
                                self.model.CoaTypeName = responsesData.data.data.CoaTypeName;
                                self.model.Comment = responsesData.data.data.Comment;
                                self.model.FCTotalAmount = responsesData.data.data.FCTotalAmount;
                                self.model.LCTotalAmount = responsesData.data.data.LCTotalAmount;
                                self.model.FCTotalTaxAmount = responsesData.data.data.FCTotalTaxAmount;
                                self.model.LCTotalTaxAmount = responsesData.data.data.LCTotalTaxAmount;
                                self.model.CompanyID = responsesData.data.data.CompanyID;
                                self.model.CompanyNo = responsesData.data.data.CompanyNo;
                                self.model.CompanyMOFNo = responsesData.data.data.CompanyMOFNo;
                                self.model.CompanyLocationNo = responsesData.data.data.CompanyLocationNo;
                                self.model.CompanyName = responsesData.data.data.CompanyName;
                                self.model.CompanyAddress = responsesData.data.data.CompanyAddress;
                                self.model.CompanyBankAccount = responsesData.data.data.CompanyBankAccount;
                                self.model.CompanyBankName = responsesData.data.data.CompanyBankName;
                                self.model.CompanyContactName = responsesData.data.data.CompanyContactName;
                                self.model.CompanyContactPosition = responsesData.data.data.CompanyContactPosition;
                                self.model.EmployeeID = responsesData.data.data.EmployeeID;
                                self.model.EmployeeNo = responsesData.data.data.EmployeeNo;
                                self.model.EmployeeName = responsesData.data.data.EmployeeName;
                                self.model.EmployeeCitizenIdNo = responsesData.data.data.EmployeeCitizenIdNo;
                                self.model.EmployeeCitizenIdDate = __.convertDateToString(responsesData.data.data.EmployeeCitizenIdDate);
                                self.model.EmployeeCitizenIdAt = responsesData.data.data.EmployeeCitizenIdAt;
                                self.model.EmployeeDeparttmentName = responsesData.data.data.EmployeeDeparttmentName;
                                self.model.EmployeePosition = responsesData.data.data.EmployeePosition;
                                self.model.EmployeeBankAccount = responsesData.data.data.EmployeeBankAccount;
                                self.model.EmployeeBankName = responsesData.data.data.EmployeeBankName;
                                self.model.VendorID = responsesData.data.data.VendorID;
                                self.model.VendorNo = responsesData.data.data.VendorNo;
                                self.model.VendorName = responsesData.data.data.VendorName;
                                self.model.VendorTaxcode = responsesData.data.data.VendorTaxcode;
                                self.model.VendorLocationNo = responsesData.data.data.VendorLocationNo;
                                self.model.VendorAddress = responsesData.data.data.VendorAddress;
                                self.model.VendorBankAccount = responsesData.data.data.VendorBankAccount;
                                self.model.VendorBankName = responsesData.data.data.VendorBankName;
                                self.model.VendorContactName = responsesData.data.data.VendorContactName;
                                self.model.VendorContactPosition = responsesData.data.data.VendorContactPosition;
                                self.model.CustomerID = responsesData.data.data.CustomerID;
                                self.model.CustomerNo = responsesData.data.data.CustomerNo;
                                self.model.CustomerName = responsesData.data.data.CustomerName;
                                self.model.CustomerTaxcode = responsesData.data.data.CustomerTaxcode;
                                self.model.CustomerLocationNo = responsesData.data.data.CustomerLocationNo;
                                self.model.CustomerAddress = responsesData.data.data.CustomerAddress;
                                self.model.CustomerBankAccount = responsesData.data.data.CustomerBankAccount;
                                self.model.CustomerBankName = responsesData.data.data.CustomerBankName;
                                self.model.CustomerContactName = responsesData.data.data.CustomerContactName;
                                self.model.CustomerContactPosition = responsesData.data.data.CustomerContactPosition;
                                self.model.PartnerID = responsesData.data.data.PartnerID;
                                self.model.PartnerName = responsesData.data.data.PartnerName;
                                self.model.PartnerTaxcode = responsesData.data.data.PartnerTaxcode;
                                self.model.PartnerLocationNo = responsesData.data.data.PartnerLocationNo;
                                self.model.PartnerAddress = responsesData.data.data.PartnerAddress;
                                self.model.PartnerBankAccount = responsesData.data.data.PartnerBankAccount;
                                self.model.PartnerBankName = responsesData.data.data.PartnerBankName;
                                self.model.PartnerContactName = responsesData.data.data.PartnerContactName;
                                self.model.PartnerContactPosition = responsesData.data.data.PartnerContactPosition;
                                self.model.PartnerCitizenIdNo = responsesData.data.data.PartnerCitizenIdNo;
                                self.model.PartnerCitizenIdDate = __.convertDateToString(responsesData.data.data.PartnerCitizenIdDate);
                                self.model.PartnerCitizenIdAt = responsesData.data.data.PartnerCitizenIdAt;
                                self.model.Locked = responsesData.data.data.Locked;
                                self.model.LockedDate = responsesData.data.data.LockedDate;
                                self.model.LockedUserID = responsesData.data.data.LockedUserID;
                                self.model.StatusID = responsesData.data.data.StatusID;
                                self.model.StatusValue = responsesData.data.data.StatusValue;
                                self.model.StatusDescription = responsesData.data.data.StatusDescription;
                                //self.model.inactive = (responsesData.data.data.Inactive) ? true : false;
                                self.model.NormTableID = responsesData.data.data.NormTableID;
                                self.model.NormTableNo = responsesData.data.data.NormTableNo;
                                self.model.NormTableName = responsesData.data.data.NormTableName;
                                self.model.PeriodID = responsesData.data.data.PeriodID;
                                self.model.PeriodType = responsesData.data.data.PeriodType;
                                self.model.PeriodName = responsesData.data.data.PeriodName;
                                self.model.PeriodFromDate =  __.convertDateToString(responsesData.data.data.PeriodFromDate);
                                self.model.PeriodToDate =  __.convertDateToString(responsesData.data.data.PeriodToDate);
                                self.model.isAdjustTrans = (responsesData.data.data.isAdjustTrans) ? true : false;

                                self.model.ActgvoucTransItem = responsesData.data.AccountingTrans;
                                _.forEach(self.model.ActgvoucTransItem, function (value, key){
                                  if(value.actautoact){
                                    value.AutoactName = value.actautoact.AutoactName;
                                  }else{value.AutoactName = '';}
                                  self.$set(self.model.ActgvoucTransItem[key], 'Show', true);
                                  self.$set(self.model.ActgvoucTransItem[key], 'ShowPagination', false);
                                  if (value.LineID > self.RowItem) {
                                    self.RowItem = value.LineIDTmp;
                                  }
                                })
                                self.RowItem += 1;
                                self.model.Status = responsesData.data.Status;
                                self.model.StatusItem = responsesData.data.StatusItem;
                                self.model.ArrCoatype = responsesData.data.ArrCoatype;
                                self.model.ArrCoaChecked = JSON.parse(responsesData.data.data.ArrCoaChecked);
                                self.model.Sysperiod = responsesData.data.Sysperiod;
                                self.model.ArrAutoact = responsesData.data.ArrAutoact;
                                _.forEach(self.model.ArrCoaChecked,function(item,key){
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

                            }
                            if (!_.isEmpty(self.itemCopy)) {
                                self.model.TransNo = responsesData.data.auto;
                                self.model.eTransNo = Number(self.model.TransNo) + 1;
                                self.model.ActgvoucTransItem = self.itemCopy.data.AccountingTrans;
                                self.model.Actautoact = self.itemCopy.data.Actautoact;
                                _.forEach(self.model.ActgvoucTransItem, function (value, key){
                                  let ArrPeriodObj = _.find(self.model.Actautoact, ['AutoactID', value.AutoactID]);
                                  if(ArrPeriodObj){
                                  value.AutoactName =  ArrPeriodObj.AutoactName;
                                  }else{ value.AutoactName ='';}
                                  self.$set(self.model.ActgvoucTransItem[key], 'Show', true);
                                  self.$set(self.model.ActgvoucTransItem[key], 'ShowPagination', false);
                                  if (value.LineID > self.RowItem) {
                                    self.RowItem = value.LineIDTmp;
                                  }
                                })
                                self.RowItem += 1;
                             }
                            this.changePage();
                        }else {
                            self.model.TransNo = responsesData.data.auto;
                            self.model.eTransNo = Number(self.model.TransNo) + 1;
                        }

                        if (_.isArray(responsesData.data.company)) {

                            self.model.companyOption = [];
                            _.forEach(responsesData.data.company, function (value, key) {
                                let tmpObj = {};
                                tmpObj.id = value.CompanyID;
                                tmpObj.text = value.CompanyName;
                                self.model.companyOption.push(tmpObj);
                            });
                        }

                        if (_.isArray(responsesData.data.employee)) {

                            self.model.employeeOption = [];
                            _.forEach(responsesData.data.employee, function (value, key) {
                                let tmpObj = {};
                                tmpObj.id = value.EmployeeID;
                                tmpObj.text = value.EmployeeName;
                                self.model.employeeOption.push(tmpObj);
                            });
                        }
                      if (_.isArray(responsesData.data.ArrCoatype)) {
                        self.Coatype = [];
                        _.forEach(responsesData.data.ArrCoatype, function (value, key) {
                            let tmpObj = {};
                            tmpObj.value = value.CoaTypeID;
                            tmpObj.text = value.CoaTypeName;
                            self.model.Coatype.push(tmpObj);
                        });
                      }
                      //ArrAutoact
                      if (_.isArray(responsesData.data.ArrAutoact)) {
                        self.Autoact = [];
                        _.forEach(responsesData.data.ArrAutoact, function (value, key) {
                          //if(value.SysAutoactTypeID>=116 && value.SysAutoactTypeID <=120){
                            let tmpObj = {};
                            tmpObj.value = value.AutoactID;
                            tmpObj.text = value.AutoactName;
                            tmpObj.AccountNo = value.DebitAccountNo;
                            tmpObj.AccountNo = value.DebitAccountName;
                            tmpObj.CoAccountNo = value.CreditAccountNo;
                            tmpObj.CoAccountNo = value.CreditAccountName;
                            self.model.Autoact.push(tmpObj);
                          //}
                        });
                      }//console.log(self.model.Autoact);
                      //
                      if (_.isArray(responsesData.data.StatusItem)) {
                        self.AccoutingStatus = [];
                        _.forEach(responsesData.data.StatusItem, function (value, key) {
                          if(value.StatusID==1){
                          let tmpObj = {};
                          tmpObj.value = value.StatusValue;
                          tmpObj.text = value.StatusDescription;
                          tmpObj.StatusID = value.StatusID;
                          self.model.AccoutingStatus.push(tmpObj);
                          }
                        });
                      }
                      //ActPeriod
                      if (_.isArray(responsesData.data.Sysperiod)) {
                        self.model.PeriodOption = [{value: null, text: '--Chọn kỳ HT--'}];
                        _.forEach(responsesData.data.Sysperiod, function (value, key) {
                          let tmpObj = {};
                          tmpObj.value = value.PeriodID;
                          tmpObj.PeriodType = value.PeriodType;
                          tmpObj.text = value.PeriodName;
                          tmpObj.PeriodFromDate = value.PeriodFromDate;
                          tmpObj.PeriodToDate = value.PeriodToDate;
                          self.model.PeriodOption.push(tmpObj);
                        });
                        this.changePeriodType();
                      }

                    }
                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                });
            },
            changeParent(){
              let self = this;
              let urlApi = this.api;
              let requestData = {
                method: 'post',
                url: '/accounting/api/common/auto-child',
                data: {
                  per_page: 10,
                  page: this.currentPage,
                  table: 'company',
                  ParentID: this.model.parentID,
                },

              };

              ApiService.customRequest(requestData).then((response) => {
                let responseData = response.data;

                this.model.companyNo = responseData.data;
                self.$store.commit('isLoading', false);
              }, (error) => {
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
            getItemIds(type){
                let self = this;
                let requestData = {
                    method: 'post',
                    url: ListApi,
                    data: {
                        per_page: this.reqParams.perPage,
                        page: this.reqParams.currentPage,
                        type: 'only-id'
                    }
                };

                if (this.reqParams.search.companyNo !== '') {
                    requestData.data.CompanyNo = this.reqParams.search.companyNo;
                }
                if (this.reqParams.search.companyName !== '') {
                    requestData.data.CompanyName = this.reqParams.search.companyName;
                }
                if (this.reqParams.search.tel !== '') {
                    requestData.data.Tel = this.reqParams.search.tel;
                }
                if (this.reqParams.search.fax !== '') {
                    requestData.data.Fax = this.reqParams.search.fax;
                }
                if (this.reqParams.search.email !== '') {
                    requestData.data.Email = this.reqParams.search.email;
                }

                this.$store.commit('isLoading', true);
                ApiService.customRequest(requestData).then((response) => {
                    let dataResponse = response.data;
                    if (dataResponse.status === 1) {
                        self.reqParams.total = dataResponse.data.total;
                        self.reqParams.perPage = String(dataResponse.data.per_page);
                        self.reqParams.currentPage = dataResponse.data.current_page;

                        this.reqParams.idsArray = [];
                        _.forEach(dataResponse.data.data, function (value, key) {
                            self.reqParams.idsArray.push(value.CompanyID);
                        });

                        (type == 'prev') ? self.idParams = self.reqParams.idsArray[self.reqParams.idsArray.length - 1] : self.idParams = self.reqParams.idsArray[0];
                    }
                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    self.$store.commit('isLoading', false);
                });

            },
            onAddFieldChildrenOnTable(keyParent){
              let tmpObj = {};
              this.model.ActgvoucTransItem[keyParent].Detail = 0;
              tmpObj.LineID = this.RowItem; this.RowItem ++;
              tmpObj.Level = this.model.ActgvoucTransItem[keyParent].Level + 1;
              tmpObj.Detail = 1;
              tmpObj.ParentID = this.model.ActgvoucTransItem[keyParent].LineID;
              tmpObj.AutoactID = this.model.ActgvoucTransItem[keyParent].AutoactID;
              tmpObj.AutoactName = this.model.ActgvoucTransItem[keyParent].AutoactName;
              tmpObj.AccountID = this.model.ActgvoucTransItem[keyParent].AccountID;
              tmpObj.AccountNo = this.model.ActgvoucTransItem[keyParent].AccountNo;
              tmpObj.CoAccountID = this.model.ActgvoucTransItem[keyParent].CoAccountID;
              tmpObj.CoAccountNo = this.model.ActgvoucTransItem[keyParent].CoAccountNo;
              tmpObj.FCDebitAmount = this.model.ActgvoucTransItem[keyParent].FCDebitAmount;
              tmpObj.LCDebitAmount = this.model.ActgvoucTransItem[keyParent].LCDebitAmount;
              tmpObj.FCExecAmount = this.model.ActgvoucTransItem[keyParent].FCExecAmount;
              tmpObj.LCExecAmount = this.model.ActgvoucTransItem[keyParent].LCExecAmount;
              tmpObj.Description = this.model.ActgvoucTransItem[keyParent].Description;
              tmpObj.CcyID = this.model.ActgvoucTransItem[keyParent].CcyID;
              tmpObj.CcyName = 'VND';
              tmpObj.ExchangeRate = this.model.ActgvoucTransItem[keyParent].ExchangeRate;
              tmpObj.Quantity = this.model.ActgvoucTransItem[keyParent].Quantity;
              tmpObj.FiscalPeriod = this.model.ActgvoucTransItem[keyParent].FiscalPeriod;
              tmpObj.FiscalYear = this.model.ActgvoucTransItem[keyParent].FiscalYear;
              tmpObj.InvoiceDate = this.model.ActgvoucTransItem[keyParent].InvoiceDate;
              tmpObj.SectorID = this.model.ActgvoucTransItem[keyParent].SectorID;
              tmpObj.SectorNo = this.model.ActgvoucTransItem[keyParent].SectorNo;
              tmpObj.SectorName = this.model.ActgvoucTransItem[keyParent].SectorName;
              tmpObj.SbiCategoryID = this.model.ActgvoucTransItem[keyParent].SbiCategoryID;
              tmpObj.SbiCategoryName = this.model.ActgvoucTransItem[keyParent].SbiCategoryName;
              tmpObj.SbiCategoryNo = this.model.ActgvoucTransItem[keyParent].SbiCategoryNo;
              tmpObj.NormTableItemID = this.model.ActgvoucTransItem[keyParent].NormTableItemID;
              tmpObj.NormTableItemNo = this.model.ActgvoucTransItem[keyParent].NormTableItemNo;
              tmpObj.NormTableItemName = this.model.ActgvoucTransItem[keyParent].NormTableItemName;
              tmpObj.NormID = this.model.ActgvoucTransItem[keyParent].NormID;
              tmpObj.NormNo = this.model.ActgvoucTransItem[keyParent].NormNo;
              tmpObj.NormName = this.model.ActgvoucTransItem[keyParent].NormName;
              tmpObj.NormID = this.model.ActgvoucTransItem[keyParent].NormID;
              tmpObj.NormNo = this.model.ActgvoucTransItem[keyParent].NormNo;
              tmpObj.NormName = this.model.ActgvoucTransItem[keyParent].NormName;
              tmpObj.ProgramID = this.model.ActgvoucTransItem[keyParent].ProgramID;
              tmpObj.ProgramNo = this.model.ActgvoucTransItem[keyParent].ProgramNo;
              tmpObj.ProgramName = this.model.ActgvoucTransItem[keyParent].ProgramName;
              tmpObj.ProgramID = this.model.ActgvoucTransItem[keyParent].ProgramID;
              tmpObj.ProgramNo = this.model.ActgvoucTransItem[keyParent].ProgramNo;
              tmpObj.ProgramName = this.model.ActgvoucTransItem[keyParent].ProgramName;
              tmpObj.EmployeeID = this.model.ActgvoucTransItem[keyParent].EmployeeID;
              tmpObj.EmployeeNo = this.model.ActgvoucTransItem[keyParent].EmployeeNo;
              tmpObj.EmployeeName = this.model.ActgvoucTransItem[keyParent].EmployeeName;
              tmpObj.CustomerID = this.model.ActgvoucTransItem[keyParent].CustomerID;
              tmpObj.CustomerNo = this.model.ActgvoucTransItem[keyParent].CustomerNo;
              tmpObj.CustomerName = this.model.ActgvoucTransItem[keyParent].CustomerName;
              tmpObj.VendorID = this.model.ActgvoucTransItem[keyParent].VendorID;
              tmpObj.VendorNo = this.model.ActgvoucTransItem[keyParent].VendorNo;
              tmpObj.VendorName = this.model.ActgvoucTransItem[keyParent].VendorName;
              tmpObj.PartnerID = this.model.ActgvoucTransItem[keyParent].PartnerID;
              tmpObj.PartnerNo = this.model.ActgvoucTransItem[keyParent].PartnerNo;
              tmpObj.FullName = this.model.ActgvoucTransItem[keyParent].FullName;
              tmpObj.RevenueID = this.model.ActgvoucTransItem[keyParent].RevenueID;
              tmpObj.RevenueNo = this.model.ActgvoucTransItem[keyParent].RevenueNo;
              tmpObj.RevenueName = this.model.ActgvoucTransItem[keyParent].RevenueName;
              tmpObj.ExpenseID = this.model.ActgvoucTransItem[keyParent].ExpenseID;
              tmpObj.ExpenseNo = this.model.ActgvoucTransItem[keyParent].ExpenseNo;
              tmpObj.ExpenseName = this.model.ActgvoucTransItem[keyParent].ExpenseName;
              tmpObj.FundID = this.model.ActgvoucTransItem[keyParent].FundID;
              tmpObj.FundNo = this.model.ActgvoucTransItem[keyParent].FundNo;
              tmpObj.FundName = this.model.ActgvoucTransItem[keyParent].FundName;
              tmpObj.CapitalID = this.model.ActgvoucTransItem[keyParent].CapitalID;
              tmpObj.CapitalNo = this.model.ActgvoucTransItem[keyParent].CapitalNo;
              tmpObj.CapitalName = this.model.ActgvoucTransItem[keyParent].CapitalName;
              tmpObj.ExpenseID = this.model.ActgvoucTransItem[keyParent].ExpenseID;
              tmpObj.ExpenseNo = this.model.ActgvoucTransItem[keyParent].ExpenseNo;
              tmpObj.ExpenseName = this.model.ActgvoucTransItem[keyParent].ExpenseName;
              tmpObj.SbiChapterID = this.model.ActgvoucTransItem[keyParent].SbiChapterID;
              tmpObj.SbiChapterNo = this.model.ActgvoucTransItem[keyParent].SbiChapterNo;
              tmpObj.SbiChapterName = this.model.ActgvoucTransItem[keyParent].SbiChapterName;
              tmpObj.SbiItemID = this.model.ActgvoucTransItem[keyParent].SbiItemID;
              tmpObj.SbiItemNo = this.model.ActgvoucTransItem[keyParent].SbiItemNo;
              tmpObj.SbiItemName = this.model.ActgvoucTransItem[keyParent].SbiItemName;
              tmpObj.ProjectID = this.model.ActgvoucTransItem[keyParent].ProjectID;
              tmpObj.ProjectNo = this.model.ActgvoucTransItem[keyParent].ProjectNo;
              tmpObj.TabmisNo = this.model.ActgvoucTransItem[keyParent].TabmisNo;
              tmpObj.ProjectName = this.model.ActgvoucTransItem[keyParent].ProjectName;
              tmpObj.ContractID = this.model.ActgvoucTransItem[keyParent].ContractID;
              tmpObj.ContractNo = this.model.ActgvoucTransItem[keyParent].ContractNo;
              tmpObj.ContractName = this.model.ActgvoucTransItem[keyParent].ContractName;
              tmpObj.FixedAssetID = this.model.ActgvoucTransItem[keyParent].FixedAssetID;
              tmpObj.FixedAssetNo = this.model.ActgvoucTransItem[keyParent].FixedAssetNo;
              tmpObj.FixedAssetName = this.model.ActgvoucTransItem[keyParent].FixedAssetName;
              tmpObj.ToolID = this.model.ActgvoucTransItem[keyParent].ToolID;
              tmpObj.ToolNo = this.model.ActgvoucTransItem[keyParent].ToolNo;
              tmpObj.ToolName = this.model.ActgvoucTransItem[keyParent].ToolName;
              tmpObj.InvestAssetID = this.model.ActgvoucTransItem[keyParent].InvestAssetID;
              tmpObj.InvestAssetNo = this.model.ActgvoucTransItem[keyParent].InvestAssetNo;
              tmpObj.InvestAssetName = this.model.ActgvoucTransItem[keyParent].InvestAssetName;
              tmpObj.ItemID = this.model.ActgvoucTransItem[keyParent].ItemID;
              tmpObj.ItemNo = this.model.ActgvoucTransItem[keyParent].ItemNo;
              tmpObj.ItemName = this.model.ActgvoucTransItem[keyParent].ItemName;
              tmpObj.UomID = this.model.ActgvoucTransItem[keyParent].UomID;
              tmpObj.UomNo = this.model.ActgvoucTransItem[keyParent].UomNo;
              tmpObj.UomName = this.model.ActgvoucTransItem[keyParent].UomName;
              tmpObj.BudgetLevel = this.model.ActgvoucTransItem[keyParent].BudgetLevel;
              tmpObj.BudgetAllocTypeID = this.model.ActgvoucTransItem[keyParent].BudgetAllocTypeID;
              tmpObj.ReceiveBy = this.model.ActgvoucTransItem[keyParent].ReceiveBy;
              tmpObj.Show = true;
              tmpObj.ShowPagination = true;

              let indexInsert = keyParent + 1;
              let allChild = _.filter(this.model.ActgvoucTransItem, ['ParentID', this.model.ActgvoucTransItem[keyParent].LineID]);
              if (allChild.length) {
                let lastItemChild = allChild[allChild.length - 1];
                let indexLastChild = _.findIndex(this.model.ActgvoucTransItem, ['LineID', lastItemChild.LineID]);
                indexInsert = indexLastChild + 1;
              }
              this.model.ActgvoucTransItem = __.insertBeforeKey(this.model.ActgvoucTransItem, indexInsert, tmpObj);
            },
            onCloneFieldChildrenOnTable(keyClone){
              let cloneObj = {};
              //cloneObj.LineID = this.model.ActgvoucTransItem[keyClone].LineID + 1 ;
              cloneObj.LineID = this.RowItem; this.RowItem ++;
              cloneObj.ParentID = this.model.ActgvoucTransItem[keyClone].ParentID;
              cloneObj.Level = this.model.ActgvoucTransItem[keyClone].Level;
              cloneObj.Detail = 1;
              cloneObj.AutoactID = this.model.ActgvoucTransItem[keyClone].AutoactID;
              cloneObj.AutoactName = this.model.ActgvoucTransItem[keyClone].AutoactName;
              cloneObj.AccountID = this.model.ActgvoucTransItem[keyClone].AccountID;
              cloneObj.AccountNo = this.model.ActgvoucTransItem[keyClone].AccountNo;
              cloneObj.CoAccountID = this.model.ActgvoucTransItem[keyClone].CoAccountID;
              cloneObj.CoAccountNo = this.model.ActgvoucTransItem[keyClone].CoAccountNo;
              cloneObj.FCDebitAmount = this.model.ActgvoucTransItem[keyClone].FCDebitAmount;
              cloneObj.LCDebitAmount = this.model.ActgvoucTransItem[keyClone].LCDebitAmount;
              cloneObj.Description = this.model.ActgvoucTransItem[keyClone].Description;
              cloneObj.CcyID = this.model.ActgvoucTransItem[keyClone].CcyID;
              cloneObj.CcyName = 'VND';
              cloneObj.ExchangeRate = this.model.ActgvoucTransItem[keyClone].ExchangeRate;
              cloneObj.Quantity = this.model.ActgvoucTransItem[keyClone].Quantity;
              cloneObj.FiscalPeriod = this.model.ActgvoucTransItem[keyClone].FiscalPeriod;
              cloneObj.FiscalYear = this.model.ActgvoucTransItem[keyClone].FiscalYear;
              cloneObj.InvoiceDate = this.model.ActgvoucTransItem[keyClone].InvoiceDate;
              cloneObj.SectorID = this.model.ActgvoucTransItem[keyClone].SectorID;
              cloneObj.SectorNo = this.model.ActgvoucTransItem[keyClone].SectorNo;
              cloneObj.SectorName = this.model.ActgvoucTransItem[keyClone].SectorName;
              cloneObj.SbiCategoryID = this.model.ActgvoucTransItem[keyClone].SbiCategoryID;
              cloneObj.SbiCategoryName = this.model.ActgvoucTransItem[keyClone].SbiCategoryName;
              cloneObj.SbiCategoryNo = this.model.ActgvoucTransItem[keyClone].SbiCategoryNo;
              cloneObj.NormTableItemID = this.model.ActgvoucTransItem[keyClone].NormTableItemID;
              cloneObj.NormTableItemNo = this.model.ActgvoucTransItem[keyClone].NormTableItemNo;
              cloneObj.NormTableItemName = this.model.ActgvoucTransItem[keyClone].NormTableItemName;
              cloneObj.NormID = this.model.ActgvoucTransItem[keyClone].NormID;
              cloneObj.NormNo = this.model.ActgvoucTransItem[keyClone].NormNo;
              cloneObj.NormName = this.model.ActgvoucTransItem[keyClone].NormName;
              cloneObj.ProgramID = this.model.ActgvoucTransItem[keyClone].ProgramID;
              cloneObj.ProgramNo = this.model.ActgvoucTransItem[keyClone].ProgramNo;
              cloneObj.ProgramName = this.model.ActgvoucTransItem[keyClone].ProgramName;
              cloneObj.ProgramID = this.model.ActgvoucTransItem[keyClone].ProgramID;
              cloneObj.ProgramNo = this.model.ActgvoucTransItem[keyClone].ProgramNo;
              cloneObj.ProgramName = this.model.ActgvoucTransItem[keyClone].ProgramName;
              cloneObj.EmployeeID = this.model.ActgvoucTransItem[keyClone].EmployeeID;
              cloneObj.EmployeeNo = this.model.ActgvoucTransItem[keyClone].EmployeeNo;
              cloneObj.EmployeeName = this.model.ActgvoucTransItem[keyClone].EmployeeName;
              cloneObj.CustomerID = this.model.ActgvoucTransItem[keyClone].CustomerID;
              cloneObj.CustomerNo = this.model.ActgvoucTransItem[keyClone].CustomerNo;
              cloneObj.CustomerName = this.model.ActgvoucTransItem[keyClone].CustomerName;
              cloneObj.VendorID = this.model.ActgvoucTransItem[keyClone].VendorID;
              cloneObj.VendorNo = this.model.ActgvoucTransItem[keyClone].VendorNo;
              cloneObj.VendorName = this.model.ActgvoucTransItem[keyClone].VendorName;
              cloneObj.PartnerID = this.model.ActgvoucTransItem[keyClone].PartnerID;
              cloneObj.PartnerNo = this.model.ActgvoucTransItem[keyClone].PartnerNo;
              cloneObj.FullName = this.model.ActgvoucTransItem[keyClone].FullName;
              cloneObj.RevenueID = this.model.ActgvoucTransItem[keyClone].RevenueID;
              cloneObj.RevenueNo = this.model.ActgvoucTransItem[keyClone].RevenueNo;
              cloneObj.RevenueName = this.model.ActgvoucTransItem[keyClone].RevenueName;
              cloneObj.ExpenseID = this.model.ActgvoucTransItem[keyClone].ExpenseID;
              cloneObj.ExpenseNo = this.model.ActgvoucTransItem[keyClone].ExpenseNo;
              cloneObj.ExpenseName = this.model.ActgvoucTransItem[keyClone].ExpenseName;
              cloneObj.FundID = this.model.ActgvoucTransItem[keyClone].FundID;
              cloneObj.FundNo = this.model.ActgvoucTransItem[keyClone].FundNo;
              cloneObj.FundName = this.model.ActgvoucTransItem[keyClone].FundName;
              cloneObj.CapitalID = this.model.ActgvoucTransItem[keyClone].CapitalID;
              cloneObj.CapitalNo = this.model.ActgvoucTransItem[keyClone].CapitalNo;
              cloneObj.CapitalName = this.model.ActgvoucTransItem[keyClone].CapitalName;
              cloneObj.ExpenseID = this.model.ActgvoucTransItem[keyClone].ExpenseID;
              cloneObj.ExpenseNo = this.model.ActgvoucTransItem[keyClone].ExpenseNo;
              cloneObj.ExpenseName = this.model.ActgvoucTransItem[keyClone].ExpenseName;
              cloneObj.SbiChapterID = this.model.ActgvoucTransItem[keyClone].SbiChapterID;
              cloneObj.SbiChapterNo = this.model.ActgvoucTransItem[keyClone].SbiChapterNo;
              cloneObj.SbiChapterName = this.model.ActgvoucTransItem[keyClone].SbiChapterName;
              cloneObj.SbiItemID = this.model.ActgvoucTransItem[keyClone].SbiItemID;
              cloneObj.SbiItemNo = this.model.ActgvoucTransItem[keyClone].SbiItemNo;
              cloneObj.SbiItemName = this.model.ActgvoucTransItem[keyClone].SbiItemName;
              cloneObj.ProjectID = this.model.ActgvoucTransItem[keyClone].ProjectID;
              cloneObj.ProjectNo = this.model.ActgvoucTransItem[keyClone].ProjectNo;
              cloneObj.TabmisNo = this.model.ActgvoucTransItem[keyClone].TabmisNo;
              cloneObj.ProjectName = this.model.ActgvoucTransItem[keyClone].ProjectName;
              cloneObj.ContractID = this.model.ActgvoucTransItem[keyClone].ContractID;
              cloneObj.ContractNo = this.model.ActgvoucTransItem[keyClone].ContractNo;
              cloneObj.ContractName = this.model.ActgvoucTransItem[keyClone].ContractName;
              cloneObj.FixedAssetID = this.model.ActgvoucTransItem[keyClone].FixedAssetID;
              cloneObj.FixedAssetNo = this.model.ActgvoucTransItem[keyClone].FixedAssetNo;
              cloneObj.FixedAssetName = this.model.ActgvoucTransItem[keyClone].FixedAssetName;
              cloneObj.ToolID = this.model.ActgvoucTransItem[keyClone].ToolID;
              cloneObj.ToolNo = this.model.ActgvoucTransItem[keyClone].ToolNo;
              cloneObj.ToolName = this.model.ActgvoucTransItem[keyClone].ToolName;
              cloneObj.InvestAssetID = this.model.ActgvoucTransItem[keyClone].InvestAssetID;
              cloneObj.InvestAssetNo = this.model.ActgvoucTransItem[keyClone].InvestAssetNo;
              cloneObj.InvestAssetName = this.model.ActgvoucTransItem[keyClone].InvestAssetName;
              cloneObj.ItemID = this.model.ActgvoucTransItem[keyClone].ItemID;
              cloneObj.ItemNo = this.model.ActgvoucTransItem[keyClone].ItemNo;
              cloneObj.ItemName = this.model.ActgvoucTransItem[keyClone].ItemName;
              cloneObj.UomID = this.model.ActgvoucTransItem[keyClone].UomID;
              cloneObj.UomNo = this.model.ActgvoucTransItem[keyClone].UomNo;
              cloneObj.UomName = this.model.ActgvoucTransItem[keyClone].UomName;
              cloneObj.BudgetLevel = this.model.ActgvoucTransItem[keyClone].BudgetLevel;
              cloneObj.BudgetAllocTypeID = this.model.ActgvoucTransItem[keyClone].BudgetAllocTypeID;
              cloneObj.ReceiveBy = this.model.ActgvoucTransItem[keyClone].ReceiveBy;
              cloneObj.Show = true;
              cloneObj.ShowPagination = true;

              if (this.model.ActgvoucTransItem[keyClone].Detail === 1) {
                this.model.ActgvoucTransItem = __.insertBeforeKey(this.model.ActgvoucTransItem, keyClone + 1, cloneObj);
              } else {
                let allChild = _.filter(this.model.ActgvoucTransItem, ['ParentID', this.model.ActgvoucTransItem[keyClone].LineID]);
                if (allChild.length) {
                  let lastItemChild = allChild[allChild.length - 1];
                  let indexLastChild = _.findIndex(this.model.ActgvoucTransItem, ['LineID', lastItemChild.LineID]);
                  this.model.ActgvoucTransItem = __.insertBeforeKey(this.model.ActgvoucTransItem, indexLastChild + 1, cloneObj);
                }
              }
            },
            onAddFieldOnTable() {
              let fieldObj = {};
              this.isAddNormTable = true;
              fieldObj.LineID = this.RowItem;
              this.RowItem ++;
              fieldObj.ParentID = null
              fieldObj.Level = 1;
              fieldObj.Detail = 1;
              fieldObj.CoAccountID = '';
              fieldObj.CoAccountNo = null;
              fieldObj.AccountID = null;
              fieldObj.AccountNo = '';
              fieldObj.AccountName = '';
              fieldObj.CoAccountID = null;
              fieldObj.CoAccountNo = '';
              fieldObj.CoAccountName = '';
              fieldObj.FCDebitAmount = this.model.FCDebitAmount;
              fieldObj.Description = '';
              fieldObj.SectorID = null;
              fieldObj.SectorNo = '';
              fieldObj.SectorName = '';
              fieldObj.SbrSectorID = null;
              fieldObj.SbrSectorNo = '';
              fieldObj.SbrSectorName = '';
              fieldObj.CcyID = 33;
              fieldObj.CcyNo = '033';
              fieldObj.CcyName = 'VND';
              fieldObj.ExchangeRate = 1;
              fieldObj.Quantity = 1;
              fieldObj.FiscalPeriod = 2;
              fieldObj.ReceiveBy = 1;
              fieldObj.BudgetLevel = 0;
              fieldObj.BudgetAllocTypeID = 1;
              fieldObj.UomID = 7;
              fieldObj.UomNo = 401;
              fieldObj.UomName = 'Đồng';
              fieldObj.FiscalYear = new Date().getFullYear();
              fieldObj.InvoiceDate = moment().format('L');
              fieldObj.SbiCategoryID = '';
              fieldObj.SbiCategoryName = '';
              fieldObj.SbiCategoryNo = '';
              fieldObj.Show = true;
              fieldObj.ShowPagination = true;

              let currentItems = _.filter(this.model.ActgvoucTransItem, ['ShowPagination', true]);
              if (currentItems.length) {
                let lastItem = currentItems.pop();
                if (lastItem) {
                  let lastIndexItem = _.findIndex(this.model.ActgvoucTransItem, ['LineID', lastItem.LineID]);
                  if (lastIndexItem > -1) {
                    this.model.ActgvoucTransItem = __.insertBeforeKey(this.model.ActgvoucTransItem, lastIndexItem + 1, fieldObj);
                  }else {
                    this.model.ActgvoucTransItem.push(fieldObj);
                  }
                }else {
                  this.model.ActgvoucTransItem.push(fieldObj);
                }
              }else {
                this.model.ActgvoucTransItem.push(fieldObj);
              }
            },
            onDeleteFieldOnTable(key) {
              if (this.model.ActgvoucTransItem[key].ParentID) {
                let siblings = _.filter(this.model.ActgvoucTransItem, ['ParentID', this.model.ActgvoucTransItem[key].ParentID]);
                if (siblings && siblings.length === 1) {
                  let indexParent = _.findIndex(this.model.ActgvoucTransItem, ['LineID', this.model.ActgvoucTransItem[key].ParentID]);
                  if (indexParent > -1) {
                    this.model.ActgvoucTransItem[indexParent].Detail = 1;
                  }
                }
              }
              this.model.ActgvoucTransItem.splice(key, 1);
            },
            handleSubmitForm(){
                let self = this;
                const requestData = {
                    method: 'post',
                    url: StoreApi,
                    data: {
                        master: {
                          TransDate: this.model.TransDate,
                          PostDate: this.model.PostDate,
                          eTransDate: this.model.eTransDate,
                          TransNo: this.model.TransNo,
                          eTransNo: this.model.eTransNo,
                          TransTypeID: this.model.TransTypeID,
                          TransTypeName: this.model.TransTypeName,
                          CoaTypeID: this.model.CoaTypeID,
                          CoaTypeNo: this.model.CoaTypeNo,
                          CoaTypeName: this.model.CoaTypeName,
                          Comment: this.model.Comment,
                          FCTotalAmount: this.model.FCTotalAmount,
                          LCTotalAmount: this.model.LCTotalAmount,
                          FCTotalTaxAmount: this.model.FCTotalTaxAmount,
                          LCTotalTaxAmount: this.model.LCTotalTaxAmount,
                          CompanyID: this.model.CompanyID,
                          CompanyNo: this.model.CompanyNo,
                          CompanyMOFNo: this.model.CompanyMOFNo,
                          CompanyLocationNo: this.model.CompanyLocationNo,
                          CompanyName: this.model.CompanyName,
                          CompanyAddress: this.model.CompanyAddress,
                          CompanyBankAccount: this.model.CompanyBankAccount,
                          CompanyBankName: this.model.CompanyBankName,
                          CompanyContactName: this.model.CompanyContactName,
                          CompanyContactPosition: this.model.CompanyContactPosition,
                          EmployeeID: this.model.EmployeeID,
                          EmployeeNo: this.model.EmployeeNo,
                          EmployeeName: this.model.EmployeeName,
                          EmployeeCitizenIdNo: this.model.EmployeeCitizenIdNo,
                          EmployeeCitizenIdDate: this.model.EmployeeCitizenIdDate,
                          EmployeeCitizenIdAt: this.model.EmployeeCitizenIdAt,
                          EmployeeDeparttmentName: this.model.EmployeeDeparttmentName,
                          EmployeePosition: this.model.EmployeePosition,
                          EmployeeBankAccount: this.model.EmployeeBankAccount,
                          EmployeeBankName: this.model.EmployeeBankName,
                          VendorID: this.model.VendorID,
                          VendorNo: this.model.VendorNo,
                          VendorName: this.model.VendorName,
                          VendorTaxcode: this.model.VendorTaxcode,
                          VendorLocationNo: this.model.VendorLocationNo,
                          VendorAddress: this.model.VendorAddress,
                          VendorBankAccount: this.model.VendorBankAccount,
                          VendorBankName: this.model.VendorBankName,
                          VendorContactName: this.model.VendorContactName,
                          VendorContactPosition: this.model.VendorContactPosition,
                          CustomerID: this.model.CustomerID,
                          CustomerNo: this.model.CustomerNo,
                          CustomerName: this.model.CustomerName,
                          CustomerTaxcode: this.model.CustomerTaxcode,
                          CustomerLocationNo: this.model.CustomerLocationNo,
                          CustomerAddress: this.model.CustomerAddress,
                          CustomerBankAccount: this.model.CustomerBankAccount,
                          CustomerBankName: this.model.CustomerBankName,
                          CustomerContactName: this.model.CustomerContactName,
                          CustomerContactPosition: this.model.CustomerContactPosition,
                          PartnerID: this.model.PartnerID,
                          PartnerName: this.model.PartnerName,
                          PartnerTaxcode: this.model.PartnerTaxcode,
                          PartnerLocationNo: this.model.PartnerLocationNo,
                          PartnerAddress: this.model.PartnerAddress,
                          PartnerBankAccount: this.model.PartnerBankAccount,
                          PartnerBankName: this.model.PartnerBankName,
                          PartnerContactName: this.model.PartnerContactName,
                          PartnerContactPosition: this.model.PartnerContactPosition,
                          PartnerCitizenIdNo: this.model.PartnerCitizenIdNo,
                          PartnerCitizenIdDate: this.model.PartnerCitizenIdDate,
                          PartnerCitizenIdAt: this.model.PartnerCitizenIdAt,
                          NormTableID: this.model.NormTableID,
                          NormTableNo: this.model.NormTableNo,
                          NormTableName: this.model.NormTableName,
                          PeriodID: this.model.PeriodID,
                          PeriodType: this.model.PeriodType,
                          PeriodName: this.model.PeriodName,
                          PeriodFromDate: this.model.PeriodFromDate,
                          PeriodToDate: this.model.PeriodToDate,
                          Locked: this.model.Locked,
                          LockedDate: this.model.LockedDate,
                          LockedUserID: this.model.LockedUserID,
                          StatusID: this.model.StatusID,
                          StatusValue: this.model.StatusValue,
                          StatusDescription: this.model.StatusDescription,
                          ArrCoaChecked: this.model.ArrCoaChecked,
                          isAdjustTrans: (this.model.isAdjustTrans) ? 1 : 0,
                          //Inactive: (this.model.inactive) ? 1 : 0,
                        },
                        detail: this.model.ActgvoucTransItem
                    }
                };

                // edit user
                if (this.idParams) {
                    requestData.data.master.TransID = this.idParams;
                    requestData.url = UpdateApi + '/' + this.idParams;
                }

                this.$store.commit('isLoading', true);
                ApiService.setHeader();
                ApiService.customRequest(requestData).then((responses) => {
                    let responsesData = responses.data; //console.log(responses.data);
                    if (responsesData.status === 1) {
                      if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
                      self.$router.push({
                        name: ViewRouter,
                        params: {id: self.idParams, message: 'Bản ghi đã được cập nhật!'}
                      });
                    } else {
                        let htmlErrors = __.renderErrorApiHtml(responsesData.data);
                        Swal.fire(
                            'Thông báo',
                            htmlErrors,
                            'error'
                        )
                    }

                    self.$store.commit('isLoading', false);
                }, (error) => {
                    console.log(error);
                    Swal.fire(
                        'Thông báo',
                        'Không kết nối được với máy chủ',
                        'error'
                    );
                    self.$store.commit('isLoading', false);
                });
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
            onBackToList() {
                this.$router.push({name: ListRouter});
            },

            updateModel() {
                if (this.stage.updatedData) {
                    this.$forceUpdate();
                }
            },

            autoCorrectedTaxRatePipe() {

            },
          changeUserContact() {
            let employee = _.find(this.model.employeeOption, ['id', Number(this.model.employeeID)]);
            if (employee) {
              this.model.contactName = employee.text;
            }
          },
          handleGetChangedCompany(){
            this.model.CompanyAddress = this.model.Address;
            this.model.CompanyContactName = this.model.ContactName;
            this.$forceUpdate();
          },
          handleGetChangedEmployee(){
            this.model.EmployeeCitizenIdNo = this.model.CitizenIdNo;
            this.model.EmployeeDepartmentName = this.model.DepartmentName;
            this.model.EmployeePosition = this.model.PositionName;
            this.model.EmployeeCitizenIdNo = this.model.CitizenIdNo;
            this.model.EmployeeCitizenIdDate = this.model.CitizenIdDate;
            this.model.EmployeeCitizenIdAt = this.model.CitizenIdAt;
            this.$forceUpdate();
          },
          handleGetChangedVendor(){

            this.$forceUpdate();
          },
          handleGetChangedCustomer(){

            this.$forceUpdate();
          },
          handleGetChangedPartner(){

            this.$forceUpdate();
          },
          onToggleModalObject() {
            this.$forceUpdate();
            this.$refs['onToggleModalObject'].show();
          },
          changeTransDate(){
            let TranDate = this.model.TransDate;
            this.model.eTransDate = TranDate;
            this.model.PostDate = TranDate;
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
          onToggleModalProperty(){
            this.$refs['onToggleModalProperty'].show();
            this.$forceUpdate();
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
          changeCoatype(){
            let check_ct = this.model.CoaTypeID;
            if(check_ct){
              switch(check_ct){
                case 1:
                  this.model.CoaTypeID = 1;
                  this.model.CoaTypeNo = '01';
                  this.model.CoaTypeName = 'TKHN';
                  this.Table_CoaType = 'coa_con';
                  break;
                case 2:
                  this.model.CoaTypeID = 2;
                  this.model.CoaTypeNo = '02';
                  this.model.CoaTypeName = 'TK Tabmis';
                  this.Table_CoaType = 'coa_tab';
                  break;
                case 3:
                  this.model.CoaTypeID = 3;
                  this.model.CoaTypeNo = '03';
                  this.model.CoaTypeName = 'TKQG';
                  this.Table_CoaType = 'coa_sna';
                  break;
                case 4:
                  this.model.CoaTypeID = 4;
                  this.model.CoaTypeNo = '04';
                  this.model.CoaTypeName = 'TK HCSN';
                  this.Table_CoaType = 'coa_anu';
                  break;
                case 5:
                  this.model.CoaTypeID = 5;
                  this.model.CoaTypeNo = '05';
                  this.model.CoaTypeName = 'TK BQLDA';
                  this.Table_CoaType = 'coa_pmu';
                  break;
                case 6:
                  this.model.CoaTypeID = 6;
                  this.model.CoaTypeNo = '06';
                  this.model.CoaTypeName = 'TK Xã phường';
                  this.Table_CoaType = 'coa_scb';
                  break;
                default:
                  this.model.CoaTypeID = 1;
                  this.model.CoaTypeNo = '01';
                  this.model.CoaTypeName = 'TKHN';
                  this.Table_CoaType = 'coa_con';
                  break;
              }
            }
            this.$forceUpdate();
          },
          changeProperty(value){
            this.model.ActgvoucTransItem[key].ItemName  = this.model.OptionsProperty;
            this.$forceUpdate();
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
            // console.log(typeof(link))
            // console.log(link)
            this.model.item_hcsn = false;
            this.model.item_bqlda  = false;
            this.model.item_tkhn  = false;
            this.model.item_kbnn = false;
            this.model.ArrCoaChecked = [];
            _.forEach(link,function(item,key){
              if(item !== undefined){
                self.model.ArrCoaChecked.push(item.CoaTypeID);
                switch(item.CoaTypeID){
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
                self.model.item_httk = true;
              }
            });

            this.$forceUpdate();
          },
          addFCDebit(key){
              //let FCDebitAmount = Number(this.model.ActgvoucTransItem[key].FCDebitAmount);
              let totalFCDebitAmount = 0;
              _.forEach(this.model.ActgvoucTransItem, function (item, key) {
                totalFCDebitAmount += Number(item.FCDebitAmount);
              });
              if(totalFCDebitAmount){
                this.totalFCDebit = totalFCDebitAmount;
              }
          },
          addFCCredit(key){
            //let FCCreditAmount = this.model.ActgvoucTransItem[key].FCCreditAmount;
            let totalFCCreditAmount = 0;
            _.forEach(this.model.ActgvoucTransItem, function (item, key) {
              totalFCCreditAmount += Number(item.FCDebitAmount);
            });
            if(totalFCCreditAmount){
              this.totalFCCredit = totalFCCreditAmount;
            }
          },
          addtoFCUnitPrice(evt, key){
            let FCUnitPrice = this.model.ActgvoucTransItem[key].FCUnitPrice;
            let ExchangeRate = this.model.ActgvoucTransItem[key].ExchangeRate;
            let LCUnitPrice = Number(FCUnitPrice) * Number(ExchangeRate);
            if(LCUnitPrice=='Infinity'){LCUnitPrice='';}
            if(LCUnitPrice){//alert(LCUnitPrice);
              this.model.ActgvoucTransItem[key].LCUnitPrice = LCUnitPrice;
            }else{
              this.model.ActgvoucTransItem[key].LCUnitPrice ='';
            }

            let Quantity = this.model.ActgvoucTransItem[key].Quantity;
            let FCDebitAmount = Number(FCUnitPrice) * Number(Quantity);
            if(FCDebitAmount){
              this.model.ActgvoucTransItem[key].FCDebitAmount = FCDebitAmount;
              this.model.ActgvoucTransItem[key].LCDebitAmount = FCDebitAmount;
            }else{
              this.model.ActgvoucTransItem[key].FCDebitAmount ='';
              this.model.ActgvoucTransItem[key].LCDebitAmount ='';
            }
            //this.$forceUpdate()
          },
          addtoLCDebitAmount(evt, key){
            let FCDebitAmount = this.model.ActgvoucTransItem[key].FCDebitAmount;
            let ExchangeRate = this.model.ActgvoucTransItem[key].ExchangeRate;
            let LCDebitAmount = Number(FCDebitAmount) * Number(ExchangeRate);
            if(LCDebitAmount=='Infinity'){LCDebitAmount='';}
            if(LCDebitAmount){//alert(LCDebitAmount);
              this.model.ActgvoucTransItem[key].LCDebitAmount = LCDebitAmount;
            }else{
              this.model.ActgvoucTransItem[key].LCDebitAmount ='';
            }
            //Thành tiền -> Đơn gía
            let Quantity = this.model.ActgvoucTransItem[key].Quantity;
            let LCDebitAmount1 = this.model.ActgvoucTransItem[key].LCDebitAmount;
            let FCUnitPrice = Number(LCDebitAmount1) / Number(Quantity);
            if(FCUnitPrice){
              this.model.ActgvoucTransItem[key].FCUnitPrice = FCUnitPrice;
            }
            //this.$forceUpdate()
          },
          addtoLCCreditAmount(evt, key){
            let FCCreditAmount = this.model.ActgvoucTransItem[key].FCCreditAmount;
            let ExchangeRate = this.model.ActgvoucTransItem[key].ExchangeRate;
            let LCCreditAmount = Number(FCCreditAmount) * Number(ExchangeRate);
            if(LCCreditAmount=='Infinity'){LCCreditAmount='';}
            if(LCCreditAmount){//alert(LCDebitAmount);
              this.model.ActgvoucTransItem[key].LCCreditAmount = LCCreditAmount;
            }else{
              this.model.ActgvoucTransItem[key].LCCreditAmount ='';
            }
            //Thành tiền -> Đơn gía
            let Quantity = this.model.ActgvoucTransItem[key].Quantity;
            let LCCreditAmount1 = this.model.ActgvoucTransItem[key].LCCreditAmount;
            let FCUnitPrice = Number(LCCreditAmount1) / Number(Quantity);
            if(FCUnitPrice){
              this.model.ActgvoucTransItem[key].FCUnitPrice = FCUnitPrice;
            }
            //this.$forceUpdate()
          },
          changeAutoact($event,key){

            this.$set(this.model.ActgvoucTransItem[key], 'AccountID', 0);
            this.$set(this.model.ActgvoucTransItem[key], 'AccountNo', '');
            this.$set(this.model.ActgvoucTransItem[key], 'AccountName', '');
            this.$set(this.model.ActgvoucTransItem[key], 'CoAccountID', 0);
            this.$set(this.model.ActgvoucTransItem[key], 'CoAccountNo', '');//this.idParams
            this.$set(this.model.ActgvoucTransItem[key], 'CoAccountName', '');
            if(!this.model.NormTableID && !this.idParams && !this.model.ActgvoucTransItem[key].Description) {
              this.$set(this.model.ActgvoucTransItem[key], 'Description', '');
            }

            if($event){
              this.model.ActgvoucTransItem[key].AccountID = $event.DebitAccountID;
              this.model.ActgvoucTransItem[key].AccountNo = $event.DebitAccountNo;
              this.model.ActgvoucTransItem[key].AccountName = $event.DebitAccountName;
              this.model.ActgvoucTransItem[key].CoAccountID = $event.CreditAccountID;
              this.model.ActgvoucTransItem[key].CoAccountNo = $event.CreditAccountNo;
              this.model.ActgvoucTransItem[key].CoAccountName = $event.CreditAccountName;
              if(!this.model.NormTableID && !this.idParams && !this.model.ActgvoucTransItem[key].Description) {
                this.model.ActgvoucTransItem[key].Description = $event.AutoactName;
              }
            }

             this.$forceUpdate();
          },
          changeAutoact1($event,key){
            this.$set(this.model.ActgvoucTransItem[key], 'AccountID', 0);
            this.$set(this.model.ActgvoucTransItem[key], 'AccountNo', '');
            this.$set(this.model.ActgvoucTransItem[key], 'CoAccountID', 0);
            this.$set(this.model.ActgvoucTransItem[key], 'CoAccountNo', '');
            if(!_.isEmpty(this.model.NormTableID) && _.isEmpty(this.idParams)) {
              this.$set(this.model.ActgvoucTransItem[key], 'Description', '');
            }
            let ArrAutoactObj = _.find(this.model.Autoact, ['value', this.model.ActgvoucTransItem[key].AutoactID]);
            if (_.isObject(ArrAutoactObj)){
              this.model.ActgvoucTransItem[key].AccountID = ArrAutoactObj.value;
              this.model.ActgvoucTransItem[key].AccountNo = ArrAutoactObj.AccountNo;
              this.model.ActgvoucTransItem[key].CoAccountID = ArrAutoactObj.CoAccountID;
              this.model.ActgvoucTransItem[key].CoAccountNo = ArrAutoactObj.CoAccountNo;
              if(!_.isEmpty(this.model.NormTableID) && !_.isEmpty(this.idParams)) {
                this.model.ActgvoucTransItem[key].Description = ArrAutoactObj.text;
              }
            }
            this.$forceUpdate();
          },
          changePeriodType(){
            //ActPeriod
            let self = this;
            self.model.ArrPeriodOption = [];
            let ArrPeriod_1 = _.filter(this.model.PeriodOption, ['PeriodType', Number(this.model.PeriodType)]);
            _.forEach(ArrPeriod_1, function (item, key) {
              let tmpObj = {};
              tmpObj.value = item.value;
              tmpObj.PeriodID = item.value;
              tmpObj.text = item.text;
              tmpObj.PeriodName = item.text;
              tmpObj.PeriodFromDate = item.PeriodFromDate;
              tmpObj.PeriodToDate = item.PeriodToDate;
              self.model.ArrPeriodOption.push(tmpObj);
            });

            let PeriodType = this.model.PeriodType;
            if(PeriodType == 5 || PeriodType == 99)
            {
              this.model.item_599 = true;
              this.model.item_DF599 = false;
              this.model.PeriodFromDate =  moment().format('L');
            }else{this.model.item_599 = false; this.model.item_DF599 = true;}
            self.$forceUpdate();
          },
          changePeriodName(){
            let ArrPeriodObj = _.find(this.model.ArrPeriodOption, ['PeriodID', this.model.PeriodID]);
            if (_.isObject(ArrPeriodObj)){
              this.model.PeriodName = ArrPeriodObj.PeriodName;
              this.model.PeriodFromDate = ArrPeriodObj.PeriodFromDate;
              this.model.PeriodToDate = ArrPeriodObj.PeriodToDate;
            }
          },
          changeStatusValue(){
            let ArrAccoutingStatus = _.find(this.model.AccoutingStatus, {'StatusID': this.model.StatusID, 'value': this.model.StatusValue});
            if (_.isObject(ArrAccoutingStatus)){
              this.model.StatusDescription = ArrAccoutingStatus.text;
            }
          },
          selectednormtable(){
            let self = this;
            const requestData = {
              method: 'post',
              url: 'state-budget-planning/api/sbpmakeplan/getNormTableItem',
              data: {
                NormTableID: this.model.NormTableID,
              }
            };
            ApiService.setHeader();
            ApiService.customRequest(requestData).then((responses) => {
              let responsesData = responses.data;
              if (responsesData.status === 1) {
                if (!self.idParams && responsesData.data) self.idParams = responsesData.data.param;
                this.model.ActgvoucTransItem=[];
                self.model.NormTableItem = responsesData.data.NormTableItem;
                _.forEach(self.model.NormTableItem, function (field, key) {
                  let fieldObj = {};
                  fieldObj.LineID = key;
                  fieldObj.NormTableItemID = self.model.NormTableItem[key].NormTableItemID;
                  fieldObj.NormTableItemNo = self.model.NormTableItem[key].NormTableItemNo;
                  fieldObj.NormID = self.model.NormTableItem[key].NormID;
                  fieldObj.NormNo = self.model.NormTableItem[key].NormNo;
                  fieldObj.NormName = self.model.NormTableItem[key].NormName;
                  fieldObj.Description = self.model.NormTableItem[key].Description;
                  fieldObj.CoAccountID = '';
                  fieldObj.CoAccountNo = null;
                  fieldObj.FCDebitAmount = '';
                  fieldObj.AutoactID = 33;
                  fieldObj.AutoactName = 'Chứng từ chung';
                  fieldObj.AutoactNo = '';
                  fieldObj.CcyID = 33;
                  fieldObj.CcyNo = '033';
                  fieldObj.CcyName = 'VND';
                  fieldObj.ExchangeRate = 1;
                  fieldObj.FiscalPeriod = 2;
                  fieldObj.ReceiveBy = 1;
                  fieldObj.UomID = 7;
                  fieldObj.UomNo = 401;
                  fieldObj.UomName = 'Đồng';
                  fieldObj.FiscalYear = new Date().getFullYear();
                  fieldObj.InvoiceDate = moment().format('L');
                  self.model.ActgvoucTransItem.push(fieldObj);
                  self.$forceUpdate();
                });

                this.$bvToast.toast('Bảng định mức đã được thêm!', {
                  title: 'Thông báo',
                  variant: 'success',
                  solid: true
                });
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

            this.$forceUpdate();
          },
          onAddLine(data){
            if (data && data[0]) {
              this.NormTable.NormTableID = data[0].NormTableID;
            }
            let self = this;
            if(data.length){
              //console.log(data);
              //<!-- Thêm từ 1 bảng đmdt -->
              this.model.NormTableID = data[data.length-1].NormTableID;
              this.model.NormTableNo = data[data.length-1].NormTableNo;
              this.model.NormTableName = data[data.length-1].NormTableName;
              self.model.ActgvoucTransItem = [];

              data.map(function (val, key) {
                let AccountNo = '011';
                if(val.NormType==2){ AccountNo = '021';}
                self.model.ActgvoucTransItem.push({
                  LineID: val.LineID,
                  ParentID: val.ParentID,
                  Level: val.Level,
                  Detail: val.Detail,
                  NormTableID: val.NormTableID,
                  NormTableName: val.NormTableName,
                  NormTableNo: val.NormTableNo,
                  NormID: val.NormID,
                  NormNo: val.NormNo,
                  NormName: val.NormName,
                  NormAllotID: val.NormAllotID,
                  NormAllotNo: val.NormAllotNo,
                  NormAllotName: val.NormAllotName,
                  NormAllotILevelID: val.NormAllotILevelID,
                  NormAllotLevelNo: val.NormAllotLevelNo,
                  NormAllotLevelName: val.NormAllotLevelName,
                  NormTableItemID : val.NormTableItemID,
                  NormTableItemNo: val.NormTableItemNo,
                  NormTableItemName: val.NormTableItemName,
                  NormType: val.NormType,
                  Description: val.Description,
                  Quantity: (val.Quantity) ? val.Quantity : 1,
                  FCUnitPrice: val.FCUnitPrice,
                  IsCheck: false,
                  CoAccountID: null,
                  CoAccountNo: '',
                  FCDebitAmount: val.FCAmount,
                  LCDebitAmount: val.LCAmount,
                  AutoactID: 300,
                  AutoactName: 'Thực hiện thu, chi',
                  UomID: (val.UomID) ? val.UomID : 7,
                  UomName: (val.UomName) ? val.UomName : 'Đồng',
                  UomNo: (val.UomNo) ? val.UomNo : 401,
                  CcyID: val.CcyID,
                  CcyName: val.CcyName,
                  CcyNo: val.CcyNo,
                  ExchangeRate: (val.ExchangeRate) ? val.ExchangeRate : 1,
                  FiscalPeriod: 2,
                  BudgetLevel: 0,
                  ReceiveBy: 1,
                  BudgetAllocTypeID: 1,
                  FiscalYear: new Date().getFullYear(),
                  InvoiceDate: moment().format('L'),
                  Show: true,
                  ShowPagination: false
                });
                // self.$forceUpdate();
              });
            }
          },
          scrollToBottom() {
            //const container = this.$el.querySelector("#scrollHeight");
            //container.scrollTop = container.scrollHeight;
          },
          changeNormTableItemNo(data, key){
            let itemExits = _.filter(this.model.ActgvoucTransItem, ['NormTableItemNo', this.model.ActgvoucTransItem[key].NormTableItemNo]);
            if (itemExits.length > 1) {
              this.model.ActgvoucTransItem[key].NormTableItemID = null;
              this.model.ActgvoucTransItem[key].NormTableItemNo = '';
              this.model.ActgvoucTransItem[key].NormTableItemName = '';
              this.model.ActgvoucTransItem[key].NormAllotID = null;
              this.model.ActgvoucTransItem[key].NormAllotNo = '';
              this.model.ActgvoucTransItem[key].NormAllotName = '';
              this.model.ActgvoucTransItem[key].NormAllotLevelID = null;
              this.model.ActgvoucTransItem[key].NormAllotLevelNo = '';
              this.model.ActgvoucTransItem[key].NormAllotLevelName = '';
              this.model.ActgvoucTransItem[key].NormID = null;
              this.model.ActgvoucTransItem[key].NormNo = '';
              this.model.ActgvoucTransItem[key].NormName = '';
              this.model.ActgvoucTransItem[key].Description = '';

              this.$bvToast.toast('Mã định mức dự toán chi tiết đã tồn tại', {
                title: 'Thông báo',
                variant: 'warning',
                solid: true
              });
            } else {
              this.model.ActgvoucTransItem[key].Description = this.model.ActgvoucTransItem[key].NormTableItemName;
              this.model.ActgvoucTransItem[key].UomName = data.UomName;
              this.model.ActgvoucTransItem[key].UomID = data.UomID;
              this.model.ActgvoucTransItem[key].UomNo = data.UomNo;
              this.model.ActgvoucTransItem[key].CcyID = data.CcyID;
              this.model.ActgvoucTransItem[key].CcyNo = data.CcyNo;
              this.model.ActgvoucTransItem[key].CcyName = data.CcyName;
              this.model.ActgvoucTransItem[key].Quantity = data.Quantity;
              this.model.ActgvoucTransItem[key].FCUnitPrice = data.FCUnitPrice;
              this.model.ActgvoucTransItem[key].FCDebitAmount = data.FCDebitAmount;
            }
          },
          changePerPage(perPage){
            this.perPage = String(perPage);
            this.currentPage = 1;
            this.changePage();
          },
          changePage(){
            let self = this;
            let indexStart = (this.currentPage - 1) * Number(this.perPage);
            let indexEnd = this.currentPage * Number(this.perPage) - 1;
            _.forEach(self.model.ActgvoucTransItem, function (NormTableItem, key) {
              self.model.ActgvoucTransItem[key].ShowPagination = false;
            });
            for (let key = indexStart; key <= indexEnd; key++) {
              if (self.model.ActgvoucTransItem[key]) {
                self.model.ActgvoucTransItem[key].ShowPagination = true;
              }
            }
            this.$nextTick(() => {
              $('.table-responsive').scrollTop(0);
            });
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
          onToggleChildNodes(e, itemParent) {
            let self = this;
            if (e && e.target.classList.contains('fa-minus-square-o')) {
              // close children
              e.target.classList.remove('fa-minus-square-o');
              e.target.classList.add('fa-plus-square-o');
              let allChildTableItem = this.getAllChildTableItem(itemParent, this.model.ActgvoucTransItem);
              if (allChildTableItem && allChildTableItem.length) {
                _.forEach(allChildTableItem, function (childTableItem, key) {
                  let indexItem = _.findIndex(self.model.ActgvoucTransItem, ['LineID', childTableItem.LineID]);
                  if (indexItem > -1) {
                    self.model.ActgvoucTransItem[indexItem].Show = false;
                  }
                });
              }
            } else {
              // open children
              e.target.classList.remove('fa-plus-square-o');
              e.target.classList.add('fa-minus-square-o');
              let allChildren = _.filter(this.model.ActgvoucTransItem, ['ParentID', itemParent.LineID]);
              if (allChildren.length) {
                _.forEach(allChildren, function (childTableItem, key) {
                  let indexItem = _.findIndex(self.model.ActgvoucTransItem, ['LineID', childTableItem.LineID]);
                  if (indexItem > -1) {
                    self.model.ActgvoucTransItem[indexItem].Show = true;
                    $('#table-item-' + self.model.ActgvoucTransItem[indexItem].LineID + ' .bg-tree-icon-action').removeClass('fa-minus-square-o');
                    $('#table-item-' + self.model.ActgvoucTransItem[indexItem].LineID + ' .bg-tree-icon-action').addClass('fa-plus-square-o');
                  }
                });
              }
            }
            this.$nextTick(() => {
              $('.table-responsive').scrollTop();
            });
          },

        },

        watch: {
          idParams() {
              this.fetchData();
          },
          'model.OptionsProperty'(newVal){
            _.forEach(this.model.ActgvoucTransItem, function(item,key){
              item.ItemID = newVal;
            });

          },
          currentPage(){
            this.changePage();
          },
          'model.ActgvoucTransItem': {
            handler(val){
              // do stuff
              this.totalRows = this.model.ActgvoucTransItem.length;
              let currentItem = _.filter(this.model.ActgvoucTransItem, ['ShowPagination', true]);
              if (!currentItem.length) {
                if (this.currentPage > 1) {
                  this.currentPage = this.currentPage - 1;
                }
                this.changePage();
              }
            },
            deep: true
          }
        },
    }
</script>
<!--<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>-->
<style lang="css">
  .form-group{ margin-bottom: 0.5rem !important;}
  .tab-content{ border-bottom: none !important; border-right: none !important; border-left: none !important;}
  .nav-tabs .nav-link{ border-top: none !important; }
  .td-action-fix-right-form {
    position: absolute;
    width: 39px;
    right: 10px;
    top: auto;
    /*only relevant for first row*/
    background: #fff;
    border-bottom: none !important;
    /*compensate for top border*/
    height: 34px;
  }
  .td-action-fix-left-form {
    position: absolute;
    left: 0px;
    top: auto;
    /*only relevant for first row*/
    background: #fff;
    border-bottom: none !important;
  }
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
  #bar_detail .form-control{ padding-right: 2px !important; padding-left: 9px !important; border:  none !important; }
  .card-header{ padding-top: 5px !important; padding-bottom: 5px !important; background: none !important;}
  .card-header .nav-tabs .nav-link{ color: #0b0e0f !important; padding: 0.55rem 0.625rem;}
  .card-header .nav-tabs .nav-link:hover{ text-decoration: underline;}
  .card-header .nav-tabs .nav-link.active{ font-weight: bold; text-decoration:underline; /*background: #f0f3f5;*/}
  .tab-content{ border: none !important;}
  .card-header .nav-tabs{ border: none !important;}
  .nav-tabs .nav-link{ border: none !important;}
  .table-bordered thead th, .table-bordered thead td {
    border-bottom-width: 1px !important;
  }
  .comments{ }
  .mx-3{ margin-right: 0px !important;}
  #bar_detail .new-row{ margin-top: 6px; font-weight: normal; font-size: 14px;}
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
  #member-radio .form-check-inline{ display: block; margin-bottom: 5px;}
  .w-form-control .form-control, .w-form-control .custom-select{ padding: 0.275rem 0.35rem !important;}
  .custom-checkbox{ border: none !important; width: 98%;}
  .w-form-control .custom-select{ padding-right: 1.45rem !important;}
  .table-tree th{ background: #ffffff;}
  .main #main-body {
    overflow: auto;
    -webkit-box-flex: 1;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
  }
  .table-tree tr th:first-child, .table-tree tr td:first-child{ width: 0px !important; margin: 0; padding: 0;}
  .table-tree tr th { z-index: 10 !important;}
  .mx-datepicker, .mx-input-wrapper{ width: 100% !important;}
  @media only screen
  and (min-device-width : 768px)
  and (max-device-width : 1024px)
  and (orientation:landscape)
  {
    .w-form-control .form-group { margin-bottom: 0px !important;}
    #bar_detail div div{ max-width: 130px !important;}
  }
  @media only screen
  and (min-device-width : 768px)
  and (max-device-width : 1024px)
  and (orientation:portrait)
  {
    .w-form-control .form-group { margin-bottom: 0px !important;}
    #bar_detail div div{ max-width: 130px !important;}
  }

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
