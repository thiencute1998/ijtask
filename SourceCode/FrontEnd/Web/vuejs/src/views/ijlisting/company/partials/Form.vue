<template>
    <div class="main-entry">
        <div class="main-header">
            <div class="main-header-padding">
                <b-row class="mb-2">
                    <b-col class="col-md-18">
                        <div class="main-header-item main-header-name">
                            <span v-if="idParams"><i class="fa fa-edit mr-2"></i> Cơ quan nhà nước<span v-if="model.CompanyName">:</span> {{model.CompanyName}}</span>
                            <span v-if="!idParams"><i class="fa fa-plus mr-2"></i> Cơ quan nhà nước<span v-if="model.CompanyName">:</span> {{model.CompanyName}}</span>
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
                            <div class="main-header-collapse">
                                <sidebar-toggle class="d-md-down-none btn btn-sm" display="lg" :defaultOpen=true />
                            </div>

                        </div>
                    </b-col>
                </b-row>
            </div>

        </div>
        <div class="main-body main-body-view-action">
            <vue-perfect-scrollbar class="scroll-area" :settings="$store.state.psSettings">
                <div class="container-fluid">
                    <b-card>
                      <div class="form-group row ij-line-head ">
                        <label class="col-md-24">Đơn vị quan hệ ngân sách: </label>
                      </div>
                      <div class="form-group row align-items-center mt-3">
                        <div class="col-md-3 mb-md-0 col-sm-2 mb-sm-0 col-4 mb-3" style="white-space: nowrap">Tên</div>
                        <div class="col-md-15 col-sm-14 mb-sm-0 mb-md-0 col-20 mb-3 app-object-name">
                          <input v-model="model.CompanyName" type="text" id="CompanyName" class="form-control" placeholder="Tên đơn vị" name="CompanyName"/>
                        </div>
                        <div class="col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                          <span>Mã số</span>
                          <input type="text" v-model="model.CompanyNo" class="form-control" placeholder="Mã số"/>
                        </div>
                      </div>
                      <div class="form-group row align-items-center">
                          <label class="col-md-3 m-0">Là mục con của</label>
                          <div class="col-md-15">
                              <IjcoreModalParent v-model="model"
                                                 :title="'Cơ quan nhà nước'"
                                                 :api="'/listing/api/common/get-parent'"
                                                 :table="'company'" :fieldID="'CompanyID'"
                                                 :fieldNo="'CompanyNo'"
                                                 :fieldName="'CompanyName'"
                                                 :placeholderInput="'Chọn đơn vị cha'"
                                                 :placeholderSearch="'Nhập tên đơn vị'">
                              </IjcoreModalParent>
                          </div>
                          <div v-if="model.ParentID" class="col-lg-6 col-md-6 col-sm-8 col-24 d-flex align-items-center app-object-code">
                            <span>Mã số</span>
                            <input type="text" v-model="model.ParentNo" class="form-control" placeholder="Mã số"/>
                          </div>
                      </div>
                      <div class="form-group row align-items-center">
                          <label class="col-md-3 m-0">Loại đơn vị</label>
                          <div class="col-md-21">
                              <company-modal-search-input-catelist
                                v-model="model.CompanyCate"
                                title-modal="Loại đơn vị"
                                placeholder="Loại đơn vị"
                              ></company-modal-search-input-catelist>
                          </div>
                      </div>
                      <div class="form-group row align-items-center">
                        <label class="col-md-3 m-0">Quyền truy cập</label>
                        <div class="col-md-9">
                          <b-form-select v-model="model.AccessType" :options="AccessTypeOptions" id="item-uom">
                          </b-form-select>
                        </div>
                        <label class="col-md-3 m-0">Chương</label>
                        <div class="col-md-9">
                          <ijcore-modal-search-listing
                            v-model="model" :title="'chương'" :table="'sbi_chapter'" :api="'/listing/api/common/list'"
                            :fieldID="'SbiChapterID'" :fieldNo="'SbiChapterNo'" :fieldName="'SbiChapterName'"
                            :fieldAssignID="'SbiChapterID'" :fieldAssignNo="'SbiChapterNo'" :fieldAssignName="'SbiChapterName'"
                          >
                          </ijcore-modal-search-listing>
                        </div>
                      </div>
                      <div class="form-group row align-items-center">
                        <label class="col-md-3 m-0">Người liên hệ</label>
                        <div class="col-md-9 mb-3 mb-sm-0">
                          <ijcore-modal-search-listing
                            v-model="model" :title="'Liên hệ'" :table="'employee'" :api="'/listing/api/common/list'"
                            :fieldID="'EmployeeID'" :fieldNo="'EmployeeNo'" :fieldName="'EmployeeName'"
                            :fieldAssignID="'EmployeeID'" :fieldAssignNo="'EmployeeNo'" :fieldAssignName="'ContactName'"
                          >
                          </ijcore-modal-search-listing>
                        </div>
                        <label class="col-md-3 m-0" for="ContactTel">ĐTCN</label>
                        <div class="col-md-9">
                          <input v-model="model.ContactTel" type="text" id="ContactTel" class="form-control" placeholder="Số điện thoại" name="ContactTel">
                        </div>
                      </div>
                      <div class="form-group row align-items-center">
                        <label class="col-md-3 m-0" for="Tel">ĐTĐV</label>
                        <div class="col-md-5 mb-3 mb-sm-0">
                          <input type="text" v-model="model.Tel" id="Tel" class="form-control" placeholder="Số điện thoại" name="Tel"/>
                        </div>

                        <label class="col-md-2 m-0" for="Fax">Số Fax</label>
                        <div class="col-md-5 mb-3 mb-sm-0">
                          <input type="text" v-model="model.Fax" id="Fax" class="form-control" placeholder="Số Fax" name="Fax"/>
                        </div>

                        <label class="col-md-2 m-0" for="Email">Email</label>
                        <div class="col-md-7">
                          <input type="text" v-model="model.Email" id="Email" class="form-control" placeholder="Email" name="Email"/>
                        </div>
                      </div>
                      <div class="form-group row align-items-center">
                        <label class="col-md-3 m-0" for="Note">Địa chỉ</label>
                        <div class="col-md-12">
                          <input v-model="model.Address" class="form-control" rows="3" placeholder="Địa chỉ" name="Address"/>
                        </div>
                        <label class="col-md-2" title="Loại dữ liệu tổng hợp">DLTH</label>
                        <div class="col-md-7">
                          <b-form-select v-model="model.SumCompanyType" :options="SumCompanyTypeOptions"></b-form-select>
                        </div>
                      </div>
                      <div class="form-group row align-items-center">
                        <label class="col-md-3 m-0">Cấp quản lý</label>
                        <div class="col-md-9">
                          <b-form-select v-model="model.ManagementLevel" :options="ManagementLevelOptions" id="item-uom">
                          </b-form-select>
                        </div>
                        <label class="col-md-3 m-0" title="Cơ quan trung ương">CQ trung ương</label>
                        <div class="col-md-9 mb-3 mb-sm-0">
                          <ijcore-modal-search-listing
                            v-if="model.ManagementLevel === 1"
                            v-model="model" :title="'Cơ quan trung ương'" :table="'center'" :api="'/listing/api/common/list'"
                            :fieldID="'CenterID'" :fieldNo="'CenterNo'" :fieldName="'CenterName'"
                            :fieldAssignID="'CenterID'" :fieldAssignNo="'CenterNo'" :fieldAssignName="'CenterName'"
                          >
                          </ijcore-modal-search-listing>
                          <b-form-input v-if="model.ManagementLevel != 1" disabled placeholder="Chọn cơ quan trung ương"></b-form-input>
                        </div>
                      </div>
                      <div class="form-group row align-items-center" >
                        <label class="col-md-3 m-0">Tỉnh</label>
                        <div class="col-md-5 mb-3 mb-sm-0 mb-md-0 mb-lg-0">
                          <ijcore-modal-search-input
                            v-if="model.ManagementLevel >= 2"
                            v-model="model.Province"
                            :select-fields-api="[
                            {field: 'ProvinceID',fieldForSelected: 'id', showInTable: false, key: 'ProvinceID'},
                            {field: 'ProvinceName', fieldForSelected: 'name', showInTable: true, label: 'Tên đơn vị', key: 'ProvinceName', sortable: true, thClass: 'd-none'}
                          ]"
                            :search-fields-api="[{field: 'ProvinceName', placeholder: 'Nhập tên', name: 'ProvinceName', class: '', style: ''}]"
                            table="province"
                            ref="myModalSearchInputProvince"
                            id-modal="myModalSearchInputProvince"
                            :item-per-page="8"
                            placeholder="Tỉnh"
                            :url-api="$store.state.appRootApi + '/listing/api/common/get-province'"
                            name-input="input-province"
                            title-modal="Tỉnh" size-modal="lg">
                          </ijcore-modal-search-input>
                          <b-form-input type="text" v-if="model.ManagementLevel === 1 || !model.ManagementLevel" placeholder="Tỉnh" disabled></b-form-input>
                        </div>
                        <label class="col-md-2 m-0" >Huyện</label>
                        <div class="col-md-5 mb-3 mb-sm-0 mb-md-0 mb-lg-0" >
                          <ijcore-modal-search-input
                            v-model="model.District"
                            v-if="model.ManagementLevel >=3"
                            :select-fields-api="[
                            {field: 'DistrictID',fieldForSelected: 'id', showInTable: false, key: 'DistrictID'},
                            {field: 'DistrictName', fieldForSelected: 'name', showInTable: true, label: 'Tên đơn vị', key: 'DistrictName', sortable: true, thClass: 'd-none'}
                          ]"
                            :search-fields-api="[{field: 'DistrictName', placeholder: 'Nhập tên', name: 'DistrictName', class: '', style: ''}]"
                            table="district"
                            ref="myModalSearchInputDistrict"
                            id-modal="myModalSearchInputDistrict"
                            :item-per-page="8"
                            placeholder="Huyện"
                            :request-data="{ProvinceID: (model.Province) ? model.Province.ProvinceID : null}"
                            :url-api="$store.state.appRootApi + '/listing/api/common/get-district'"
                            name-input="input-district"
                            title-modal="Huyện" size-modal="lg">
                          </ijcore-modal-search-input>
                          <b-form-input v-if="model.ManagementLevel < 3 || !model.ManagementLevel" disabled placeholder="Huyện"></b-form-input>
                        </div>
                        <label class="col-md-2 m-0" >Xã</label>
                        <div class="col-md-7 " >
                          <ijcore-modal-search-input
                            v-if="model.ManagementLevel >=4"
                            v-model="model.Commune"
                            :select-fields-api="[
                            {field: 'CommuneID',fieldForSelected: 'id', showInTable: false, key: 'CommuneID'},
                            {field: 'CommuneName', fieldForSelected: 'name', showInTable: true, label: 'Tên đơn vị', key: 'CommuneName', sortable: true, thClass: 'd-none'}
                          ]"
                            :search-fields-api="[{field: 'CommuneName', placeholder: 'Nhập tên', name: 'CommuneName', class: '', style: ''}]"
                            table="commune"
                            ref="myModalSearchInputCommune"
                            id-modal="myModalSearchInputCommune"
                            :item-per-page="8"
                            placeholder="Xã"
                            :request-data="{
                            ProvinceID: (model.Province) ? model.Province.ProvinceID : null,
                            DistrictID: (model.District) ? model.District.DistrictID : null
                          }"
                            :url-api="$store.state.appRootApi + '/listing/api/common/get-commune'"
                            name-input="input-commune"
                            title-modal="Xã" size-modal="lg">
                          </ijcore-modal-search-input>
                          <b-form-input v-if="model.ManagementLevel < 4 || !model.ManagementLevel" disabled placeholder="Xã"></b-form-input>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-md-3" for="Note">Ghi chú</label>
                        <div class="col-md-21">
                          <textarea v-model="model.Note" class="form-control" id="Note" rows="3" placeholder="Ghi chú" name="Note"></textarea>
                        </div>
                      </div>
                      <div class="form-group row align-items-center mt-1">
                        <div class="col-md-8">
                          <b-form-checkbox v-model="model.isAutOrg">Là cơ quan chủ quản</b-form-checkbox>
                        </div>
                        <div class="col-md-8">
                          <b-form-checkbox v-model="model.isFinOrg">Là cơ quan tài chính</b-form-checkbox>
                        </div>
                        <div class="col-md-8">
                          <b-form-checkbox v-model="model.isTreOrg">Là kho bạc nhà nước</b-form-checkbox>
                        </div>
                      </div>
                      <!--Cơ quan chủ quản-->
                      <div class="form-group row ij-line-head " v-if="model.isAutOrg && model.ManagementLevel >=2">
                        <label class="col-md-24">Cơ quan chủ quản: </label>
                      </div>
                      <div class="form-group row align-items-center mt-3" v-if="model.isAutOrg && model.ManagementLevel >=2" >
                        <label class="col-md-3">Tên</label>
                        <div class="col-md-15">
                          <b-form-input type="text"  v-model="model.AutOrgName"></b-form-input>
                        </div>
                        <div class="col-md-6">
                          <div class="row align-items-center">
                            <label class="col-md-5 pl-0">Mã số</label>
                            <div class="col-md-19">
                              <ijcore-modal-search-listing
                                v-model="model" :title="'CQCQ'" :table="'company'" :api="'/listing/api/common/list'"
                                :fieldID="'CompanyID'" :fieldNo="'CompanyNo'" :fieldName="'CompanyName'"
                                :fieldAssignID="'AutOrgID'" :fieldAssignNo="'AutOrgNo'" :fieldAssignName="'AutOrgName'"
                                :showNo="true"
                                :FieldWhere="{isAutOrg: 1}"
                                @selectOrg="selectOrg"
                                @clearOrg="clearOrg(1)"
                              ></ijcore-modal-search-listing>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="form-group row align-items-center" v-if="model.isAutOrg && model.ManagementLevel >=2">
                        <label class="col-md-3">Địa chỉ</label>
                        <div class="col-md-15">
                          <b-form-input v-model="model.AutOrgAddress"></b-form-input>
                        </div>
                        <div class="col-md-6">
                          <div class="row align-items-center">
                            <label class="col-md-5 pr-0 pl-0">Chương</label>
                            <div class="col-md-19">
                              <ijcore-modal-search-listing
                                v-model="model" :title="'Chương'" :table="'sbi_chapter'" :api="'/listing/api/common/list'"
                                :fieldID="'SbiChapterID'" :fieldNo="'SbiChapterNo'" :fieldName="'SbiChapterName'"
                                :fieldAssignID="'AutOrgChapterID'" :fieldAssignNo="'AutOrgChapterNo'" :fieldAssignName="'AutOrgChapterName'"
                                :showNo="true"
                              ></ijcore-modal-search-listing>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="form-group row align-items-center" v-if="model.isAutOrg && model.ManagementLevel >=2">
                        <label class="col-md-3" >Người liên hệ</label>
                        <div class="col-md-9">
                          <b-form-input type="text" placeholder="Người liên hệ" v-model="model.AutOrgContactName"></b-form-input>
                        </div>
                        <label class="col-md-3">Chức vụ</label>
                        <div class="col-md-9">
                          <b-form-input type="text" placeholder="Chức vụ" v-model="model.AutOrgContactPosition"></b-form-input>
                        </div>
                      </div>
                      <div class="form-group row align-items-center" v-if="model.isAutOrg && model.ManagementLevel >=2">
                        <label class="col-md-3"  title="Điện thoại cơ quan">ĐTCQ</label>
                        <div class="col-md-9">
                          <b-form-input type="number" placeholder="Điện thoại cơ quan"  v-model="model.AutOrgContactOfficePhone"></b-form-input>
                        </div>
                        <label class="col-md-3" title="Điện thoại di dộng">ĐTDĐ</label>
                        <div class="col-md-9">
                          <b-form-input type="number" placeholder="Điện thoại di dộng" v-model="model.AutOrgContactHandPhone"></b-form-input>
                        </div>
                      </div>
                      <div class="form-group row align-items-center" v-if="model.isAutOrg && model.ManagementLevel >=2">
                        <label class="col-md-3">Email</label>
                        <div class="col-md-21">
                          <b-form-input type="email" placeholder="Email" v-model="model.AutOrgContactMail"></b-form-input>
                        </div>
                      </div>
                      <div class="form-group row ij-line-head " v-if="model.isFinOrg && model.ManagementLevel >=2">
                        <label class="col-md-24">Cơ quan tài chính: </label>
                      </div>
                      <div class="form-group row align-items-center mt-3" v-if="model.isFinOrg && model.ManagementLevel >=2">
                        <label class="col-md-3" >Tên đơn vị</label>
                        <div class="col-md-15">
                          <b-form-input type="text" v-model="Fin.FinName">
                          </b-form-input>
                        </div>
                        <div class="col-md-6">
                          <div class="row align-items-center">
                            <label class="col-md-5 pl-0">Mã số</label>
                            <div class="col-md-19">
                              <ijcore-modal-search-listing
                                v-model="Fin" :title="'cơ quan TC'" :table="'company'" :api="'/listing/api/common/list'"
                                :fieldID="'CompanyID'" :fieldNo="'CompanyNo'" :fieldName="'CompanyName'"
                                :fieldAssignID="'FinID'" :fieldAssignNo="'FinNo'" :fieldAssignName="'FinName'"
                                :showNo="true"
                                :FieldWhere="{isFinOrg: 1}"
                                @clearOrg="clearOrg(2)"
                              ></ijcore-modal-search-listing>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="form-group row align-items-center ij-line-head" v-if="model.ManagementLevel >=2 && model.isTreOrg">
                        <label class="col-md-24">Kho bạc nhà nước</label>
                      </div>
                      <div class="form-group row align-items-center mt-3" v-if="model.ManagementLevel >=2 && model.isTreOrg">
                        <label class="col-md-3">Tên đơn vị</label>
                        <div class="col-md-15">
                          <b-form-input type="text" v-model="Tre.TreName"></b-form-input>
                        </div>
                        <div class="col-md-6">
                          <div class="row align-items-center">
                            <label class="col-md-5 pl-0">Mã số</label>
                            <div class="col-md-19">
                              <ijcore-modal-search-listing
                                v-model="Tre" :title="'Kho bạc nhà nước'" :table="'treasury'" :api="'/listing/api/common/list'"
                                :fieldID="'TreasuryID'" :fieldNo="'TreasuryNo'" :fieldName="'TreasuryName'"
                                :fieldAssignID="'TreID'" :fieldAssignNo="'TreNo'" :fieldAssignName="'TreName'"
                                :showNo="true"
                              ></ijcore-modal-search-listing>
                            </div>
                          </div>
                        </div>
                      </div>
                    </b-card>
                </div>
            </vue-perfect-scrollbar>
        </div>
    </div>
</template>

<script>
    import ApiService from '@/services/api.service';
    import Swal from 'sweetalert2';
    import 'sweetalert2/src/sweetalert2.scss';
    import Select2 from 'v-select2-component'
    import IjcoreModalListing from "../../../../components/IjcoreModalListing";
    import IjcoreModalSearchInput from "../../../../components/IjcoreModalSearchInput";
    import CompanyModalSearchInputCatelist from "@/views/ijlisting/company/partials/CompanyModalSearchInputCatelist";
    import IjcoreModalParent from "../../../../components/IjcoreModalParent";
    import IjcoreModalSearchListing from "@/components/IjcoreModalSearchListing";

    const ListRouter = 'listing-company';
    const EditRouter = 'listing-company-edit';
    const ViewRouter = 'listing-company-view';
    const CreateRouter = 'listing-company-create';
    const ViewApi = 'listing/api/company/view';
    const EditApi = 'listing/api/company/edit';
    const CreateApi = 'listing/api/company/create';
    const StoreApi = 'listing/api/company/store';
    const UpdateApi = 'listing/api/company/update';
    const ListApi = 'listing/api/company';
    const getOrgApi = 'listing/api/company/get-org';
    const getTreOrgApi = 'listing/api/treasury/get-tre-org';

    export default {
        name: 'listing-view-item',
        data () {
            return {
              idParams: this.idParamsProps,
              reqParams: this.reqParamsProps,
              model: {
                CompanyID: null,
                CompanyNo: '',
                CompanyName: '',
                ParentID: null,
                ParentNo: '',
                ParentName: '',
                Address: '',
                Tel: '',
                Fax: '',
                Email: '',
                ContactName: '',
                ContactTel: '',
                SbiChapterID: null,
                SbiChapterNo: '',
                ChapterName: '',
                Note: '',
                NOrder: '',
                NumberValue: 1,
                Prefix: '',
                Suffix: '',
                Inactive: false,
                EmployeeName: '',
                EmployeeID: null,
                CompanyOption: [],
                EmployeeOption: [],
                Province: {},
                District: {},
                Commune: {},
                AccessType: 1,
                IsFinancialCompany: false,
                CompanyCate: [],
                CompanyCateList: [],
                CompanyCateValue: [],
                ManagementLevel: 1,
                CenterID: null,
                CenterNo: '',
                CenterName: '',
                SumCompanyType: 1,
                isAutOrg : false,
                AutOrgID : null,
                AutOrgNo : '',
                AutOrgName : '',
                AutOrgChapterID: null,
                AutOrgChapterNo: '',
                AutOrgAddress: '',
                AutOrgContactName: '',
                AutOrgContactPosition: '',
                AutOrgContactOfficePhone:'',
                AutOrgContactHandPhone: '',
                AutOrgContactMail: '',
                isFinOrg: false,
                FinMofID: null,
                FinMofNo: '',
                FinMofName: '',
                FinDofID: null,
                FinDofNo: '',
                FinDofName: '',
                FinDfpID: null,
                FinDfpNo: '',
                FinDfpName: '',
                isTreOrg: false,
                TreMofID: null,
                TreMofNo: '',
                TreMofName: '',
                TreDofID: null,
                TreDofNo: '',
                TreDofName: '',
                TreDfpID: null,
                TreDfpNo: '',
                TreDfpName: '',
              },
              Fin : {
                FinID: null,
                FinNo: '',
                FinName: '',
              },
              Tre: {
                TreID: null,
                TreNo: '',
                TreName: '',
              },
              AccessTypeOptions:{
                1: 'Chia sẻ',
                2: 'Công khai',
                3: 'Riêng tư'
              },
              ManagementLevelOptions: [
                {value: null, text: '--- Chọn cấp quản lý---'},
                {value: 1, text:'Trung ương'},
                {value: 2, text:'Tỉnh'},
                {value: 3, text:'Huyện'},
                {value: 4, text:'Xã'},
              ],
              SumCompanyTypeOptions: [
                {value: 1, text: 'Không tổng hợp dữ liệu'},
                {value: 2, text: 'Tổng hợp dữ liệu theo cấp chính quyền '},
                {value: 3, text: 'Tổng hợp dữ liệu theo ngành'},
              ],
              AutOrgLevelOption: [
                {value: null, text: '--- Chọn cấp cơ quan chính quyền ---'},
                {value: 1, text: 'Cơ quan chính quyền cấp chính phủ'},
                {value: 2, text: 'Cơ quan chính quyền cấp tỉnh'},
                {value: 3, text: 'Cơ quan chính quyền cấp huyện'},
                {value: 4, text: 'Cơ quan chính quyền cấp xã'},
              ],
              FinOrgLevelOptions: [
                {value: null, text: '--- Chọn cơ quan tài chính ---'},
                {value: 1, text: 'Bộ tài chính'},
                {value: 2, text: 'Sở tài chính'},
                {value: 3, text: 'Phòng tài chính kế hoạch'},
              ],
              TreOrgLevelOptions: [
                {value: null, text: '--- Chọn kho bạc nhà nước ---'},
                {value: 1, text: 'Kho bạc nhà nước trung ương'},
                {value: 2, text: 'Kho bạc nhà Tỉnh'},
                {value: 3, text: 'Kho bạc nhà nước Huyện'},
              ],
              stage: {
                  isNotification: false,
                  updatedData: false
              }
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
          Select2,
          CompanyModalSearchInputCatelist,
          IjcoreModalParent,
          IjcoreModalSearchInput,
          IjcoreModalSearchListing,
        },
        beforeCreate() {},
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
            }
        },
        methods: {
            fetchData() {
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
                                self.model.CompanyID = responsesData.data.data.CompanyID;
                                self.model.ParentID = responsesData.data.data.ParentID;
                                self.model.ParentNo = responsesData.data.data.ParentNo;
                                self.model.EmployeeID = responsesData.data.data.EmployeeID;
                                self.model.CompanyName = responsesData.data.data.CompanyName;
                                self.model.CompanyNo = responsesData.data.data.CompanyNo;
                                self.model.Tel = responsesData.data.data.Tel;
                                self.model.Fax = responsesData.data.data.Fax;
                                self.model.Email = responsesData.data.data.Email;
                                self.model.Address = responsesData.data.data.Address;
                                self.model.ContactName = responsesData.data.data.ContactName;
                                self.model.ContactTel = responsesData.data.data.ContactTel;
                                self.model.ChapterID = responsesData.data.data.ChapterID;
                                self.model.ChapterNo = responsesData.data.data.ChapterNo;
                                self.model.ChapterName = responsesData.data.data.ChapterName;
                                self.model.IsFinancialCompany = responsesData.data.data.IsFinancialCompany;
                                self.model.ManagementLevel = responsesData.data.data.ManagementLevel;
                                self.model.CenterID = responsesData.data.data.CenterID;
                                self.model.CenterNo = responsesData.data.data.CenterNo;
                                self.model.CenterName = responsesData.data.data.CenterName;
                                self.model.Province = {
                                  ProvinceID: responsesData.data.data.ProvinceID,
                                  ProvinceName: responsesData.data.data.ProvinceName,
                                }
                                self.model.District = {
                                  DistrictID: responsesData.data.data.DistrictID,
                                  DistrictName: responsesData.data.data.DistrictName,
                                }
                                self.model.Commune = {
                                  CommuneID: responsesData.data.data.CommuneID,
                                  CommuneName: responsesData.data.data.CommuneName,
                                }
                                self.model.Note = responsesData.data.data.Note;
                                self.model.NOrder = responsesData.data.data.NOrder;
                                self.model.NumberValue = responsesData.data.data.NumberValue;
                                self.model.Prefix = responsesData.data.data.Prefix;
                                self.model.Suffix = responsesData.data.data.Suffix;
                                self.model.AccessType = responsesData.data.data.AccessType;
                                self.model.SumCompanyType = responsesData.data.data.SumCompanyType;
                                self.model.Inactive = (responsesData.data.data.Inactive) ? true : false;

                                self.model.CompanyCate = [];
                                if(self.itemCopy.data.CompanyCate){
                                  _.forEach(self.itemCopy.data.CompanyCate, (companyCate, key)=>{
                                    let tmpObj = {};
                                    if(companyCate.CateID){
                                      let cateList = _.find(self.itemCopy.data.CompanyCateList, ['CateID', companyCate.CateID]);
                                      if(cateList){
                                        tmpObj.CateID = cateList.CateID;
                                        tmpObj.CateName = cateList.CateName;
                                      }
                                    }
                                    if(companyCate.CateValue){
                                      // let cateValue = _.find(self.itemCopy.data.CompanyCateValue, (cate)=> {
                                      //   return cate.CateID === companyCate.CateID && cate.CateValue === companyCate.CateValue;
                                      // });
                                      let cateValue = _.find(self.itemCopy.data.CompanyCateValue,{
                                        CateID: companyCate.CateID,
                                        CateValue: companyCate.CateValue
                                      });
                                      if(cateValue){
                                        tmpObj.CateValue = cateValue.CateValue;
                                        tmpObj.Description = cateValue.Description;
                                      }
                                    }
                                    else{
                                      tmpObj.CateValue = null;
                                      tmpObj.Description = '';
                                    }
                                    // self.model.CompanyCate.push(tmpObj);
                                    self.$set(self.model.CompanyCate, self.model.CompanyCate.length, tmpObj);
                                  })
                                }
                                if(self.itemCopy.data.Parent){
                                  self.model.ParentID = self.itemCopy.data.Parent.ParentID;
                                  self.model.ParentNo = self.itemCopy.data.Parent.ParentNo;
                                  self.model.ParentName = self.itemCopy.data.Parent.ParentName;
                                }
                            }

                            if (!_.isEmpty(self.itemCopy)) {
                                self.model.CompanyNo = responsesData.data.auto;
                            }
                        }else {
                            self.model.CompanyNo = responsesData.data.auto;
                        }


                        if (_.isArray(responsesData.data.company)) {

                            self.model.CompanyOption = [];
                            _.forEach(responsesData.data.company, function (value, key) {
                                let tmpObj = {};
                                tmpObj.id = value.CompanyID;
                                tmpObj.text = value.CompanyName;
                                self.model.CompanyOption.push(tmpObj);
                            });
                        }


                        if (_.isArray(responsesData.data.employee)) {

                            self.model.EmployeeOption = [];
                            _.forEach(responsesData.data.employee, function (value, key) {
                                let tmpObj = {};
                                tmpObj.id = value.EmployeeID;
                                tmpObj.text = value.EmployeeName;
                                self.model.EmployeeOption.push(tmpObj);
                            });
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
                url: '/listing/api/common/auto-child',
                data: {
                  per_page: 10,
                  page: this.currentPage,
                  table: 'company',
                  ParentID: this.model.ParentID,
                },

              };

              ApiService.customRequest(requestData).then((response) => {
                let responseData = response.data;

                this.model.CompanyNo = responseData.data;
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

            handleSubmitForm(){
              let self = this;
              if(self.model.ManagementLevel == 1){
                self.model.Province.ProvinceID = null;
                self.model.Province.ProvinceNo = '';
                self.model.Province.ProvinceName = '';
                self.model.District.DistrictID = null;
                self.model.District.DistrictNo = '';
                self.model.District.DistrictName = '';
                self.model.Commune.CommuneID = null;
                self.model.Commune.CommuneNo = '';
                self.model.Commune.CommuneName = '';
              }
              if(self.model.ManagementLevel == 2){
                self.model.CenterID = null;
                self.model.CenterName = '';
                self.model.CenterNo = '';
                self.model.District.DistrictID = null;
                self.model.District.DistrictNo = '';
                self.model.District.DistrictName = '';
                self.model.Commune.CommuneID = null;
                self.model.Commune.CommuneNo = '';
                self.model.Commune.CommuneName = '';
                if(self.model.isFinOrg){
                  self.model.FinMofID = self.Fin.FinID;
                  self.model.FinMofNo = self.Fin.FinNo;
                  self.model.FinMofName = self.Fin.FinName;
                }
                if(self.model.isTreOrg){
                  self.model.TreMofID = self.Tre.TreID;
                  self.model.TreMofNo = self.Tre.TreNo;
                  self.model.TreMofName = self.Tre.TreName;
                }
              }
              if(self.model.ManagementLevel == 3){
                self.model.CenterID = null;
                self.model.CenterName = '';
                self.model.CenterNo = '';
                self.model.Commune.CommuneID = null;
                self.model.Commune.CommuneNo = '';
                self.model.Commune.CommuneName = '';
                if(self.model.isFinOrg){
                  self.model.FinDofID = self.Fin.FinID;
                  self.model.FinDofNo = self.Fin.FinNo;
                  self.model.FinDofName = self.Fin.FinName;
                }
                if(self.model.isTreOrg){
                  self.model.TreDofID = self.Tre.TreID;
                  self.model.TreDofNo = self.Tre.TreNo;
                  self.model.TreDofName = self.Tre.TreName;
                }
              }
              if(self.model.ManagementLevel == 4){
                self.model.CenterID = null;
                self.model.CenterName = '';
                self.model.CenterNo = '';
                if(self.model.isFinOrg){
                  self.model.FinDfpID = self.Fin.FinID;
                  self.model.FinDfpNo = self.Fin.FinNo;
                  self.model.FinDfpName = self.Fin.FinName;
                }
                if(self.model.isTreOrg){
                  self.model.TreDfpID = self.Tre.TreID;
                  self.model.TreDfpNo = self.Tre.TreNo;
                  self.model.TreDfpName = self.Tre.TreName;
                }
              }
              const requestData = {
                  method: 'post',
                  url: StoreApi+'?XDEBUG_SESSION_START=PHPSTORM',
                  data: {
                    CompanyNo: this.model.CompanyNo,
                    CompanyName: this.model.CompanyName,
                    Address: this.model.Address,
                    Inactive: (this.model.Inactive) ? 1 : 0,
                    Tel: this.model.Tel,
                    Fax: this.model.Fax,
                    Email: this.model.Email,
                    Note: this.model.Note,
                    ContactName: this.model.ContactName,
                    ContactTel: this.model.ContactTel,
                    SbiChapterID: this.model.SbiChapterID,
                    SbiChapterNo: this.model.SbiChapterNo,
                    SbiChapterName: this.model.ChapterName,
                    ParentID: this.model.ParentID,
                    ParentNo: this.model.ParentNo,
                    EmployeeID: this.model.EmployeeID,
                    ProvinceID: this.model.Province.ProvinceID,
                    ProvinceName: this.model.Province.ProvinceName,
                    DistrictID: this.model.District.DistrictID,
                    DistrictName: this.model.District.DistrictName,
                    CommuneID: this.model.Commune.CommuneID,
                    CommuneName: this.model.Commune.CommuneName,
                    AccessType: this.model.AccessType,
                    IsFinancialCompany: this.model.IsFinancialCompany,
                    NumberValue: this.model.NumberValue,
                    CompanyCate: this.model.CompanyCate,
                    ManagementLevel: this.model.ManagementLevel,
                    CenterID: this.model.CenterID,
                    CenterNo: this.model.CenterNo,
                    CenterName: this.model.CenterName,
                    SumCompanyType: this.model.SumCompanyType,
                    isAutOrg: this.model.isAutOrg ? 1 : 0,
                    AutOrgID: this.model.AutOrgName,
                    AutOrgNo: this.model.AutOrgNo,
                    AutOrgName: this.model.AutOrgName,
                    AutOrgChapterID: this.model.AutOrgChapterID,
                    AutOrgChapterNo: this.model.AutOrgChapterNo,
                    AutOrgContactName : this.model.AutOrgContactName,
                    AutOrgContactPosition: this.model.AutOrgContactPosition,
                    AutOrgContactHandPhone : this.model.AutOrgContactHandPhone,
                    AutOrgContactOfficePhone : this.model.AutOrgContactOfficePhone,
                    AutOrgContactMail : this.model.AutOrgContactMail,
                    AutOrgAddress : this.model.AutOrgAddress,
                    isFinOrg: this.model.isFinOrg ? 1 : 0,
                    FinMofID: this.model.FinMofID,
                    FinMofNo: this.model.FinMofNo,
                    FinMofName: this.model.FinMofName,
                    FinDofID: this.model.FinDofID,
                    FinDofNo: this.model.FinDofNo,
                    FinDofName: this.model.FinDofName,
                    FinDfpID: this.model.FinDfpID,
                    FinDfpNo: this.model.FinDfpNo,
                    FinDfpName: this.model.FinDfpName,
                    isTreOrg : this.model.isTreOrg,
                    TreMofID : this.model.TreMofID,
                    TreMofNo : this.model.TreMofNo,
                    TreMofName : this.model.TreMofName,
                    TreDofID : this.model.TreDofID,
                    TreDofNo : this.model.TreDofNo,
                    TreDofName : this.model.TreDofName,
                    TreDfpID : this.model.TreDfpID,
                    TreDfpNo : this.model.TreDfpNo,
                    TreDfpName : this.model.TreDfpName,
                  }
                };

                // edit user
                if (this.idParams) {
                    requestData.data.ItemID = this.idParams;
                    requestData.url = UpdateApi + '/' + this.idParams;
                }

                this.$store.commit('isLoading', true);
                ApiService.setHeader();
                ApiService.customRequest(requestData).then((responses) => {
                    let responsesData = responses.data;
                    if (responsesData.status === 1) {
                      if (!self.idParams && responsesData.data) self.idParams = responsesData.data;
                      let item = {
                        CompanyID: responsesData.data,
                        CompanyNo: self.model.CompanyNo,
                        CompanyName: self.model.CompanyName,
                        Address: self.model.Address,
                        Inactive: (self.model.Inactive) ? 1 : 0,
                        Tel: self.model.Tel,
                        Fax: self.model.Fax,
                        Email: self.model.Email,
                        Note: self.model.Note,
                        ContactName: self.model.ContactName,
                        ContactTel: self.model.ContactTel,
                        ChapterID: self.model.ChapterID,
                        ChapterNo: self.model.ChapterNo,
                        ChapterName: self.model.ChapterName,
                        ParentID: self.model.ParentID,
                        ParentNo: self.model.ParentNo,
                        EmployeeID: self.model.EmployeeID,
                        ProvinceID: self.model.Province.ProvinceID,
                        ProvinceName: self.model.Province.ProvinceName,
                        DistrictID: self.model.District.DistrictID,
                        DistrictName: self.model.District.DistrictName,
                        CommuneID: self.model.Commune.CommuneID,
                        CommuneName: self.model.Commune.CommuneName,
                        AccessType: self.model.AccessType,
                        NumberValue: self.model.NumberValue,
                        CompanyCate: self.model.CompanyCate,
                        ManagementLevel: self.model.ManagementLevel,
                        CenterID: self.model.CenterID,
                        CenterNo: self.model.CenterNo,
                        CenterName: self.model.CenterName,
                        SumCompanyType: self.model.SumCompanyType
                      }
                      if(self.$route.params.req){
                        let indexold =  _.findIndex(self.$route.params.req.itemsArray, {'CompanyID': item.CompanyID});
                        let indexParent = null;
                        if(indexold  < 0){
                          if(self.model.ParentID){
                            indexParent = _.findIndex(self.$route.params.req.itemsArray, {'CompanyID': self.model.ParentID});
                            if(indexParent >= 0){
                              let level = self.$route.params.req.itemsArray[indexParent].Level;
                              if(self.$route.params.req.itemsArray[indexParent].Detail) {
                                self.$set(self.$route.params.req.itemsArray[indexParent], 'Detail', 0);
                                self.$set(self.$route.params.req.itemsArray[indexParent], 'Class', 'fa fa-minus-square-o');
                              }
                              item.Level = level + 1;
                              item.Detail = 1;
                              self.$route.params.req.itemsArray = __.insertBeforeKey(self.$route.params.req.itemsArray, indexParent + 1, item);
                            }
                          } else {
                            item.Level = 1;
                            item.Detail = 1;
                            self.$route.params.req.itemsArray.push(item);
                            _.orderBy(self.$route.params.req.itemsArray, ['CompanyID'], 'asc');
                          }
                        }
                      }
                      self.onBackToList('Bản ghi đã được cập nhật');
                    }else if (responsesData.status === 4){
                      Swal.fire(
                        'Lỗi',
                        'Không được sửa bản ghi Tổng hợp',
                        'error'
                      )
                    }else if (responsesData.status === 5){
                      Swal.fire(
                        'Lỗi',
                        'Cấp quản lý Huyện hoặc Xã : nhập cấp đơn vị cha!',
                        'error'
                      )
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

          onBackToList(message = '') {

            let self = this;
            let params = (this.$route.params.req) ? this.$route.params.req:{};
            let query = this.$route.query;
            query.isBackToList = true;
            if (_.isString(message)) {
              params.message = message;
              this.$router.push({
                name: ViewRouter,
                query: query,
                params: {id: self.idParams, req: params, message: 'Bản ghi đã được cập nhật!'}
              });
            } else {
              this.$router.push({
                name: ListRouter,
                query: query,
                params: params
              });
            }
          },

          updateModel() {
                if (this.stage.updatedData) {
                    this.$forceUpdate();
                }
            },

            autoCorrectedTaxRatePipe() {

            },
          changeUserContact() {
            let employee = _.find(this.model.EmployeeOption, ['id', Number(this.model.EmployeeID)]);
            if (employee) {
              this.model.ContactName = employee.text;
            }
          },
          autoOrg(companyType, urlApi) {
            let self = this;
            this.clearOrg(companyType);
            let provinceID = null;
            let districtID = null;
            let isAutOrg = null;
            let isFinOrg = null;
            let isTreOrg = null;
            if(self.model.District.DistrictID && self.model.ManagementLevel == 3){
              provinceID = self.model.District.ProvinceID;
            }
            if(self.model.Commune.CommuneID && self.model.ManagementLevel == 4){
              provinceID = self.model.Commune.ProvinceID;
              districtID = self.model.Commune.DistrictID;
            }
            if(companyType === 1){
              isAutOrg = 1;
            } else if(companyType === 2){
              isFinOrg = 1;
            } else {
              isTreOrg = 1;
            }
            let requestData = {
              method: 'post',
              url: urlApi +'?XDEBUG_SESSION_START=PHPSTORM',
              data: {
                ManagementLevel: self.model.ManagementLevel - 1,
                ProvinceID: provinceID,
                DistrictID: districtID,
                isAutOrg: isAutOrg,
                isFinOrg: isFinOrg,
                isTreOrg: isTreOrg,
              }
            }
            self.$store.commit('isLoading',true)
            ApiService.setHeader();
            ApiService.customRequest(requestData)
              .then(response=>{
                let responseData = response.data;
                if(responseData.status === 1){
                  debugger
                  if(responseData.data){
                    if(companyType === 1){
                      self.model.AutOrgID = responseData.data.CompanyID;
                      self.model.AutOrgNo = responseData.data.CompanyNo;
                      self.model.AutOrgName = responseData.data.CompanyName;
                      self.model.AutOrgChapterID = responseData.data.SbiChapterID;
                      self.model.AutOrgChapterNo = responseData.data.SbiChapterNo;
                      self.model.AutOrgAddress = responseData.data.Address;
                      self.model.AutOrgContactOfficePhone = responseData.data.Tel;
                      self.model.AutOrgContactHandPhone = responseData.data.ContactTel;
                      self.model.AutOrgContactMail = responseData.data.Email;
                      if(responseData.position){
                        self.model.AutOrgContactName = responseData.position.EmployeeName;
                        self.model.AutOrgContactPosition = responseData.position.PositionName;
                      }
                    }
                   if(companyType === 2){
                     self.Fin.FinID =  responseData.data.CompanyID;
                     self.Fin.FinNo =  responseData.data.CompanyNo;
                     self.Fin.FinName =  responseData.data.CompanyName;
                   }
                   if(companyType === 3){
                     self.Tre.TreID =  responseData.data.TreasuryID;
                     self.Tre.TreNo =  responseData.data.TreasuryNo;
                     self.Tre.TreName =  responseData.data.TreasuryName;
                   }
                  }

                } else if (responsesData.status === 4){
                  Swal.fire(
                    'Lỗi',
                    'Không có kho bạc nhà nước cấp Xã!',
                    'error'
                  )
                } else {
                  let htmlErrors = __.renderErrorApiHtml(responsesData.data);
                  Swal.fire(
                    'Thông báo',
                    htmlErrors,
                    'error'
                  )
                }
                self.$store.commit('isLoading',false)
              }).catch(error=> {
              self.$store.commit('isLoading',false)
            })
          },
          selectOrg(companyID){
            console.log(companyID);
            let self = this;
            let requestData = {
              method: 'get',
              url: 'listing/api/company/view' + '/' + companyID,
              data: {
                id: companyID
              }
            }
            self.$store.commit('isLoading',true)
            ApiService.setHeader();
            ApiService.customRequest(requestData)
              .then(response=>{
                let responseData = response.data;
                if(responseData.status === 1){
                  self.model.AutOrgAddress = responseData.data.data.Address;
                  self.model.AutOrgChapterID = responseData.data.data.SbrSectorID;
                  self.model.AutOrgChapterNo = responseData.data.data.SbrSectorNo;
                  self.model.AutOrgChapter = responseData.data.data.SbrSectorNo;
                  self.model.AutOrgContactOfficePhone = responseData.data.data.Tel;
                  self.model.AutOrgContactHandPhone = responseData.data.data.ContactTel;
                  self.model.AutOrgContactMail = responseData.data.data.Email;
                  let contact = _.find(responseData.Employee, ['EmployeeID', responseData.data.data.EmployeeID]);
                  self.model.AutOrgContactName = (contact) ? contact['EmployeeName'] : null;
                  self.model.AutOrgContactPosition = (contact) ? contact['PositionName'] : null;
                }
                self.$store.commit('isLoading',false)
              }).catch(error=> {
              self.$store.commit('isLoading',false)
            });
          },
          clearOrg(companyType){
            let self = this;
            if(companyType = 1){
              self.model.AutOrgID = null;
              self.model.AutOrgNo = '';
              self.model.AutOrgName = '';
              self.model.AutOrgChapterID = null;
              self.model.AutOrgChapterNo = null;
              self.model.AutOrgAddress = '';
              self.model.AutOrgContactOfficePhone = '';
              self.model.AutOrgContactHandPhone = '';
              self.model.AutOrgContactMail = '';
              self.model.AutOrgContactName = '';
              self.model.AutOrgContactPosition = '';
            }
            if (companyType == 2){
              self.Fin.FinID = null;
              self.Fin.FinNo = '';
              self.Fin.FinName = '';
            }
            if(companyType == 3){
              self.Tre.TreID = null;
              self.Tre.TreNo = '';
              self.Tre.TreName = '';
            }
          }
        },
        watch: {
          idParams() {
              this.fetchData();
          },
          'model.ParentID'(){
              let self = this;
              let urlApi = '/listing/api/common/auto-child';
              let requestData = {
                method: 'post',
                url: urlApi,
                data: {
                  per_page: 10,
                  page: this.currentPage,
                  table: 'company',
                  ParentID: this.model.ParentID,
                }
              }
              self.$store.commit('isLoading',true)
              ApiService.setHeader();
              ApiService.customRequest(requestData)
                .then(response=>{
                  let responseData = response.data;
                  if(responseData.status === 1){
                    self.model.CompanyNo = responseData.data;
                  }
                  self.$store.commit('isLoading',false)
                }).catch(error=> {
                self.$store.commit('isLoading',false)
              })
          },
          'model.Province'() {
            let self = this;
            // Cơ quan chủ quản
            if(this.model.ManagementLevel == 2 && this.model.isAutOrg ){
              if(this.model.Province.ProvinceID){
                this.autoOrg(1, getOrgApi);
              } else {
                self.clearOrg(1);
              }
            }
            // Cơ quản lý
            if(this.model.ManagementLevel == 2 && this.model.isFinOrg ){
              if(this.model.Province.ProvinceID){
                this.autoOrg(2, getOrgApi);
              } else {
                self.clearOrg(2);
              }
            }
            if(this.model.ManagementLevel == 2 && this.model.isTreOrg){
              if(this.model.Province.ProvinceID){
                this.autoOrg(3, getTreOrgApi);
              } else {
                self.clearOrg(3);
              }
            }
          },
          'model.District'() {
            let self = this;
            if(this.model.ManagementLevel == 3 && this.model.isAutOrg){
              if( this.model.District.DistrictID){
                this.autoOrg(1, getOrgApi);
              } else {
                self.clearOrg(1);
              }
            }
            if(this.model.ManagementLevel == 3 && this.model.isFinOrg){
              if( this.model.District.DistrictID){
                this.autoOrg(2, getOrgApi);
              } else {
                self.clearOrg(2);
              }
            }
            if(this.model.ManagementLevel == 3 && this.model.isTreOrg){
              if( this.model.District.DistrictID){
                this.autoOrg(3, getTreOrgApi);
              } else {
                self.clearOrg(3);
              }
            }
          },
          'model.Commune'(){
            let self = this;
            if(this.model.ManagementLevel == 4 && this.model.isAutOrg) {
              if(this.model.Commune.CommuneID){
                this.autoOrg(1, getOrgApi);
              } else {
                self.clearOrg(1);
              }
            }
            if(this.model.ManagementLevel == 4 && this.model.isFinOrg) {
              if(this.model.Commune.CommuneID){
                this.autoOrg(2, getOrgApi);
              } else {
                self.clearOrg(2);
              }
            }
            if(this.model.ManagementLevel == 4 && this.model.isTreOrg) {
              if(this.model.Commune.CommuneID){
                this.autoOrg(3, getTreOrgApi);
              } else {
                self.clearOrg(3);
              }
            }
          },
          'model.isAutOrg'() {
            if(this.model.ManagementLevel > 1 && this.model.isAutOrg){
              this.autoOrg(1, getOrgApi);
            }
          },
          'model.isFinOrg'() {
            if(this.model.ManagementLevel > 1 && this.model.isFinOrg){
              this.autoOrg(2, getOrgApi);
            }
          },
          'model.isTreOrg'() {
            debugger
            if(this.model.ManagementLevel > 1 && this.model.isTreOrg){
              this.autoOrg(3, getTreOrgApi);
            }
          },

        }
    }
</script>
<!--<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>-->
<style lang="css">
  .select2-container{
    width: 100% !important;
  }
</style>
